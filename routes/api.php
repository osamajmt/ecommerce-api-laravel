
<?php
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


//Auth
Route::post('/register', [AuthController::class,'register']);
Route::post('customer/login', [AuthController::class,'customerLogin']);
Route::post('delivery/login', [AuthController::class,'deliveryLogin']);
Route::post('admin/login', [AuthController::class,'adminLogin']);
Route::post('/verifyEmail', [AuthController::class, 'verifyEmail']);
Route::post('/sendCode', [AuthController::class, 'sendVerifyCode']);
Route::patch('/resetPassword', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);
    Route::post('/image/update', [AuthController::class,'updateImage']);
    // notifications
    Route::post('/save-fcm-token', [NotificationController::class, 'saveToken']);
    Route::get('/notifications', [NotificationController::class, 'index']);

});


///////
//Categories
Route::get('/categories',[CategoryController::class,'index']);


/////////

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/items/favorites',[ItemController::class,'FavItems']);
    Route::post('/items/toggle',[ItemController::class,'toggleFavorite']);

    // Cart
    Route::get('/cart', [CartController::class,'getCart']);
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::delete('/cart/remove', [CartController::class, 'removeFromCart']);
    Route::post('/cart/decrease', [CartController::class, 'decreaseCount']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);
    Route::get('/cart/count/{item}', [CartController::class, 'cartItemCount']);
    //coupon
    Route::post('/coupon', [CouponController::class, 'checkCoupon']);


    //addresses
    Route::get('addresses',[AddressController::class,'view']);
    Route::post('address',[AddressController::class,'store']);
    Route::patch('address/{address}',[AddressController::class,'update']);
    Route::delete('address/{address}',[AddressController::class,'remove']);

    //orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/pending', [OrderController::class, 'PendingOrders']);
    Route::get('/orders/archive', [OrderController::class, 'userArchivedOrders']);
    Route::delete('/orders/{order}', [OrderController::class, 'remove']);
    Route::get('/orders/{order}', [OrderController::class, 'orderDetails']);
    Route::post('/orders/rate/{order}', [OrderController::class, 'rate']);

    //items
    Route::get('/items',[ItemController::class,'index']);
    Route::get('/items/{category}',[ItemController::class,'items']);
    Route::post('/items/discount',[ItemController::class,'discountItems']);
    Route::post('/items/search',[ItemController::class,'search']);
    Route::post('/items/offers/search',[ItemController::class,'searchOffers']);
    Route::post('/items/topSelling',[ItemController::class,'topSellingItems']);
});


//settings
Route::get('/settings', [SettingController::class, 'index']);


///////////////////////////////
///////// Delivery Application
Route::middleware(['auth:sanctum','delivery'])->prefix('delivery')->group(function () {
    Route::get('orders/available', [DeliveryController::class, 'available']);
    Route::post('orders/accept/{order}', [DeliveryController::class, 'accept']);
    Route::post('orders/start/{order}', [DeliveryController::class, 'startDelivery']);
    Route::post('orders/current', [DeliveryController::class, 'current']);
    Route::post('orders/complete/{order}', [DeliveryController::class, 'complete']);

});



// 0 => pending
// 1 => approved (being prepared)
// 2 => ready (for pickup) — when type = 0 (receive-from-store order)
// 2 => ready (for delivery) — when type = 1
// 3 => assigned (delivery worker accepted) — when type = 1 (delivery order)
// 4 => on the way (delivery order)
// 5 => delivered or completed
// 6 => cancelled



///////////////////////////////////////////////////////////////////////////////////////
///// admin

Route::middleware(['auth:sanctum','admin'])->prefix('admin')->group(function () {
 // Categories
Route::get('categories', [CategoryController::class, 'index']);
Route::post('categories', [CategoryController::class, 'store']);
Route::patch('categories/{category}', [CategoryController::class, 'update']);
Route::delete('categories/{category}', [CategoryController::class, 'delete']);

// Items
Route::get('items', [ItemController::class, 'index']);
Route::post('items', [ItemController::class, 'store']);
Route::post('items/edit/{item}', [ItemController::class, 'update']);
Route::delete('items/{item}', [ItemController::class, 'delete']);


// Orders
 Route::get('orders/archive', [OrderController::class, 'archive']);
 Route::get('orders/pending', [OrderController::class, 'AllPendingOrders']);
 Route::post('orders/approve/{order}', [OrderController::class, 'approve']);
 Route::get('orders/beingPrepared', [OrderController::class, 'beingPreparedOrders']);
 Route::post('orders/finish/{order}', [OrderController::class, 'finish']);
 Route::post('orders/moveToDelivery/{order}', [OrderController::class, 'moveToDelivery']);
 Route::post('orders/complete/{order}', [OrderController::class, 'complete']);

});








