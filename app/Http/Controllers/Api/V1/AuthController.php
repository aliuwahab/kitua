<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\RegisterUser;
use App\Actions\Auth\LogoutUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterUserRequest;
use App\Http\Requests\Api\V1\Auth\VerifyPinRequest;
use App\Http\Requests\Api\V1\Auth\LoginUserRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthController extends Controller
{
    use ApiResponses;

    public function __construct(
        private RegisterUser $registerUser,
        private LogoutUser $logoutUser
    ) {}

    /**
     * Initiate registration/login - Send PIN via SMS
     * 
     * @group Authentication
     * @unauthenticated
     * 
     * @bodyParam mobile_number string required User's mobile number. Example: 233244123456
     * @bodyParam first_name string required User's first name. Example: John
     * @bodyParam surname string required User's surname. Example: Doe  
     * @bodyParam other_names string optional User's other names. Example: Michael
     * @bodyParam provider string optional Mobile money provider. Example: MTN
     * @bodyParam device_id string required Unique device identifier. Example: ABC123
     * @bodyParam device_name string optional User-friendly device name. Example: John's iPhone
     * @bodyParam device_type string required Device type (android/ios). Example: android
     * @bodyParam app_version string optional App version. Example: 1.0.0
     * @bodyParam os_version string optional OS version. Example: Android 12
     * @bodyParam device_model string optional Device model. Example: Samsung Galaxy S21
     * @bodyParam screen_resolution string optional Screen resolution. Example: 1080x2340
     * @bodyParam push_token string optional Firebase push token for notifications.
     * 
     * @response 200 {
     *   "data": {
     *     "user_exists": false,
     *     "mobile_number": "233244123456", 
     *     "message": "Registration PIN sent to your mobile number",
     *     "pin": "123456"
     *   },
     *   "message": "PIN sent successfully",
     *   "status": 200
     * }
     * 
     * @response 422 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "mobile_number": ["This mobile number is already registered."]
     *   },
     *   "status": 422
     * }
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $result = $this->registerUser->initiate(
                $request->getUserData(),
                $request->getDeviceData()
            );

            return $this->ok('PIN sent successfully', $result);
        } catch (\Exception $e) {
            return $this->error('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify PIN to complete registration/login
     * 
     * @group Authentication
     * @unauthenticated
     * 
     * @bodyParam mobile_number string required Mobile number used in registration. Example: 233244123456
     * @bodyParam pin string required 6-digit PIN received via SMS. Example: 123456
     * @bodyParam device_id string required Unique device identifier. Example: ABC123
     * @bodyParam device_name string optional User-friendly device name. Example: John's iPhone
     * @bodyParam device_type string required Device type (android/ios). Example: android
     * @bodyParam app_version string optional App version. Example: 1.0.0
     * @bodyParam os_version string optional OS version. Example: Android 12
     * @bodyParam device_model string optional Device model. Example: Samsung Galaxy S21
     * @bodyParam screen_resolution string optional Screen resolution. Example: 1080x2340
     * @bodyParam push_token string optional Firebase push token for notifications.
     * 
     * @response 200 {
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "mobile_number": "233244123456",
     *       "first_name": "John",
     *       "surname": "Doe",
     *       "full_name": "John Doe",
     *       "user_type": "mobile",
     *       "is_active": true,
     *       "payment_accounts": [
     *         {
     *           "id": 1,
     *           "account_type": "momo",
     *           "account_number": "233244123456",
     *           "provider": "MTN",
     *           "is_primary": true,
     *           "is_verified": false
     *         }
     *       ]
     *     },
     *     "token": "1|xyz789token123",
     *     "is_new_user": true,
     *     "is_new_device": true
     *   },
     *   "message": "Authentication successful",
     *   "status": 200
     * }
     * 
     * @response 422 {
     *   "message": "Invalid PIN or mobile number",
     *   "status": 422
     * }
     */
    public function verifyPin(VerifyPinRequest $request): JsonResponse
    {
        try {
            $result = $this->registerUser->complete(
                $request->mobile_number,
                $request->pin,
                $request->getDeviceData()
            );

            return $this->ok('Authentication successful', $result);
        } catch (\Exception $e) {
            return $this->error('Invalid PIN or mobile number', 422);
        }
    }

    /**
     * Alternative login endpoint (uses same flow as register)
     * 
     * @group Authentication
     * @unauthenticated
     * 
     * This endpoint is provided for apps that want separate login/register flows,
     * but internally it uses the same unified flow as the register endpoint.
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        // This could check if user exists first, then call verify directly
        // But for simplicity, we'll just verify the PIN directly
        try {
            $result = $this->registerUser->complete(
                $request->mobile_number,
                $request->pin,
                $request->getDeviceData()
            );

            return $this->ok('Login successful', $result);
        } catch (\Exception $e) {
            return $this->error('Invalid PIN or mobile number', 422);
        }
    }

    /**
     * Logout current device
     * 
     * @group Authentication
     * @authenticated
     * 
     * @response 200 {
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "mobile_number": "233244123456",
     *       "first_name": "John"
     *     },
     *     "reason": "user_initiated",
     *     "logged_out_at": "2025-08-15T08:45:00Z"
     *   },
     *   "message": "Logged out successfully",
     *   "status": 200
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $result = $this->logoutUser->execute($request);
            return $this->ok('Logged out successfully', $result);
        } catch (\Exception $e) {
            return $this->error('Logout failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Logout from all devices
     * 
     * @group Authentication  
     * @authenticated
     * 
     * @response 200 {
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "mobile_number": "233244123456"
     *     },
     *     "device_sessions_count": 3,
     *     "reason": "user_initiated"
     *   },
     *   "message": "Logged out from all devices",
     *   "status": 200
     * }
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $result = $this->logoutUser->logoutFromAllDevices($request->user());
            return $this->ok('Logged out from all devices', $result);
        } catch (\Exception $e) {
            return $this->error('Logout failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get current user profile
     * 
     * @group Authentication
     * @authenticated
     * 
     * @response 200 {
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "mobile_number": "233244123456",
     *       "first_name": "John",
     *       "surname": "Doe",
     *       "full_name": "John Doe",
     *       "country": {
     *         "id": 1,
     *         "name": "Ghana",
     *         "code": "GH",
     *         "currency_code": "GHS",
     *         "currency_symbol": "₵",
     *         "currency_name": "Ghana Cedi"
     *       },
     *       "payment_accounts": [],
     *       "device_sessions": []
     *     }
     *   },
     *   "message": "User profile retrieved",
     *   "status": 200
     * }
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['paymentAccounts', 'activeDeviceSessions', 'country']);
        return $this->ok('User profile retrieved', ['user' => $user]);
    }
}
