<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\User;
use App\Models\FileShare;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FileShareSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        FileShare::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $files = File::where('status', 'active')->get();
        $users = User::where('status', 'active')->get();

        $permissions = ['view', 'download', 'edit', 'print', 'full_control'];

        // Create 300 file shares
        for ($i = 1; $i <= 300; $i++) {
            $file = $files->random();
            $sharedBy = User::find($file->owner_id);
            $sharedWith = $users->where('id', '!=', $file->owner_id)->random();
            
            // Don't create duplicate shares
            $existingShare = FileShare::where('file_id', $file->id)
                ->where('shared_with', $sharedWith->id)
                ->first();

            if ($existingShare) {
                continue;
            }

            $permission = $permissions[array_rand($permissions)];
            $expiresAt = rand(0, 1) ? now()->addDays(rand(7, 90)) : null;
            $status = rand(0, 10) > 2 ? 'active' : (rand(0, 1) ? 'expired' : 'revoked');
            $createdAt = now()->subDays(rand(1, 60));

            FileShare::create([
                'file_id' => $file->id,
                'shared_by' => $sharedBy->id,
                'shared_with' => $sharedWith->id,
                'permission_level' => $permission,
                'expires_at' => $expiresAt,
                'status' => $status,
                'access_token' => Str::random(64),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(0, 10)),
            ]);
        }

        $this->command->info('File shares seeded successfully!');
        $this->command->info('Total shares: ' . FileShare::count());
    }
}