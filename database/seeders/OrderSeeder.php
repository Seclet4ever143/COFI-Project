<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::create([
            'customer_name' => 'John Doe',
            'items' => json_encode([
                ['name' => 'Burger', 'quantity' => 2, 'price' => 100],
                ['name' => 'Fries', 'quantity' => 1, 'price' => 50],
            ]),
            'total_price' => 250,
            'status' => 'pending',
        ]);

        Order::create([
            'customer_name' => 'Jane Smith',
            'items' => json_encode([
                ['name' => 'Pizza', 'quantity' => 1, 'price' => 200],
                ['name' => 'Soda', 'quantity' => 2, 'price' => 60],
            ]),
            'total_price' => 320,
            'status' => 'pending',
        ]);
    }
}
