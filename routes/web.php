<?php

<<<<<<< HEAD
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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes (guest only)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Password Reset
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
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
=======
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// OTP Verification
Route::get('/otp/verify/{purpose}', [AuthController::class, 'showOtpVerification'])
    ->name('otp.verify');

Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])
    ->name('otp.verify.submit');

Route::post('/otp/resend', [AuthController::class, 'resendOtp'])
    ->name('otp.resend');



// Device Verification
Route::get('/device/verify', [DeviceController::class, 'showVerification'])->name('device.verify');
Route::post('/device/verify', [DeviceController::class, 'verifyDevice']);
Route::post('/device/resend-otp', [DeviceController::class, 'resendDeviceOtp'])->name('device.resend-otp');

// Authenticated Routes
Route::middleware(['auth', 'device.validation'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile
    
    Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::get('/activity-logs', [ProfileController::class, 'activityLogs'])->name('activity-logs');
    Route::post('/notifications/preferences', [ProfileController::class, 'updateNotificationPreferences'])->name('notifications.preferences');
});

Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('get-unread-count');
    Route::get('/recent', [NotificationController::class, 'getRecent'])->name('get-recent');
});

    // Device Management
    Route::get('/devices', [DeviceController::class, 'manageDevices'])->name('devices.index');
    Route::delete('/devices/{id}', [DeviceController::class, 'revokeDevice'])->name('devices.revoke');

    // File Routes
    Route::prefix('files')->name('files.')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('index');
        Route::get('/create', [FileController::class, 'create'])->name('create');
        Route::post('/', [FileController::class, 'store'])->name('store');
        Route::get('/{file}', [FileController::class, 'show'])->name('show');
        Route::get('/{file}/preview', [FileController::class, 'preview'])->name('preview');
        Route::get('/{file}/download', [FileController::class, 'download'])->name('download');
        Route::put('/{file}/archive', [FileController::class, 'archive'])->name('archive');
        Route::put('/{file}/restore', [FileController::class, 'restore'])->name('restore');
        
        // File Sharing
        Route::get('/{file}/shares', [FileController::class, 'shares'])->name('shares');
        Route::get('/{file}/shares/create', [FileController::class, 'createShare'])->name('shares.create');
        Route::post('/{file}/shares', [FileController::class, 'storeShare'])->name('shares.store');
        Route::delete('/shares/{share}', [FileController::class, 'revokeShare'])->name('shares.revoke');
    });

    // Transfer Routes
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    Route::prefix('transfers')->name('transfers.')->group(function () {
        Route::get('/', [TransferController::class, 'index'])->name('index');
        Route::get('/create', [TransferController::class, 'create'])->name('create');
        Route::post('/', [TransferController::class, 'store'])->name('store');
        Route::get('/{transfer}', [TransferController::class, 'show'])->name('show');
<<<<<<< HEAD
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
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{log}', [ActivityLogController::class, 'show'])->name('show');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
        Route::get('/stats', [ActivityLogController::class, 'stats'])->name('stats');
        Route::delete('/cleanup', [ActivityLogController::class, 'cleanup'])->name('cleanup');
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
});

/*
|--------------------------------------------------------------------------
| Route Caching Optimization
|--------------------------------------------------------------------------
|
| To cache your routes for better performance, run:
| php artisan route:cache
|
| Note: Remove the route cache if you make any changes to routes:
| php artisan route:clear
|
*/

// Fallback route for 404
// Route::fallback(function () {
//     return response()->view('errors.404', [], 404);
// });


// Add this at the top of your routes file, before any other routes
Route::get('/debug-user', function() {
    if (!auth()->check()) {
        return 'Not logged in. Please <a href="/login">login</a> first.';
    }
    
    $user = auth()->user();
    
    // Load the role relationship
    $user->load('role');
    
    $data = [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ],
        'role' => $user->role ? [
            'id' => $user->role->id,
            'name' => $user->role->name,
            'slug' => $user->role->slug,
        ] : 'NO ROLE ASSIGNED - This is your problem!',
        'is_super_admin' => $user->isSuperAdmin(),
        'is_department_admin' => $user->isDepartmentAdmin(),
        'can_view_departments' => $user->can('viewAny', App\Models\Department::class),
    ];
    
    // If no role, suggest fix
    if (!$user->role) {
        $data['suggestion'] = 'Run this in tinker to fix: $user = App\\Models\\User::find(' . $user->id . '); $role = App\\Models\\Role::where(\'slug\', \'super-admin\')->first(); $user->role_id = $role->id; $user->save();';
    }
    
    return $data;
})->middleware('auth');
=======
        Route::put('/{transfer}/send', [TransferController::class, 'send'])->name('send');
        Route::put('/{transfer}/deliver', [TransferController::class, 'deliver'])->name('deliver');
        Route::put('/{transfer}/receive', [TransferController::class, 'receive'])->name('receive');
        Route::post('/{transfer}/receive/confirm', [TransferController::class, 'confirmReceipt'])->name('receive.confirm');
        Route::delete('/{transfer}/cancel', [TransferController::class, 'cancel'])->name('cancel');
        
        // Third-party access
        Route::post('/{transfer}/third-party/request', [TransferController::class, 'requestThirdPartyAccess'])->name('third-party.request');
        Route::post('/third-party/approve', [TransferController::class, 'approveThirdPartyAccess'])->name('third-party.approve');
        
        // Cloud sharing
        Route::post('/{transfer}/cloud/share', [TransferController::class, 'cloudShare'])->name('cloud.share');
    });

    // Shared File Access (Public routes with token)
    Route::prefix('shared')->name('shared.')->group(function () {
        Route::get('/{token}', [FileController::class, 'sharedShow'])->name('show');
        Route::get('/{token}/otp', [FileController::class, 'sharedOtp'])->name('otp');
        Route::post('/{token}/otp/verify', [FileController::class, 'verifySharedOtp'])->name('otp.verify');
        Route::post('/{token}/otp/request', [FileController::class, 'requestOtpForShared'])->name('otp.request');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:super-admin,admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users routes
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'createUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');
    
    // Departments routes
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('departments.store');
    Route::put('/departments/{department}', [AdminController::class, 'updateDepartment'])->name('departments.update');
        
        // Audit Logs
        Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit-logs');
        
        // Statistics
        Route::get('/stats', [AdminController::class, 'systemStats'])->name('stats');
        
        // File Management
        Route::get('/files', [AdminController::class, 'fileManagement'])->name('files');
        Route::post('/files/bulk-actions', [AdminController::class, 'bulkActions'])->name('files.bulk-actions');
        
        // Transfer Monitoring
        Route::get('/transfers', [AdminController::class, 'transferMonitoring'])->name('transfers');

         // Reports & Exports
    Route::get('/stats/export', [AdminController::class, 'exportStats'])->name('stats.export');
    Route::get('/audit-logs/export', [AdminController::class, 'exportAuditLogs'])->name('audit-logs.export');
    
    // System Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    
    // Backup Management
    Route::get('/backups', [AdminController::class, 'backups'])->name('backups');
    Route::post('/backups/create', [AdminController::class, 'createBackup'])->name('backups.create');
    Route::post('/backups/restore/{filename}', [AdminController::class, 'restoreBackup'])->name('backups.restore');
    });
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'services' => [
            'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
            'storage' => is_writable(storage_path()) ? 'writable' : 'readonly',
            'cache' => function_exists('apcu_clear_cache') ? 'enabled' : 'disabled',
        ],
    ]);
});
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
