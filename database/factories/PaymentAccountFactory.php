<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentAccount>
 */
class PaymentAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_type' => 'momo',
            'account_number' => fake()->regexify('233[0-9]{9}'),
            'account_name' => fake()->name(),
            'provider' => fake()->randomElement(['MTN', 'Vodafone', 'AirtelTigo', 'Glo']),
            'provider_code' => fake()->optional()->numerify('*###*'),
            'is_primary' => false,
            'is_verified' => false,
            'verified_at' => null,
            'metadata' => null,
        ];
    }

    /**
     * Create a primary payment account.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Create a verified payment account.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Create a bank account.
     */
    public function bank(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => 'bank',
            'account_number' => fake()->bankAccountNumber(),
            'provider' => fake()->randomElement(['GCB Bank', 'Ecobank', 'Standard Chartered', 'Fidelity Bank']),
            'provider_code' => fake()->numerify('###'),
        ]);
    }
}
