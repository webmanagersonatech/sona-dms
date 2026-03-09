<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'device_id' => 'required|string',
            'device_info' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found or account is disabled.',
            ], 404);
        }

        // Generate and send OTP
        $result = $this->otpService->generateAndSendOTP(
            $user->email,
            'login',
            $user->id,
            null,
            null,
            [
                'device_id' => $request->device_id,
                'device_info' => $request->device_info,
            ]
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'otp_id' => $result['otp_id'],
            'expires_at' => $result['expires_at'],
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->verifyOTP($request->email, $request->otp, 'login');

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        
        // Create device session
        $deviceSession = $user->deviceSessions()->updateOrCreate(
            ['device_id' => $request->device_id],
            [
                'device_name' => $request->device_info['name'] ?? 'Unknown',
                'device_type' => $request->device_info['type'] ?? 'unknown',
                'browser' => $request->device_info['browser'] ?? 'Unknown',
                'os' => $request->device_info['os'] ?? 'Unknown',
                'ip_address' => $request->ip(),
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'is_active' => true,
            ]
        );

        // Create API token
        $token = $user->createToken('dms-api-token')->plainTextToken;

        ActivityLogger::log('login', 'API login', $user->id);

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->slug,
                'department' => $user->department->name,
            ],
            'device_session_id' => $deviceSession->id,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            ActivityLogger::log('logout', 'API logout', $user->id);
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load(['role', 'department']);
        
        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.',
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            ActivityLogger::log('profile_update', 'Changed password via API', $user->id);
        }

        ActivityLogger::log('profile_update', 'Updated profile via API', $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(['role', 'department']),
        ]);
    }

    public function notifications(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    public function markAllNotificationsAsRead(Request $request)
    {
        $request->user()->notifications()->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    public function activityLogs(Request $request)
    {
        $logs = $request->user()
            ->activityLogs()
            ->with(['file', 'transfer'])
            ->orderBy('performed_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }
}