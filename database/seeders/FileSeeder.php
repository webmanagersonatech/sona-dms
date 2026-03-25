<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FileSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        File::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $departments = Department::all();

        $fileTypes = [
            ['extension' => 'pdf', 'mime' => 'application/pdf', 'icon' => 'bi-file-pdf', 'category' => 'document'],
            ['extension' => 'docx', 'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'icon' => 'bi-file-word', 'category' => 'document'],
            ['extension' => 'xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'icon' => 'bi-file-excel', 'category' => 'spreadsheet'],
            ['extension' => 'pptx', 'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'icon' => 'bi-file-ppt', 'category' => 'presentation'],
            ['extension' => 'jpg', 'mime' => 'image/jpeg', 'icon' => 'bi-file-image', 'category' => 'image'],
            ['extension' => 'png', 'mime' => 'image/png', 'icon' => 'bi-file-image', 'category' => 'image'],
            ['extension' => 'gif', 'mime' => 'image/gif', 'icon' => 'bi-file-image', 'category' => 'image'],
            ['extension' => 'txt', 'mime' => 'text/plain', 'icon' => 'bi-file-text', 'category' => 'text'],
            ['extension' => 'zip', 'mime' => 'application/zip', 'icon' => 'bi-file-zip', 'category' => 'archive'],
            ['extension' => 'rar', 'mime' => 'application/x-rar-compressed', 'icon' => 'bi-file-zip', 'category' => 'archive'],
        ];

        $fileNames = [
            'Annual Report', 'Project Plan', 'Budget Summary', 'Meeting Minutes', 
            'Contract Agreement', 'Employee Handbook', 'Marketing Strategy', 
            'Sales Data', 'Inventory List', 'Customer Feedback', 'Technical Documentation',
            'Training Material', 'Policy Document', 'Financial Statement', 'Audit Report',
            'Research Paper', 'Presentation Deck', 'Invoice', 'Purchase Order',
            'Resume', 'Cover Letter', 'Proposal', 'Quote', 'Timesheet',
        ];

        $descriptions = [
            'Official document for quarterly review',
            'Contains confidential information',
            'Draft version - pending approval',
            'Final version approved by management',
            'Archived copy from previous year',
            'Shared with external stakeholders',
            'Internal use only',
            'Encrypted for security',
            'Backup copy',
            'Temporary working file',
        ];

        // Create 200 files
        for ($i = 1; $i <= 200; $i++) {
            $user = $users->random();
            $fileType = $fileTypes[array_rand($fileTypes)];
            $fileName = $fileNames[array_rand($fileNames)];
            $size = rand(1024, 10485760); // 1KB to 10MB
            $createdAt = now()->subDays(rand(1, 180))->subHours(rand(1, 23))->subMinutes(rand(1, 59));
            $lastAccessed = rand(0, 1) ? $createdAt->copy()->addDays(rand(1, 30)) : null;
            $downloadCount = rand(0, 500);
            $viewCount = rand(0, 1000);
            $isEncrypted = (bool)rand(0, 1);
            $status = rand(0, 10) > 1 ? 'active' : (rand(0, 1) ? 'archived' : 'deleted');

            File::create([
                'uuid' => Str::uuid(),
                'name' => $fileName . ' ' . $i . '.' . $fileType['extension'],
                'original_name' => $fileName . ' ' . $i . '.' . $fileType['extension'],
                'file_path' => 'uploads/' . $createdAt->format('Y/m/d') . '/' . Str::uuid() . '.' . $fileType['extension'],
                'file_type' => $fileType['mime'],
                'mime_type' => $fileType['mime'],
                'file_size' => $size,
                'extension' => $fileType['extension'],
                'description' => rand(0, 1) ? $descriptions[array_rand($descriptions)] : null,
                'owner_id' => $user->id,
                'department_id' => $user->department_id ?? $departments->random()->id,
                'status' => $status,
                'is_encrypted' => $isEncrypted,
                'encryption_key' => $isEncrypted ? Str::random(32) : null,
                'download_count' => $downloadCount,
                'view_count' => $viewCount,
                'last_accessed_at' => $lastAccessed,
                'checksum' => md5($fileName . $i . time()),
                'version' => rand(1, 5),
                'tags' => json_encode([
                    'category' => $fileType['category'],
                    'priority' => rand(1, 5),
                    'department' => $user->department?->name ?? 'General',
                ]),
                'metadata' => json_encode([
                    'author' => $user->name,
                    'created_with' => rand(0, 1) ? 'Microsoft Office' : 'Adobe',
                    'page_count' => $fileType['category'] === 'document' ? rand(5, 100) : null,
                    'resolution' => $fileType['category'] === 'image' ? '1920x1080' : null,
                ]),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(0, 30)),
                'deleted_at' => $status === 'deleted' ? now()->subDays(rand(1, 30)) : null,
            ]);
        }

        // Create some shared files relationships in FileShareSeeder
        $this->command->info('Files seeded successfully!');
        $this->command->info('Total files: ' . File::count());
    }
}