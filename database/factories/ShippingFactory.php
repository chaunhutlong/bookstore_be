<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Schema;
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
        Schema::disableForeignKeyConstraints();
        return [
            'tracking_num' => fake()->numerify('BOXO##########'),
            'address_id' => fake()->numberBetween(1, 5),
            'order_id' => fake()->numberBetween(1, 5),
            'value' => fake()->numberBetween(1, 20) * 10,
            'shipping_on' => fake()->dateTimeThisYear(),
            'description' => fake()->sentence(10, true)
        ];
        Schema::enableForeignKeyConstraints();
    }
}