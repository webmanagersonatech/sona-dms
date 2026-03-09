<?php

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
    }
}