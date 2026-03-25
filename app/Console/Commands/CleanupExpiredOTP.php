<?php

namespace App\Console\Commands;

use App\Models\Otp;
use Illuminate\Console\Command;

class CleanupExpiredOTP extends Command
{
    protected $signature = 'otp:cleanup';
    protected $description = 'Clean up expired OTP records';

    public function handle()
    {
        $count = Otp::where('expires_at', '<', now()->subDay())
            ->orWhere('created_at', '<', now()->subWeek())
            ->delete();

        $this->info("Cleaned up {$count} expired OTP records.");
        
        // Log cleanup
        \App\Services\ActivityLogger::log('system', 'Cleaned up expired OTP records');
    }
}