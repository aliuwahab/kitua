<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentProviderInterface
{
    /**
     * Initialize a payment with the provider.
     * This should create the payment on the provider's side and return
     * the necessary data for the client to complete the payment.
     *
     * @param Payment $payment
     * @param array $options Additional options for the payment (like callback URLs)
     * @return array Response data from the provider
     * @throws \Exception If payment initialization fails
     */
    public function initializePayment(Payment $payment, array $options = []): array;

    /**
     * Verify a payment with the provider using their reference.
     *
     * @param string $providerReference
     * @return array Payment status and details from the provider
     * @throws \Exception If verification fails
     */
    public function verifyPayment(string $providerReference): array;

    /**
     * Handle webhook callback from the provider.
     * This should validate the webhook and return the payment status.
     *
     * @param array $payload The webhook payload
     * @param array $headers The request headers
     * @return array Normalized payment status data
     * @throws \Exception If webhook validation fails
     */
    public function handleWebhook(array $payload, array $headers = []): array;

    /**
     * Get the provider name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the supported payment methods for this provider.
     *
     * @return array
     */
    public function getSupportedPaymentMethods(): array;

    /**
     * Get the supported currencies for this provider.
     *
     * @return array
     */
    public function getSupportedCurrencies(): array;

    /**
     * Check if the provider supports a specific payment method.
     *
     * @param string $method
     * @return bool
     */
    public function supportsPaymentMethod(string $method): bool;

    /**
     * Check if the provider supports a specific currency.
     *
     * @param string $currency
     * @return bool
     */
    public function supportsCurrency(string $currency): bool;

    /**
     * Refund a payment.
     *
     * @param Payment $payment
     * @param float|null $amount Amount to refund (null for full refund)
     * @param string|null $reason Reason for refund
     * @return array Refund response data
     * @throws \Exception If refund fails
     */
    public function refundPayment(Payment $payment, float $amount = null, string $reason = null): array;

    /**
     * Get payment fees for this provider.
     *
     * @param float $amount
     * @param string $currency
     * @param string $paymentMethod
     * @return array Fee breakdown
     */
    public function calculateFees(float $amount, string $currency, string $paymentMethod): array;
}
