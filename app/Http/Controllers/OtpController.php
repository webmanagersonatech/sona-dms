<?php

namespace App\Http\Controllers;

use App\Models\OtpLog;
use App\Models\ActivityLog;
use App\Services\BrevoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class OtpController extends Controller
{
    protected $brevoService;

    public function __construct(BrevoService $brevoService)
    {
        $this->brevoService = $brevoService;
    }

    public function showVerifyForm()
    {
        return view('auth.otp-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please login again.']);
        }

        $otpLog = OtpLog::where('user_id', $userId)
            ->where('purpose', 'login')
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpLog) {
            return back()->withErrors(['otp' => 'No valid OTP found. Please request a new one.']);
        }

        if ($otpLog->attempts >= 3) {
            $otpLog->update(['status' => 'expired']);
            return back()->withErrors(['otp' => 'Maximum attempts exceeded. Please request a new OTP.']);
        }

        $otpLog->increment('attempts');

        if ($otpLog->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP code.']);
        }

        $otpLog->update([
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        session()->forget('otp_user_id');
        
        return redirect()->intended('dashboard');
    }

    public function resend()
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired. Please login again.']);
        }

        $user = \App\Models\User::find($userId);
        
        // Invalidate old OTPs
        OtpLog::where('user_id', $user->id)
            ->where('purpose', 'login')
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpLog::create([
            'user_id' => $user->id,
            'otp_code' => $otp,
            'purpose' => 'login',
            'expires_at' => now()->addMinutes(5),
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Send OTP via email
        $this->brevoService->sendOtpEmail($user->email, $otp);

        return back()->with('success', 'New OTP has been sent to your email.');
    }

    public function verifyFileAccess(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'file_id' => 'required|exists:files,id',
        ]);

        $fileId = $request->file_id;
        $userId = Auth::id();

        $otpLog = OtpLog::where('file_id', $fileId)
            ->where('target_user_id', $userId)
            ->where('purpose', 'file_access')
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpLog) {
            return response()->json([
                'success' => false,
                'message' => 'No valid OTP found.'
            ], 400);
        }

        if ($otpLog->otp_code !== $request->otp) {
            $otpLog->increment('attempts');
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code.'
            ], 400);
        }

        $otpLog->update([
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        // Log the activity
        $agent = new Agent();
        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'otp_verified',
            'module' => 'file',
            'file_id' => $fileId,
            'description' => 'OTP verified for file access',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.'
        ]);
    }

    public function verifyThirdParty(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'file_id' => 'required|exists:files,id',
            'third_party_email' => 'required|email',
        ]);

        $fileId = $request->file_id;
        $ownerId = Auth::id();

        $otpLog = OtpLog::where('file_id', $fileId)
            ->where('user_id', $ownerId)
            ->where('target_email', $request->third_party_email)
            ->where('purpose', 'third_party_access')
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpLog) {
            return response()->json([
                'success' => false,
                'message' => 'No valid OTP found.'
            ], 400);
        }

        if ($otpLog->otp_code !== $request->otp) {
            $otpLog->increment('attempts');
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code.'
            ], 400);
        }

        $otpLog->update([
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        // Generate temporary access token
        $accessToken = \Str::random(64);
        
        // Store in session or create temporary access record
        session(['third_party_access_' . $fileId => $accessToken]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'access_token' => $accessToken
        ]);
    }
}