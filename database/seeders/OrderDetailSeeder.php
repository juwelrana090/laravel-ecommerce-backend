<?php

namespace Database\Seeders;

use App\Models\OrderDetail;
use Illuminate\Database\Seeder;

class OrderDetailSeeder extends Seeder
{
    public function run()
    {
        // Create 50 order details
        OrderDetail::factory()->count(50)->create();
    }
}
