<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceSession>
 */
class DeviceSessionFactory extends Factory
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
            'device_id' => Str::uuid()->toString(),
            'device_name' => fake()->randomElement(['My Phone', 'iPhone', 'Samsung Galaxy', 'Work Phone']),
            'device_type' => fake()->randomElement(['android', 'ios']),
            'device_fingerprint' => hash('sha256', fake()->uuid()),
            'app_version' => fake()->semver(),
            'os_version' => fake()->randomElement(['Android 12', 'iOS 16.1', 'Android 13', 'iOS 15.5']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'push_token' => fake()->optional()->regexify('[A-Za-z0-9]{64}'),
            'first_login_at' => now(),
            'last_activity_at' => now(),
            'is_trusted' => false,
            'is_active' => true,
            'revoked_at' => null,
            'metadata' => null,
        ];
    }

    /**
     * Create a trusted device session.
     */
    public function trusted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_trusted' => true,
        ]);
    }

    /**
     * Create a revoked device session.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Create an iOS device session.
     */
    public function ios(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'ios',
            'os_version' => fake()->randomElement(['iOS 16.1', 'iOS 15.5', 'iOS 17.0']),
            'device_name' => fake()->randomElement(['iPhone', 'iPad', 'My iPhone']),
        ]);
    }

    /**
     * Create an Android device session.
     */
    public function android(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'android',
            'os_version' => fake()->randomElement(['Android 12', 'Android 13', 'Android 14']),
            'device_name' => fake()->randomElement(['Samsung Galaxy', 'My Phone', 'Work Phone']),
        ]);
    }
}
