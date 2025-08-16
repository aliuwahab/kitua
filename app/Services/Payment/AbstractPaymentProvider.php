<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractPaymentProvider implements PaymentProviderInterface
{
    protected array $config;
    protected string $baseUrl;
    protected array $defaultHeaders;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->baseUrl = $config['base_url'] ?? '';
        $this->defaultHeaders = $this->getDefaultHeaders();
    }

    /**
     * Get default headers for API requests.
     */
    protected function getDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Make an HTTP request to the provider's API.
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $headers = array_merge($this->defaultHeaders, $headers);

        Log::info("Making {$method} request to {$this->getName()}", [
            'url' => $url,
            'data' => $data,
            'headers' => array_keys($headers)
        ]);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->{strtolower($method)}($url, $data);

            $responseData = $response->json();

            if (!$response->successful()) {
                Log::error("Payment provider request failed", [
                    'provider' => $this->getName(),
                    'status' => $response->status(),
                    'response' => $responseData
                ]);

                throw new \Exception(
                    "Payment provider request failed: " . ($responseData['message'] ?? 'Unknown error'),
                    $response->status()
                );
            }

            Log::info("Payment provider request successful", [
                'provider' => $this->getName(),
                'status' => $response->status()
            ]);

            return $responseData ?? [];

        } catch (\Exception $e) {
            Log::error("Payment provider request exception", [
                'provider' => $this->getName(),
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Normalize payment status from provider to our internal status.
     */
    protected function normalizePaymentStatus(string $providerStatus): string
    {
        $statusMap = $this->getStatusMap();
        return $statusMap[strtolower($providerStatus)] ?? 'failed';
    }

    /**
     * Get the mapping between provider statuses and our internal statuses.
     * Override this in concrete implementations.
     */
    protected function getStatusMap(): array
    {
        return [
            'pending' => 'pending',
            'processing' => 'processing',
            'success' => 'completed',
            'successful' => 'completed',
            'completed' => 'completed',
            'paid' => 'completed',
            'failed' => 'failed',
            'error' => 'failed',
            'cancelled' => 'cancelled',
            'canceled' => 'cancelled',
            'refunded' => 'refunded',
        ];
    }

    /**
     * Generate a unique reference for this payment.
     */
    protected function generatePaymentReference(Payment $payment): string
    {
        return strtoupper($this->getName()) . '_' . $payment->id . '_' . time();
    }

    /**
     * Validate webhook signature if supported by the provider.
     */
    protected function validateWebhookSignature(array $payload, array $headers): bool
    {
        // Override in concrete implementations if provider supports webhook signature validation
        return true;
    }

    /**
     * Check if the provider supports a specific payment method.
     */
    public function supportsPaymentMethod(string $method): bool
    {
        return in_array(strtolower($method), array_map('strtolower', $this->getSupportedPaymentMethods()));
    }

    /**
     * Check if the provider supports a specific currency.
     */
    public function supportsCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), array_map('strtoupper', $this->getSupportedCurrencies()));
    }

    /**
     * Get configuration value.
     */
    protected function getConfig(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Log payment activity.
     */
    protected function logPaymentActivity(?Payment $payment, string $activity, array $data = []): void
    {
        $logData = [
            'provider' => $this->getName(),
            'data' => $data
        ];
        
        if ($payment !== null) {
            $logData['payment_id'] = $payment->id;
            $logData['user_id'] = $payment->user_id;
            $logData['amount'] = $payment->amount;
            $logData['currency'] = $payment->currency_code;
        }
        
        Log::info("Payment activity: {$activity}", $logData);
    }

    /**
     * Get the service fee rate from configuration.
     * This is Kitua's service fee that applies across all providers.
     */
    protected function getServiceFeeRate(): float
    {
        // First check provider-specific service fee
        $providerFee = $this->getConfig('service_fee_rate');
        if ($providerFee !== null) {
            return (float) $providerFee;
        }

        // Fall back to global service fee from config
        return config('payment.service_fee_rate', 0.01); // Default 1%
    }

    /**
     * Calculate standardized service fees.
     * This method can be used by all providers to calculate service fees consistently.
     */
    protected function calculateServiceFees(float $amount, string $currency, array $additionalFees = []): array
    {
        $serviceFeeRate = $this->getServiceFeeRate();
        $serviceFee = $amount * $serviceFeeRate;
        
        $totalAdditionalFees = array_sum($additionalFees);
        $totalFees = $serviceFee + $totalAdditionalFees;

        return [
            'service_fee_percentage' => $serviceFeeRate,
            'service_fee_amount' => round($serviceFee, 2),
            'additional_fees' => $additionalFees,
            'total_additional_fees' => round($totalAdditionalFees, 2),
            'total_fee_amount' => round($totalFees, 2),
            'total_amount' => $amount + $totalFees,
            'currency' => $currency,
            'breakdown' => array_merge([
                'base_amount' => $amount,
                'service_fee' => round($serviceFee, 2),
            ], $additionalFees, [
                'total_fees' => round($totalFees, 2),
            ])
        ];
    }
}
