<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'full_name' => 'Administrator',
                'password' => Hash::make('11111111'),
                'role' => User::ROLE_ADMIN,  
            ]
        );

        User::firstOrCreate(
            ['email' => 'seller@gmail.com'],
            [
                'full_name' => 'Seller',
                'password' => Hash::make('11111111'),
                'role' => User::ROLE_SELLER,
            ]
        );

        User::firstOrCreate(
            ['email' => 'seller2@gmail.com'],
            [
                'full_name' => 'Seller 2',
                'password' => Hash::make('11111111'),
                'role' => User::ROLE_SELLER,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'full_name' => 'User',
                'password' => Hash::make('11111111'),
                'role' => User::ROLE_USER,
            ]
        );
    }
}
