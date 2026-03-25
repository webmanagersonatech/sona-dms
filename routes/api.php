<?php

<<<<<<< HEAD
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
=======
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\TransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    
    // Protected routes
    Route::middleware(['auth:sanctum', 'device.validation'])->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        // Files
        Route::apiResource('files', FileController::class);
        Route::post('/files/{file}/share', [FileController::class, 'share']);
        Route::get('/files/{file}/download', [FileController::class, 'download']);
        
        // Transfers
        Route::apiResource('transfers', TransferController::class);
        Route::post('/transfers/{transfer}/send', [TransferController::class, 'send']);
        Route::post('/transfers/{transfer}/receive', [TransferController::class, 'receive']);
        
        // Shared files
        Route::get('/shared/{token}', [FileController::class, 'sharedShow']);
        Route::post('/shared/{token}/verify-otp', [FileController::class, 'verifySharedOtp']);
        
        // Profile
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        
        // Notifications
        Route::get('/notifications', [AuthController::class, 'notifications']);
        Route::post('/notifications/mark-all-read', [AuthController::class, 'markAllNotificationsAsRead']);
        
        // Activity logs
        Route::get('/activity-logs', [AuthController::class, 'activityLogs']);
        
        // Health check
        Route::get('/health', function () {
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
            ]);
        });
    });
    
    // Shared file access (public with token)
    Route::get('/shared/{token}/preview', [FileController::class, 'sharedPreview']);
    Route::post('/shared/{token}/request-otp', [FileController::class, 'requestSharedOtp']);
});
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
