<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create departments
        $departments = [
            ['name' => 'IT Department', 'code' => 'IT', 'description' => 'Information Technology'],
            ['name' => 'HR Department', 'code' => 'HR', 'description' => 'Human Resources'],
            ['name' => 'Finance Department', 'code' => 'FIN', 'description' => 'Finance and Accounting'],
            ['name' => 'Operations Department', 'code' => 'OPS', 'description' => 'Operations Management'],
            ['name' => 'Sales Department', 'code' => 'SALES', 'description' => 'Sales and Marketing'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        // Create super admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@dms.com',
            'password' => Hash::make('password123'),
            'role_id' => Role::where('slug', 'super-admin')->first()->id,
            'department_id' => Department::where('code', 'IT')->first()->id,
            'employee_id' => 'SA001',
            'phone' => '1234567890',
            'is_active' => true,
        ]);

        // Create admin users for each department
        $adminRole = Role::where('slug', 'admin')->first();
        foreach ($departments as $dept) {
            $department = Department::where('code', $dept['code'])->first();
            
            User::create([
                'name' => $dept['name'] . ' Admin',
                'email' => strtolower($dept['code']) . '.admin@dms.com',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'department_id' => $department->id,
                'employee_id' => $dept['code'] . '001',
                'phone' => '1234567890',
                'is_active' => true,
            ]);
        }

        // Create sample users with different roles
        $roles = Role::whereNotIn('slug', ['super-admin', 'admin'])->get();
        
        foreach ($departments as $dept) {
            $department = Department::where('code', $dept['code'])->first();
            
            foreach ($roles as $index => $role) {
                User::create([
                    'name' => $role->name . ' ' . $dept['name'],
                    'email' => strtolower($role->slug) . '.' . strtolower($dept['code']) . '@dms.com',
                    'password' => Hash::make('password123'),
                    'role_id' => $role->id,
                    'department_id' => $department->id,
                    'employee_id' => $dept['code'] . str_pad($index + 2, 3, '0', STR_PAD_LEFT),
                    'phone' => '1234567890',
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Super Admin: superadmin@dms.com / password123');
        $this->command->info('Department Admins: [dept].admin@dms.com / password123');
    }
}