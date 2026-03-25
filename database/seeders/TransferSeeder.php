<?php

namespace Database\Seeders;

use App\Models\Transfer;
use App\Models\User;
use App\Models\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransferSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Transfer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::where('status', 'active')->get();
        $files = File::where('status', 'active')->get();

        $purposes = [
            'Document delivery for client meeting',
            'Hardware transfer to new employee',
            'Confidential files for legal review',
            'Project assets for remote team',
            'Backup data transfer',
            'Annual report submission',
            'Contract signing documents',
            'Training materials distribution',
            'Software installation files',
            'Presentation for board meeting',
        ];

        $couriers = ['DHL', 'FedEx', 'UPS', 'USPS', 'Blue Dart', 'DTDC', 'EMS', 'Private Courier'];
        $statuses = ['pending', 'in_transit', 'delivered', 'cancelled', 'failed'];
        $locations = [
            'Main Office - Reception',
            'Warehouse - Loading Bay',
            'IT Department - Server Room',
            'HR Department - Desk 101',
            'Conference Room A',
            'Building 2, Floor 3',
            'Security Gate',
            'Mail Room',
        ];

        // Create 150 transfers
        for ($i = 1; $i <= 150; $i++) {
            $sender = $users->random();
            $receiver = $users->where('id', '!=', $sender->id)->random();
            $file = rand(0, 1) ? $files->random() : null;
            
            $status = $statuses[array_rand($statuses)];
            $createdAt = now()->subDays(rand(1, 90))->subHours(rand(1, 23));
            $expectedDelivery = $createdAt->copy()->addDays(rand(1, 14));
            $actualDelivery = null;
            
            if ($status === 'delivered') {
                $actualDelivery = $expectedDelivery->copy()->addHours(rand(-48, 48));
            } elseif ($status === 'cancelled') {
                $actualDelivery = null;
            }

            $hasTracking = rand(0, 1);
            $cost = rand(10, 500) + (rand(0, 99) / 100);

            Transfer::create([
                'transfer_id' => 'TRF-' . strtoupper(uniqid()),
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'receiver_name' => $receiver->name,
                'receiver_email' => $receiver->email,
                'receiver_phone' => $receiver->phone,
                'file_id' => $file?->id,
                'purpose' => $purposes[array_rand($purposes)],
                'description' => rand(0, 1) ? 'Urgent delivery - handle with care' : null,
                'expected_delivery_time' => $expectedDelivery,
                'actual_delivery_time' => $actualDelivery,
                'status' => $status,
                'tracking_number' => $hasTracking ? 'TRK' . rand(100000, 999999) : null,
                'courier_name' => $hasTracking ? $couriers[array_rand($couriers)] : null,
                'delivery_location' => $status === 'delivered' ? $locations[array_rand($locations)] : null,
                'received_by' => $status === 'delivered' ? $receiver->name : null,
                'signature' => $status === 'delivered' ? 'data:image/png;base64,' . base64_encode(Str::random(100)) : null,
                'notes' => rand(0, 1) ? 'Left at reception' : null,
                'qr_code' => rand(0, 1) ? 'data:image/png;base64,' . base64_encode(Str::random(100)) : null,
                'proof_of_delivery' => $status === 'delivered' ? 'data:image/jpeg;base64,' . base64_encode(Str::random(100)) : null,
                'cost' => $cost,
                'currency' => 'USD',
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(0, 15)),
            ]);
        }

        $this->command->info('Transfers seeded successfully!');
        $this->command->info('Total transfers: ' . Transfer::count());
    }
}