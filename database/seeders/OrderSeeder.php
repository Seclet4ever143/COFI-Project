<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Customer IDs
        $customerIds = [8, 13, 14, 15, 16];
        // Staff IDs
        $staffIds = [4, 7, 10, 11, 12];

        foreach ($customerIds as $customerId) {
            DB::table('orders')->insert([
                'customer_id' => $customerId,
                'staff_id' => $faker->randomElement($staffIds), // Random staff ID from the list
                'total_amount' => $faker->randomFloat(2, 20, 100), // Random total amount between 20 and 100
                'status' => $faker->randomElement(['pending', 'completed', 'shipped']), // Random status
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
