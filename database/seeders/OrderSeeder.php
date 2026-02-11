<?php

namespace Database\Seeders;

use App\Models\address;
use App\Models\cart;
use App\Models\order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // order::truncate();
        $customers = User::where('role', 'customer')->get();
        $deliveryUsers = User::where('role', 'delivery')->get();

        $orderTypes = [
            0 => 'pickup',
            1 => 'Delivery',
        ];
         $paymentMethods = [
            0 => 'Cash on Delivery',
            1 => 'Credit Card',
        ];

        $statuses = [
            0 => 'Pending',
            1 => 'approved(beaing prepared)',
            2 => 'ready (for pickup) — when type = 0 (receive-from-store order), ready (for delivery) — when type = 1',
            3 => 'assigned (delivery worker accepted) — when type = 1 (delivery order)',
            4 => 'on the way (delivery order)',
            5 => 'delivered or completed',
            6 => 'cancelled'
        ];

         foreach ($customers as $customer) {
            $addresses = address::where('user_id', $customer->id)->get();

            if ($addresses->isEmpty()) {
                continue;
            }
            // Create 1-3 orders per customer
            for ($i = 0; $i < rand(1, 3); $i++) {
                $address = $addresses->random();
                $deliveryUser = $deliveryUsers->isNotEmpty() ? $deliveryUsers->random() : null;
                $orderType = array_rand($orderTypes);
                $paymentMethod = 0;
                 // Set status based on order type
                $status = $this->getStatusBasedOnType($orderType);
                // Calculate order total
                $subtotal = rand(10000, 50000); // Random subtotal between 100 and 500
                $deliveryPrice = $orderType == 1 ? rand(1000, 3000) : 0; // Delivery has price, pickup is free
                $totalPrice = $subtotal + $deliveryPrice;

                $deliveryId = null;
                if ($orderType == 1 && $status >= 3) { // For delivery orders with status 3+
                    $deliveryId = $deliveryUser ? $deliveryUser->id : null;
                }
                // Create order
                $order = order::create([
                    'user_id' => $customer->id,
                    'address_id' => $address->id,
                    'coupon_id' => null, // No coupons in seed
                    'delivery_id' => $deliveryId,
                    'type' => $orderType,
                    'total_price' => $totalPrice,
                    'delivery_price' => $deliveryPrice,
                    'payment_method' => $paymentMethod,
                    'status' => $status,
                    'rating' => $status == 5 ? rand(3, 5) : null, // Only rate completed orders (status 5)
                    'rating_comment' => $status == 5 ? $this->getRandomComment() : null,
                ]);
                // Move some cart items to this order
                $cartItems = cart::where('user_id', $customer->id)
                    ->where('status', 0)
                    ->limit(rand(1, 3))
                    ->get();
                 foreach ($cartItems as $cartItem) {
                    $cartItem->update([
                        'order_id' => $order->id,
                        'status' => 1 // Mark as ordered
                    ]);
                }
            }
        }
    }
    private function getStatusBasedOnType(int $orderType): int
        {
            // For pickup orders (type 0)
            if ($orderType == 0) {
                $statuses = [0, 1, 2, 5, 6]; // pickup valid statuses: 0,1,2,5,6
                return $statuses[array_rand($statuses)];
            }

            // For delivery orders (type 1)
            $statuses = [0, 1, 2, 3, 4, 5, 6];

            // Weight the statuses so we get more realistic distribution
                $weights = [
            0 => 10, // pending
            1 => 20, // approved
            2 => 15, // ready
            3 => 15, // assigned
            4 => 15, // on the way
            5 => 20, // delivered
            6 => 5,  // cancelled
        ];

        $totalWeight = array_sum($weights);
        $rand = rand(1, $totalWeight);
        $current = 0;
         foreach ($weights as $status => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $status;
            }
        }

        return 0; // default to pending
    }
    private function getRandomComment(): string
    {
        $comments = [
            'Great service, items arrived on time!',
            'Products were as described, very satisfied.',
            'Delivery was quick and professional.',
            'Good quality items, will order again.',
            'Packaging could be better but overall good.',
            'Excellent customer service.',
            'Items were exactly what I expected.',
            'Fast delivery, good communication.',
            'Very happy with my purchase.',
            'Would recommend to friends and family.'
        ];

        return $comments[array_rand($comments)];
    }
}
