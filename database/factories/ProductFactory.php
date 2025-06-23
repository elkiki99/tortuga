<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Brand;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
            'discount_price' => $this->faker->optional()->randomFloat(2, 5, 500),
            'in_stock' => $this->faker->boolean(),
            'brand_id' => Brand::inRandomOrder()->value('id'),
            'category_id' => Category::whereNotNull('parent_id')->inRandomOrder()->value('id'),
        ];
    }
}
