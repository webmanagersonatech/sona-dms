<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;

class OtpService
{
    protected $brevoService;
    protected $expiryMinutes;

    public function __construct(BrevoEmailService $brevoService)
    {
        $this->brevoService = $brevoService;
        $this->expiryMinutes = config('otp.expiry_minutes', 10);
    }

    public function generateAndSendOTP($email, $purpose, $userId = null, $fileId = null, $transferId = null)
    {
        // Clean up expired OTPs
        Otp::where('email', $email)
            ->where('is_used', false)
            ->where('expires_at', '<', now())
            ->delete();

        // Check rate limiting (max 5 OTPs per hour)
        $recentCount = Otp::where('email', $email)
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($recentCount >= 10) {
            return [
                'success' => false,
                'message' => 'Too many OTP requests. Please try again later.',
            ];
        }

        // Generate OTP
        $otpCode = Otp::generateCode();
        $expiresAt = now()->addMinutes($this->expiryMinutes);

        // Create OTP record
        $otp = Otp::create([
            'email' => $email,
            'otp_code' => $otpCode,
            'purpose' => $purpose,
            'user_id' => $userId,
            'file_id' => $fileId,
            'transfer_id' => $transferId,
            'device_id' => session('device_id'),
            'ip_address' => request()->ip(),
            'metadata' => [
                'user_agent' => request()->userAgent(),
            ],
            'expires_at' => $expiresAt,
        ]);

        // Send OTP via Brevo
        $sent = $this->brevoService->sendOTP($email, $otpCode, $purpose);

        if ($sent) {
            ActivityLogger::log('otp_sent', "OTP sent to {$email} for {$purpose}", $userId, $fileId, $transferId);
            
            return [
                'success' => true,
                'otp_id' => $otp->id,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to send OTP',
        ];
    }

    public function verifyOTP($email, $otpCode, $purpose = null)
    {
        $otp = Otp::where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ];
        }

        if ($purpose && $otp->purpose !== $purpose) {
            return [
                'success' => false,
                'message' => 'OTP not valid for this purpose',
            ];
        }

        // Mark as used
        $otp->markAsUsed();

        ActivityLogger::log('otp_verified', "OTP verified for {$email}", $otp->user_id, $otp->file_id, $otp->transfer_id);

        return [
            'success' => true,
            'otp' => $otp,
        ];
    }

    public function getValidOTP($email, $purpose)
    {
        return Otp::where('email', $email)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }
}