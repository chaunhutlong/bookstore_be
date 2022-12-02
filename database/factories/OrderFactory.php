<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'status' => fake()->numberBetween(1, 3),
            'order_on' => fake()->dateTimeThisYear(),
            'user_id' => fake()->numberBetween(1, 10),
            'payment_id' => fake()->numberBetween(1, 5)
        ];
    }
}