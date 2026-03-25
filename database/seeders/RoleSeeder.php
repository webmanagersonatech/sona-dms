<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Department Admin',
                'slug' => 'department-admin',
                'description' => 'Administrative access for specific department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user with basic access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Auditor',
                'slug' => 'auditor',
                'description' => 'Read-only access for auditing purposes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Department manager with approval权限',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $this->command->info('Roles seeded successfully!');
    }
}