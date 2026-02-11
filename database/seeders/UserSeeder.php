<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

         // Create admin user
        User::create([
            'user_name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'phone_number' => '09234567890',
            'image' => $this->storeFakeImage('users'),
            'approval' => true,
            'email_verified_at' => now(),
            'role' => 'admin',
            'verify_code' => null,
            'fcm_token' => 'fcm_token_admin',
        ]);

         // Create delivery users
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'user_name' => 'Delivery User ' . $i,
                'email' => 'delivery' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'phone_number' => '0923456789' . $i,
                'image' => $this->storeFakeImage('users'),
                'approval' => true,
                'email_verified_at' => now(),
                'role' => 'delivery',
                'verify_code' => null,
                'fcm_token' => 'fcm_token_delivery_' . $i,
            ]);
        }
         // Create customer users
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'user_name' => 'Customer ' . $i,
                'email' => 'customer' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'phone_number' => '+123456788' . $i,
                'image' => $this->storeFakeImage('users'),
                'approval' => rand(0, 1) == 1,
                'email_verified_at' => now(),
                'role' => 'customer',
                'verify_code' => rand(1000, 9999),
                'fcm_token' => 'fcm_token_customer_' . $i,
            ]);
        }

    }
    private function storeFakeImage(string $folder): string
    {
        $faker = \Faker\Factory::create();

        Storage::makeDirectory("public/images/$folder");

        return "$folder/avatar_" . uniqid() . '.jpg';
    }
}
