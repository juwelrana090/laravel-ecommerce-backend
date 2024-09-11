<?php

namespace Database\Factories;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    protected $model = OrderDetail::class;

    public function definition()
    {
        return [
            'order_id' => function () {
                return Order::inRandomOrder()->first()->id; // Randomly assign an existing order
            },
            'product_id' => function () {
                return Product::inRandomOrder()->first()->id; // Randomly assign an existing product
            },
            'unit_price' => $this->faker->randomFloat(2, 10, 200), // Random unit price
            'quantity' => $this->faker->numberBetween(1, 5), // Random quantity
        ];
    }
}
