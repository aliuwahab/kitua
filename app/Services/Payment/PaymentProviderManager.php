<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Manager;

class PaymentProviderManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('payment.default_provider', 'dummy');
    }

    /**
     * Create a dummy driver instance for testing.
     */
    public function createDummyDriver(): PaymentProviderInterface
    {
        return new DummyPaymentProvider(
            $this->config->get('payment.providers.dummy', [])
        );
    }

    /**
     * Create a Paystack driver instance.
     */
    public function createPaystackDriver(): PaymentProviderInterface
    {
        return App::make(PaystackPaymentProvider::class, [
            'config' => $this->config->get('payment.providers.paystack', [])
        ]);
    }

    /**
     * Create a Flutterwave driver instance.
     */
    public function createFlutterwaveDriver(): PaymentProviderInterface
    {
        return App::make(FlutterwavePaymentProvider::class, [
            'config' => $this->config->get('payment.providers.flutterwave', [])
        ]);
    }

    /**
     * Create a MTN MoMo driver instance.
     */
    public function createMtnMomoDriver(): PaymentProviderInterface
    {
        return new MtnMoMoPaymentProvider(
            $this->config->get('payment.providers.mtn_momo', [])
        );
    }

    /**
     * Get the best provider for a payment based on various criteria.
     */
    public function getBestProviderForPayment(Payment $payment): PaymentProviderInterface
    {
        // Get the user's preferred provider if they have one
        $user = $payment->user;
        if ($user && $user->preferred_payment_provider && $this->isProviderAvailable($user->preferred_payment_provider)) {
            return $this->driver($user->preferred_payment_provider);
        }

        // Choose provider based on payment method
        if ($payment->payment_method) {
            $provider = $this->getProviderForPaymentMethod($payment->payment_method, $payment->currency_code);
            if ($provider) {
                return $provider;
            }
        }

        // Choose provider based on currency
        $provider = $this->getProviderForCurrency($payment->currency_code);
        if ($provider) {
            return $provider;
        }

        // Choose provider based on phone number country (for mobile money)
        if ($payment->phone_number) {
            $provider = $this->getProviderForPhoneNumber($payment->phone_number);
            if ($provider) {
                return $provider;
            }
        }

        // Fallback to default provider
        return $this->driver();
    }

    /**
     * Get a provider that supports a specific payment method and currency.
     */
    public function getProviderForPaymentMethod(string $paymentMethod, string $currency): ?PaymentProviderInterface
    {
        $availableProviders = $this->getAvailableProviders();

        foreach ($availableProviders as $providerName) {
            try {
                $provider = $this->driver($providerName);
                if ($provider->supportsPaymentMethod($paymentMethod) && $provider->supportsCurrency($currency)) {
                    return $provider;
                }
            } catch (\Exception $e) {
                // Continue to next provider if this one fails to load
                continue;
            }
        }

        return null;
    }

    /**
     * Get a provider that supports a specific currency.
     */
    public function getProviderForCurrency(string $currency): ?PaymentProviderInterface
    {
        $availableProviders = $this->getAvailableProviders();

        foreach ($availableProviders as $providerName) {
            try {
                $provider = $this->driver($providerName);
                if ($provider->supportsCurrency($currency)) {
                    return $provider;
                }
            } catch (\Exception $e) {
                // Continue to next provider if this one fails to load
                continue;
            }
        }

        return null;
    }

    /**
     * Get a provider based on phone number (for mobile money).
     */
    public function getProviderForPhoneNumber(string $phoneNumber): ?PaymentProviderInterface
    {
        $country = $this->getCountryFromPhoneNumber($phoneNumber);
        
        $countryProviderMap = [
            'GH' => 'paystack',  // Ghana
            'NG' => 'paystack',  // Nigeria
            'KE' => 'flutterwave', // Kenya
            'UG' => 'flutterwave', // Uganda
            'TZ' => 'flutterwave', // Tanzania
            'RW' => 'flutterwave', // Rwanda
        ];

        $preferredProvider = $countryProviderMap[$country] ?? null;

        if ($preferredProvider && $this->isProviderAvailable($preferredProvider)) {
            try {
                return $this->driver($preferredProvider);
            } catch (\Exception $e) {
                // Fall through to default logic
            }
        }

        return null;
    }

    /**
     * Get available providers from configuration.
     */
    public function getAvailableProviders(): array
    {
        return array_keys($this->config->get('payment.providers', []));
    }

    /**
     * Check if a provider is available and configured.
     */
    public function isProviderAvailable(string $provider): bool
    {
        return in_array($provider, $this->getAvailableProviders()) && 
               $this->config->get("payment.providers.{$provider}.enabled", false);
    }

    /**
     * Get supported payment methods across all providers.
     */
    public function getAllSupportedPaymentMethods(): array
    {
        $methods = [];
        
        foreach ($this->getAvailableProviders() as $providerName) {
            try {
                $provider = $this->driver($providerName);
                $methods = array_merge($methods, $provider->getSupportedPaymentMethods());
            } catch (\Exception $e) {
                // Continue if provider fails to load
                continue;
            }
        }

        return array_unique($methods);
    }

    /**
     * Get supported currencies across all providers.
     */
    public function getAllSupportedCurrencies(): array
    {
        $currencies = [];
        
        foreach ($this->getAvailableProviders() as $providerName) {
            try {
                $provider = $this->driver($providerName);
                $currencies = array_merge($currencies, $provider->getSupportedCurrencies());
            } catch (\Exception $e) {
                // Continue if provider fails to load
                continue;
            }
        }

        return array_unique($currencies);
    }

    /**
     * Simple country detection from phone number.
     * This is a basic implementation - consider using a proper phone number parsing library.
     */
    private function getCountryFromPhoneNumber(string $phoneNumber): ?string
    {
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

        $countryPrefixes = [
            '+233' => 'GH', // Ghana
            '+234' => 'NG', // Nigeria  
            '+254' => 'KE', // Kenya
            '+256' => 'UG', // Uganda
            '+255' => 'TZ', // Tanzania
            '+250' => 'RW', // Rwanda
            '+27' => 'ZA',  // South Africa
        ];

        foreach ($countryPrefixes as $prefix => $country) {
            if (str_starts_with($phoneNumber, $prefix)) {
                return $country;
            }
        }

        return null;
    }

    /**
     * Set a user's preferred payment provider.
     */
    public function setUserPreferredProvider(User $user, string $provider): void
    {
        if ($this->isProviderAvailable($provider)) {
            $user->update(['preferred_payment_provider' => $provider]);
        }
    }
}
