<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word, // Random category name
            'slug' => $this->faker->unique()->slug, // Unique slug
            'parent_id' => $this->faker->optional()->randomElement(
                Category::pluck('id')->toArray() // Randomly assign an existing category as parent
            ),
        ];
    }
}
