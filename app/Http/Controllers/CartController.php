<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\CartItemCountRequest;
use App\Http\Requests\DecreaseCartRequest;
use App\Http\Requests\RemoveFromCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Item;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function getCart(Request $request)
    {
         $user = auth()->user();
        $cartItems = Cart::where('user_id', $user->id)
            ->where('status', 0)
            ->with('item')
            ->get();

        $totalPrice = 0;
        $itemsCount = 0;

        foreach ($cartItems as $cartItem) {
            $price = $cartItem->item->discount > 0
                ? $cartItem->item->price - ($cartItem->item->price * $cartItem->item->discount / 100)
                : $cartItem->item->price;

            $totalPrice += $price * $cartItem->count;
            $itemsCount += $cartItem->count;
        }
            return response()->json([
                'status' => 'success',
                'data' => [
                'items' => CartResource::collection($cartItems),
                'total_count' => $itemsCount,
                'total_price' => round($totalPrice, 2),
                'shipping_cost' => $itemsCount * 10,
            ]
        ]);
    }

    public function addToCart(AddToCartRequest $request)
    {
         $user = auth()->user();

        $cartItem = Cart::where('user_id', $user->id)
            ->where('item_id', $request->item_id)
            ->where('status', 0)
            ->first();

        if ($cartItem) {
            $cartItem->increment('count');
        } else {
            Cart::create([
                'user_id' =>$user->id,
                'item_id' => $request->item_id,
                'count' => 1,
                'status' => 0
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cart updated successfully'
        ], 200);
    }



    public function removeFromCart(RemoveFromCartRequest $request)
    {
            $user = auth()->user();
            Cart::where('user_id', $user->id)
            ->where('item_id', $request->item_id)
            ->where('status', 0)
            ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item removed from cart'
            ]);
    }

    public function decreaseCount(DecreaseCartRequest $request)
    {
        $user = auth()->user();
        $cartItem = Cart::where('user_id', $user->id)
            ->where('item_id', $request->item_id)
            ->where('status', 0)
            ->firstOrFail();


        if ($cartItem->count > 1) {
            $cartItem->decrement('count');
        } else {
            $cartItem->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cart updated'
        ]);
    }


    public function cartItemCount($id){
         $userId = auth()->id();
          $cartItem = Cart::where('user_id', $userId)
            ->where('item_id', $id)
            ->where('status', 0)
            ->first();

        return response()->json([
            'status' => 'success',
            'itemCount' => $cartItem?->count ?? 0
        ]);
    }

    public function clearCart(Request $request)
    {
            $user = auth()->user();
            $deleted = Cart::where('user_id', $user->id)
            ->where('status', 0)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart cleared',
            'deleted_count' => $deleted
        ]);
    }
}


