<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\File;
use App\Models\User;
use App\Services\OtpService;
use App\Services\ActivityLogger;
use App\Services\BrevoEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
    protected $otpService;
    protected $emailService;

    public function __construct(OtpService $otpService, BrevoEmailService $emailService)
    {
        $this->otpService = $otpService;
        $this->emailService = $emailService;
    }

    public function index()
    {
        $user = Auth::user();
        
        $transfers = Transfer::with(['sender', 'receiver', 'file'])
            ->when(!$user->isSuperAdmin(), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('transfers.index', compact('transfers'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->hasPermission('transfers.create') && !$user->isSender() && !$user->isOwner()) {
            abort(403, 'Unauthorized action.');
        }

        // Get files owned by user
        $files = File::where('owner_id', $user->id)
            ->where('is_archived', false)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->get();

        // Get potential receivers (same department)
        $receivers = User::where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->get();

        return view('transfers.create', compact('files', 'receivers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasPermission('transfers.create') && !$user->isSender() && !$user->isOwner()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
    'file_id' => 'required|exists:files,id',
    'receiver_id' => 'required|exists:users,id',
    'transfer_type' => 'required|in:physical,cloud',
    'purpose' => 'required|string|max:500',
    'expected_delivery_time' => 'required|date|after:now',

    'third_party_involved' => 'nullable|boolean',
    'third_party_name' => 'nullable|required_if:third_party_involved,1|string|max:255',
    'third_party_email' => 'nullable|required_if:third_party_involved,1|email',

    'notes' => 'nullable|string|max:1000',
]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check file ownership
        $file = File::findOrFail($request->file_id);
        if ($file->owner_id !== $user->id) {
            return redirect()->back()->with('error', 'You can only transfer files you own.');
        }

        // Create transfer
        $transfer = Transfer::create([
    'sender_id' => $user->id,
    'receiver_id' => $request->receiver_id,
    'file_id' => $request->file_id,
    'transfer_type' => $request->transfer_type,
    'purpose' => $request->purpose,
    'status' => 'pending',
    'expected_delivery_time' => $request->expected_delivery_time,

    'third_party_involved' => $request->boolean('third_party_involved'),

    'third_party_name' => $request->boolean('third_party_involved')
        ? $request->third_party_name
        : null,

    'third_party_email' => $request->boolean('third_party_involved')
        ? $request->third_party_email
        : null,

    'notes' => $request->notes,
]);


        // Log activity
        ActivityLogger::log('transfer_create', "Created transfer for file: {$file->original_name}", $user->id, $file->id, $transfer->id);

        // Send email notification to receiver
        $receiver = User::find($request->receiver_id);
        $this->emailService->sendAlert(
            $receiver->email,
            'File Transfer Initiated',
            "A file transfer has been initiated for you. Please check your transfers.",
            [
                'file_name' => $file->original_name,
                'sender' => $user->name,
                'purpose' => $request->purpose,
                'expected_delivery' => $transfer->expected_delivery_time->format('Y-m-d H:i:s'),
            ]
        );

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Transfer initiated successfully.');
    }

    public function show(Transfer $transfer)
    {
        $user = Auth::user();
        
        // Check authorization
        if ($transfer->sender_id !== $user->id && $transfer->receiver_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $transfer->load(['sender', 'receiver', 'file', 'activityLogs']);

        return view('transfers.show', compact('transfer'));
    }

    public function send(Transfer $transfer)
    {
        $user = Auth::user();
        
        // Only sender can send
        if ($transfer->sender_id !== $user->id) {
            abort(403, 'Only the sender can send the transfer.');
        }

        if ($transfer->status !== 'pending') {
            return redirect()->back()->with('error', 'Transfer cannot be sent in current status.');
        }

        // Update status
        $transfer->update(['status' => 'in_transit']);

        // Log activity
        ActivityLogger::log('transfer_send', "Sent transfer to receiver", $user->id, $transfer->file_id, $transfer->id);

        // Send email to receiver
        $receiver = $transfer->receiver;
        $this->emailService->sendAlert(
            $receiver->email,
            'File Transfer In Transit',
            "Your file transfer is now in transit. Please be ready to receive it.",
            [
                'file_name' => $transfer->file->original_name,
                'transfer_id' => $transfer->transfer_uuid,
                'sender' => $user->name,
            ]
        );

        return redirect()->back()->with('success', 'Transfer marked as in transit.');
    }

    public function deliver(Request $request, Transfer $transfer)
    {
        $user = Auth::user();
        
        // Only sender can mark as delivered
        if ($transfer->sender_id !== $user->id) {
            abort(403, 'Only the sender can mark as delivered.');
        }

        $validator = Validator::make($request->all(), [
            'delivery_location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $transfer->update([
            'status' => 'delivered',
            'actual_delivery_time' => now(),
            'delivery_location' => $request->delivery_location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Log activity
        ActivityLogger::log('transfer_send', "Delivered transfer to location: {$request->delivery_location}", $user->id, $transfer->file_id, $transfer->id);

        // Send email to receiver
        $receiver = $transfer->receiver;
        $this->emailService->sendAlert(
            $receiver->email,
            'File Delivered',
            "Your file has been delivered. Please confirm receipt.",
            [
                'file_name' => $transfer->file->original_name,
                'delivery_location' => $request->delivery_location,
                'delivery_time' => now()->format('Y-m-d H:i:s'),
            ]
        );

        return redirect()->back()->with('success', 'Transfer marked as delivered.');
    }

    public function receive(Transfer $transfer)
    {
        $user = Auth::user();
        
        // Only receiver can receive
        if ($transfer->receiver_id !== $user->id) {
            abort(403, 'Only the receiver can confirm receipt.');
        }

        if ($transfer->status !== 'delivered') {
            return redirect()->back()->with('error', 'Transfer must be delivered before receiving.');
        }

        // Require OTP for receipt confirmation
        session(['otp_required_transfer_approval' => true]);
        session(['receive_transfer_id' => $transfer->id]);
        
        return redirect()->route('otp.verify', ['purpose' => 'transfer_approval'])
            ->with('warning', 'OTP required to confirm receipt.');
    }

    public function confirmReceipt(Request $request)
    {
        $user = Auth::user();
        $transferId = session('receive_transfer_id');
        
        if (!$transferId) {
            return redirect()->route('transfers.index')->with('error', 'Session expired.');
        }

        $transfer = Transfer::findOrFail($transferId);
        
        if ($transfer->receiver_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Update status
        $transfer->update(['status' => 'received']);

        // Log activity
        ActivityLogger::log('transfer_receive', "Confirmed receipt of transfer", $user->id, $transfer->file_id, $transfer->id);

        // Clear session
        session()->forget(['receive_transfer_id', 'otp_required_transfer_approval']);

        // Send email to sender
        $sender = $transfer->sender;
        $this->emailService->sendAlert(
            $sender->email,
            'File Received',
            "Your file transfer has been received and confirmed.",
            [
                'file_name' => $transfer->file->original_name,
                'receiver' => $user->name,
                'received_at' => now()->format('Y-m-d H:i:s'),
            ]
        );

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Receipt confirmed successfully.');
    }

    public function cancel(Transfer $transfer)
    {
        $user = Auth::user();
        
        // Only sender or receiver can cancel
        if ($transfer->sender_id !== $user->id && $transfer->receiver_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($transfer->status, ['pending', 'in_transit'])) {
            return redirect()->back()->with('error', 'Transfer cannot be cancelled in current status.');
        }

        $oldStatus = $transfer->status;
        $transfer->update(['status' => 'cancelled']);

        // Log activity
        ActivityLogger::log('transfer_cancel', "Cancelled transfer (was: {$oldStatus})", $user->id, $transfer->file_id, $transfer->id);

        // Send email to other party
        $otherParty = $transfer->sender_id === $user->id ? $transfer->receiver : $transfer->sender;
        $this->emailService->sendAlert(
            $otherParty->email,
            'Transfer Cancelled',
            "A file transfer has been cancelled.",
            [
                'file_name' => $transfer->file->original_name,
                'cancelled_by' => $user->name,
                'cancelled_at' => now()->format('Y-m-d H:i:s'),
            ]
        );

        return redirect()->route('transfers.index')->with('success', 'Transfer cancelled successfully.');
    }

    public function requestThirdPartyAccess(Transfer $transfer)
    {
        $user = Auth::user();
        
        // Only receiver can request third-party access
        if ($transfer->receiver_id !== $user->id) {
            abort(403, 'Only the receiver can request third-party access.');
        }

        if (!$transfer->third_party_involved) {
            return redirect()->back()->with('error', 'This transfer does not involve third parties.');
        }

        // Send OTP to owner for approval
        $owner = $transfer->sender;
        $result = $this->otpService->generateAndSendOTP($owner->email, 'third_party_access', $owner->id, $transfer->file_id, $transfer->id);

        if ($result['success']) {
            // Store transfer ID in session
            session(['third_party_transfer_id' => $transfer->id]);
            
            return redirect()->route('otp.verify', ['purpose' => 'third_party_access'])
                ->with('warning', 'OTP sent to file owner for third-party access approval.');
        }

        return redirect()->back()->with('error', 'Failed to send OTP for approval.');
    }

    public function approveThirdPartyAccess(Request $request)
    {
        $user = Auth::user();
        $transferId = session('third_party_transfer_id');
        
        if (!$transferId) {
            return redirect()->route('transfers.index')->with('error', 'Session expired.');
        }

        $transfer = Transfer::findOrFail($transferId);
        
        // Only owner can approve
        if ($transfer->sender_id !== $user->id) {
            abort(403, 'Only the file owner can approve third-party access.');
        }

        // Verify OTP
        $result = $this->otpService->verifyOTP($user->email, $request->otp, 'third_party_access');

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        // Log third-party access
        ActivityLogger::log('third_party_access', "Approved third-party access for: {$transfer->third_party_name}", $user->id, $transfer->file_id, $transfer->id, [
            'third_party_name' => $transfer->third_party_name,
            'third_party_email' => $transfer->third_party_email,
        ]);

        // Send email to third party with access instructions
        $this->emailService->sendAlert(
            $transfer->third_party_email,
            'File Access Granted',
            "You have been granted access to a file. Please contact the receiver for further instructions.",
            [
                'file_name' => $transfer->file->original_name,
                'owner' => $user->name,
                'receiver' => $transfer->receiver->name,
                'purpose' => $transfer->purpose,
            ]
        );

        // Clear session
        session()->forget('third_party_transfer_id');

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Third-party access approved and notified.');
    }

    public function cloudShare(Transfer $transfer)
    {
        $user = Auth::user();
        
        // Only sender can create cloud share
        if ($transfer->sender_id !== $user->id) {
            abort(403, 'Only the sender can create cloud shares.');
        }

        if ($transfer->transfer_type !== 'cloud') {
            return redirect()->back()->with('error', 'Only cloud transfers can be shared via link.');
        }

        // Create a share link (using FileShare model)
        $share = \App\Models\FileShare::create([
            'file_id' => $transfer->file_id,
            'shared_by' => $user->id,
            'shared_with' => $transfer->receiver_id,
            'shared_email' => $transfer->receiver->email,
            'permissions' => ['view', 'download'],
            'valid_until' => $transfer->expected_delivery_time,
            'requires_otp_approval' => true,
            'is_active' => true,
        ]);

        // Log activity
        ActivityLogger::log('file_share', "Created cloud share link for transfer", $user->id, $transfer->file_id, $transfer->id);

        // Send email with share link
        $shareUrl = route('shared.show', ['token' => $share->share_token]);
        $this->emailService->sendAlert(
            $transfer->receiver->email,
            'Cloud File Share',
            "A file has been shared with you via cloud. Click <a href='{$shareUrl}'>here</a> to access it.",
            [
                'file_name' => $transfer->file->original_name,
                'share_url' => $shareUrl,
                'valid_until' => $share->valid_until->format('Y-m-d H:i:s'),
            ]
        );

        return redirect()->back()->with('success', 'Cloud share link created and sent to receiver.');
    }
}