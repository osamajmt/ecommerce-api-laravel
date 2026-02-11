<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    //
    protected $fillable = [
        'id',
        'user_id',
        'address_id',
        'coupon_id',
        'type',
        'total_price',
        'delivery_id', 
        'delivery_price',
        'status',
        'payment_method',
        'date',
        'rating',
        'rating_comment'
    ];
     public function orderedBy()
    {
        return $this->belongsTo(User::class);
    }
       public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
       public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function coupon(): order
    {
        return $this->has(coupon::class);
    }
}
