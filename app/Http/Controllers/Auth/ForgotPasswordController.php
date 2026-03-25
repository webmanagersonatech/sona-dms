<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpLog;
use App\Models\ActivityLog;
use App\Services\BrevoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Jenssegers\Agent\Agent;

class ForgotPasswordController extends Controller
{
    protected $brevoService;

    public function __construct(BrevoService $brevoService)
    {
        $this->brevoService = $brevoService;
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We cannot find a user with that email address.']);
        }

        // Generate password reset OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpLog::create([
            'user_id' => $user->id,
            'otp_code' => $otp,
            'purpose' => 'password_reset',
            'expires_at' => now()->addMinutes(30),
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send reset OTP via email
        $this->brevoService->sendPasswordResetOtp($user->email, $otp, $user->name);

        // Store email in session for reset form
        session(['reset_email' => $user->email]);

        return redirect()->route('password.reset.form')
            ->with('success', 'Password reset code has been sent to your email.');
    }

    public function showResetForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.passwords.reset');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Session expired. Please try again.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'User not found.']);
        }

        $otpLog = OtpLog::where('user_id', $user->id)
            ->where('purpose', 'password_reset')
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

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'password_reset',
            'module' => 'auth',
            'description' => 'Password reset successfully',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        session()->forget('reset_email');

        return redirect()->route('login')
            ->with('success', 'Password reset successful! You can now login with your new password.');
    }
}