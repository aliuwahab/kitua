<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\RegisterUser;
use App\Actions\Auth\LogoutUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterUserRequest;
use App\Http\Requests\Api\V1\Auth\VerifyPinRequest;
use App\Http\Requests\Api\V1\Auth\LoginUserRequest;
use App\Http\Resources\V1\Auth\RegistrationResource;
use App\Http\Resources\V1\Auth\AuthenticationResource;
use App\Http\Resources\V1\Auth\LogoutResource;
use App\Http\Resources\V1\UserResource;
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
     *     "type": "registration",
     *     "id": "689f7aae2da9f",
     *     "attributes": {
     *       "userExists": false,
     *       "mobileNumber": "233244123456",
     *       "message": "Registration PIN sent to your mobile number",
     *       "pin": "250964"
     *     },
     *     "links": {
     *       "verifyPin": "http://localhost/api/v1/auth/verify-pin",
     *       "login": "http://localhost/api/v1/auth/login"
     *     }
     *   },
     *   "message": "Registration PIN sent to your mobile number",
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
    public function register(RegisterUserRequest $request)
    {
        try {
            $result = $this->registerUser->initiate(
                $request->getUserData(),
                $request->getDeviceData()
            );

            $resource = new RegistrationResource($result);
            return $this->success(
                $result['message'] ?? 'PIN sent successfully',
                $resource->toArray(request())
            );
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
     *     "type": "authentication",
     *     "id": "0198aef7-7dd4-73f5-b45b-ffa0e2f2cd76",
     *     "attributes": {
     *       "token": "1|1U35ymFSmoMn02wdjtQNVALwbJhHw2epENTb7a1Bbeb73e31",
     *       "isNewUser": true,
     *       "isNewDevice": true,
     *       "userExists": null,
     *       "mobileNumber": "233244123456",
     *       "message": null,
     *       "pin": null
     *     },
     *     "relationships": {
     *       "user": {
     *         "data": {
     *           "type": "user",
     *           "id": "0198aef7-7dd4-73f5-b45b-ffa0e2f2cd76"
     *         },
     *         "links": {
     *           "self": "#"
     *         }
     *       }
     *     },
     *     "includes": {
     *       "user": {
     *         "type": "user",
     *         "id": "0198aef7-7dd4-73f5-b45b-ffa0e2f2cd76",
     *         "attributes": {
     *           "mobileNumber": "233244123456",
     *           "firstName": "John",
     *           "surname": "Doe",
     *           "otherNames": null,
     *           "fullName": "John Doe",
     *           "userType": "customer",
     *           "isActive": true,
     *           "emailVerifiedAt": null,
     *           "createdAt": "2025-08-15T18:21:51.000000Z",
     *           "updatedAt": "2025-08-15T18:21:51.000000Z"
     *         },
     *         "relationships": {
     *           "country": {
     *             "data": {
     *               "type": "country",
     *               "id": "53c153e8-9d2d-4b60-9844-985e6e2c7db2"
     *             },
     *             "links": {
     *               "self": "http://localhost/api/v1/countries/53c153e8-9d2d-4b60-9844-985e6e2c7db2"
     *             }
     *           },
     *           "paymentAccounts": {
     *             "data": [
     *               {
     *                 "type": "paymentAccount",
     *                 "id": "0198aef7-7dd6-7097-830d-fabd3845c8b7"
     *               }
     *             ],
     *             "links": {
     *               "related": "#"
     *             }
     *           }
     *         },
     *         "includes": {
     *           "paymentAccounts": [
     *             {
     *               "type": "paymentAccount",
     *               "id": "0198aef7-7dd6-7097-830d-fabd3845c8b7",
     *               "attributes": {
     *                 "accountType": "momo",
     *                 "accountNumber": "233244123456",
     *                 "accountName": "John Doe",
     *                 "provider": null,
     *                 "isPrimary": true,
     *                 "isVerified": false,
     *                 "isActive": null,
     *                 "verifiedAt": null,
     *                 "createdAt": "2025-08-15T18:21:51.000000Z",
     *                 "updatedAt": "2025-08-15T18:21:51.000000Z"
     *               },
     *               "relationships": {
     *                 "user": {
     *                   "data": {
     *                     "type": "user",
     *                     "id": "0198aef7-7dd4-73f5-b45b-ffa0e2f2cd76"
     *                   },
     *                   "links": {
     *                     "self": "#"
     *                   }
     *                 }
     *               },
     *               "links": {
     *                 "self": "#"
     *               }
     *             }
     *           ]
     *         },
     *         "links": {
     *           "self": "#"
     *         }
     *       }
     *     },
     *     "links": {
     *       "self": "#"
     *     }
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
    public function verifyPin(VerifyPinRequest $request)
    {
        try {
            $result = $this->registerUser->complete(
                $request->mobile_number,
                $request->pin,
                $request->getDeviceData()
            );
            
            $resource = new AuthenticationResource($result);
            return $this->success(
                $result['message'] ?? 'Authentication successful',
                $resource->toArray(request())
            );
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
     * 
     * @bodyParam mobile_number string required Mobile number used for login. Example: 233244123456
     * @bodyParam pin string required 6-digit PIN for authentication. Example: 123456
     * @bodyParam device_id string required Unique device identifier. Example: ABC123
     * @bodyParam device_name string optional User-friendly device name. Example: John's iPhone
     * @bodyParam device_type string required Device type (android/ios). Example: android
     * @bodyParam app_version string optional App version. Example: 1.0.0
     * @bodyParam os_version string optional OS version. Example: Android 12
     * @bodyParam device_model string optional Device model. Example: Samsung Galaxy S21
     * @bodyParam screen_resolution string optional Screen resolution. Example: 1080x2340
     * @bodyParam push_token string optional Firebase push token for notifications.
     * 
     * @response 200 scenario="Login successful" {
     *   "data": {
     *     "type": "authentication",
     *     "id": 1,
     *     "attributes": {
     *       "token": "1|xyz789token123",
     *       "isNewUser": false,
     *       "isNewDevice": false,
     *       "userExists": null,
     *       "mobileNumber": "233244123456",
     *       "message": null,
     *       "pin": null
     *     },
     *     "relationships": {
     *       "user": {
     *         "data": {
     *           "type": "user",
     *           "id": 1
     *         },
     *         "links": {
     *           "self": "/api/v1/users/1"
     *         }
     *       }
     *     },
     *     "includes": {
     *       "user": {
     *         "type": "user",
     *         "id": 1,
     *         "attributes": {
     *           "mobileNumber": "233244123456",
     *           "firstName": "John",
     *           "surname": "Doe",
     *           "fullName": "John Doe",
     *           "userType": "customer",
     *           "isActive": true
     *         }
     *       }
     *     },
     *     "links": {
     *       "self": "/api/v1/users/1"
     *     }
     *   }
     * }
     * 
     * @response 422 scenario="Invalid credentials" {
     *   "message": "Invalid PIN or mobile number",
     *   "status": 422
     * }
     */
    public function login(LoginUserRequest $request)
    {
        // This could check if user exists first, then call verify directly
        // But for simplicity, we'll just verify the PIN directly
        try {
            $result = $this->registerUser->complete(
                $request->mobile_number,
                $request->pin,
                $request->getDeviceData()
            );

            $resource = new AuthenticationResource($result);
            return $this->success(
                $result['message'] ?? 'Login successful',
                $resource->toArray(request())
            );
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
     *     "type": "logout",
     *     "id": "66c8f91a8f4d3",
     *     "attributes": {
     *       "reason": "user_initiated",
     *       "loggedOutAt": "2025-08-15T08:45:00Z",
     *       "deviceSessionsCount": null,
     *       "message": "Logged out successfully"
     *     },
     *     "relationships": {
     *       "user": {
     *         "data": {
     *           "type": "user",
     *           "id": 1
     *         },
     *         "links": {
     *           "self": "/api/v1/users/1"
     *         }
     *       }
     *     },
     *     "includes": {
     *       "user": {
     *         "type": "user",
     *         "id": 1,
     *         "attributes": {
     *           "mobileNumber": "233244123456",
     *           "firstName": "John"
     *         }
     *       }
     *     }
     *   }
     * }
     */
    public function logout(Request $request)
    {
        try {
            $result = $this->logoutUser->execute($request);
            $resource = new LogoutResource($result);
            return $this->success(
                $result['message'] ?? 'Logged out successfully',
                $resource->toArray(request())
            );
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
     *     "type": "logout",
     *     "id": "66c8f91a8f4d3",
     *     "attributes": {
     *       "reason": "user_initiated",
     *       "loggedOutAt": "2025-08-15T08:45:00Z",
     *       "deviceSessionsCount": 3,
     *       "message": "Logged out from all devices"
     *     },
     *     "relationships": {
     *       "user": {
     *         "data": {
     *           "type": "user",
     *           "id": 1
     *         },
     *         "links": {
     *           "self": "/api/v1/users/1"
     *         }
     *       }
     *     },
     *     "includes": {
     *       "user": {
     *         "type": "user",
     *         "id": 1,
     *         "attributes": {
     *           "mobileNumber": "233244123456"
     *         }
     *       }
     *     }
     *   }
     * }
     */
    public function logoutAll(Request $request)
    {
        try {
            $result = $this->logoutUser->logoutFromAllDevices($request->user());
            $resource = new LogoutResource($result);
            return $this->success(
                $result['message'] ?? 'Logged out from all devices',
                $resource->toArray(request())
            );
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
     *     "type": "user",
     *     "id": "0198aef7-c446-7064-aa5b-f97eec114ba2",
     *     "attributes": {
     *       "mobileNumber": "233337138993",
     *       "firstName": "Camren",
     *       "surname": "Simonis",
     *       "otherNames": "Jarrell",
     *       "fullName": "Camren Simonis Jarrell",
     *       "userType": "customer",
     *       "isActive": true,
     *       "emailVerifiedAt": null,
     *       "createdAt": "2025-08-15T18:22:09.000000Z",
     *       "updatedAt": "2025-08-15T18:22:09.000000Z"
     *     },
     *     "relationships": {
     *       "country": {
     *         "data": {
     *           "type": "country",
     *           "id": "cdcd6ddb-7bd2-4775-a1c4-0d565510c79f"
     *         },
     *         "links": {
     *           "self": "http://localhost/api/v1/countries/cdcd6ddb-7bd2-4775-a1c4-0d565510c79f"
     *         }
     *       },
     *       "paymentAccounts": {
     *         "data": [],
     *         "links": {
     *           "related": "#"
     *         }
     *       }
     *     },
     *     "includes": {
     *       "country": {
     *         "type": "country",
     *         "id": "cdcd6ddb-7bd2-4775-a1c4-0d565510c79f",
     *         "attributes": {
     *           "name": "Ghana",
     *           "code": "GH",
     *           "dialingCode": null,
     *           "currencyCode": "GHS",
     *           "currencySymbol": "GH₵",
     *           "currencyName": "Ghana Cedi",
     *           "flag": null,
     *           "isActive": true,
     *           "createdAt": "2025-08-15T18:22:09.000000Z",
     *           "updatedAt": "2025-08-15T18:22:09.000000Z"
     *         },
     *         "links": {
     *           "self": "http://localhost/api/v1/countries/cdcd6ddb-7bd2-4775-a1c4-0d565510c79f"
     *         }
     *       },
     *       "paymentAccounts": []
     *     },
     *     "links": {
     *       "self": "#"
     *     }
     *   },
     *   "message": "User profile retrieved successfully",
     *   "status": 200
     * }
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['paymentAccounts', 'activeDeviceSessions', 'country']);
        $resource = new UserResource($user);
        return $this->success(
            'User profile retrieved successfully',
            $resource->toArray(request())
        );
    }
}
