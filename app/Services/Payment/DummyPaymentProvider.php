<?php

namespace App\Services\Payment;

use App\Models\Payment;

class DummyPaymentProvider extends AbstractPaymentProvider
{
    /**
     * Get the provider name.
     */
    public function getName(): string
    {
        return 'dummy';
    }

    /**
     * Get supported payment methods.
     */
    public function getSupportedPaymentMethods(): array
    {
        return ['card', 'bank_transfer', 'mobile_money', 'ussd'];
    }

    /**
     * Get supported currencies.
     */
    public function getSupportedCurrencies(): array
    {
        return ['GHS', 'NGN', 'KES', 'UGX', 'TZS', 'USD', 'EUR'];
    }

    /**
     * Initialize payment with the dummy provider.
     */
    public function initializePayment(Payment $payment, array $options = []): array
    {
        $this->logPaymentActivity($payment, 'initializing_payment', $options);

        // Simulate provider response
        $reference = $this->generatePaymentReference($payment);
        
        // For dummy provider, we'll simulate different scenarios based on amount
        if ($payment->amount > 1000) {
            // Simulate failure for high amounts
            throw new \Exception('Amount too high for dummy provider');
        }

        $authorizationUrl = $options['callback_url'] ?? 'https://dummy-payment.test/pay';
        $authorizationUrl .= '?reference=' . $reference . '&payment_id=' . $payment->id;

        return [
            'status' => 'success',
            'message' => 'Payment initialized successfully',
            'data' => [
                'reference' => $reference,
                'authorization_url' => $authorizationUrl,
                'access_code' => 'dummy_access_' . substr($reference, -8),
            ]
        ];
    }

    /**
     * Verify payment status.
     */
    public function verifyPayment(string $providerReference): array
    {
        $this->logPaymentActivity(null, 'verifying_payment', ['reference' => $providerReference]);

        // Simulate verification response based on reference
        if (str_contains($providerReference, 'FAIL')) {
            return [
                'status' => 'failed',
                'message' => 'Payment verification failed',
                'data' => [
                    'reference' => $providerReference,
                    'status' => 'failed',
                    'gateway_response' => 'Declined by issuer'
                ]
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Payment verification successful',
            'data' => [
                'reference' => $providerReference,
                'status' => 'success',
                'amount' => rand(100, 1000),
                'currency' => 'GHS',
                'gateway_response' => 'Successful',
                'paid_at' => now()->toISOString(),
                'channel' => 'card'
            ]
        ];
    }

    /**
     * Handle webhook from dummy provider.
     */
    public function handleWebhook(array $payload, array $headers = []): array
    {
        if (!$this->validateWebhookSignature($payload, $headers)) {
            throw new \Exception('Invalid webhook signature');
        }

        // Normalize dummy webhook payload
        return [
            'reference' => $payload['reference'] ?? '',
            'status' => $this->normalizePaymentStatus($payload['status'] ?? 'failed'),
            'amount' => $payload['amount'] ?? 0,
            'currency' => $payload['currency'] ?? 'GHS',
            'gateway_response' => $payload['gateway_response'] ?? '',
            'channel' => $payload['channel'] ?? 'unknown',
            'paid_at' => $payload['paid_at'] ?? now()->toISOString(),
            'raw_data' => $payload
        ];
    }

    /**
     * Refund a payment.
     */
    public function refundPayment(Payment $payment, float $amount = null, string $reason = null): array
    {
        $refundAmount = $amount ?? $payment->amount;
        
        $this->logPaymentActivity($payment, 'processing_refund', [
            'amount' => $refundAmount,
            'reason' => $reason
        ]);

        // Simulate refund processing
        if ($refundAmount > $payment->amount) {
            throw new \Exception('Refund amount cannot exceed original payment amount');
        }

        return [
            'status' => 'success',
            'message' => 'Refund processed successfully',
            'data' => [
                'refund_reference' => 'REF_' . $this->generatePaymentReference($payment),
                'amount' => $refundAmount,
                'currency' => $payment->currency_code,
                'status' => 'processing',
                'created_at' => now()->toISOString()
            ]
        ];
    }

    /**
     * Calculate fees for dummy provider including service fees.
     */
    public function calculateFees(float $amount, string $currency, string $paymentMethod): array
    {
        // Provider fee structure
        $providerFeePercentage = match ($paymentMethod) {
            'card' => 2.5,
            'bank_transfer' => 0.5,
            'mobile_money' => 1.5,
            'ussd' => 1.0,
            default => 2.0
        };

        $providerFee = ($amount * $providerFeePercentage) / 100;
        $maxProviderFee = match ($currency) {
            'GHS' => 50,
            'NGN' => 2000,
            'USD' => 10,
            default => 50
        };

        $finalProviderFee = min($providerFee, $maxProviderFee);

        // Use the standardized service fee calculation
        return $this->calculateServiceFees($amount, $currency, [
            'dummy_provider_fee' => round($finalProviderFee, 2)
        ]);
    }

    /**
     * Override status mapping for dummy provider.
     */
    protected function getStatusMap(): array
    {
        return array_merge(parent::getStatusMap(), [
            'success' => 'completed',
            'pending' => 'pending',
            'processing' => 'processing',
            'failed' => 'failed',
            'abandoned' => 'cancelled',
        ]);
    }

    /**
     * Validate webhook signature for dummy provider.
     */
    protected function validateWebhookSignature(array $payload, array $headers): bool
    {
        // For dummy provider, we'll just check if a signature header exists
        return isset($headers['x-dummy-signature']) || isset($headers['X-Dummy-Signature']);
    }
}
