<?php

namespace Tests\Unit\Services\Payment;

use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Services\Payment\DummyPaymentProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DummyPaymentProviderTest extends TestCase
{
    use RefreshDatabase;

    private DummyPaymentProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('payment.service_fee_rate', 0.01); // 1%
        
        $this->provider = new DummyPaymentProvider([
            'name' => 'Dummy Provider',
            'enabled' => true,
        ]);
    }

    /** @test */
    public function it_has_correct_provider_name()
    {
        $this->assertEquals('dummy', $this->provider->getName());
    }

    /** @test */
    public function it_returns_supported_payment_methods()
    {
        $methods = $this->provider->getSupportedPaymentMethods();
        
        $this->assertContains('card', $methods);
        $this->assertContains('bank_transfer', $methods);
        $this->assertContains('mobile_money', $methods);
        $this->assertContains('ussd', $methods);
    }

    /** @test */
    public function it_returns_supported_currencies()
    {
        $currencies = $this->provider->getSupportedCurrencies();
        
        $this->assertContains('GHS', $currencies);
        $this->assertContains('NGN', $currencies);
        $this->assertContains('USD', $currencies);
        $this->assertContains('EUR', $currencies);
    }

    /** @test */
    public function it_checks_payment_method_support_correctly()
    {
        $this->assertTrue($this->provider->supportsPaymentMethod('card'));
        $this->assertTrue($this->provider->supportsPaymentMethod('MOBILE_MONEY'));
        $this->assertFalse($this->provider->supportsPaymentMethod('crypto'));
    }

    /** @test */
    public function it_checks_currency_support_correctly()
    {
        $this->assertTrue($this->provider->supportsCurrency('GHS'));
        $this->assertTrue($this->provider->supportsCurrency('usd'));
        $this->assertFalse($this->provider->supportsCurrency('BTC'));
    }

    /** @test */
    public function it_initializes_payment_successfully()
    {
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create();
        
        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'payable_type' => PaymentRequest::class,
            'payable_id' => $paymentRequest->id,
            'amount' => 100.00,
            'currency_code' => 'GHS',
        ]);

        $response = $this->provider->initializePayment($payment, [
            'callback_url' => 'https://example.com/callback'
        ]);

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('Payment initialized successfully', $response['message']);
        $this->assertArrayHasKey('reference', $response['data']);
        $this->assertArrayHasKey('authorization_url', $response['data']);
        $this->assertArrayHasKey('access_code', $response['data']);
    }

    /** @test */
    public function it_fails_initialization_for_high_amounts()
    {
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create();
        
        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'payable_type' => PaymentRequest::class,
            'payable_id' => $paymentRequest->id,
            'amount' => 2000.00, // Over the limit
            'currency_code' => 'GHS',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Amount too high for dummy provider');

        $this->provider->initializePayment($payment);
    }

    /** @test */
    public function it_verifies_successful_payment()
    {
        $reference = 'DUMMY_SUCCESS_123456';
        
        $response = $this->provider->verifyPayment($reference);
        
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('Payment verification successful', $response['message']);
        $this->assertEquals($reference, $response['data']['reference']);
        $this->assertEquals('success', $response['data']['status']);
    }

    /** @test */
    public function it_verifies_failed_payment()
    {
        $reference = 'DUMMY_FAIL_123456';
        
        $response = $this->provider->verifyPayment($reference);
        
        $this->assertEquals('failed', $response['status']);
        $this->assertEquals('Payment verification failed', $response['message']);
        $this->assertEquals('failed', $response['data']['status']);
    }

    /** @test */
    public function it_handles_webhook_data()
    {
        $payload = [
            'reference' => 'DUMMY_123456',
            'status' => 'success',
            'amount' => 100,
            'currency' => 'GHS',
            'gateway_response' => 'Successful',
            'channel' => 'card',
            'paid_at' => now()->toISOString(),
        ];

        $headers = ['X-Dummy-Signature' => 'test_signature'];

        $result = $this->provider->handleWebhook($payload, $headers);

        $this->assertEquals('DUMMY_123456', $result['reference']);
        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(100, $result['amount']);
        $this->assertEquals('GHS', $result['currency']);
    }

    /** @test */
    public function it_calculates_fees_with_service_fee()
    {
        $fees = $this->provider->calculateFees(100, 'GHS', 'mobile_money');

        $this->assertEquals(0.01, $fees['service_fee_percentage']); // 1%
        $this->assertEquals(1.0, $fees['service_fee_amount']); // 1% of 100 = 1
        $this->assertEquals(1.5, $fees['additional_fees']['dummy_provider_fee']); // 1.5% for mobile_money
        $this->assertEquals(2.5, $fees['total_fee_amount']); // 1.0 + 1.5 = 2.5
        $this->assertEquals(102.5, $fees['total_amount']); // 100 + 2.5 = 102.5
        $this->assertEquals('GHS', $fees['currency']);
    }

    /** @test */
    public function it_calculates_different_fees_for_different_payment_methods()
    {
        $cardFees = $this->provider->calculateFees(100, 'GHS', 'card');
        $bankFees = $this->provider->calculateFees(100, 'GHS', 'bank_transfer');

        // Card should have higher provider fees (2.5%) than bank transfer (0.5%)
        $this->assertEquals(2.5, $cardFees['additional_fees']['dummy_provider_fee']);
        $this->assertEquals(0.5, $bankFees['additional_fees']['dummy_provider_fee']);
        
        // Both should have same service fee (1% of 100 = 1.0)
        $this->assertEquals(1.0, $cardFees['service_fee_amount']);
        $this->assertEquals(1.0, $bankFees['service_fee_amount']);
    }

    /** @test */
    public function it_respects_maximum_provider_fees()
    {
        // Test with high amount to trigger fee cap
        $fees = $this->provider->calculateFees(10000, 'GHS', 'card');

        // Provider fee should be capped at 50 GHS for Ghana
        $this->assertEquals(50, $fees['additional_fees']['dummy_provider_fee']);
        
        // Service fee should still be calculated normally (1% of 10000 = 100)
        $this->assertEquals(100, $fees['service_fee_amount']);
    }
}
