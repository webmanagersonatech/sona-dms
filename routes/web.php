<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\Dashboard\WidgetController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::view('/login', 'auth.login')->name('login');

// Authentication routes (guest only)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Password Reset
    // Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    // Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    // Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    // Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

     Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/form', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

      // OTP Verification (during login flow)
    Route::get('/otp-verify', [LoginController::class, 'showOtpForm'])->name('otp.verify.form');
    Route::post('/otp-verify', [LoginController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp-resend', [LoginController::class, 'resendOtp'])->name('otp.resend');
    
    // Email Verification (after registration)
    Route::get('/email/verify', [RegisterController::class, 'showVerificationForm'])->name('verification.notice');
    Route::post('/email/verify', [RegisterController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/resend', [RegisterController::class, 'resendVerification'])->name('verification.resend');
});

// Routes for authenticated users
Route::middleware(['auth'])->group(function () {
    
    // Logout (POST only for security)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
  
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | File Management Routes
    |--------------------------------------------------------------------------
    */
Route::prefix('files')->name('files.')->group(function () {
    // STATIC ROUTES FIRST - these must come BEFORE any {file} routes
    Route::get('/create', [FileController::class, 'create'])->name('create');
    Route::get('/shared-with-me', [FileController::class, 'sharedWithMe'])->name('shared-with-me');
    Route::get('/my-files', [FileController::class, 'myFiles'])->name('my-files');
    Route::get('/by-permission/{permission}', [FileController::class, 'byPermission'])->name('by-permission');
    Route::get('/view-only', [FileController::class, 'viewOnlyFiles'])->name('view-only');
    Route::get('/downloadable', [FileController::class, 'downloadableFiles'])->name('downloadable');
    Route::get('/editable', [FileController::class, 'editableFiles'])->name('editable');
    Route::get('/printable', [FileController::class, 'printableFiles'])->name('printable');
    
    // INDEX ROUTE
    Route::get('/', [FileController::class, 'index'])->name('index');
    
    // POST ROUTE
    Route::post('/', [FileController::class, 'store'])->name('store');
    
    // ROUTES WITH PARAMETERS - these come after all static routes
    Route::get('/{file}', [FileController::class, 'show'])->name('show');
    Route::get('/{file}/edit', [FileController::class, 'edit'])->name('edit');
    Route::put('/{file}', [FileController::class, 'update'])->name('update');
    Route::delete('/{file}', [FileController::class, 'destroy'])->name('destroy');
    Route::get('/{file}/download', [FileController::class, 'download'])->name('download');
    Route::post('/{file}/share', [FileController::class, 'share'])->name('share');
    Route::post('/{file}/archive', [FileController::class, 'archive'])->name('archive');
    Route::post('/{file}/restore', [FileController::class, 'restore'])->name('restore');
    Route::get('/{file}/preview', [FileController::class, 'preview'])->name('preview');
    Route::delete('/shares/{share}', [FileController::class, 'revokeAccess'])->name('shares.revoke');
    Route::get('/{uuid}/verify', [FileController::class, 'verifyAccess'])->name('access.verify');
    Route::post('/{uuid}/verify', [FileController::class, 'confirmAccess'])->name('access.confirm');
});
    /*
    |--------------------------------------------------------------------------
    | Transfer Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('transfers')->name('transfers.')->group(function () {
        Route::get('/', [TransferController::class, 'index'])->name('index');
        Route::get('/create', [TransferController::class, 'create'])->name('create');
        Route::post('/', [TransferController::class, 'store'])->name('store');
        Route::get('/{transfer}', [TransferController::class, 'show'])->name('show');
        Route::get('/{transfer}/edit', [TransferController::class, 'edit'])->name('edit');
        Route::put('/{transfer}', [TransferController::class, 'update'])->name('update');
        Route::delete('/{transfer}', [TransferController::class, 'destroy'])->name('destroy');
        
        // Custom transfer routes
        Route::post('/{transfer}/confirm', [TransferController::class, 'confirmDelivery'])->name('confirm');
        Route::post('/{transfer}/cancel', [TransferController::class, 'cancel'])->name('cancel');
        Route::post('/{transfer}/transit', [TransferController::class, 'markInTransit'])->name('transit');
        Route::get('/track/{transferId}', [TransferController::class, 'track'])->name('track');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Department Management Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('departments', DepartmentController::class);
    Route::post('/departments/{department}/assign-admin', [DepartmentController::class, 'assignAdmin'])->name('departments.assign-admin');
    Route::delete('/departments/{department}/remove-admin/{user}', [DepartmentController::class, 'removeAdmin'])->name('departments.remove-admin');
    
    /*
    |--------------------------------------------------------------------------
    | User Management Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    
    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'password'])->name('password');
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('avatar');
        Route::delete('/avatar', [ProfileController::class, 'removeAvatar'])->name('avatar.remove');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Settings Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::put('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');
        Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('/appearance', [SettingsController::class, 'appearance'])->name('appearance');
        Route::put('/appearance', [SettingsController::class, 'updateAppearance'])->name('appearance.update');
        Route::delete('/sessions/{session}', [SettingsController::class, 'revokeSession'])->name('sessions.revoke');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Report Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/files', [ReportController::class, 'files'])->name('files');
        Route::get('/transfers', [ReportController::class, 'transfers'])->name('transfers');
        Route::get('/users', [ReportController::class, 'users'])->name('users');
        Route::get('/activities', [ReportController::class, 'activities'])->name('activities');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Activity Log Routes
    |--------------------------------------------------------------------------
    */
// Correct order - Specific routes BEFORE parameter routes
Route::prefix('logs')->name('logs.')->group(function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('index');
    Route::get('/stats', [ActivityLogController::class, 'stats'])->name('stats'); // Must be BEFORE {log}
    Route::get('/export', [ActivityLogController::class, 'export'])->name('export'); // Must be BEFORE {log}
    Route::delete('/cleanup', [ActivityLogController::class, 'cleanup'])->name('cleanup'); // Must be BEFORE {log}
    Route::get('/{log}', [ActivityLogController::class, 'show'])->name('show'); // Parameter route LAST
});
    
    /*
    |--------------------------------------------------------------------------
    | Notification Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
    });
    
    /*
    |--------------------------------------------------------------------------
    | API Routes (for AJAX)
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');
        Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');
        Route::get('/dashboard/stats', [WidgetController::class, 'getStats'])->name('dashboard.stats');
        Route::get('/dashboard/chart-data', [WidgetController::class, 'getChartData'])->name('dashboard.chart');
        Route::get('/dashboard/recent-activities', [WidgetController::class, 'getRecentActivities'])->name('dashboard.activities');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Search Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::get('/advanced', [SearchController::class, 'advanced'])->name('advanced');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('twofactor')->name('twofactor.')->group(function () {
        Route::get('/setup', [TwoFactorController::class, 'showSetup'])->name('setup');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
        Route::get('/disable', [TwoFactorController::class, 'showDisable'])->name('disable');
        Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
        Route::get('/challenge', [TwoFactorController::class, 'showChallenge'])->name('challenge');
        Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify');
        Route::get('/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('recovery-codes');
        Route::post('/recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('recovery-codes.regenerate');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Import/Export Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('import-export')->name('import-export.')->group(function () {
        Route::get('/', [ImportExportController::class, 'index'])->name('index');
        Route::get('/export/users', [ImportExportController::class, 'exportUsers'])->name('export.users');
        Route::get('/export/files', [ImportExportController::class, 'exportFiles'])->name('export.files');
        Route::get('/export/transfers', [ImportExportController::class, 'exportTransfers'])->name('export.transfers');
        Route::get('/template/{type}', [ImportExportController::class, 'downloadTemplate'])->name('template');
        Route::post('/import/users', [ImportExportController::class, 'importUsers'])->name('import.users');
    });

     Route::resource('roles', RoleController::class);
    Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    
    // Permission routes
    Route::resource('permissions', PermissionController::class);
});

