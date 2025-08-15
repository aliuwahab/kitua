<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PaymentRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Health Check
 * 
 * Check the health status of the API service.
 * 
 * @group Health Check
 * @unauthenticated
 * 
 * @response 200 {
 *   "status": "ok",
 *   "service": "Kitua API",
 *   "version": "1.0.0",
 *   "timestamp": "2025-08-15T10:15:30Z"
 * }
 */
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
        
        /**
         * Get current user profile (legacy endpoint)
         * 
         * @group User Profile
         * @authenticated
         * 
         * @response 200 {
         *   "id": 1,
         *   "mobile_number": "233244123456",
         *   "first_name": "John",
         *   "surname": "Doe",
         *   "full_name": "John Doe",
         *   "payment_accounts": [],
         *   "active_device_sessions": []
         * }
         */
        Route::get('/user', function (Request $request) {
            return $request->user()->load(['paymentAccounts', 'activeDeviceSessions']);
        });
        
        /**
         * Get user by UUID (JSON:API endpoint)
         * 
         * @group User Profile
         * @authenticated
         * 
         * @urlParam uuid string required The UUID of the user. Currently returns authenticated user data. Example: f47ac10b-58cc-4372-a567-0e02b2c3d479
         * 
         * @response 200 {
         *   "id": 1,
         *   "mobile_number": "233244123456",
         *   "first_name": "John",
         *   "surname": "Doe",
         *   "full_name": "John Doe",
         *   "payment_accounts": [],
         *   "active_device_sessions": []
         * }
         */
        Route::get('/users/{uuid}', function (Request $request, $uuid) {
            // For now, just return the authenticated user regardless of the ID
            // In a real app, you'd implement proper authorization
            return $request->user()->load(['paymentAccounts', 'activeDeviceSessions']);
        })->name('users.show');
        
        // Payment Requests (Kitua) Routes - Following masterclass patterns
        Route::apiResource('payment-requests', PaymentRequestController::class, [
            'parameters' => ['payment-requests' => 'uuid']
        ])->except(['update']);
        
        // Separate PUT and PATCH operations (masterclass pattern)
        Route::put('payment-requests/{uuid}', [PaymentRequestController::class, 'replace']);
        Route::patch('payment-requests/{uuid}', [PaymentRequestController::class, 'update']);
        
        // TODO: Add other protected routes here
        // - Payment Accounts management
        // - Group Payments management
        // - Transaction history
        // - Device management
    });
});
