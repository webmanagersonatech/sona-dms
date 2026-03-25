<?php
<<<<<<< HEAD
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
=======

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register services
        $this->app->singleton(\App\Services\BrevoEmailService::class);
        $this->app->singleton(\App\Services\OtpService::class);
        $this->app->singleton(\App\Services\FileEncryptionService::class);
        $this->app->singleton(\App\Services\ActivityLogger::class);
    }

    public function boot()
    {
        // Set default string length
        Schema::defaultStringLength(191);

        // Register custom validation rules
        $this->registerValidationRules();

        // Set timezone
      date_default_timezone_set(config('app.timezone', 'Asia/Kolkata'));


        require_once app_path('Helpers/role_helpers.php');
    }

    protected function registerValidationRules()
    {
        // Custom validation rules can be added here
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}