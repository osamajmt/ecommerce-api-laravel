<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'desc' => $this->desc,
            'desc_ar' => $this->desc_ar,
            'price' => $this->price,
            'count' => $this->count,
            'discount' => $this->discount,
            'is_active' => $this->is_active,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_favorite' => $this->is_favorite ?? 0,
        ];
    }
}
