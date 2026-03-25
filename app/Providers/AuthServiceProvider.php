<?php

namespace App\Providers;

use App\Models\User;
use App\Models\File;
use App\Models\Department;
use App\Models\Transfer;
use App\Models\ActivityLog;
use App\Policies\UserPolicy;
use App\Policies\FilePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\TransferPolicy;
use App\Policies\ActivityLogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Department::class => DepartmentPolicy::class,
        User::class => UserPolicy::class,
        File::class => FilePolicy::class,
        Transfer::class => TransferPolicy::class,
        ActivityLog::class => ActivityLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gate for viewing reports
        Gate::define('view-reports', function ($user) {
            // Super admin can always view reports
            if ($user->isSuperAdmin()) {
                return true;
            }

            // Department admin can view reports
            if ($user->isDepartmentAdmin()) {
                return true;
            }

            // Check for specific permission if your system supports it
            if (method_exists($user, 'hasPermission') && $user->hasPermission('view-reports')) {
                return true;
            }

            // Regular users can only see their own data in reports
            // So we allow them access but filter in the controller
            return true; // Allow access but with data filtering
        });

        // Define more specific report gates if needed
        Gate::define('view-file-reports', function ($user) {
            return Gate::allows('view-reports');
        });

        Gate::define('view-transfer-reports', function ($user) {
            return Gate::allows('view-reports');
        });

        Gate::define('view-user-reports', function ($user) {
            return $user->isSuperAdmin() || $user->isDepartmentAdmin();
        });

        Gate::define('view-activity-reports', function ($user) {
            return Gate::allows('view-reports');
        });

        Gate::define('export-reports', function ($user) {
            return $user->isSuperAdmin() || $user->isDepartmentAdmin();
        });

        // Keep your existing gates
        Gate::define('view-departments', function ($user) {
            return $user->isSuperAdmin() || $user->isDepartmentAdmin();
        });
    }
}