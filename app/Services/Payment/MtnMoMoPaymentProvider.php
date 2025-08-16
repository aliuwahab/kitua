<?php

namespace App\Services\Payment;

use App\Models\Payment;

class MtnMoMoPaymentProvider extends AbstractPaymentProvider
{
    /**
     * Get the provider name.
     */
    public function getName(): string
    {
        return 'mtn_momo';
    }

    /**
     * Get supported payment methods.
     */
    public function getSupportedPaymentMethods(): array
    {
        return ['mobile_money', 'momo'];
    }

    /**
     * Get supported currencies.
     */
    public function getSupportedCurrencies(): array
    {
        return ['GHS', 'UGX', 'ZMW', 'XAF', 'XOF']; // MTN operates in these currencies
    }

    /**
     * Get default headers with API credentials.
     */
    protected function getDefaultHeaders(): array
    {
        $headers = parent::getDefaultHeaders();
        
        $apiKey = $this->getConfig('api_key');
        $subscriptionKey = $this->getConfig('subscription_key');
        
        if ($apiKey) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }
        
        if ($subscriptionKey) {
            $headers['Ocp-Apim-Subscription-Key'] = $subscriptionKey;
        }

        $headers['X-Reference-Id'] = $this->generateUuid();
        $headers['X-Target-Environment'] = $this->getConfig('environment', 'sandbox');

