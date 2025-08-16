<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentMethods = ['mobile_money', 'card', 'bank_transfer', 'ussd'];
        $currencies = ['GHS', 'NGN', 'KES', 'UGX', 'USD'];
        $statuses = ['pending', 'processing', 'completed', 'failed', 'cancelled'];
        $providers = ['dummy', 'mtn_momo', 'paystack', 'flutterwave'];

        return [
            'user_id' => User::factory(),
            'payable_type' => PaymentRequest::class,
            'payable_id' => PaymentRequest::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency_code' => $this->faker->randomElement($currencies),
            'provider' => $this->faker->randomElement($providers),
            'provider_reference' => strtoupper($this->faker->lexify('???')) . '_' . $this->faker->numberBetween(100000, 999999),
            'provider_payment_method' => $this->faker->randomElement($paymentMethods),
            'status' => $this->faker->randomElement($statuses),
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'phone_number' => $this->faker->optional()->e164PhoneNumber(),
            'account_number' => $this->faker->optional()->numerify('##########'),
            'initiated_at' => $this->faker->optional()->dateTimeBetween('-1 hour', 'now'),
            'completed_at' => null,
            'failed_at' => null,
            'provider_response' => null,
            'metadata' => [
                'user_agent' => $this->faker->userAgent,
                'ip_address' => $this->faker->ipv4,
                'created_via' => 'api',
            ],
            'failure_reason' => null,
            'failure_message' => null,
        ];
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'provider_response' => [
                'status' => 'success',
                'reference' => $attributes['provider_reference'],
                'gateway_response' => 'Successful',
            ],
        ]);
    }

    /**
     * Indicate that the payment is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failed_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'failure_reason' => 'insufficient_funds',
            'failure_message' => 'Insufficient funds in account',
            'provider_response' => [
                'status' => 'failed',
                'reference' => $attributes['provider_reference'],
                'gateway_response' => 'Insufficient funds',
            ],
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'initiated_at' => null,
            'completed_at' => null,
            'failed_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
            'initiated_at' => $this->faker->dateTimeBetween('-30 minutes', 'now'),
            'completed_at' => null,
            'failed_at' => null,
        ]);
    }

    /**
     * Set a specific payment method.
     */
    public function paymentMethod(string $method): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => $method,
            'provider_payment_method' => $method,
            'phone_number' => in_array($method, ['mobile_money', 'momo']) 
                ? $this->faker->e164PhoneNumber() 
                : null,
        ]);
    }

    /**
     * Set a specific provider.
     */
    public function provider(string $provider): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => $provider,
            'provider_reference' => strtoupper($provider) . '_' . $this->faker->numberBetween(100000, 999999),
        ]);
    }

    /**
     * Set a specific currency.
     */
    public function currency(string $currency): static
    {
        return $this->state(fn (array $attributes) => [
            'currency_code' => $currency,
        ]);
    }
}
