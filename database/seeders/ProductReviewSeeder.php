<?php

namespace Database\Seeders;

use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run()
    {
        // Create 30 product reviews
        ProductReview::factory()->count(30)->create();
    }
}
