<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            // 'slug' => $this->faker->slug(),
            // 'logo_path' => $this->faker->imageUrl(480, 480, 'logo'),
            'description' => $this->faker->paragraph(),
        ];
    }
}
