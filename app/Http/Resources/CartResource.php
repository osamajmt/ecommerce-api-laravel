<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $item = $this->item;

        $price = $item->discount > 0
            ? $item->price - ($item->price * $item->discount / 100)
            : $item->price;

        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'name' => $item->name,
            'name_ar' => $item->name_ar,
            'price' => round($price,2),
            'original_price' => $item->price,
            'discount' => $item->discount,
            'image' => $item->image,
            'count' => $this->count,
        ];
    }
}
