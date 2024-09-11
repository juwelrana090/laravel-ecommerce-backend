<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductReviewFactory extends Factory
{
    protected $model = ProductReview::class;

    public function definition()
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id, // Randomly select a product
            'user_id' => User::inRandomOrder()->first()->id, // Randomly select a user
            'comment' => $this->faker->text(200), // Random comment text
            'rating' => $this->faker->numberBetween(1, 5), // Random rating between 1 and 5
        ];
    }
}
