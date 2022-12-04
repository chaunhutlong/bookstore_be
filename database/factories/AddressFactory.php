<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'distance' => fake()->randomFloat(1, 0, 10),
            'user_id' => fake()->numberBetween(1, 10),
            'city_id' => fake()->numberBetween(1, 5),
            'description' => fake()->sentence(10, true),
            'is_default' => fake()->boolean(),
        ];
    }
}