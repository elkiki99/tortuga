<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => 'products/' . $this->faker->unique()->imageUrl(640, 640, 'image'),
            'alt_text' => $this->faker->sentence(),
            'product_id' => Product::inRandomOrder()->value('id'),
            'is_featured' => $this->faker->boolean(50),
        ];
    }
}
