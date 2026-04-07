<?php
// app/Services/BrevoService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $apiKey;
    protected $apiUrl;
    protected $senderEmail;
    protected $senderName;

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key');
        $this->apiUrl = 'https://api.brevo.com/v3';
        $this->senderEmail = config('mail.from.address');
        $this->senderName = config('mail.from.name');
        
        // Log configuration for debugging
        Log::info('Brevo Service initialized', [
            'has_api_key' => !empty($this->apiKey),
            'sender_email' => $this->senderEmail,
            'sender_name' => $this->senderName
        ]);
    }

    public function sendOtpEmail($email, $otp)
    {
        $subject = 'Your OTP for ' . config('app.name');
        $content = "
            <h2>OTP Verification</h2>
            <p>Your OTP code is: <strong style='font-size: 24px;'>{$otp}</strong></p>
            <p>This code will expire in 5 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
        ";
        
        return $this->sendEmail($email, $subject, $content);
    }

    public function sendFileAccessOtp($email, $otp, $fileName, $requesterName)
    {
        $subject = 'File Access Request - ' . config('app.name');
        $content = "
            <h2>File Access Request</h2>
            <p>User <strong>{$requesterName}</strong> is requesting access to file: <strong>{$fileName}</strong></p>
            <p>OTP code: <strong style='font-size: 24px;'>{$otp}</strong></p>
            <p>This code will expire in 5 minutes.</p>
            <p>If you didn't authorize this request, please ignore this email.</p>
        ";
        
        return $this->sendEmail($email, $subject, $content);
    }

    public function sendFileSharedEmail($email, $sharedByName, $fileName, $permission, $expiresAt)
    {
        $subject = 'File Shared With You - ' . config('app.name');
        $expiryText = $expiresAt ? 'Expires: ' . $expiresAt->format('Y-m-d H:i:s') : 'No expiry';
        
        $content = "
            <h2>File Shared</h2>
            <p>User <strong>{$sharedByName}</strong> has shared a file with you: <strong>{$fileName}</strong></p>
            <p>Permission: <strong>{$permission}</strong></p>
            <p>{$expiryText}</p>
            <p>Login to your account to access this file.</p>
        ";
        
        return $this->sendEmail($email, $subject, $content);
    }

    public function sendTransferCreatedEmail($email, $senderName, $transferId, $purpose)
    {
        $subject = 'New Transfer Created - ' . config('app.name');
        $content = "
            <h2>New Transfer</h2>
            <p>User <strong>{$senderName}</strong> has created a transfer for you.</p>
            <p>Transfer ID: <strong>{$transferId}</strong></p>
            <p>Purpose: <strong>{$purpose}</strong></p>
            <p>Login to your account to view transfer details.</p>
        ";
        
        return $this->sendEmail($email, $subject, $content);
    }

    public function sendTransferDeliveredEmail($email, $transferId, $receivedBy, $location)
    {
        $subject = 'Transfer Delivered - ' . config('app.name');
        $content = "
            <h2>Transfer Delivered</h2>
            <p>Transfer <strong>{$transferId}</strong> has been delivered.</p>
            <p>Received by: <strong>{$receivedBy}</strong></p>
            <p>Location: <strong>{$location}</strong></p>
        ";
        
        return $this->sendEmail($email, $subject, $content);
    }

    public function sendPasswordResetOtp($email, $otp, $userName)
    {
        $subject = 'Password Reset Request - ' . config('app.name');
        $content = "
            <h2>Password Reset</h2>
            <p>Hello <strong>{$userName}</strong>,</p>
            <p>You requested to reset your password. Use this OTP code:</p>
            <p><strong style='font-size: 24px;'>{$otp}</strong></p>
            <p>This code will expire in 30 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
        ";
        
        return $this->sendEmail($email, $subject, $content);
    }

    public function sendVerificationOtp($email, $otp, $userName)
    {
        $subject = 'Email Verification - ' . config('app.name');
        $content = "
            <h2>Verify Your Email</h2>
            <p>Hello <strong>{$userName}</strong>,</p>
            <p>Welcome to " . config('app.name') . "! Please verify your email address using this code:</p>
            <p><strong style='font-size: 24px;'>{$otp}</strong></p>
            <p>This code will expire in 30 minutes.</p>
        ";
        
        return $this->sendEmail($email, $subject, $content);
    }

    public function sendPasswordChangedEmail($email, $userName, $newPassword)
    {
        $subject = 'Your Password Has Been Reset - ' . config('app.name');
        $content = "
            <h2>Password Reset</h2>
            <p>Hello <strong>{$userName}</strong>,</p>
            <p>Your password has been reset by an administrator.</p>
            <p>Your new temporary password is: <strong style='font-size: 20px; background: #f4f4f4; padding: 10px; border-radius: 5px; display: inline-block;'>{$newPassword}</strong></p>
            <p>Please login and change your password immediately for security.</p>
            <p><a href='" . url('/login') . "' style='display: inline-block; padding: 10px 20px; background: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px;'>Login Now</a></p>
        ";
        
        return $this->sendEmail($email, $subject, $content);
    }

    protected function sendEmail($to, $subject, $htmlContent)
    {
        // Validate sender email
        if (empty($this->senderEmail)) {
            Log::error('Brevo email failed: No sender email configured');
            return false;
        }

        // Validate API key
        if (empty($this->apiKey)) {
            Log::error('Brevo email failed: No API key configured');
            return false;
        }

        $payload = [
            'sender' => [
                'name' => $this->senderName,
                'email' => $this->senderEmail,
            ],
            'to' => [
                ['email' => $to]
            ],
            'subject' => $subject,
            'htmlContent' => $this->getEmailTemplate($subject, $htmlContent),
        ];

        Log::info('Sending Brevo email', [
            'to' => $to,
            'subject' => $subject,
            'sender' => $this->senderEmail
        ]);

        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->apiUrl . '/smtp/email', $payload);

            if (!$response->successful()) {
                Log::error('Brevo email failed', [
                    'to' => $to,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return false;
            }

            Log::info('Brevo email sent successfully', [
                'to' => $to,
                'message_id' => $response->json('messageId') ?? 'unknown'
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Brevo email exception', [
                'message' => $e->getMessage(),
                'to' => $to
            ]);
            return false;
        }
    }

    protected function getEmailTemplate($subject, $content)
    {
        return view('emails.template', compact('subject', 'content'))->render();
    }
}