<?php

namespace Database\Seeders;

use App\Models\address;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $users = User::where('role', 'customer')->get();

        foreach ($users as $user) {
            address::create([
                'user_id' => $user->id,
                'name' => 'Home',
                'city' => 'Damascus',
                'street' => 'st1',
                'latitude' => 24.7136 + (rand(-100, 100) / 1000),
                'longitude' => 46.6753 + (rand(-100, 100) / 1000),
            ]);
              Address::create([
                'user_id' => $user->id,
                'name' => 'Work',
                'city' => 'Damascus',
                'street' => 'st2',
                'latitude' => 24.7136 + (rand(-100, 100) / 1000),
                'longitude' => 46.6753 + (rand(-100, 100) / 1000),
            ]);
        }

    }
}
