<?php

namespace App\Http\Controllers;

use App\Http\Controllers\NotificationController;
use App\Http\Requests\OrderActionRequest;
 use App\Http\Requests\RateOrderRequest;
 use App\Http\Requests\StoreOrderRequest;
 use App\Http\Resources\OrderDetailsResource;
 use App\Http\Resources\OrderResource;
 use App\Models\cart;
use App\Models\coupon;
use App\Models\item;
 use App\Models\order;
use App\Models\User;
use Illuminate\Http\Request;
 use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //
    public function store(StoreOrderRequest $request)
    {


        DB::beginTransaction();

        try {

            $user = auth()->user();

            //  Get active cart items
            $carts = Cart::where('user_id', $user->id)
                ->where('status', 0)
                ->with('item')
                ->lockForUpdate()
                ->get();

            if ($carts->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cart is empty'
                ], 400);
            }

            //  Validate & decrease stock
            foreach ($carts as $cart) {
                $item = item::lockForUpdate()->find($cart->item_id);

                if ($item->count < $cart->count) {
                    throw new \Exception("Not enough stock for {$item->name}");
                }

                $item->decrement('count', $cart->count);
            }

            //  Handle coupon
            $couponId = $request->coupon_id == 0 ? null : $request->coupon_id;

            if ($couponId) {
                $coupon = Coupon::lockForUpdate()->find($couponId);
                if (!$coupon || $coupon->count <= 0) {
                    throw new \Exception("Invalid or expired coupon");
                }
                $coupon->decrement('count');
            }

            //  Create order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'coupon_id' => $couponId,
                'type' => $request->type,
                'total_price' => $request->total_price,
                'delivery_price' => $request->delivery_price,
                'payment_method' => $request->payment_method,
                'status' => 0,
            ]);

            //  Attach carts to order
            Cart::where('user_id', $user->id)
                ->where('status', 0)
                ->update([
                    'order_id' => $order->id,
                    'status' => 1,
                ]);

            DB::commit();

            //  Notification (after commit)
            if ($user->fcm_token) {
                NotificationController::sendFirebaseNotification(
                    $user->fcm_token,
                    'Order Submitted 📦',
                    "Your order #{$order->id} has been placed successfully!",
                    'order_details'
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'order' => new OrderResource($order)
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


        public function AllPendingOrders() {
            $orders = order::whereIn('status', [0])
            ->get();
            return response()->json([
                'status' => 'success',
                'message'=>'pending orders fetched successfully',
               'orders' => OrderResource::collection($orders)
            ]);
        }
        public function archive() {
         $orders = Order::where('status', 5)
        ->orWhere(function ($query) {
            $query->where('status', 2)
                  ->where('type', 0);
        })
        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'archived orders fetched successfully',
            'orders' => OrderResource::collection($orders)
        ]);
        }
        public function beingPreparedOrders() {
            $orders = order::whereIn('status', [1])
            ->get();
            return response()->json([
                'status' => 'success',
                'message'=>'beaing prepared orders fetched successfully',
                'orders' => OrderResource::collection($orders)
            ]);
        }
        public function PendingOrders() {
            $orders = Order::where('user_id', auth()->id())
            ->whereIn('status',[0,1,2,3])
            ->get();

            return response()->json([
                'status'=>'success',
                 'message'=>'pending orders fetched successfully',
                'orders' => OrderResource::collection($orders)
            ]);
        }
        public function userArchivedOrders() {
                $orders = Order::where('user_id', auth()->id())
                ->where('status',5)
                ->get();

            return response()->json([
                'status' => 'success',
                'message'=>'Archived orders fetched successfully',
                'orders' => OrderResource::collection($orders)
            ]);
        }

        public function completedOrders($userId) {
            $orders = order::where('user_id', $userId)
            ->where('status',4)
            ->get();

            return response()->json([
                'status' => 'success',
                'message'=>'Your completed orders fetched successfully',
                'orders' => OrderResource::collection($orders)
            ],200);
        }

      public function orderDetails($orderId) {
        $cartItems = cart::where('order_id', $orderId)->with('item')
        ->get();
        $totalPrice = 0;
            $orderData = $cartItems->map(function ($cartItem) use (&$totalPrice, &$itemsCount) {
                $item = $cartItem->item;
                $itemPrice=$item->price;
                // $totalPrice += ($itemPrice*$cartItem['count']) ?? 0;
                return [
                    'id' => $cartItem->id,
                    'item_id' => $cartItem->item_id,
                    'user_id' => $cartItem->user_id,
                    'name' => $item->name ?? 'Unknown Item',
                    'name_ar' => $item->name_ar ?? 'Unknown Item',
                    'price' => $itemPrice ?? 0,
                    'image' => $item->image ?? null,
                    'count' => $cartItem->count,
                    'created_at' => $cartItem->created_at,
                    'updated_at' => $cartItem->updated_at,
                ];
            });

            $order = Order::where('id',$orderId)
            ->with('address')
            ->firstOrFail();
             if (!$order) {
            $order=Order::where('id', $orderId)->first();
                if(!$order){
                        return response()->json([
                        'status' => 'error',
                        'message' => 'Order not found'
                        ], 404);
                }
            }
            $address=$order->address;
            return response()->json([
            'status' => 'success',
            'message'=>'order details fetched successfully',
            'data' => [
                'order_data'=>$orderData,
                'address'=>$address??null,
                'type'=>$order->type,
                'total_price'=>$order->total_price
            ]
        ],200);
    }


    public function remove(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
            ]);

            $order = Order::where('id',$request->order_id)
            ->where('user_id',auth()->id())
            ->where('status',0)
            ->firstOrFail();

             $order->delete();

                $user = User::find($order->user_id);
                if ($user && $user->fcm_token) {
                NotificationController::sendFirebaseNotification(
                    $user->fcm_token,
                    'Order Update 📦',
                    "Your order #{$order->id} has been deleted.",
                    'order_details'
                );
            }
            if ($order) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Order deleted  successfully'
                ],200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Order not found '
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function approve($id)
    {
        try {

            $order = order::where('id', $id)
            ->where('status', 0)
            ->firstOrFail();
             $order->update([
                'status'=>1
             ]);
                $user = User::find($order->user_id);
                if ($user && $user->fcm_token) {
                NotificationController::sendFirebaseNotification(
                    $user->fcm_token,
                    'Order Update 📦',
                    "Your order #{$order->id} has been approved, it's beaing prepared.",
                    'order_details'
                  );
                  }
                $users = User::where("role",'delivery')->get();
                foreach($users as $user){
                if ($user && $user->fcm_token) {
                NotificationController::sendFirebaseNotification(
                    $user->fcm_token,
                    'New Order 📦',
                    "New order {$order->id} needs delivery.",
                    'order_details'
                  );
                  }
                }

            return response()->json([
                    'status' => 'success',
                    'message' => 'Order approved successfully'
                ],200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to approve order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function finish($id)
    {
        try {

            $order = order::where('id', $id)
                        ->where('type', 0) // Pick-up
                        ->where('status', 1) //approved, being prepared
                        ->firstOrFail();
             $order->update([
                'status'=>2 //ready for pickup
             ]);
                $user = User::find($order->user_id);
                if ($user && $user->fcm_token) {
                NotificationController::sendFirebaseNotification(
                    $user->fcm_token,
                    'Order Update 📦',
                    "Your order #{$order->id} is ready to pick up.",
                    'order_details'
                  );
                  }

            return response()->json([
                    'status' => 'success',
                    'message' => 'Order is ready '
                ],200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to approve order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function moveToDelivery($id)
    {
        try {
            $order = order::where('id', $id)
                        ->where('type', 1) // Delivery
                        ->where('status', 1) //approved, being prepared
                        ->firstOrFail();
           if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found'
                ], 404);
            }
             $order->update([
                'status'=>2 //ready for Delivery
             ]);
                $user = User::find($order->user_id);
                if ($user && $user->fcm_token) {
                NotificationController::sendFirebaseNotification(
                    $user->fcm_token,
                    'Order Update 📦',
                    "Your order #{$order->id} is ready to pick up.",
                    'order_details'
                  );
                  }

            return response()->json([
                    'status' => 'success',
                    'message' => 'Order is ready '
                ],200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to approve order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function complete($id)
    {
        try {
            $order = order::where('id', $id)
                        ->where('type', 0) // Pick-up
                        ->where('status', 2) //finished
                        ->firstOrFail();

             $order->update([
                'status'=>5 //completed
             ]);


            return response()->json([
                    'status' => 'success',
                    'message' => 'Order completed successfully'
                ],200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to approve order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
     public function rate(RateOrderRequest $request,$id)
        {

        $order = Order::where('id',$id)
        ->where('user_id',auth()->id())
        ->firstOrFail();

        if (!$order) {
            return response()->json(['status' => 'failure',
            'message'=>"Order not found"],404);
        } else {
            $order->update([
                'rating' => $request->rating,
                'rating_comment' => $request->ratingComment,
            ]);
            $user = User::find($order->user_id);
                if ($user && $user->fcm_token) {
                    NotificationController::sendFirebaseNotification(
                        $user->fcm_token,
                        'Order Rating 📦',
                        "Your order #{$order->id} has been rated successfully.",
                        'order_details'
                    );
                }
        return response()->json(['status' => 'success',
        "message"=>"rated successfully"],200);
        }
        }
}

