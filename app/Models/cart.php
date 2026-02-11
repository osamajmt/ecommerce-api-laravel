<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
     protected $fillable = [
        'user_id',
        'item_id',
        'count',
        'order_id',
        'status'
    ];
    function user(){
        return $this->belongsTo(user::class);
    }
    function item(){
        return $this->belongsTo(item::class);
    }
}
