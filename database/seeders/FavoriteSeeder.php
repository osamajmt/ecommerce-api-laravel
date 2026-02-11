<?php

namespace Database\Seeders;

use App\Models\favorite;
use App\Models\item;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $items = item::all();

         foreach ($customers as $customer) {
            // Each user favorites 4-8 random items
            $favoriteItems = $items->random(rand(4, 8));

            foreach ($favoriteItems as $item) {
                // Check if already favorited
                $existingFavorite = Favorite::where('user_id', $customer->id)
                    ->where('item_id', $item->id)
                    ->first();

                  if (!$existingFavorite) {
                    Favorite::create([
                        'user_id' => $customer->id,
                        'item_id' => $item->id,
                    ]);
                }
            }
        }
    }
}
