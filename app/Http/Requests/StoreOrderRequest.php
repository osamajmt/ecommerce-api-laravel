<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address_id' => 'required|exists:addresses,id',
            'type' => 'required|in:0,1',
            'payment_method' => 'required',
            'total_price' => 'required|numeric',
            'delivery_price' => 'required|numeric',
            'coupon_id' => 'nullable|exists:coupons,id'
        ];
    }
}
