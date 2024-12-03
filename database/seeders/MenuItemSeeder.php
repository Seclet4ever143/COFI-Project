<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MenuItem;


class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Add sample data to the menu_items table
        MenuItem::create([
            'name' => 'Coffee',
            'price' => 100,
            'qty' => 50,
            'category' => 'Beverage',
        ]);

        MenuItem::create([
            'name' => 'Tea',
            'price' => 80,
            'qty' => 30,
            'category' => 'Beverage',
        ]);

        MenuItem::create([
            'name' => 'Sandwich',
            'price' => 150,
            'qty' => 20,
            'category' => 'Food',
        ]);

        MenuItem::create([
            'name' => 'Cake',
            'price' => 120,
            'qty' => 25,
            'category' => 'Dessert',
        ]);
    }
}
