<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register any application services here
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for older MySQL versions
        Schema::defaultStringLength(191);
        
        // Use Bootstrap for pagination
        Paginator::useBootstrapFive();
        
        // Register custom Blade directives
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->isSuperAdmin();
        });
        
        Blade::if('deptadmin', function () {
            return auth()->check() && auth()->user()->isDepartmentAdmin();
        });
        
        Blade::if('manager', function () {
            return auth()->check() && auth()->user()->isManager();
        });
        
        Blade::if('auditor', function () {
            return auth()->check() && auth()->user()->isAuditor();
        });
    }
}