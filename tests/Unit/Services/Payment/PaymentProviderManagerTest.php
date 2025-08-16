<?php

namespace Tests\Unit\Services\Payment;

use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Services\Payment\DummyPaymentProvider;
use App\Services\Payment\MtnMoMoPaymentProvider;
use App\Services\Payment\PaymentProviderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PaymentProviderManagerTest extends TestCase
{
    use RefreshDatabase;

    private PaymentProviderManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configure payment providers
        Config::set('payment.providers', [
            'dummy' => [
                'enabled' => true,
                'name' => 'Dummy Provider',
            ],
            'mtn_momo' => [
                'enabled' => true,
                'name' => 'MTN MoMo',
                'api_user' => 'test_user',
                'api_key' => 'test_key',
                'subscription_key' => 'test_subscription',
            ],
        ]);
        
        $this->manager = new PaymentProviderManager($this->app);
    }

    /** @test */
    public function it_creates_dummy_provider_instance()
    {
        $provider = $this->manager->driver('dummy');
        
        $this->assertInstanceOf(DummyPaymentProvider::class, $provider);
        $this->assertEquals('dummy', $provider->getName());
    }

    /** @test */
    public function it_creates_mtn_momo_provider_instance()
    {
        $provider = $this->manager->driver('mtn_momo');
        
        $this->assertInstanceOf(MtnMoMoPaymentProvider::class, $provider);
        $this->assertEquals('mtn_momo', $provider->getName());
    }

    /** @test */
    public function it_returns_default_provider_when_no_driver_specified()
    {
        Config::set('payment.default_provider', 'dummy');
        
        $provider = $this->manager->driver();
        
        $this->assertInstanceOf(DummyPaymentProvider::class, $provider);
    }

    /** @test */
    public function it_returns_available_providers()
    {
        $providers = $this->manager->getAvailableProviders();
        
        $this->assertContains('dummy', $providers);
        $this->assertContains('mtn_momo', $providers);
    }

    /** @test */
    public function it_checks_if_provider_is_available()
    {
        $this->assertTrue($this->manager->isProviderAvailable('dummy'));
        $this->assertTrue($this->manager->isProviderAvailable('mtn_momo'));
        $this->assertFalse($this->manager->isProviderAvailable('nonexistent'));
    }

    /** @test */
    public function it_gets_all_supported_payment_methods()
    {
        $methods = $this->manager->getAllSupportedPaymentMethods();
        
        $this->assertContains('mobile_money', $methods);
        $this->assertContains('card', $methods);
        $this->assertContains('bank_transfer', $methods);
    }

    /** @test */
    public function it_gets_all_supported_currencies()
    {
        $currencies = $this->manager->getAllSupportedCurrencies();
        
        $this->assertContains('GHS', $currencies);
        $this->assertContains('UGX', $currencies);
        $this->assertContains('USD', $currencies);
    }
}
