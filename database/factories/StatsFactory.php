<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stats>
 */
class StatsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->unique()->dateTimeBetween('-730 days', 'now')->format('Y-m-d'),
            'orders_count' => $this->faker->numberBetween(1, 25),
            'total_revenue' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}
