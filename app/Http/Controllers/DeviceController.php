<?php

namespace App\Http\Controllers;

use App\Models\DeviceSession;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function showVerification()
    {
        if (!session('device_verification_required')) {
            return redirect()->route('dashboard');
        }

        return view('auth.device-verification', [
            'device' => session('pending_device'),
        ]);
    }

    public function verifyDevice(Request $request)
    {
        // ✅ FIX 1: validate correct field
        $request->validate([
            'otp_code' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $pendingDevice = session('pending_device');

        if (!$user || !$pendingDevice) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }

        // ✅ FIX 2: verify correct OTP value
        $result = $this->otpService->verifyOTP(
            $user->email,
            $request->otp_code,
            'login'
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        // ✅ FIX 3: prevent duplicate device insert
        DeviceSession::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_id' => $pendingDevice['device_id'],
            ],
            [
                'device_name' => $pendingDevice['device_name'],
                'device_type' => $pendingDevice['device_type'],
                'browser' => $pendingDevice['browser'],
                'os' => $pendingDevice['os'],
                'ip_address' => $pendingDevice['ip_address'],
                'location' => 'Verified Location',
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'is_active' => true,
            ]
        );

        // Clear device verification session
        session()->forget([
            'device_verification_required',
            'pending_device',
            'new_device_detected',
        ]);

        session(['device_id' => $pendingDevice['device_id']]);

        return redirect()->route('dashboard')
            ->with('success', 'Device verified and registered successfully.');
    }

    public function resendDeviceOtp()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $result = $this->otpService->generateAndSendOTP(
            $user->email,
            'login',
            $user->id
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', 'OTP resent successfully.');
    }

    public function manageDevices()
    {
        $devices = DeviceSession::where('user_id', Auth::id())
            ->orderByDesc('last_activity_at')
            ->get();

        return view('devices.index', compact('devices'));
    }

    public function revokeDevice($id)
    {
        $device = DeviceSession::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $device->update(['is_active' => false]);

        if (session('device_id') === $device->device_id) {
            Auth::logout();
            return redirect()->route('login')
                ->with('success', 'Current device access revoked.');
        }

        return redirect()->route('devices.index')
            ->with('success', 'Device access revoked.');
    }
}
