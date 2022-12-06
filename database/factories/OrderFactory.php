<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        $is_deleted = fake()->boolean();
        $deleted_at = null;
        $order_on = null;
        while ($is_deleted <= $order_on || $order_on != null) {

        }
        if ($is_deleted)
        return [
            'status' => fake()->numberBetween(0, 4),
            'order_on' =>
            'user_id' => fake()->numberBetween(1, 10),
            'payment_id' => fake()->numberBetween(1, 5),
            'is_deleted' => fake()->boolean()
        ];
    }
}
