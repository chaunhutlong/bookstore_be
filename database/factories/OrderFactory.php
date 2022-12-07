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
        $active = fake()->boolean();
        $order_on = fake()->dateTimeThisYear();
        $deleted_at = null;
        if (!$active) {
            while ($order_on > $deleted_at || $deleted_at == null) {
                $order_on = fake()->dateTimeThisYear();
                $deleted_at = fake()->dateTimeThisYear();
            }
        }
        return [
            'status' => fake()->numberBetween(0, 4),
            'order_on' => $order_on,
            'user_id' => fake()->numberBetween(1, 10),
            'payment_id' => fake()->numberBetween(1, 5),
            'is_deleted' => !$active,
            'deleted_at' => $deleted_at
        ];
    }
}
