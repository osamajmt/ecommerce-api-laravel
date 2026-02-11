<?php

namespace Database\Seeders;

use App\Models\cart;
use App\Models\item;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        $items = item::all();

        foreach ($users as $user) {
           
            $cartItems = $items->random(rand(1, 3));

            foreach ($cartItems as $item) {
                $existingCart = Cart::where('user_id', $user->id)
                    ->where('item_id', $item->id)
                    ->where('status', 0)
                    ->first();

                if (!$existingCart) {
                    Cart::create([
                        'user_id' => $user->id,
                        'item_id' => $item->id,
                        'count' => rand(1, 3),
                        'status' => 0, // active cart
                        'order_id' => null,
                    ]);
                }
            }
        }
    }
}
