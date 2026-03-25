<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\File;
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
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Transfer::with(['sender', 'receiver', 'file'])
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('transfer_type', $request->type);
        }

        $transfers = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'transfers' => $transfers,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasPermission('transfers.create') && !$user->isSender() && !$user->isOwner()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'file_id' => 'required|exists:files,id',
            'receiver_id' => 'required|exists:users,id',
            'transfer_type' => 'required|in:physical,cloud',
            'purpose' => 'required|string|max:500',
            'expected_delivery_time' => 'required|date|after:now',
            'third_party_involved' => 'boolean',
            'third_party_name' => 'required_if:third_party_involved,true',
            'third_party_email' => 'required_if:third_party_involved,true|email',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = File::findOrFail($request->file_id);
        if ($file->owner_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only transfer files you own.',
            ], 403);
        }

        $transfer = Transfer::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'file_id' => $request->file_id,
            'transfer_type' => $request->transfer_type,
            'purpose' => $request->purpose,
            'status' => 'pending',
            'expected_delivery_time' => $request->expected_delivery_time,
            'third_party_involved' => $request->boolean('third_party_involved'),
            'third_party_name' => $request->third_party_name,
            'third_party_email' => $request->third_party_email,
            'notes' => $request->notes,
        ]);

        ActivityLogger::log('transfer_create', "Created transfer via API: {$file->original_name}", $user->id, $file->id, $transfer->id);

        // Send notification
        $receiver = $transfer->receiver;
        $this->emailService->sendAlert(
            $receiver->email,
            'File Transfer Initiated',
            "A file transfer has been initiated for you via API.",
            [
                'file_name' => $file->original_name,
                'sender' => $user->name,
                'transfer_id' => $transfer->transfer_uuid,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Transfer initiated successfully.',
            'transfer' => $transfer->load(['sender', 'receiver', 'file']),
        ], 201);
    }

    public function show(Transfer $transfer)
    {
        $user = Auth::user();
        
        if ($transfer->sender_id !== $user->id && $transfer->receiver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        $transfer->load(['sender', 'receiver', 'file', 'activityLogs']);

        return response()->json([
            'success' => true,
            'transfer' => $transfer,
        ]);
    }

    public function send(Request $request, Transfer $transfer)
    {
        $user = Auth::user();
        
        if ($transfer->sender_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the sender can send the transfer.',
            ], 403);
        }

        if ($transfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transfer cannot be sent in current status.',
            ], 400);
        }

        $transfer->update(['status' => 'in_transit']);
        ActivityLogger::log('transfer_send', "Sent transfer via API", $user->id, $transfer->file_id, $transfer->id);

        return response()->json([
            'success' => true,
            'message' => 'Transfer marked as in transit.',
            'transfer' => $transfer->fresh(),
        ]);
    }

    public function receive(Request $request, Transfer $transfer)
    {
        $user = Auth::user();
        
        if ($transfer->receiver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the receiver can receive the transfer.',
            ], 403);
        }

        if ($transfer->status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Transfer must be delivered before receiving.',
            ], 400);
        }

        // Check for OTP if required
        if ($request->has('otp')) {
            $result = $this->otpService->verifyOTP($user->email, $request->otp, 'transfer_approval');
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 401);
            }
        }

        $transfer->update(['status' => 'received']);
        ActivityLogger::log('transfer_receive', "Received transfer via API", $user->id, $transfer->file_id, $transfer->id);

        return response()->json([
            'success' => true,
            'message' => 'Transfer received successfully.',
            'transfer' => $transfer->fresh(),
        ]);
    }

    public function update(Request $request, Transfer $transfer)
    {
        $user = Auth::user();
        
        if ($transfer->sender_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the sender can update the transfer.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'delivery_location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $transfer->update([
            'status' => 'delivered',
            'actual_delivery_time' => now(),
            'delivery_location' => $request->delivery_location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        ActivityLogger::log('transfer_send', "Delivered transfer via API: {$request->delivery_location}", $user->id, $transfer->file_id, $transfer->id);

        return response()->json([
            'success' => true,
            'message' => 'Transfer marked as delivered.',
            'transfer' => $transfer->fresh(),
        ]);
    }

    public function destroy(Transfer $transfer)
    {
        $user = Auth::user();
        
        if ($transfer->sender_id !== $user->id && $transfer->receiver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        if (!in_array($transfer->status, ['pending', 'in_transit'])) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer cannot be cancelled in current status.',
            ], 400);
        }

        $oldStatus = $transfer->status;
        $transfer->update(['status' => 'cancelled']);
        ActivityLogger::log('transfer_cancel', "Cancelled transfer via API (was: {$oldStatus})", $user->id, $transfer->file_id, $transfer->id);

        return response()->json([
            'success' => true,
            'message' => 'Transfer cancelled successfully.',
        ]);
    }

    public function requestThirdPartyAccess(Request $request, Transfer $transfer)
    {
        $user = Auth::user();
        
        if ($transfer->receiver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the receiver can request third-party access.',
            ], 403);
        }

        if (!$transfer->third_party_involved) {
            return response()->json([
                'success' => false,
                'message' => 'This transfer does not involve third parties.',
            ], 400);
        }

        $result = $this->otpService->generateAndSendOTP(
            $transfer->sender->email,
            'third_party_access',
            $transfer->sender->id,
            $transfer->file_id,
            $transfer->id
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to file owner for third-party access approval.',
            'otp_id' => $result['otp_id'],
        ]);
    }

    public function approveThirdPartyAccess(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'transfer_id' => 'required|exists:transfers,id',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $transfer = Transfer::findOrFail($request->transfer_id);
        
        if ($transfer->sender_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the file owner can approve third-party access.',
            ], 403);
        }

        $result = $this->otpService->verifyOTP($user->email, $request->otp, 'third_party_access');

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 401);
        }

        ActivityLogger::log('third_party_access', "Approved third-party access via API: {$transfer->third_party_name}", $user->id, $transfer->file_id, $transfer->id);

        return response()->json([
            'success' => true,
            'message' => 'Third-party access approved successfully.',
        ]);
    }
}