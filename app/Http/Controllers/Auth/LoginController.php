<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\OtpLog;
use App\Services\BrevoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class LoginController extends Controller
{
    protected $brevoService;

    public function __construct(BrevoService $brevoService)
    {
        $this->brevoService = $brevoService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        if ($user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is not active. Please contact administrator.',
            ]);
        }

        // Store user id in session for OTP verification
        session(['otp_user_id' => $user->id]);
        
        // Generate and send OTP
        $this->generateAndSendOtp($user, $request);

        return redirect()->route('otp.verify.form');
    }

    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp-verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login');
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

        $user = User::find($userId);
        Auth::login($user, $request->boolean('remember'));

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $this->logActivity($user, $request, 'login', 'User logged in successfully');

        session()->forget('otp_user_id');

        return redirect()->intended('dashboard');
    }

    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        
        // Invalidate old OTPs
        OtpLog::where('user_id', $user->id)
            ->where('purpose', 'login')
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // Generate new OTP
        $this->generateAndSendOtp($user, $request);

        return back()->with('success', 'New OTP has been sent to your email.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $this->logActivity($user, $request, 'logout', 'User logged out');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function generateAndSendOtp($user, $request)
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpLog::create([
            'user_id' => $user->id,
            'otp_code' => $otp,
            'purpose' => 'login',
            'expires_at' => now()->addMinutes(5),
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->brevoService->sendOtpEmail($user->email, $otp);
    }

    private function logActivity($user, $request, $action, $description)
    {
        $agent = new Agent();
        
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'module' => 'auth',
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
        ]);
    }
}