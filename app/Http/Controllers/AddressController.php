<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    //
         public function view(Request $request) {
        $user = auth()->user();
         $addresses = Address::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'addresses' => AddressResource::collection($addresses),
        ]);

    }
         public function store(StoreAddressRequest $request) {
             $user = auth()->user();
            $address = Address::create([
            'user_id' => $user->id,
            ...$request->validated()
             ]);

            return response()->json([
            'message' => 'Address registered successfully.',
            'status'=>"success",
            'user' => new AddressResource($address),
            ],201);
    }
         public function update(UpdateAddressRequest $request,$id) {
              $address = address::findOrFail($id);
            if ($address){
              $address->update($request->validated());

            return response()->json([
            'message' => 'Address updated successfully.',
            'status'=>"success",
            'address' => new AddressResource($address),
            ]);
            }
            return response()->json([
            'message' => 'Address not found.',
            'status'=>"failure",
            ]);
    }
         public function remove($id) {
            $address = address::findOrFail($id);
            if ($address){
            $address->delete();
            return response()->json([
            'message' => 'Address deleted successfully.',
            'status'=>"success",
            ]);
            }
            return response()->json([
            'message' => 'Address not found.',
            'status'=>"failure",
            ]);
    }


}

