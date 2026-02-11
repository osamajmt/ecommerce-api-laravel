<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    //
        use HasApiTokens, Notifiable;
     protected $fillable = [
        'user_name',
        'email',
        'phone_number',
        'password',
        'verify_code',
        'approval',
        'date_created',
        'fcm_token',
         'role'
    ];
     protected $hidden = [
        'password',
        'verify_code',
        'remember_token',
    ];
     function favorites(){
        return $this->hasMany(favorite::class);
    }
     function orders(){
        return $this->hasMany(order::class);
    }
    public function favoriteItems()
{
    return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id');
}
}




// <?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class User extends Model
// {
//     //
//      protected $fillable = [
//         'user_name',
//         'email',
//         'phone_number',
//         'password',
//         'verify_code',
//         'approval',
//         'date_created',
//          'fcm_token',
//          'role'
//     ];
//      function favorites(){
//         return $this->hasMany(favorite::class);
//     }
//      function orders(){
//         return $this->hasMany(order::class);
//     }
//     public function favoriteItems()
// {
//     return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id');
// }
// }
