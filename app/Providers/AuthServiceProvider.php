<?php
<<<<<<< HEAD
// app/Providers/AuthServiceProvider.php

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

        // Define additional gates if needed
        Gate::define('view-departments', function ($user) {
            return $user->isSuperAdmin() || $user->isDepartmentAdmin();
        });
    }
}
=======

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
   public function boot(): void
{
    Gate::before(function ($user, $ability) {
        return $user->hasPermission($ability) ? true : null;
    });
}
}

>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
