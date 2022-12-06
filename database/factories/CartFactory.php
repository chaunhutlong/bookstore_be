<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition()
    {
        return [
            'quantity' => fake()->numberBetween(1, 20),
            'user_id' => fake()->numberBetween(1, 5),
            'book_id' => fake()->numberBetween(1, 10),
            'is_checked' => fake()->boolean(),
            'price' => fake()->numberBetween(10, 90) * 10,
        ];
    }
}
