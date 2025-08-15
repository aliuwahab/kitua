<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Kitua API',
        'version' => '1.0.0',
        'timestamp' => now()->toISOString()
    ]);
});

// V1 API Routes
Route::prefix('v1')->group(function () {
    
    // Authentication Routes (Unauthenticated)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/verify-pin', [AuthController::class, 'verifyPin']);
        Route::post('/login', [AuthController::class, 'login']); // Alternative endpoint
    });
    
    // Protected Routes (Authenticated)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Authentication Routes
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
            Route::get('/me', [AuthController::class, 'me']);
        });
        
        // User Profile (legacy endpoint)
        Route::get('/user', function (Request $request) {
            return $request->user()->load(['paymentAccounts', 'activeDeviceSessions']);
        });
        
        // TODO: Add other protected routes here
        // - Payment Accounts management
        // - Kitua (Payment requests) management
        // - Group Payments management
        // - Transaction history
        // - Device management
    });
});
