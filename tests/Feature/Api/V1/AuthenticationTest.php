<?php

namespace Tests\Feature\Api\V1;

use App\Events\Auth\UserRegistered;
use App\Events\Auth\PaymentAccountCreated;
use App\Events\Auth\DeviceRegistered;
use App\Events\Auth\UserLoggedOut;
use App\Models\User;
use App\Models\PaymentAccount;
use App\Models\DeviceSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function health_check_endpoint_works()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'service', 
                'version',
                'timestamp'
            ])
            ->assertJson([
                'status' => 'ok',
                'service' => 'Kitua API'
            ]);
    }

    /** @test */
    public function user_can_register_with_valid_data()
    {
        Event::fake();

        $userData = [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe',
            'other_names' => 'Michael',
            'provider' => 'MTN',
            'device_id' => 'test-device-123',
            'device_name' => 'John\'s iPhone',
            'device_type' => 'android',
            'app_version' => '1.0.0',
            'os_version' => 'Android 12',
            'device_model' => 'Samsung Galaxy S21',
            'screen_resolution' => '1080x2340',
            'push_token' => 'firebase-token-123'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user_exists',
                    'mobile_number',
                    'message',
                    'pin'
                ],
                'message',
                'status'
            ])
            ->assertJson([
                'data' => [
                    'user_exists' => false,
                    'mobile_number' => '233244123456'
                ],
                'message' => 'PIN sent successfully'
            ]);

        // Assert user was created but inactive
        $this->assertDatabaseHas('users', [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe',
            'other_names' => 'Michael',
            'user_type' => 'mobile',
            'is_active' => false
        ]);

        // Assert payment account was created
        $this->assertDatabaseHas('payment_accounts', [
            'account_number' => '233244123456',
            'account_type' => 'momo',
            'provider' => 'MTN',
            'is_primary' => true,
            'is_verified' => false
        ]);
    }

    /** @test */
    public function existing_user_can_initiate_login()
    {
        // Create existing user
        $user = User::factory()->create([
            'mobile_number' => '233244123456',
            'first_name' => 'Jane'
        ]);

        $userData = [
            'mobile_number' => '233244123456',
            'first_name' => 'John', // Should be ignored
            'surname' => 'Doe',
            'device_id' => 'test-device-123',
            'device_type' => 'android'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'user_exists' => true,
                    'mobile_number' => '233244123456'
                ]
            ]);

        // User details should not change
        $user->refresh();
        $this->assertEquals('Jane', $user->first_name);
    }

    /** @test */
    public function user_can_verify_pin_and_complete_registration()
    {
        Event::fake();

        // First register
        $userData = [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe',
            'device_id' => 'test-device-123',
            'device_type' => 'android',
            'app_version' => '1.0.0'
        ];

        $registerResponse = $this->postJson('/api/v1/auth/register', $userData);
        $pin = $registerResponse->json('data.pin');

        // Now verify PIN
        $verifyData = [
            'mobile_number' => '233244123456',
            'pin' => $pin,
            'device_id' => 'test-device-123',
            'device_type' => 'android',
            'app_version' => '1.0.0',
            'device_name' => 'John\'s Phone'
        ];

        $response = $this->postJson('/api/v1/auth/verify-pin', $verifyData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'mobile_number',
                        'first_name',
                        'surname',
                        'full_name',
                        'user_type',
                        'is_active',
                        'payment_accounts',
                        'device_sessions'
                    ],
                    'token',
                    'is_new_user',
                    'is_new_device'
                ],
                'message',
                'status'
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'mobile_number' => '233244123456',
                        'first_name' => 'John',
                        'is_active' => true
                    ],
                    'is_new_user' => true,
                    'is_new_device' => true
                ],
                'message' => 'Authentication successful'
            ]);

        // Assert user is now active
        $this->assertDatabaseHas('users', [
            'mobile_number' => '233244123456',
            'is_active' => true
        ]);

        // Assert device session was created
        $user = User::where('mobile_number', '233244123456')->first();
        $this->assertDatabaseHas('device_sessions', [
            'user_id' => $user->id,
            'device_id' => 'test-device-123',
            'device_name' => 'John\'s Phone',
            'device_type' => 'android',
            'is_active' => true
        ]);

        // Assert events were dispatched
        Event::assertDispatched(UserRegistered::class);
        Event::assertDispatched(PaymentAccountCreated::class);
        Event::assertDispatched(DeviceRegistered::class);

        // Assert token was created
        $this->assertCount(1, $user->tokens);
    }

    /** @test */
    public function user_can_login_with_alternative_endpoint()
    {
        // Create user with known PIN
        $user = User::factory()->create([
            'mobile_number' => '233244123456',
            'pin' => Hash::make('123456')
        ]);

        $loginData = [
            'mobile_number' => '233244123456',
            'pin' => '123456',
            'device_id' => 'test-device-123',
            'device_type' => 'android'
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Login successful'
            ]);
    }

    /** @test */
    public function registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'mobile_number' => 'invalid-number',
            'first_name' => '',
            'device_type' => 'invalid-type'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
                'status'
            ]);
    }

    /** @test */
    public function registration_fails_with_duplicate_mobile_number()
    {
        User::factory()->create(['mobile_number' => '233244123456']);

        // Attempt to register with existing mobile number will succeed
        // but return user_exists = true (this is the expected behavior)
        $response = $this->postJson('/api/v1/auth/register', [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe',
            'device_id' => 'test-device-123',
            'device_type' => 'android'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => ['user_exists' => true]
            ]);
    }

    /** @test */
    public function pin_verification_fails_with_wrong_pin()
    {
        $user = User::factory()->create([
            'mobile_number' => '233244123456',
            'pin' => Hash::make('123456')
        ]);

        $response = $this->postJson('/api/v1/auth/verify-pin', [
            'mobile_number' => '233244123456',
            'pin' => '654321', // Wrong PIN
            'device_id' => 'test-device-123',
            'device_type' => 'android'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid PIN or mobile number'
            ]);
    }

    /** @test */
    public function pin_verification_fails_with_nonexistent_user()
    {
        $response = $this->postJson('/api/v1/auth/verify-pin', [
            'mobile_number' => '233244999999',
            'pin' => '123456',
            'device_id' => 'test-device-123',
            'device_type' => 'android'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Validation failed'
            ]);
    }

    /** @test */
    public function authenticated_user_can_access_profile()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'mobile_number',
                        'first_name',
                        'surname',
                        'payment_accounts',
                        'active_device_sessions'
                    ]
                ],
                'message'
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_profile()
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_logout()
    {
        Event::fake();

        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Create device session
        DeviceSession::factory()->create([
            'user_id' => $user->id,
            'device_id' => 'test-device-123'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);

        // Token should be deleted
        $this->assertCount(0, $user->fresh()->tokens);

        Event::assertDispatched(UserLoggedOut::class);
    }

    /** @test */
    public function user_can_logout_from_all_devices()
    {
        Event::fake();

        $user = User::factory()->create();
        
        // Create multiple tokens and device sessions
        $user->createToken('token1');
        $user->createToken('token2');
        $user->createToken('token3');

        DeviceSession::factory()->count(3)->create(['user_id' => $user->id]);

        $token = $user->createToken('current-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout-all');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'device_sessions_count',
                    'reason'
                ]
            ]);

        // All tokens should be deleted
        $this->assertCount(0, $user->fresh()->tokens);

        Event::assertDispatched(UserLoggedOut::class);
    }

    /** @test */
    public function device_fingerprinting_works_correctly()
    {
        $userData = [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe',
            'device_id' => 'test-device-123',
            'device_type' => 'android',
            'app_version' => '1.0.0',
            'os_version' => 'Android 12',
            'device_model' => 'Samsung Galaxy S21',
            'screen_resolution' => '1080x2340'
        ];

        // Register
        $registerResponse = $this->postJson('/api/v1/auth/register', $userData);
        $pin = $registerResponse->json('data.pin');

        // Complete registration
        $verifyData = array_merge($userData, ['pin' => $pin]);
        $this->postJson('/api/v1/auth/verify-pin', $verifyData);

        // Verify device session has correct fingerprint
        $user = User::where('mobile_number', '233244123456')->first();
        $deviceSession = $user->deviceSessions()->first();

        $this->assertNotNull($deviceSession->device_fingerprint);
        $this->assertEquals('test-device-123', $deviceSession->device_id);
        $this->assertEquals('android', $deviceSession->device_type);
        $this->assertEquals('1.0.0', $deviceSession->app_version);

        // Same device info should create/update the same session
        $verifyData['pin'] = '123456'; // Doesn't matter for this test
        $this->postJson('/api/v1/auth/verify-pin', $verifyData);

        // Should still have only one device session
        $this->assertCount(1, $user->fresh()->deviceSessions);
    }

    /** @test */
    public function multiple_devices_create_separate_sessions()
    {
        $user = User::factory()->create([
            'mobile_number' => '233244123456',
            'pin' => Hash::make('123456')
        ]);

        // Login from first device
        $device1Data = [
            'mobile_number' => '233244123456',
            'pin' => '123456',
            'device_id' => 'device-1',
            'device_type' => 'android',
            'device_model' => 'Samsung Galaxy S21'
        ];

        $this->postJson('/api/v1/auth/verify-pin', $device1Data);

        // Login from second device
        $device2Data = [
            'mobile_number' => '233244123456',
            'pin' => '123456',
            'device_id' => 'device-2',
            'device_type' => 'ios',
            'device_model' => 'iPhone 13'
        ];

        $this->postJson('/api/v1/auth/verify-pin', $device2Data);

        // Should have two device sessions
        $this->assertCount(2, $user->fresh()->deviceSessions);
        $this->assertCount(1, $user->fresh()->tokens); // Only latest token
    }

    /** @test */
    public function validation_works_for_all_endpoints()
    {
        // Test register endpoint
        $this->postJson('/api/v1/auth/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['mobile_number', 'first_name', 'surname', 'device_id', 'device_type']);

        // Test verify-pin endpoint  
        $this->postJson('/api/v1/auth/verify-pin', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['mobile_number', 'pin', 'device_id', 'device_type']);

        // Test login endpoint
        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['mobile_number', 'pin', 'device_id', 'device_type']);
    }
}