        return $headers;
    }

    /**
     * Initialize payment with MTN MoMo.
     */
    public function initializePayment(Payment $payment, array $options = []): array
    {
        $this->logPaymentActivity($payment, 'initializing_mtn_momo_payment', $options);

        // Generate reference
        $reference = $this->generatePaymentReference($payment);
        
        // Prepare request payload
        $payload = [
            'amount' => (string) $payment->amount,
            'currency' => $payment->currency_code,
            'externalId' => $reference,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $this->cleanPhoneNumber($payment->phone_number),
            ],
            'payerMessage' => 'Payment for ' . ($payment->payable->purpose ?? 'service'),
            'payeeNote' => 'Payment via Kitua App',
        ];

        // Add callback URL if provided
        if (isset($options['webhook_url'])) {
            $payload['callbackUrl'] = $options['webhook_url'];
        }

        try {
            // First, get access token
            $accessToken = $this->getAccessToken();
            $this->defaultHeaders['Authorization'] = 'Bearer ' . $accessToken;

            // Make payment request
            $response = $this->makeRequest('POST', '/collection/v1_0/requesttopay', $payload);

            return [
                'status' => 'success',
                'message' => 'Payment request initiated successfully',
                'data' => [
                    'reference' => $reference,
                    'mtn_reference' => $response['referenceId'] ?? $reference,
                    'status' => 'pending',
                    'amount' => $payment->amount,
                    'currency' => $payment->currency_code,
                    'phone_number' => $payment->phone_number,
                ]
            ];

        } catch (\Exception $e) {
            $this->logPaymentActivity($payment, 'mtn_momo_initialization_failed', [
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('MTN MoMo payment initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment status with MTN MoMo.
     */
    public function verifyPayment(string $providerReference): array
    {
        $this->logPaymentActivity(null, 'verifying_mtn_momo_payment', ['reference' => $providerReference]);

        try {
            // Get access token
            $accessToken = $this->getAccessToken();
            $this->defaultHeaders['Authorization'] = 'Bearer ' . $accessToken;

            // Check payment status
            $response = $this->makeRequest('GET', "/collection/v1_0/requesttopay/{$providerReference}");

            $status = $this->normalizePaymentStatus($response['status'] ?? 'failed');

            return [
                'status' => $status === 'completed' ? 'success' : 'failed',
                'message' => 'Payment verification completed',
                'data' => [
                    'reference' => $providerReference,
                    'status' => $status,
                    'amount' => $response['amount'] ?? 0,
                    'currency' => $response['currency'] ?? 'GHS',
                    'gateway_response' => $response['reason'] ?? 'Success',
                    'paid_at' => now()->toISOString(),
                    'raw_response' => $response,
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Payment verification failed',
                'data' => [
                    'reference' => $providerReference,
                    'status' => 'failed',
                    'gateway_response' => $e->getMessage(),
                ]
            ];
        }
    }

    /**
     * Handle webhook from MTN MoMo.
     */
    public function handleWebhook(array $payload, array $headers = []): array
    {
        if (!$this->validateWebhookSignature($payload, $headers)) {
            throw new \Exception('Invalid MTN MoMo webhook signature');
        }

        // Normalize MTN MoMo webhook payload
        $reference = $payload['externalId'] ?? $payload['reference'] ?? '';
        $status = $this->normalizePaymentStatus($payload['status'] ?? 'failed');

        return [
            'reference' => $reference,
            'status' => $status,
            'amount' => $payload['amount'] ?? 0,
            'currency' => $payload['currency'] ?? 'GHS',
            'gateway_response' => $payload['reason'] ?? '',
            'channel' => 'mobile_money',
            'paid_at' => $payload['finishedDateTime'] ?? now()->toISOString(),
            'raw_data' => $payload
        ];
    }

    /**
     * Refund a payment (MTN MoMo may not support direct refunds).
     */
    public function refundPayment(Payment $payment, float $amount = null, string $reason = null): array
    {
        throw new \Exception('MTN MoMo direct refunds are not supported. Please process manually.');
    }

    /**
     * Calculate fees for MTN MoMo including service fees.
     */
    public function calculateFees(float $amount, string $currency, string $paymentMethod): array
    {
        // Get service fee rate from config (default 1% = 0.01)
        $serviceFeeRate = $this->getServiceFeeRate();
        
        // MTN MoMo provider fee structure
        $providerFeeRate = match ($currency) {
            'GHS' => 0.75, // 0.75% for Ghana
            'UGX' => 1.0,  // 1.0% for Uganda
            'ZMW' => 1.5,  // 1.5% for Zambia
            default => 1.0,
        };

        // Calculate fees
        $serviceFee = ($amount * $serviceFeeRate) / 100;
        $providerFee = ($amount * $providerFeeRate) / 100;
        
        // Set maximum provider fees based on currency
        $maxProviderFee = match ($currency) {
            'GHS' => 30,
            'UGX' => 50000,
            'ZMW' => 100,
            default => 50,
        };

        $finalProviderFee = min($providerFee, $maxProviderFee);
        $totalFees = $serviceFee + $finalProviderFee;

        return [
            'service_fee_percentage' => $serviceFeeRate,
            'provider_fee_percentage' => $providerFeeRate,
            'service_fee_amount' => round($serviceFee, 2),
            'provider_fee_amount' => round($finalProviderFee, 2),
            'total_fee_amount' => round($totalFees, 2),
            'total_amount' => $amount + $totalFees,
            'currency' => $currency,
            'breakdown' => [
                'base_amount' => $amount,
                'service_fee' => round($serviceFee, 2),
                'mtn_provider_fee' => round($finalProviderFee, 2),
                'tax' => 0,
                'total_fees' => round($totalFees, 2),
            ]
        ];
    }

    /**
     * Get access token from MTN MoMo API.
     */
    private function getAccessToken(): string
    {
        $apiUser = $this->getConfig('api_user');
        $apiKey = $this->getConfig('api_key');
        
        if (!$apiUser || !$apiKey) {
            throw new \Exception('MTN MoMo API credentials not configured');
        }

        $credentials = base64_encode($apiUser . ':' . $apiKey);
        
        $headers = [
            'Authorization' => 'Basic ' . $credentials,
            'Ocp-Apim-Subscription-Key' => $this->getConfig('subscription_key'),
        ];

        $response = $this->makeRequest('POST', '/collection/token/', [], $headers);
        
        if (!isset($response['access_token'])) {
            throw new \Exception('Failed to obtain MTN MoMo access token');
        }

        return $response['access_token'];
    }

    /**
     * Clean phone number for MTN format.
     */
    private function cleanPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove leading + if present
        $cleaned = ltrim($cleaned, '+');
        
        // Ensure it starts with country code
        if (strlen($cleaned) === 10) {
            // Assume Ghana if 10 digits
            $cleaned = '233' . substr($cleaned, 1);
        } elseif (strlen($cleaned) === 9) {
            // Assume Uganda if 9 digits
            $cleaned = '256' . $cleaned;
        }
        
        return $cleaned;
    }

    /**
     * Generate UUID for MTN MoMo requests.
     */
    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Override status mapping for MTN MoMo.
     */
    protected function getStatusMap(): array
    {
        return array_merge(parent::getStatusMap(), [
            'successful' => 'completed',
            'pending' => 'pending',
            'failed' => 'failed',
            'timeout' => 'failed',
            'cancelled' => 'cancelled',
        ]);
    }

    /**
     * Validate webhook signature for MTN MoMo.
     */
    protected function validateWebhookSignature(array $payload, array $headers): bool
    {
        // MTN MoMo webhook validation would go here
        // For now, we'll do basic validation
        $signature = $headers['X-MTN-Signature'] ?? $headers['x-mtn-signature'] ?? null;
        
        if (!$signature) {
            return false;
        }

        // In production, you would validate the signature using MTN's public key
        // For now, we'll just check if the signature exists
        return !empty($signature);
    }
}
