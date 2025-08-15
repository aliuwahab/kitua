<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mobile_number' => fake()->unique()->regexify('233[0-9]{9}'),
            'email' => fake()->optional()->safeEmail(),
            'first_name' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'other_names' => fake()->optional()->firstName(),
            'pin' => static::$password ??= Hash::make('123456'),
            'password' => null, // Customer users don't have passwords
            'user_type' => 'customer',
            'is_active' => true,
            'mobile_verified_at' => now(),
            'email_verified_at' => null,
            'country_id' => function () {
                $ghana = Country::where('code', 'GH')->first();
                if ($ghana) {
                    return $ghana->id;
                }
                return Country::factory()->create()->id;
            },
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'mobile_verified_at' => null,
        ]);
    }

    /**
     * Create a web user (with email and password).
     */
    public function web(): static
    {
        return $this->state(fn (array $attributes) => [
            'mobile_number' => null,
            'email' => fake()->unique()->safeEmail(),
            'pin' => null,
            'password' => Hash::make('password'),
            'user_type' => 'customer', // Web customers are still customers
            'mobile_verified_at' => null,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'mobile_number' => null,
            'email' => fake()->unique()->safeEmail(),
            'pin' => null,
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'mobile_verified_at' => null,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create an inactive user.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'mobile_verified_at' => null,
        ]);
    }
}
