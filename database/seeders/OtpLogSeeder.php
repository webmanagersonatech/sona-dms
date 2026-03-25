<?php

namespace Database\Seeders;

use App\Models\OtpLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OtpLogSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OtpLog::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $purposes = ['login', 'file_access', 'email_verification', 'password_reset', 'third_party_access'];

        // Create 500 OTP logs
        for ($i = 1; $i <= 500; $i++) {
            $user = $users->random();
            $purpose = $purposes[array_rand($purposes)];
            $status = rand(0, 10) > 3 ? 'verified' : (rand(0, 1) ? 'expired' : 'pending');
            $createdAt = now()->subDays(rand(1, 60))->subHours(rand(1, 23));
            $expiresAt = $createdAt->copy()->addMinutes(5);
            $verifiedAt = $status === 'verified' ? $createdAt->copy()->addMinutes(rand(1, 4)) : null;
            $attempts = $status === 'verified' ? rand(1, 2) : rand(1, 3);

            $data = [
                'user_id' => $user->id,
                'otp_code' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'purpose' => $purpose,
                'file_id' => $purpose === 'file_access' ? rand(1, 200) : null,
                'target_user_id' => $purpose === 'file_access' ? $users->random()->id : null,
                'target_email' => $purpose === 'third_party_access' ? 'thirdparty' . rand(1, 100) . '@example.com' : null,
                'expires_at' => $expiresAt,
                'verified_at' => $verifiedAt,
                'status' => $status,
                'attempts' => $attempts,
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(rand(1, 10)),
            ];

            OtpLog::create($data);
        }

        $this->command->info('OTP logs seeded successfully!');
        $this->command->info('Total OTP logs: ' . OtpLog::count());
    }
}