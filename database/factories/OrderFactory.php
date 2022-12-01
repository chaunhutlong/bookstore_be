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
            'status' => rand(0,4),
            'order_on' => $order_on,
            'user_id' => rand(1,10),
            'payment_id' => rand(1,5),
            'active' => $active,
            'deleted_at' => $deleted_at,
            'created_at' => fake()->dateTimeThisYear('now', 'Asia/Ho_Chi_Minh')
        ];
    }
}
