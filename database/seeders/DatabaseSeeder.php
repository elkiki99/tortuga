<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Image;
use App\Models\Brand;
use App\Models\Stats;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create();

        Brand::factory(10)->create();

        $this->call(CategorySeeder::class);

        Product::factory(100)->create();
        Image::factory(300)->create();

        Stats::factory()->count(30)->create();
    }
}
