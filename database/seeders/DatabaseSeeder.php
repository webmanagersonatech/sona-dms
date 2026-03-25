<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear all tables in correct order
        $tables = [
            'activity_logs',
            'notifications',
            'otp_logs',
            'file_shares',
            'files',
            'transfers',
            'users',
            'departments',
            'role_permissions',
            'permissions',
            'roles',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Run seeders in correct order
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            FileSeeder::class,
            FileShareSeeder::class,
            TransferSeeder::class,
            OtpLogSeeder::class,
            ActivityLogSeeder::class,
            NotificationSeeder::class,
        ]);

        $this->command->info('====================================');
        $this->command->info('All database seeders completed successfully!');
        $this->command->info('====================================');
        
        // Display statistics
        $this->command->table(
            ['Table', 'Records'],
            [
                ['Roles', \App\Models\Role::count()],
                ['Permissions', \App\Models\Permission::count()],
                ['Departments', \App\Models\Department::count()],
                ['Users', \App\Models\User::count()],
                ['Files', \App\Models\File::count()],
                ['File Shares', \App\Models\FileShare::count()],
                ['Transfers', \App\Models\Transfer::count()],
                ['OTP Logs', \App\Models\OtpLog::count()],
            ]
        );
    }
}