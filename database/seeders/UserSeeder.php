<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = Role::all()->keyBy('slug');
        $departments = Department::all();

        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@dms.com',
            'password' => Hash::make('Admin@123'),
            'role_id' => $roles['super-admin']->id,
            'department_id' => null,
            'phone' => '+1234567890',
            'status' => 'active',
            'email_verified' => true,
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'locale' => 'en',
            'two_factor_enabled' => false,
            'settings' => json_encode([
                'theme' => 'light',
                'notifications' => true,
                'language' => 'en',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Department Admins (one for each department)
        foreach ($departments as $index => $department) {
            User::create([
                'name' => $department->name . ' Admin',
                'email' => 'admin.' . strtolower($department->code) . '@dms.com',
                'password' => Hash::make('Dept@123'),
                'role_id' => $roles['department-admin']->id,
                'department_id' => $department->id,
                'phone' => '+123456789' . ($index + 1),
                'status' => 'active',
                'email_verified' => true,
                'email_verified_at' => now(),
                'timezone' => 'UTC',
                'locale' => 'en',
                'two_factor_enabled' => false,
                'settings' => json_encode([
                    'theme' => 'light',
                    'notifications' => true,
                    'language' => 'en',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Managers (2 per department)
        foreach ($departments as $department) {
            for ($i = 1; $i <= 2; $i++) {
                User::create([
                    'name' => 'Manager ' . $i . ' - ' . $department->name,
                    'email' => 'manager.' . strtolower($department->code) . $i . '@dms.com',
                    'password' => Hash::make('Manager@123'),
                    'role_id' => $roles['manager']->id,
                    'department_id' => $department->id,
                    'phone' => '+123456789' . rand(100, 999),
                    'status' => 'active',
                    'email_verified' => true,
                    'email_verified_at' => now(),
                    'timezone' => 'UTC',
                    'locale' => 'en',
                    'two_factor_enabled' => false,
                    'settings' => json_encode([
                        'theme' => 'light',
                        'notifications' => true,
                        'language' => 'en',
                    ]),
                    'created_at' => now()->subDays(rand(30, 365)),
                    'updated_at' => now(),
                ]);
            }
        }

        // Regular Users (5-10 per department)
        foreach ($departments as $department) {
            $userCount = rand(5, 10);
            for ($i = 1; $i <= $userCount; $i++) {
                $createdAt = now()->subDays(rand(1, 365));
                $lastLoginAt = rand(0, 1) ? $createdAt->copy()->addDays(rand(1, 30)) : null;
                
                User::create([
                    'name' => fake()->name(),
                    'email' => 'user.' . strtolower($department->code) . $i . '@dms.com',
                    'password' => Hash::make('User@123'),
                    'role_id' => $roles['user']->id,
                    'department_id' => $department->id,
                    'phone' => '+123456789' . rand(1000, 9999),
                    'status' => rand(0, 10) > 1 ? 'active' : (rand(0, 1) ? 'inactive' : 'suspended'),
                    'email_verified' => (bool)rand(0, 1),
                    'email_verified_at' => rand(0, 1) ? $createdAt : null,
                    'last_login_at' => $lastLoginAt,
                    'last_login_ip' => $lastLoginAt ? '192.168.1.' . rand(1, 255) : null,
                    'timezone' => 'UTC',
                    'locale' => 'en',
                    'two_factor_enabled' => (bool)rand(0, 1),
                    'settings' => json_encode([
                        'theme' => rand(0, 1) ? 'light' : 'dark',
                        'notifications' => (bool)rand(0, 1),
                        'language' => 'en',
                    ]),
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ]);
            }
        }

        // Auditors (2)
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'name' => 'Auditor ' . $i,
                'email' => 'auditor' . $i . '@dms.com',
                'password' => Hash::make('Audit@123'),
                'role_id' => $roles['auditor']->id,
                'department_id' => null,
                'phone' => '+123456789' . rand(1000, 9999),
                'status' => 'active',
                'email_verified' => true,
                'email_verified_at' => now(),
                'timezone' => 'UTC',
                'locale' => 'en',
                'two_factor_enabled' => (bool)rand(0, 1),
                'settings' => json_encode([
                    'theme' => 'light',
                    'notifications' => true,
                    'language' => 'en',
                ]),
                'created_at' => now()->subDays(rand(30, 180)),
                'updated_at' => now(),
            ]);
        }

        // Test users with specific credentials
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['user']->id,
            'department_id' => $departments->random()->id,
            'phone' => '+1234567890',
            'status' => 'active',
            'email_verified' => true,
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'locale' => 'en',
            'two_factor_enabled' => false,
            'settings' => json_encode([
                'theme' => 'light',
                'notifications' => true,
                'language' => 'en',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Users seeded successfully!');
        $this->command->info('Total users: ' . User::count());
    }
}