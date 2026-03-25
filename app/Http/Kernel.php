<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
<<<<<<< HEAD
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
=======
    protected $middleware = [
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

<<<<<<< HEAD
    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
<<<<<<< HEAD
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
=======
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

<<<<<<< HEAD
    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
=======
    protected $routeMiddleware = [
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
<<<<<<< HEAD
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    'permission' => \App\Http\Middleware\PermissionMiddleware::class,
    'department' => \App\Http\Middleware\DepartmentMiddleware::class,
    ];
}
=======
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // Custom Middleware
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'department' => \App\Http\Middleware\DepartmentMiddleware::class,
        'device.validation' => \App\Http\Middleware\DeviceValidation::class,
        'check.otp' => \App\Http\Middleware\CheckOtp::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
    ];
}
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
