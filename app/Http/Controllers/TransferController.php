<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\File;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\BrevoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Jenssegers\Agent\Agent;

class TransferController extends Controller
{
    protected $brevoService;

    public function __construct(BrevoService $brevoService)
    {
        $this->brevoService = $brevoService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Transfer::with(['sender', 'receiver', 'file']);

        if (!$user->isSuperAdmin()) {
            if ($user->isDepartmentAdmin()) {
                $query->whereHas('sender', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                });
            } else {
                $query->where(function($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
                });
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transfer_id', 'like', '%' . $request->search . '%')
                  ->orWhere('purpose', 'like', '%' . $request->search . '%')
                  ->orWhere('receiver_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('sender_id') && $user->isSuperAdmin()) {
            $query->where('sender_id', $request->sender_id);
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'in_transit' => (clone $query)->where('status', 'in_transit')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'overdue' => (clone $query)->where('status', '!=', 'delivered')
                ->where('expected_delivery_time', '<', now())
                ->count(),
        ];

        $users = User::where('status', 'active')->get();

        return view('transfers.index', compact('transfers', 'stats', 'users'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $users = User::where('status', 'active')
            ->where('id', '!=', $user->id)
            ->when(!$user->isSuperAdmin(), function($query) use ($user) {
                if ($user->isDepartmentAdmin()) {
                    $query->where('department_id', $user->department_id);
                }
            })
            ->get();

        $files = File::where('owner_id', $user->id)
            ->where('status', 'active')
            ->get();

        return view('transfers.create', compact('users', 'files'));
    }

    public function store(Request $request)
    {
        // Determine receiver type
        $receiverType = $request->input('receiver_type', 'internal');
        
        // Conditional validation rules
        $rules = [
            'receiver_type' => 'required|in:internal,external',
            'file_id' => 'nullable|exists:files,id',
            'purpose' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'expected_delivery_time' => 'required|date|after:now',
            'tracking_number' => 'nullable|string|max:100',
            'courier_name' => 'nullable|string|max:100',
        ];

        // Apply conditional validation based on receiver type
        if ($receiverType === 'internal') {
            $rules['receiver_id'] = 'required|exists:users,id';
            // Make sure external fields are not sent
            $request->merge([
                'receiver_name' => null,
                'receiver_email' => null,
                'receiver_phone' => null
            ]);
        } else {
            $rules['receiver_name'] = 'required|string|max:255';
            $rules['receiver_email'] = 'required|email|max:255';
            $rules['receiver_phone'] = 'nullable|string|max:20';
            // Make sure internal field is not sent
            $request->merge(['receiver_id' => null]);
        }

        $request->validate($rules);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $transfer = Transfer::create([
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'receiver_name' => $request->receiver_name,
                'receiver_email' => $request->receiver_email,
                'receiver_phone' => $request->receiver_phone,
                'file_id' => $request->file_id,
                'purpose' => $request->purpose,
                'description' => $request->description,
                'expected_delivery_time' => $request->expected_delivery_time,
                'tracking_number' => $request->tracking_number,
                'courier_name' => $request->courier_name,
                'status' => 'pending',
            ]);

            // Send notification email
            $receiverEmail = $request->receiver_email ?? ($transfer->receiver->email ?? null);
            if ($receiverEmail) {
                $this->brevoService->sendTransferCreatedEmail(
                    $receiverEmail,
                    $user->name,
                    $transfer->transfer_id,
                    $transfer->purpose
                );
            }

            $this->logActivity(
                $user,
                $request,
                'create_transfer',
                'transfer',
                'Created new transfer: ' . $transfer->transfer_id,
                null,
                null,
                $transfer->id,
                $transfer->toArray()
            );

            DB::commit();

            return redirect()->route('transfers.show', $transfer)
                ->with('success', 'Transfer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create transfer: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Transfer $transfer)
    {
        $this->authorize('view', $transfer);
        
        $transfer->load(['sender', 'receiver', 'file', 'activityLogs.user']);
        
        return view('transfers.show', compact('transfer'));
    }

    public function confirmDelivery(Request $request, Transfer $transfer)
    {
        $request->validate([
            'delivery_location' => 'required|string|max:500',
            'received_by' => 'required|string|max:255',
            'signature' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($transfer->receiver_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only the receiver can confirm delivery.');
        }

        if ($transfer->status !== 'pending' && $transfer->status !== 'in_transit') {
            return back()->withErrors(['error' => 'This transfer cannot be confirmed.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $transfer->toArray();
            
            $transfer->update([
                'actual_delivery_time' => now(),
                'status' => 'delivered',
                'delivery_location' => $request->delivery_location,
                'received_by' => $request->received_by,
                'signature' => $request->signature,
                'notes' => $request->notes,
            ]);

            $this->logActivity(
                Auth::user(),
                $request,
                'confirm_delivery',
                'transfer',
                'Confirmed delivery of transfer: ' . $transfer->transfer_id,
                $oldData,
                null,
                $transfer->id,
                $transfer->toArray()
            );

            // Notify sender
            if ($transfer->sender) {
                $this->brevoService->sendTransferDeliveredEmail(
                    $transfer->sender->email,
                    $transfer->transfer_id,
                    $transfer->received_by,
                    $transfer->delivery_location
                );
            }

            DB::commit();

            return redirect()->route('transfers.show', $transfer)
                ->with('success', 'Delivery confirmed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to confirm delivery.']);
        }
    }

    public function cancel(Request $request, Transfer $transfer)
    {
        if ($transfer->sender_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        if (!in_array($transfer->status, ['pending', 'in_transit'])) {
            return back()->withErrors(['error' => 'This transfer cannot be cancelled.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $transfer->toArray();
            
            $transfer->update(['status' => 'cancelled']);

            $this->logActivity(
                Auth::user(),
                $request,
                'cancel_transfer',
                'transfer',
                'Cancelled transfer: ' . $transfer->transfer_id,
                $oldData,
                null,
                $transfer->id,
                $transfer->toArray()
            );

            DB::commit();

            return redirect()->route('transfers.index')
                ->with('success', 'Transfer cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to cancel transfer.']);
        }
    }

    public function track(Request $request, $transferId)
    {
        $transfer = Transfer::where('transfer_id', $transferId)
            ->with(['sender', 'receiver'])
            ->firstOrFail();

        return view('transfers.track', compact('transfer'));
    }

    public function markInTransit(Request $request, Transfer $transfer)
    {
        if ($transfer->sender_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        if ($transfer->status !== 'pending') {
            return back()->withErrors(['error' => 'Transfer is not in pending status.']);
        }

        $oldData = $transfer->toArray();
        $transfer->update(['status' => 'in_transit']);

        $this->logActivity(
            Auth::user(),
            $request,
            'mark_in_transit',
            'transfer',
            'Marked transfer as in transit: ' . $transfer->transfer_id,
            $oldData,
            null,
            $transfer->id,
            $transfer->toArray()
        );

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Transfer marked as in transit.');
    }

    private function logActivity($user, $request, $action, $module, $description, $oldData = null, $fileId = null, $transferId = null, $newData = null)
    {
        $agent = new Agent();
        
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'module' => $module,
            'file_id' => $fileId,
            'transfer_id' => $transferId,
            'description' => $description,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
        ]);
    }
}