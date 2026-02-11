<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderActionRequest;
use App\Http\Resources\OrderResource;
use App\Models\order;
use App\Models\User;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    //
    public function available()
    {
     $orders = Order::where('type', 1)
        ->where('status', 2) // ready
        ->whereNull('delivery_id')
        ->get();
        return response()->json([
            'status' => 'success',
            'orders' => OrderResource::collection($orders)
        ]);
    }
    public function accept($id)
            {
            $deliveryId = auth()->id();
            $order = Order::where('id', $id)
                        ->where('status', 2) // ready for delivery
                        ->first();

            if (!$order) {
                return response()->json(['error' => 'Order not available'], 400);
            }
            if ($order->type !=1) {   /// delivery
                return response()->json(['error' => 'This order is not delivery'], 400);
            }
            $order->update([
                'delivery_id' =>  $deliveryId,
                'status' => 3 // assigned
            ]);
            $user = User::find($order->user_id);
                        if ($user && $user->fcm_token) {
                        NotificationController::sendFirebaseNotification(
                            $user->fcm_token,
                            'Order Update 📦',
                            "Your order #{$order->id} is beaing prepared",
                            'order_details'
                        );
                        }
            return response()->json(['status' => 'success',
            'Your order' => $order]);
            }

    public function current()
    {
          $deliveryId = auth()->id();


       $orders = Order::where('delivery_id', $deliveryId)
                        ->whereIn('status', [3, 4])
                        ->get();

        return response()->json(['status' => 'success',
            'orders' => OrderResource::collection($orders)]);
    }

    public function startDelivery($id)
    {

        $deliveryId = auth()->id();

         $order = Order::where('id', $id)
            ->where('delivery_id', $deliveryId)
            ->where('status', 3)
            ->first();


        if (!$order) {
            return response()->json([
                'error' => 'Not your order or invalid status'
            ], 403);
        }

        if ($order->type != 1) {
            return response()->json([
                'error' => 'This is not a delivery order'
            ], 400);
        }


        $order->update(['status' => 4]); ////on the way

        $user = User::find($order->user_id);
                    if ($user && $user->fcm_token) {
                    NotificationController::sendFirebaseNotification(
                        $user->fcm_token,
                        'Order Update 📦',
                        "Your order #{$order->id} is on the way",
                        'order_details'
                    );
                    }
        return response()->json(['status' => 'success', 'message' => 'Delivery started']);
    }

    public function complete($id)
    {
        $deliveryId = auth()->id();

         $order = Order::where('id', $id)
            ->where('delivery_id', $deliveryId)
            ->where('status', 4)
            ->first();

        if (!$order) return response()->json(['error' => 'Invalid order'], 400);

        if ($order->type == 1 ) { // delivery
        $order->update(['status' => 5]); // delivered
        $user = User::find($order->user_id);
                if ($user && $user->fcm_token) {
                NotificationController::sendFirebaseNotification(
                    $user->fcm_token,
                    'Order Update 📦',
                    "Your order #{$order->id} has been completed.",
                    'order_details'
                  );
                  }
        } else {
            $order->update(['status' => 5]); // completed
             $user = User::find($order->user_id);
                if ($user && $user->fcm_token) {
                NotificationController::sendFirebaseNotification(
                    $user->fcm_token,
                    'Order Update 📦',
                    "Your order #{$order->id} has been completed, please visit us to recieve it .",
                    'order_details'
                  );
                  }
        }
       return response()->json(['message' => 'Order completed']);
    }


}
