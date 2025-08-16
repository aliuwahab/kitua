<?php

namespace App\Actions\Payment;

use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Services\Payment\PaymentProviderManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SettlePayment
{
    public function __construct(
        private PaymentProviderManager $paymentProviderManager
    ) {}

    /**
     * Execute the payment settlement.
     *
     * @param PaymentRequest $paymentRequest
     * @param User $user
     * @param array $paymentData
     * @return Payment
     * @throws \Exception
     */
    public function execute(PaymentRequest $paymentRequest, User $user, array $paymentData): Payment
    {
        // Validate payment request can be paid
        $this->validatePaymentRequest($paymentRequest, $user, $paymentData);

        return DB::transaction(function () use ($paymentRequest, $user, $paymentData) {
            // Get the appropriate payment provider first
            $provider = $this->getProviderForPayment($paymentRequest, $paymentData);
            
            // Create the payment record with provider info
            $payment = $this->createPayment($paymentRequest, $user, $paymentData, $provider);

            try {
                // Initialize payment with the provider
                $providerResponse = $provider->initializePayment($payment, [
                    'callback_url' => $paymentData['callback_url'] ?? null,
                    'webhook_url' => route('webhooks.payment', ['provider' => $provider->getName()]),
                    'metadata' => [
                        'payment_id' => $payment->id,
                        'user_id' => $user->id,
                        'payable_type' => get_class($paymentRequest),
                        'payable_id' => $paymentRequest->id,
                    ]
                ]);

                // Update payment with provider response
                $this->updatePaymentWithProviderResponse($payment, $provider, $providerResponse);

                Log::info('Payment settlement initiated successfully', [
                    'payment_id' => $payment->id,
                    'provider' => $provider->getName(),
                    'user_id' => $user->id,
                    'amount' => $payment->amount
                ]);

                return $payment;

            } catch (\Exception $e) {
                // Mark payment as failed
                $payment->markAsFailed(
                    'provider_initialization_failed',
                    $e->getMessage()
                );

                Log::error('Payment settlement failed', [
                    'payment_id' => $payment->id,
                    'provider' => $provider->getName(),
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);

                throw new \Exception('Failed to initialize payment: ' . $e->getMessage());
            }
        });
    }

    /**
     * Validate that the payment request can be settled.
     */
    private function validatePaymentRequest(PaymentRequest $paymentRequest, User $user, array $paymentData): void
    {
        // Check if payment request is still active
        if ($paymentRequest->status !== 'pending') {
            throw ValidationException::withMessages([
                'payment_request' => 'Payment request is no longer available for payment.'
            ]);
        }

        // Check if payment request has expired
        if ($paymentRequest->is_expired) {
            throw ValidationException::withMessages([
                'payment_request' => 'Payment request has expired.'
            ]);
        }

        // Check if user is trying to pay their own request
        if ($paymentRequest->user_id === $user->id) {
            throw ValidationException::withMessages([
                'payment_request' => 'You cannot pay your own payment request.'
            ]);
        }

        // Validate payment amount if negotiable
        if ($paymentRequest->is_negotiable && isset($paymentData['amount'])) {
            if ($paymentData['amount'] <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount must be greater than zero.'
                ]);
            }
        } elseif (!$paymentRequest->is_negotiable && isset($paymentData['amount'])) {
            if ($paymentData['amount'] != $paymentRequest->amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount must match the requested amount for non-negotiable requests.'
                ]);
            }
        }

        // Validate payment method is supported (skip if provider check fails)
        if (isset($paymentData['payment_method'])) {
            try {
                $supportedMethods = $this->paymentProviderManager->getAllSupportedPaymentMethods();
                if (!empty($supportedMethods) && !in_array($paymentData['payment_method'], $supportedMethods)) {
                    throw ValidationException::withMessages([
                        'payment_method' => 'Unsupported payment method.'
                    ]);
                }
            } catch (\Exception $e) {
                // Continue if provider validation fails - this will be caught later during provider selection
            }
        }

        // Validate currency is supported (skip if provider check fails)
        try {
            $supportedCurrencies = $this->paymentProviderManager->getAllSupportedCurrencies();
            if (!empty($supportedCurrencies) && !in_array($paymentRequest->currency_code, $supportedCurrencies)) {
                throw ValidationException::withMessages([
                    'currency' => 'Unsupported currency for payment processing.'
                ]);
            }
        } catch (\Exception $e) {
            // Continue if provider validation fails - this will be caught later during provider selection
        }

        // Validate phone number for mobile money payments
        if (isset($paymentData['payment_method']) && 
            in_array($paymentData['payment_method'], ['mobile_money', 'momo']) && 
            empty($paymentData['phone_number'])) {
            throw ValidationException::withMessages([
                'phone_number' => 'Phone number is required for mobile money payments.'
            ]);
        }
    }

    /**
     * Get the best provider for the payment.
     */
    private function getProviderForPayment(PaymentRequest $paymentRequest, array $paymentData)
    {
        // Create a temporary payment object for provider selection
        $tempPayment = new Payment([
            'amount' => $paymentData['amount'] ?? $paymentRequest->amount,
            'currency_code' => $paymentRequest->currency_code,
            'payment_method' => $paymentData['payment_method'] ?? null,
            'phone_number' => $paymentData['phone_number'] ?? null,
        ]);

        return $this->paymentProviderManager->getBestProviderForPayment($tempPayment);
    }

    /**
     * Create the payment record.
     */
    private function createPayment(PaymentRequest $paymentRequest, User $user, array $paymentData, $provider): Payment
    {
        $amount = $paymentData['amount'] ?? $paymentRequest->amount;

        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_type' => PaymentRequest::class,
            'payable_id' => $paymentRequest->id,
            'amount' => $amount,
            'currency_code' => $paymentRequest->currency_code,
            'provider' => $provider->getName(),
            'payment_method' => $paymentData['payment_method'] ?? null,
            'phone_number' => $paymentData['phone_number'] ?? null,
            'account_number' => $paymentData['account_number'] ?? null,
            'status' => 'pending',
            'metadata' => [
                'user_agent' => request()->header('User-Agent'),
                'ip_address' => request()->ip(),
                'created_via' => 'api',
                'original_request_amount' => $paymentRequest->amount,
            ]
        ]);

        return $payment;
    }

    /**
     * Update payment with provider response.
     */
    private function updatePaymentWithProviderResponse(Payment $payment, $provider, array $response): void
    {
        $updateData = [
            'provider' => $provider->getName(),
            'provider_response' => $response,
        ];

        // Extract provider reference if available
        if (isset($response['data']['reference'])) {
            $updateData['provider_reference'] = $response['data']['reference'];
        }

        // Mark as initiated
        if ($response['status'] === 'success' || (isset($response['data']['authorization_url']) && $response['data']['authorization_url'])) {
            $updateData['status'] = 'processing';
            $updateData['initiated_at'] = now();
        }

        $payment->update($updateData);
    }

    /**
     * Verify a payment with the provider.
     */
    public function verifyPayment(Payment $payment): Payment
    {
        if (!$payment->provider_reference) {
            throw new \Exception('Cannot verify payment without provider reference');
        }

        try {
            $provider = $this->paymentProviderManager->driver($payment->provider);
            $verificationResult = $provider->verifyPayment($payment->provider_reference);

            $status = $verificationResult['data']['status'] ?? 'failed';
            $normalizedStatus = $provider->normalizePaymentStatus($status);

            // Update payment based on verification result
            if ($normalizedStatus === 'completed') {
                $payment->markAsCompleted($verificationResult['data']);

                // Mark the payment request as paid if this payment is successful
                $paymentRequest = $payment->payable;
                if ($paymentRequest && $paymentRequest->status === 'pending') {
                    $paymentRequest->markAsPaid();
                }

            } elseif ($normalizedStatus === 'failed') {
                $payment->markAsFailed(
                    'payment_failed',
                    $verificationResult['data']['gateway_response'] ?? 'Payment verification failed',
                    $verificationResult['data']
                );
            }

            Log::info('Payment verification completed', [
                'payment_id' => $payment->id,
                'status' => $normalizedStatus,
                'provider' => $payment->provider
            ]);

            return $payment->fresh();

        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Process webhook callback for payment status update.
     */
    public function processWebhookCallback(string $providerName, array $payload, array $headers = []): ?Payment
    {
        try {
            $provider = $this->paymentProviderManager->driver($providerName);
            $webhookData = $provider->handleWebhook($payload, $headers);

            // Find the payment by provider reference
            $payment = Payment::where('provider', $providerName)
                ->where('provider_reference', $webhookData['reference'])
                ->first();

            if (!$payment) {
                Log::warning('Webhook received for unknown payment', [
                    'provider' => $providerName,
                    'reference' => $webhookData['reference']
                ]);
                return null;
            }

            // Update payment status based on webhook
            if ($webhookData['status'] === 'completed') {
                $payment->markAsCompleted($webhookData['raw_data']);

                // Mark payment request as paid
                $paymentRequest = $payment->payable;
                if ($paymentRequest && $paymentRequest->status === 'pending') {
                    $paymentRequest->markAsPaid();
                }

            } elseif ($webhookData['status'] === 'failed') {
                $payment->markAsFailed(
                    'payment_failed',
                    $webhookData['gateway_response'] ?? 'Payment failed',
                    $webhookData['raw_data']
                );
            }

            Log::info('Webhook processed successfully', [
                'payment_id' => $payment->id,
                'provider' => $providerName,
                'status' => $webhookData['status']
            ]);

            return $payment;

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'provider' => $providerName,
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            throw $e;
        }
    }
}
