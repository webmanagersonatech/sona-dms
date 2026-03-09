<?php

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
    Route::prefix('transfers')->name('transfers.')->group(function () {
        Route::get('/', [TransferController::class, 'index'])->name('index');
        Route::get('/create', [TransferController::class, 'create'])->name('create');
        Route::post('/', [TransferController::class, 'store'])->name('store');
        Route::get('/{transfer}', [TransferController::class, 'show'])->name('show');
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