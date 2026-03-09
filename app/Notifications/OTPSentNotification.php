<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OTPSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $otp;
    protected $purpose;

    public function __construct($otp, $purpose)
    {
        $this->otp = $otp;
        $this->purpose = $purpose;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $subject = match($this->purpose) {
            'login' => 'Your Login OTP',
            'file_access' => 'File Access OTP',
            'transfer_approval' => 'Transfer Approval OTP',
            'third_party_access' => 'Third Party Access OTP',
            default => 'Your Verification Code',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello!')
            ->line('Your OTP for ' . $this->purpose . ' is:')
            ->line('## ' . $this->otp)
            ->line('This OTP is valid for 10 minutes.')
            ->line('If you did not request this OTP, please ignore this email.')
            ->line('Thank you for using our DMS!');
    }
}