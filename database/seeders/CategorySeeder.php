<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = Category::factory(6)->create();

        Category::factory(12)->create([
            'parent_id' => fn() => $parents->random()->id,
        ]);
    }
}
