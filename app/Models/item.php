<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;
      protected $fillable = [
        'category_id', 'name', 'name_ar', 'desc', 'desc_ar',
        'price', 'count', 'discount', 'is_active', 'image'
    ];
     public function category()   {
        return $this->belongsTo(Category::class);
    }
    function favorites(){
        return $this->hasMany(favorite::class);
    }
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id');
    }
}
