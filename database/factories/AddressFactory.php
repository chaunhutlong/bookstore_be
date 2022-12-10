<?php

namespace Database\Factories;

use App\Models\Address;
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
        $is_default = false;
        $user_id = fake()->numberBetween(1, 5);
        if (Address::where('is_default', true)->where('user_id', $user_id)->doesntExist()) $is_default = true;
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'distance' => fake()->randomFloat(1, 0, 10),
            'user_id' => $user_id,
            'city_id' => fake()->numberBetween(1, 5),
            'description' => fake()->address(),
            'is_default' => $is_default
        ];
    }
}
