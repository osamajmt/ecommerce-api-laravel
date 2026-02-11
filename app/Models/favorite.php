<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class favorite extends Model
{
    //
     protected $fillable = [
        'user_id',
        'item_id',
    ];
    function user(){
        return $this->belongsTo(user::class);
    }
    function items(){
        return $this->belongsTo(item::class);
    }
}
