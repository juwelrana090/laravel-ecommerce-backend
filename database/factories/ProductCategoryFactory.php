<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition()
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id, // Randomly select a product
            'category_id' => Category::inRandomOrder()->first()->id, // Randomly select a category
        ];
    }
}
