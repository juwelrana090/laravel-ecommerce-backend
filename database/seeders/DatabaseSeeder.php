<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call individual seeders
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductCategorySeeder::class,
            OrderSeeder::class,
            OrderDetailSeeder::class,
            ProductReviewSeeder::class,
        ]);
    }
}
