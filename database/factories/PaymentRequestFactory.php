<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentRequest>
 */
class PaymentRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purposes = [
            'Lunch money',
            'Dinner contribution',
            'Birthday gift',
            'Groceries shopping',
            'Taxi fare',
            'Movie tickets',
            'Coffee money',
            'Book purchase',
            'Phone credit',
            'Parking fee'
        ];

        $currencies = ['GHS', 'NGN', 'KES', 'ZAR', 'USD'];

        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'currency_code' => $this->faker->randomElement($currencies),
            'purpose' => $this->faker->randomElement($purposes),
            'description' => $this->faker->optional(0.7)->sentence(),
            'status' => 'pending',
            'expires_at' => $this->faker->optional(0.3)->dateTimeBetween('+1 day', '+7 days'),
            'paid_at' => null,
            'metadata' => null,
        ];
    }

    /**
     * Indicate that the payment request is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the payment request is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the payment request is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expires_at' => $this->faker->dateTimeBetween('-7 days', '-1 day'),
        ]);
    }

    /**
     * Set a specific amount.
     */
    public function amount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    /**
     * Set a specific currency.
     */
    public function currency(string $currencyCode): static
    {
        return $this->state(fn (array $attributes) => [
            'currency_code' => $currencyCode,
        ]);
    }
}
