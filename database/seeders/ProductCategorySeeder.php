<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        // Create 50 random product-category associations
        ProductCategory::factory()
            ->count(50)
            ->create();
    }
}
