<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PaymentSettlementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('payment.providers.dummy.enabled', true);
        Config::set('payment.default_provider', 'dummy');
    }

    /** @test */
    public function it_requires_authentication_to_settle_payment()
    {
        $paymentRequest = PaymentRequest::factory()->create();

        $response = $this->postJson("/api/v1/payment-requests/{$paymentRequest->id}/settle", [
            'payment_method' => 'mobile_money',
            'phone_number' => '+233244123456',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_settles_payment_request_successfully()
    {
        $requester = User::factory()->create();
        $payer = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $requester->id,
            'amount' => 100.00,
            'currency_code' => 'GHS',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($payer)
            ->postJson("/api/v1/payment-requests/{$paymentRequest->id}/settle", [
                'payment_method' => 'mobile_money',
                'phone_number' => '+233244123456',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'amount',
                        'currencyCode',
                        'status',
                        'paymentMethod',
                        'phoneNumber',
                        'provider',
                    ],
                ],
            ]);

        // Verify payment was created in database
        $this->assertDatabaseHas('payments', [
            'user_id' => $payer->id,
            'payable_type' => PaymentRequest::class,
            'payable_id' => $paymentRequest->id,
            'payment_method' => 'mobile_money',
            'phone_number' => '+233244123456',
            'provider' => 'dummy',
        ]);
    }

    /** @test */
    public function it_validates_settlement_request_data()
    {
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/v1/payment-requests/{$paymentRequest->id}/settle", [
                // Missing required fields
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'payment_method',
            ]);
    }

    /** @test */
    public function it_requires_phone_number_for_mobile_money_payments()
    {
        $requester = User::factory()->create();
        $payer = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $requester->id,
        ]);

        $response = $this->actingAs($payer)
            ->postJson("/api/v1/payment-requests/{$paymentRequest->id}/settle", [
                'payment_method' => 'mobile_money',
                // Missing phone_number
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'phone_number',
            ]);
    }

    /** @test */
    public function it_prevents_user_from_settling_own_payment_request()
    {
        $user = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/payment-requests/{$paymentRequest->id}/settle", [
                'payment_method' => 'mobile_money',
                'phone_number' => '+233244123456',
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_allows_custom_amount_for_negotiable_requests()
    {
        $requester = User::factory()->create();
        $payer = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $requester->id,
            'amount' => 100.00,
            'is_negotiable' => true,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($payer)
            ->postJson("/api/v1/payment-requests/{$paymentRequest->id}/settle", [
                'payment_method' => 'mobile_money',
                'phone_number' => '+233244123456',
                'amount' => 80.00, // Different amount
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'user_id' => $payer->id,
            'payable_id' => $paymentRequest->id,
            'amount' => 80.00,
        ]);
    }
}
