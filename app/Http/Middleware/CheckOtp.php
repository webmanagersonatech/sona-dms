<?php

namespace App\Http\Middleware;

use App\Services\OtpService;
use Closure;
use Illuminate\Http\Request;

class CheckOtp
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function handle(Request $request, Closure $next, $purpose)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Skip OTP for super admin in development
        if ($user->isSuperAdmin() && app()->environment('local')) {
            return $next($request);
        }

        // Check if OTP is required for this action
        $otpRequired = session("otp_required_{$purpose}");

        if ($otpRequired) {
            // Check if valid OTP exists
            $validOtp = $this->otpService->getValidOTP($user->email, $purpose);

            if (!$validOtp) {
                // Store intended URL
                session(["{$purpose}_intended_url" => $request->fullUrl()]);
                
                // Redirect to OTP verification page
                return redirect()->route('otp.verify', ['purpose' => $purpose])
                    ->with('warning', 'OTP verification required to proceed.');
            }

            // Clear the OTP requirement
            session()->forget("otp_required_{$purpose}");
        }

        return $next($request);
    }
}