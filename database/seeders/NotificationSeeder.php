<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            // Welcome notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'success',
                'icon' => 'bi-person-check',
                'message' => 'Welcome to ' . config('app.name') . '! Get started by uploading your first file.',
                'link' => route('files.create'),
                'is_read' => false,
                'created_at' => now()->subDays(5)
            ]);

            // Tip notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'icon' => 'bi-info-circle',
                'message' => 'Did you know? You can encrypt sensitive files for additional security.',
                'link' => route('files.create'),
                'is_read' => false,
                'created_at' => now()->subDays(3)
            ]);

            // Security tip
            Notification::create([
                'user_id' => $user->id,
                'type' => 'warning',
                'icon' => 'bi-shield-lock',
                'message' => 'Enable two-factor authentication to secure your account.',
                'link' => route('settings.security'),
                'is_read' => false,
                'created_at' => now()->subDays(1)
            ]);

            // Random notifications based on user activity
            if ($user->files()->count() > 0) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'info',
                    'icon' => 'bi-file-earmark',
                    'message' => 'Your files are being processed. Check back later.',
                    'link' => route('files.index'),
                    'is_read' => true,
                    'read_at' => now()->subHours(2),
                    'created_at' => now()->subDays(2)
                ]);
            }

            if ($user->transfers()->count() > 0) {
                $transfer = $user->transfers()->latest()->first();
                if ($transfer) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'success',
                        'icon' => 'bi-truck',
                        'message' => 'Transfer ' . $transfer->transfer_id . ' has been delivered.',
                        'link' => route('transfers.show', $transfer),
                        'is_read' => false,
                        'created_at' => now()->subHours(6)
                    ]);
                }
            }
        }

        // Create bulk notifications for testing
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $types = ['info', 'success', 'warning', 'danger'];
            $icons = [
                'info' => 'bi-info-circle',
                'success' => 'bi-check-circle',
                'warning' => 'bi-exclamation-triangle',
                'danger' => 'bi-exclamation-circle'
            ];
            $type = $types[array_rand($types)];
            
            Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'icon' => $icons[$type],
                'message' => 'Test notification ' . ($i + 1),
                'link' => '#',
                'is_read' => (bool)random_int(0, 1),
                'read_at' => random_int(0, 1) ? now()->subHours(random_int(1, 48)) : null,
                'created_at' => now()->subHours(random_int(1, 168))
            ]);
        }
    }
}