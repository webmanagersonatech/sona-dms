<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoEmailService
{
    protected $apiKey;
    protected $senderEmail;
    protected $senderName;

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key');
        $this->senderEmail = config('services.brevo.sender_email');
        $this->senderName = config('services.brevo.sender_name');
    }

    public function sendOTP($toEmail, $otp, $purpose = 'login')
    {
        try {
            $subject = match($purpose) {
                'login' => 'Your Login OTP',
                'file_access' => 'File Access OTP',
                'transfer_approval' => 'Transfer Approval OTP',
                'third_party_access' => 'Third Party Access OTP',
                default => 'Your Verification Code',
            };

            $templateId = $this->getTemplateId($purpose);
            
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $this->senderName,
                    'email' => $this->senderEmail,
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $toEmail,
                    ]
                ],
                'subject' => $subject,
                'htmlContent' => $this->generateOTPContent($otp, $purpose),
                'params' => [
                    'otp' => $otp,
                    'purpose' => $purpose,
                ],
            ]);

            if ($response->successful()) {
                Log::info('OTP sent successfully', [
                    'email' => $toEmail,
                    'purpose' => $purpose,
                    'message_id' => $response->json('messageId'),
                ]);
                return true;
            }

            Log::error('Failed to send OTP', [
                'email' => $toEmail,
                'response' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Exception sending OTP', [
                'email' => $toEmail,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendAlert($toEmail, $subject, $content, $data = [])
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $this->senderName,
                    'email' => $this->senderEmail,
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $toEmail,
                    ]
                ],
                'subject' => $subject,
                'htmlContent' => $content,
                'params' => $data,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exception sending alert', [
                'email' => $toEmail,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function getTemplateId($purpose)
    {
        return match($purpose) {
            'login' => 1,
            'file_access' => 2,
            'transfer_approval' => 3,
            'third_party_access' => 4,
            default => 1,
        };
    }

    private function generateOTPContent($otp, $purpose)
    {
        $expiryMinutes = config('otp.expiry_minutes', 10);
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .otp-box { 
                        background: #f4f4f4; 
                        padding: 20px; 
                        text-align: center; 
                        font-size: 24px; 
                        font-weight: bold; 
                        letter-spacing: 5px; 
                        margin: 20px 0; 
                        border-radius: 5px; 
                    }
                    .footer { margin-top: 30px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Security OTP</h2>
                    <p>Your OTP for {$purpose} is:</p>
                    <div class='otp-box'>{$otp}</div>
                    <p>This OTP is valid for {$expiryMinutes} minutes.</p>
                    <p><strong>Do not share this OTP with anyone.</strong></p>
                    <div class='footer'>
                        <p>This is an automated message. Please do not reply.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
}