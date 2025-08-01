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
        $name = $this->faker->words(3, true);
        $price = $this->faker->numberBetween(10, 1000) * 1.0;

        return [
            'code' => $this->faker->unique()->ean8(),
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->paragraph(),
            'price' => number_format($price, 2, '.', ''),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
            'discount_price' => $this->faker->optional()->numberBetween(5, 500) . '.00',
            'in_stock' => $this->faker->boolean(),
            'brand_id' => Brand::inRandomOrder()->value('id'),
            'category_id' => Category::whereNotNull('parent_id')->inRandomOrder()->value('id'),
        ];
    }
}
