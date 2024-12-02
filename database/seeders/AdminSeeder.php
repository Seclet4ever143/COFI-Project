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
            'name' => 'AEDRIAN PUSPUS',
            'email' => 'aedrian.puspus@carsu.edu.ph', // Use a unique email
            'phone' => '09300618270', // Optional
            'password' => Hash::make('123123123'), // Secure password
            'role' => 'admin', // Assign admin role
        ]);
    }
}
