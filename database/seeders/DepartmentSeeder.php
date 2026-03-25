<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Department::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $departments = [
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Manages employee relations, recruitment, and company culture',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Handles all technical infrastructure and support',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Manages financial operations, budgeting, and accounting',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Handles marketing campaigns and brand management',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Manages day-to-day business operations',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Research & Development',
                'code' => 'RND',
                'description' => 'Focuses on innovation and product development',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer Support',
                'code' => 'CS',
                'description' => 'Provides customer service and technical support',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Legal',
                'code' => 'LEG',
                'description' => 'Handles legal matters and compliance',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales',
                'code' => 'SALES',
                'description' => 'Manages sales operations and client relationships',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Administration',
                'code' => 'ADMIN',
                'description' => 'Handles administrative tasks and office management',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quality Assurance',
                'code' => 'QA',
                'description' => 'Ensures product and service quality',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Training',
                'code' => 'TRN',
                'description' => 'Manages employee training and development',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        $this->command->info('Departments seeded successfully!');
    }
}