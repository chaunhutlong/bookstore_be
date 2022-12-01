<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipping>
 */
class ShippingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->numerify('shipping_####'),
            'address_id' => fake()->numberBetween(1,5),
            'phone' => fake()->phoneNumber(),
            'value' => fake()->numberBetween(1,20)*10,
            'shipping_on' => fake()->dateTimeThisYear(),
            'description' => fake()->sentence(10, true)
        ];
    }
}
