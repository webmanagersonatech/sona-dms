<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        DB::table('role_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'module' => 'users', 'description' => 'Can view user list and details'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'module' => 'users', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'module' => 'users', 'description' => 'Can edit user information'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'module' => 'users', 'description' => 'Can delete/suspend users'],
            ['name' => 'Export Users', 'slug' => 'export-users', 'module' => 'users', 'description' => 'Can export user data'],
            ['name' => 'Impersonate Users', 'slug' => 'impersonate-users', 'module' => 'users', 'description' => 'Can login as other users'],

            // Department Management
            ['name' => 'View Departments', 'slug' => 'view-departments', 'module' => 'departments', 'description' => 'Can view department list'],
            ['name' => 'Create Departments', 'slug' => 'create-departments', 'module' => 'departments', 'description' => 'Can create new departments'],
            ['name' => 'Edit Departments', 'slug' => 'edit-departments', 'module' => 'departments', 'description' => 'Can edit department information'],
            ['name' => 'Delete Departments', 'slug' => 'delete-departments', 'module' => 'departments', 'description' => 'Can delete departments'],
            ['name' => 'Assign Department Admin', 'slug' => 'assign-dept-admin', 'module' => 'departments', 'description' => 'Can assign department administrators'],

            // File Management
            ['name' => 'View Files', 'slug' => 'view-files', 'module' => 'files', 'description' => 'Can view files'],
            ['name' => 'Upload Files', 'slug' => 'upload-files', 'module' => 'files', 'description' => 'Can upload new files'],
            ['name' => 'Download Files', 'slug' => 'download-files', 'module' => 'files', 'description' => 'Can download files'],
            ['name' => 'Edit Files', 'slug' => 'edit-files', 'module' => 'files', 'description' => 'Can edit file metadata'],
            ['name' => 'Delete Files', 'slug' => 'delete-files', 'module' => 'files', 'description' => 'Can delete files'],
            ['name' => 'Share Files', 'slug' => 'share-files', 'module' => 'files', 'description' => 'Can share files with others'],
            ['name' => 'Archive Files', 'slug' => 'archive-files', 'module' => 'files', 'description' => 'Can archive files'],
            ['name' => 'Restore Files', 'slug' => 'restore-files', 'module' => 'files', 'description' => 'Can restore archived files'],
            ['name' => 'Encrypt Files', 'slug' => 'encrypt-files', 'module' => 'files', 'description' => 'Can encrypt files during upload'],

            // Transfer Management
            ['name' => 'View Transfers', 'slug' => 'view-transfers', 'module' => 'transfers', 'description' => 'Can view transfers'],
            ['name' => 'Create Transfers', 'slug' => 'create-transfers', 'module' => 'transfers', 'description' => 'Can create new transfers'],
            ['name' => 'Edit Transfers', 'slug' => 'edit-transfers', 'module' => 'transfers', 'description' => 'Can edit transfer details'],
            ['name' => 'Delete Transfers', 'slug' => 'delete-transfers', 'module' => 'transfers', 'description' => 'Can delete transfers'],
            ['name' => 'Confirm Transfers', 'slug' => 'confirm-transfers', 'module' => 'transfers', 'description' => 'Can confirm transfer delivery'],
            ['name' => 'Cancel Transfers', 'slug' => 'cancel-transfers', 'module' => 'transfers', 'description' => 'Can cancel transfers'],
            ['name' => 'Export Transfers', 'slug' => 'export-transfers', 'module' => 'transfers', 'description' => 'Can export transfer data'],
            ['name' => 'Track Transfers', 'slug' => 'track-transfers', 'module' => 'transfers', 'description' => 'Can track transfer status'],

            // Activity Logs
            ['name' => 'View Logs', 'slug' => 'view-logs', 'module' => 'logs', 'description' => 'Can view activity logs'],
            ['name' => 'Export Logs', 'slug' => 'export-logs', 'module' => 'logs', 'description' => 'Can export activity logs'],
            ['name' => 'Delete Logs', 'slug' => 'delete-logs', 'module' => 'logs', 'description' => 'Can delete old logs'],
            ['name' => 'View Log Stats', 'slug' => 'view-log-stats', 'module' => 'logs', 'description' => 'Can view log statistics'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'view-reports', 'module' => 'reports', 'description' => 'Can view reports'],
            ['name' => 'Export Reports', 'slug' => 'export-reports', 'module' => 'reports', 'description' => 'Can export reports'],
            ['name' => 'Create Reports', 'slug' => 'create-reports', 'module' => 'reports', 'description' => 'Can create custom reports'],
            ['name' => 'Schedule Reports', 'slug' => 'schedule-reports', 'module' => 'reports', 'description' => 'Can schedule automated reports'],

            // Settings
            ['name' => 'View Settings', 'slug' => 'view-settings', 'module' => 'settings', 'description' => 'Can view system settings'],
            ['name' => 'Edit Settings', 'slug' => 'edit-settings', 'module' => 'settings', 'description' => 'Can edit system settings'],
            ['name' => 'View Security Settings', 'slug' => 'view-security', 'module' => 'settings', 'description' => 'Can view security settings'],
            ['name' => 'Edit Security Settings', 'slug' => 'edit-security', 'module' => 'settings', 'description' => 'Can edit security settings'],

            // Notifications
            ['name' => 'View Notifications', 'slug' => 'view-notifications', 'module' => 'notifications', 'description' => 'Can view notifications'],
            ['name' => 'Manage Notifications', 'slug' => 'manage-notifications', 'module' => 'notifications', 'description' => 'Can manage notification settings'],
            ['name' => 'Send Notifications', 'slug' => 'send-notifications', 'module' => 'notifications', 'description' => 'Can send system notifications'],

            // API
            ['name' => 'Access API', 'slug' => 'access-api', 'module' => 'api', 'description' => 'Can access API endpoints'],
            ['name' => 'Manage API Keys', 'slug' => 'manage-api-keys', 'module' => 'api', 'description' => 'Can manage API keys'],

            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'module' => 'dashboard', 'description' => 'Can view dashboard'],
            ['name' => 'Customize Dashboard', 'slug' => 'customize-dashboard', 'module' => 'dashboard', 'description' => 'Can customize dashboard widgets'],

            // Approvals
            ['name' => 'Approve Files', 'slug' => 'approve-files', 'module' => 'approvals', 'description' => 'Can approve file uploads'],
            ['name' => 'Approve Transfers', 'slug' => 'approve-transfers', 'module' => 'approvals', 'description' => 'Can approve transfers'],
            ['name' => 'Approve Users', 'slug' => 'approve-users', 'module' => 'approvals', 'description' => 'Can approve user registrations'],
        ];

        foreach ($permissions as $perm) {
            Permission::create($perm);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Permissions seeded successfully!');
    }

    private function assignPermissionsToRoles()
    {
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $deptAdmin = Role::where('slug', 'department-admin')->first();
        $manager = Role::where('slug', 'manager')->first();
        $user = Role::where('slug', 'user')->first();
        $auditor = Role::where('slug', 'auditor')->first();

        // Super Admin gets ALL permissions
        $superAdmin->permissions()->sync(Permission::all());

        // Department Admin permissions
        $deptAdminPermissions = Permission::whereIn('slug', [
            'view-users', 'create-users', 'edit-users',
            'view-departments',
            'view-files', 'upload-files', 'download-files', 'edit-files', 'share-files', 'archive-files', 'restore-files', 'encrypt-files',
            'view-transfers', 'create-transfers', 'edit-transfers', 'confirm-transfers', 'cancel-transfers', 'track-transfers',
            'view-logs', 'export-logs',
            'view-reports', 'export-reports',
            'view-notifications', 'manage-notifications',
            'view-dashboard',
        ])->get();
        $deptAdmin->permissions()->sync($deptAdminPermissions);

        // Manager permissions
        $managerPermissions = Permission::whereIn('slug', [
            'view-users',
            'view-files', 'upload-files', 'download-files', 'share-files',
            'view-transfers', 'create-transfers', 'confirm-transfers', 'track-transfers',
            'view-logs',
            'view-reports',
            'view-notifications',
            'view-dashboard',
            'approve-files', 'approve-transfers',
        ])->get();
        $manager->permissions()->sync($managerPermissions);

        // Regular User permissions
        $userPermissions = Permission::whereIn('slug', [
            'view-files', 'upload-files', 'download-files', 'share-files',
            'view-transfers', 'create-transfers', 'track-transfers',
            'view-notifications',
            'view-dashboard',
        ])->get();
        $user->permissions()->sync($userPermissions);

        // Auditor permissions (read-only)
        $auditorPermissions = Permission::whereIn('slug', [
            'view-users',
            'view-departments',
            'view-files',
            'view-transfers',
            'view-logs',
            'view-reports',
            'view-notifications',
            'view-dashboard',
        ])->get();
        $auditor->permissions()->sync($auditorPermissions);
    }
}