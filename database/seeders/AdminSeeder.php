<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'YANYAN',
            'email' => 'aedrianberdijopuspus@gmail.com', // Use a unique email
            'phone' => '09300618271', // Optional
            'password' => Hash::make('123123123'), // Secure password
            'role' => 'staff', // Assign admin role
        ]);
    }
}
