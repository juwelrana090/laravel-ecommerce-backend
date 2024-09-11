<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'grand_total' => $this->faker->randomFloat(2, 50, 500), // Random total amount
            'shipping_cost' => $this->faker->randomFloat(2, 5, 50), // Random shipping cost
            'discount' => $this->faker->randomFloat(2, 0, 50), // Random discount amount
            'user_id' => User::inRandomOrder()->first()->id, // Randomly assign an existing user
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Create a random number of order details (between 1 and 5)
            $order->orderDetails()->createMany(
                OrderDetail::factory()->count(rand(1, 5))->make()->toArray()
            );
        });
    }
}
