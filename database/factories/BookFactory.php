<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->sentence(4, true),
            'available_quantity' => fake()->numberBetween(0,300),
            'isbn' => fake()->unique()->isbn13(),
            'language' => fake()->languageCode(),
            'total_pages' => fake()->numberBetween(0,500),
            'price' => fake()->numberBetween(10, 90) * 10,
            'book_image' => fake()->numerify('img_####'),
            'description' => fake()->sentence(10, true),
            'published_date' => fake()->dateTimeThisCentury('now', 'Asia/Ho_Chi_Minh'),
            'publisher_id' => fake()->numberBetween(1,5),
            'created_at' => fake()->dateTimeThisYear('now', 'Asia/Ho_Chi_Minh')
        ];
    }
}
