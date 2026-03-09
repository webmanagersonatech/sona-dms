<?php

namespace App\Console\Commands;

use App\Models\Transfer;
use App\Services\ActivityLogger;
use Illuminate\Console\Command;

class CleanupExpiredTransfers extends Command
{
    protected $signature = 'transfer:cleanup';
    protected $description = 'Clean up expired transfers and update statuses';

    public function handle()
    {
        // Mark pending transfers older than 7 days as cancelled
        $expiredCount = Transfer::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7))
            ->update(['status' => 'cancelled']);

        // Clean up delivered transfers older than 30 days
        $oldCount = Transfer::whereIn('status', ['delivered', 'cancelled'])
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        $this->info("Marked {$expiredCount} expired transfers as cancelled.");
        $this->info("Deleted {$oldCount} old transfer records.");

        ActivityLogger::log('system', 'Cleaned up expired transfers');
    }
}