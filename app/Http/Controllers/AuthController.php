<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DeviceSession;
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

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = $request->email;

        // Check if user exists and is active
        $user = User::where('email', $email)
            ->where('is_active', true)
            ->where('is_locked', false)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Account not found or disabled.');
        }

        // Generate and send OTP
        $result = $this->otpService->generateAndSendOTP($email, 'login', $user->id);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        // Store email in session for OTP verification
        session([
            'login_email' => $email,
            'otp_id' => $result['otp_id'],
            'otp_expires_at' => $result['expires_at'],
        ]);

        return redirect()->route('otp.verify', ['purpose' => 'login'])
    ->with('success', 'OTP sent to your email.');

    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'purpose' => 'required|in:login,file_access,transfer_approval,third_party_access',
        ]);

        $purpose = $request->purpose;
        $email = session('login_email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }

        // Verify OTP
        $result = $this->otpService->verifyOTP($email, $request->otp, $purpose);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message'])->withInput();
        }

        // Get user
        $user = User::where('email', $email)->first();

        if ($purpose === 'login') {
            // Login the user
            Auth::login($user);

            // Register device
            // $this->registerDevice($user, $request);

            // Log activity
            ActivityLogger::log('login', 'User logged in', $user->id);

            // Clear session
            session()->forget(['login_email', 'otp_id', 'otp_expires_at']);

            return redirect()->route('dashboard')->with('success', 'Login successful.');
        }

        // For other purposes, store verification in session
        session(["otp_verified_{$purpose}" => true]);
        
        // Redirect to intended URL
        $intendedUrl = session("{$purpose}_intended_url") ?? route('dashboard');
        session()->forget("{$purpose}_intended_url");

        return redirect($intendedUrl)->with('success', 'OTP verified successfully.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            ActivityLogger::log('logout', 'User logged out', $user->id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showOtpVerification($purpose)
    {
        $email = session('login_email');
        
        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }

        return view('auth.otp-verification', [
            'purpose' => $purpose,
            'email' => $email,
            'expires_at' => session('otp_expires_at'),
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'purpose' => 'required|in:login,file_access,transfer_approval,third_party_access',
        ]);

        $purpose = $request->purpose;
        $email = session('login_email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Resend OTP
        $result = $this->otpService->generateAndSendOTP($email, $purpose, $user->id);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        // Update session
        session([
            'otp_id' => $result['otp_id'],
            'otp_expires_at' => $result['expires_at'],
        ]);

        return redirect()->back()->with('success', 'OTP resent successfully.');
    }

    // private function registerDevice($user, Request $request)
    // {
    //     $deviceId = $this->generateDeviceId($request);
        
    //     // Check if device already registered
    //     $existingSession = DeviceSession::where('user_id', $user->id)
    //         ->where('device_id', $deviceId)
    //         ->first();

    //     if (!$existingSession) {
    //         // Register new device
    //         DeviceSession::create([
    //             'user_id' => $user->id,
    //             'device_id' => $deviceId,
    //             'device_name' => $this->getDeviceName($request),
    //             'device_type' => $this->getDeviceType($request),
    //             'browser' => $this->getBrowser($request),
    //             'os' => $this->getOS($request),
    //             'ip_address' => $request->ip(),
    //             'location' => $this->getLocation($request),
    //             'last_login_at' => now(),
    //             'last_activity_at' => now(),
    //             'is_active' => true,
    //         ]);
    //     } else {
    //         // Update existing device
    //         $existingSession->update([
    //             'last_login_at' => now(),
    //             'last_activity_at' => now(),
    //             'ip_address' => $request->ip(),
    //             'is_active' => true,
    //         ]);
    //     }

    //     // Store device ID in session
    //     session(['device_id' => $deviceId]);
    // }

    private function generateDeviceId(Request $request)
    {
        $fingerprint = $request->userAgent() . $request->ip();
        return hash('sha256', $fingerprint);
    }

    private function getDeviceName(Request $request)
    {
        $ua = $request->userAgent();
        
        if (strpos($ua, 'Windows')) {
            return 'Windows Device';
        } elseif (strpos($ua, 'Macintosh')) {
            return 'Mac Device';
        }
        
        return 'Unknown Device';
    }

    private function getDeviceType(Request $request)
    {
        $ua = $request->userAgent();
        return strpos($ua, 'Mobile') !== false ? 'mobile' : 'desktop';
    }

    private function getBrowser(Request $request)
    {
        $ua = $request->userAgent();
        
        if (strpos($ua, 'Chrome')) {
            return 'Chrome';
        }
        
        return 'Unknown';
    }

    private function getOS(Request $request)
    {
        $ua = $request->userAgent();
        
        if (strpos($ua, 'Windows')) {
            return 'Windows';
        }
        
        return 'Unknown';
    }

    private function getLocation(Request $request)
    {
        // Simplified location - in production use IP geolocation
        return 'Unknown';
    }
}