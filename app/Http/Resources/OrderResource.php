<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'total_price' => $this->total_price,
            'delivery_price' => $this->delivery_price,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'type' => $this->type,
            'rating' => $this->rating,
            'created_at' => $this->created_at,
        ];
    }
}
