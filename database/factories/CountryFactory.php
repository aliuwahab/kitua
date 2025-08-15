<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countries = [
            ['name' => 'Ghana', 'code' => 'GH', 'currency_code' => 'GHS', 'currency_symbol' => 'GH₵', 'currency_name' => 'Ghana Cedi'],
            ['name' => 'Nigeria', 'code' => 'NG', 'currency_code' => 'NGN', 'currency_symbol' => '₦', 'currency_name' => 'Nigerian Naira'],
            ['name' => 'Kenya', 'code' => 'KE', 'currency_code' => 'KES', 'currency_symbol' => 'KSh', 'currency_name' => 'Kenyan Shilling'],
            ['name' => 'South Africa', 'code' => 'ZA', 'currency_code' => 'ZAR', 'currency_symbol' => 'R', 'currency_name' => 'South African Rand'],
        ];

        $country = $this->faker->randomElement($countries);

        return [
            'name' => $country['name'],
            'code' => $country['code'],
            'currency_code' => $country['currency_code'],
            'currency_symbol' => $country['currency_symbol'],
            'currency_name' => $country['currency_name'],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the country is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
