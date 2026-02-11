<?php

namespace App\Http\Controllers;

use App\Models\coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function Symfony\Component\Clock\now;

class CouponController extends Controller
{


public function checkCoupon(Request $request)
{
    $request->validate([
        'coupon_name' => 'required|string'
    ]);

    $coupon = Coupon::where('name', $request->coupon_name)
        ->where('count', '>', 0)
        ->first();


    if (!$coupon) {
        return response()->json([
            'status' => 'failure',
            'message' => 'Invalid coupon'
        ], 404);
    }

    if (Carbon::parse($coupon->expire_date)->isPast()) {
        return response()->json([
            'status' => 'failure',
            'message' => 'Coupon expired'
        ], 400);
    }


    return response()->json([
        'status' => 'success',
        'data' => $coupon,
        'message' => 'Coupon applied successfully',
    ], 200);
}

}
