<?php

namespace Tests\Unit\Actions\Payment;

use App\Actions\Payment\SettlePayment;
use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Services\Payment\DummyPaymentProvider;
use App\Services\Payment\PaymentProviderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class SettlePaymentTest extends TestCase
{
    use RefreshDatabase;

    private SettlePayment $settlePayment;
    private PaymentProviderManager $mockManager;
    private DummyPaymentProvider $mockProvider;

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('payment.service_fee_rate', 0.01);
        
        $this->mockProvider = Mockery::mock(DummyPaymentProvider::class)->shouldAllowMockingProtectedMethods();
        $this->mockManager = Mockery::mock(PaymentProviderManager::class);
        
        // Mock the validation methods to avoid transaction conflicts
        $this->mockManager->shouldReceive('getAllSupportedPaymentMethods')
            ->andReturn(['mobile_money', 'card', 'bank_transfer', 'ussd']);
        $this->mockManager->shouldReceive('getAllSupportedCurrencies')
            ->andReturn(['GHS', 'NGN', 'KES', 'UGX', 'TZS', 'USD', 'EUR']);
        
        $this->settlePayment = new SettlePayment($this->mockManager);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
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

        $paymentData = [
            'payment_method' => 'mobile_money',
            'phone_number' => '+233244123456',
        ];

        // Mock provider responses
        $this->mockProvider->shouldReceive('getName')->andReturn('dummy');
        $this->mockProvider->shouldReceive('initializePayment')
            ->once()
            ->andReturn([
                'status' => 'success',
                'message' => 'Payment initialized',
                'data' => [
                    'reference' => 'DUMMY_123456',
                    'authorization_url' => 'https://dummy.test/pay',
                ]
            ]);

        $this->mockManager->shouldReceive('getBestProviderForPayment')
            ->once()
            ->andReturn($this->mockProvider);

        $payment = $this->settlePayment->execute($paymentRequest, $payer, $paymentData);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($payer->id, $payment->user_id);
        $this->assertEquals($paymentRequest->id, $payment->payable_id);
        $this->assertEquals('mobile_money', $payment->payment_method);
        $this->assertEquals('+233244123456', $payment->phone_number);
        $this->assertEquals('dummy', $payment->provider);
    }

    /** @test */
    public function it_prevents_user_from_paying_own_request()
    {
        $user = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $paymentData = [
            'payment_method' => 'mobile_money',
            'phone_number' => '+233244123456',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('You cannot pay your own payment request.');

        $this->settlePayment->execute($paymentRequest, $user, $paymentData);
    }

    /** @test */
    public function it_prevents_payment_of_non_pending_requests()
    {
        $requester = User::factory()->create();
        $payer = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $requester->id,
            'status' => 'paid', // Already paid
        ]);

        $paymentData = [
            'payment_method' => 'mobile_money',
            'phone_number' => '+233244123456',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Payment request is no longer available for payment.');

        $this->settlePayment->execute($paymentRequest, $payer, $paymentData);
    }

    /** @test */
    public function it_prevents_payment_of_expired_requests()
    {
        $requester = User::factory()->create();
        $payer = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $requester->id,
            'status' => 'pending',
            'expires_at' => now()->subHour(), // Expired
        ]);

        $paymentData = [
            'payment_method' => 'mobile_money',
            'phone_number' => '+233244123456',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Payment request has expired.');

        $this->settlePayment->execute($paymentRequest, $payer, $paymentData);
    }

    /** @test */
    public function it_validates_amount_for_negotiable_requests()
    {
        $requester = User::factory()->create();
        $payer = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $requester->id,
            'amount' => 100.00,
            'is_negotiable' => true,
            'status' => 'pending',
        ]);

        $paymentData = [
            'payment_method' => 'mobile_money',
            'phone_number' => '+233244123456',
            'amount' => 0, // Invalid amount
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Payment amount must be greater than zero.');

        $this->settlePayment->execute($paymentRequest, $payer, $paymentData);
    }

    /** @test */
    public function it_validates_amount_for_non_negotiable_requests()
    {
        $requester = User::factory()->create();
        $payer = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $requester->id,
            'amount' => 100.00,
            'is_negotiable' => false,
            'status' => 'pending',
        ]);

        $paymentData = [
            'payment_method' => 'mobile_money',
            'phone_number' => '+233244123456',
            'amount' => 150.00, // Different from request amount
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Payment amount must match the requested amount for non-negotiable requests.');

        $this->settlePayment->execute($paymentRequest, $payer, $paymentData);
    }

    /** @test */
    public function it_requires_phone_number_for_mobile_money_payments()
    {
        $requester = User::factory()->create();
        $payer = User::factory()->create();
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $requester->id,
            'status' => 'pending',
        ]);

        $paymentData = [
            'payment_method' => 'mobile_money',
            // Missing phone_number
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Phone number is required for mobile money payments.');

        $this->settlePayment->execute($paymentRequest, $payer, $paymentData);
    }

    /** @test */
    public function it_verifies_payment_successfully()
    {
        $payment = Payment::factory()->create([
            'provider' => 'dummy',
            'provider_reference' => 'DUMMY_123456',
            'status' => 'processing',
        ]);

        $this->mockProvider->shouldReceive('verifyPayment')
            ->with('DUMMY_123456')
            ->once()
            ->andReturn([
                'status' => 'success',
                'data' => [
                    'status' => 'success',
                    'gateway_response' => 'Payment completed',
                ]
            ]);

        $this->mockProvider->shouldReceive('normalizePaymentStatus')
            ->with('success')
            ->once()
            ->andReturn('completed');

        $this->mockManager->shouldReceive('driver')
            ->with('dummy')
            ->once()
            ->andReturn($this->mockProvider);

        $result = $this->settlePayment->verifyPayment($payment);

        $this->assertEquals('completed', $result->status);
        $this->assertNotNull($result->completed_at);
    }

    /** @test */
    public function it_processes_webhook_callback_successfully()
    {
        $payment = Payment::factory()->create([
            'provider' => 'dummy',
            'provider_reference' => 'DUMMY_123456',
            'status' => 'processing',
        ]);

        $webhookPayload = [
            'reference' => 'DUMMY_123456',
            'status' => 'success',
            'amount' => 100,
        ];

        $this->mockProvider->shouldReceive('handleWebhook')
            ->with($webhookPayload, [])
            ->once()
            ->andReturn([
                'reference' => 'DUMMY_123456',
                'status' => 'completed',
                'raw_data' => $webhookPayload,
            ]);

        $this->mockManager->shouldReceive('driver')
            ->with('dummy')
            ->once()
            ->andReturn($this->mockProvider);

        $result = $this->settlePayment->processWebhookCallback('dummy', $webhookPayload);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals('completed', $result->status);
    }
}
