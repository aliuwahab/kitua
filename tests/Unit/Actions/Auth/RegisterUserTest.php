<?php

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\RegisterUser;
use App\Events\Auth\UserRegistered;
use App\Events\Auth\PaymentAccountCreated;
use App\Events\Auth\DeviceRegistered;
use App\Models\User;
use App\Models\PaymentAccount;
use App\Models\DeviceSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    protected RegisterUser $registerUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registerUser = new RegisterUser();
    }

    /** @test */
    public function it_creates_new_user_and_sends_pin_during_initiation()
    {
        Event::fake();

        $userData = [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe',
            'other_names' => 'Michael',
            'provider' => 'MTN'
        ];

        $deviceInfo = [
            'device_id' => 'test-device-123',
            'device_type' => 'android',
            'app_version' => '1.0.0'
        ];

        $result = $this->registerUser->initiate($userData, $deviceInfo);

        // Assert response structure
        $this->assertFalse($result['user_exists']);
        $this->assertEquals('233244123456', $result['mobile_number']);
        $this->assertStringContainsString('Registration PIN sent', $result['message']);
        $this->assertArrayHasKey('pin', $result);
        $this->assertMatchesRegularExpression('/^\d{6}$/', $result['pin']);

        // Assert user was created but inactive
        $this->assertDatabaseHas('users', [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe',
            'other_names' => 'Michael',
            'user_type' => 'customer',
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

        // Verify PIN is hashed
        $user = User::where('mobile_number', '233244123456')->first();
        $this->assertTrue(Hash::check($result['pin'], $user->pin));
    }

    /** @test */
    public function it_handles_existing_user_during_initiation()
    {
        // Create existing user (unverified so we can test the verification reset)
        $user = User::factory()->unverified()->create([
            'mobile_number' => '233244123456',
            'first_name' => 'Jane',
            'is_active' => true
        ]);

        $userData = [
            'mobile_number' => '233244123456',
            'first_name' => 'John', // Different name, should be ignored for existing user
            'surname' => 'Doe',
        ];

        $deviceInfo = ['device_id' => 'test-device-123'];

        $result = $this->registerUser->initiate($userData, $deviceInfo);

        // Assert response for existing user
        $this->assertTrue($result['user_exists']);
        $this->assertEquals('233244123456', $result['mobile_number']);
        $this->assertStringContainsString('Login PIN sent', $result['message']);
        $this->assertArrayHasKey('pin', $result);

        // User details should not be updated during initiation
        $user->refresh();
        $this->assertEquals('Jane', $user->first_name); // Original name preserved
        $this->assertNull($user->mobile_verified_at); // Reset verification

        // Verify new PIN was set
        $this->assertTrue(Hash::check($result['pin'], $user->pin));
    }

    /** @test */
    public function it_completes_registration_for_new_user()
    {
        Event::fake();

        // First, initiate registration
        $userData = [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe'
        ];
        
        $deviceInfo = [
            'device_id' => 'test-device-123',
            'device_type' => 'android'
        ];

        $initiateResult = $this->registerUser->initiate($userData, $deviceInfo);
        $pin = $initiateResult['pin'];

        // Now complete the registration
        $result = $this->registerUser->complete('233244123456', $pin, $deviceInfo);

        // Assert response structure
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('is_new_user', $result);
        $this->assertArrayHasKey('is_new_device', $result);

        // Assert user is now active and verified
        $this->assertDatabaseHas('users', [
            'mobile_number' => '233244123456',
            'is_active' => true
        ]);

        $user = User::where('mobile_number', '233244123456')->first();
        $this->assertNotNull($user->mobile_verified_at);

        // Assert device session was created
        $this->assertDatabaseHas('device_sessions', [
            'user_id' => $user->id,
            'device_id' => 'test-device-123',
            'is_active' => true
        ]);

        // Assert events were dispatched
        Event::assertDispatched(UserRegistered::class);
        Event::assertDispatched(PaymentAccountCreated::class);
        Event::assertDispatched(DeviceRegistered::class);

        // Assert token was created
        $this->assertNotEmpty($result['token']);
        $this->assertCount(1, $user->tokens);
    }

    /** @test */
    public function it_completes_login_for_existing_user()
    {
        Event::fake();

        // Create existing user with payment account
        $user = User::factory()->create([
            'mobile_number' => '233244123456',
            'is_active' => true
        ]);
        
        PaymentAccount::factory()->create([
            'user_id' => $user->id,
            'account_number' => '233244123456'
        ]);

        // Initiate login
        $userData = ['mobile_number' => '233244123456'];
        $deviceInfo = ['device_id' => 'test-device-123', 'device_type' => 'android'];
        
        $initiateResult = $this->registerUser->initiate($userData, $deviceInfo);
        $pin = $initiateResult['pin'];

        // Complete login
        $result = $this->registerUser->complete('233244123456', $pin, $deviceInfo);

        // Assert it's not a new user
        $this->assertFalse($result['is_new_user']);
        $this->assertTrue($result['is_new_device']);

        // Assert user is verified
        $user->refresh();
        $this->assertNotNull($user->mobile_verified_at);
        $this->assertTrue($user->is_active);

        // UserRegistered should NOT be dispatched for existing users
        Event::assertNotDispatched(UserRegistered::class);
        Event::assertDispatched(DeviceRegistered::class);
    }

    /** @test */
    public function it_fails_completion_with_invalid_pin()
    {
        // Create user
        $user = User::factory()->create([
            'mobile_number' => '233244123456',
            'pin' => Hash::make('123456')
        ]);

        $deviceInfo = ['device_id' => 'test-device-123'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid PIN or mobile number');

        $this->registerUser->complete('233244123456', '654321', $deviceInfo);
    }

    /** @test */
    public function it_fails_completion_with_nonexistent_user()
    {
        $deviceInfo = ['device_id' => 'test-device-123'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid PIN or mobile number');

        $this->registerUser->complete('233244999999', '123456', $deviceInfo);
    }

    /** @test */
    public function it_revokes_existing_tokens_on_completion()
    {
        // Create user with existing token
        $user = User::factory()->create([
            'mobile_number' => '233244123456',
            'pin' => Hash::make('123456'),
            'is_active' => true
        ]);

        // Create existing tokens
        $user->createToken('old_token_1');
        $user->createToken('old_token_2');
        $this->assertCount(2, $user->tokens);

        $deviceInfo = ['device_id' => 'test-device-123', 'device_type' => 'android'];

        // Complete login
        $result = $this->registerUser->complete('233244123456', '123456', $deviceInfo);

        // Should have only 1 token (the new one)
        $user->refresh();
        $this->assertCount(1, $user->tokens);
        $this->assertNotEmpty($result['token']);
    }

    /** @test */
    public function it_creates_device_session_with_correct_data()
    {
        // Mock request data
        $this->app['request']->merge([]);
        $this->app['request']->server->set('REMOTE_ADDR', '192.168.1.1');
        $this->app['request']->server->set('HTTP_USER_AGENT', 'Test Agent');

        $userData = [
            'mobile_number' => '233244123456',
            'first_name' => 'John',
            'surname' => 'Doe'
        ];
        
        $deviceInfo = [
            'device_id' => 'test-device-123',
            'device_name' => 'John\'s Phone',
            'device_type' => 'android',
            'app_version' => '1.0.0',
            'os_version' => 'Android 12',
            'device_model' => 'Samsung Galaxy S21',
            'push_token' => 'firebase-token-123'
        ];

        // Initiate and complete registration
        $initiateResult = $this->registerUser->initiate($userData, $deviceInfo);
        $result = $this->registerUser->complete('233244123456', $initiateResult['pin'], $deviceInfo);

        // Assert device session was created with correct data (excluding device_model since it's stored in metadata)
        $this->assertDatabaseHas('device_sessions', [
            'device_id' => 'test-device-123',
            'device_name' => 'John\'s Phone',
            'device_type' => 'android',
            'app_version' => '1.0.0',
            'os_version' => 'Android 12',
            'push_token' => 'firebase-token-123',
            'ip_address' => '192.168.1.1',
            'is_active' => true
        ]);

        // Check that device model is stored in metadata
        $deviceSession = DeviceSession::where('device_id', 'test-device-123')->first();
        $this->assertEquals('Samsung Galaxy S21', $deviceSession->metadata['device_model'] ?? null);
    }
}
