<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $start_date = 0;
        $end_date = 0;
        while ($start_date >= $end_date) {
            $start_date = fake()->dateTimeThisYear();
            $end_date = fake()->dateTimeThisYear();
        }
        return [
            'name' => fake()->sentence(3, true),
            'value' => fake()->numberBetween(10, 90) * 10,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'quantity' => fake()->numberBetween(1,100),
            'description' => fake()->sentence(10, true),
            'is_public' => fake()->boolean()
        ];
    }
}
