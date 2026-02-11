<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Models\cart;
use App\Models\category;
use App\Models\item;
use App\Models\favorite;
use App\Models\User;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    //
    public function index(){
          $items = Item::join('categories', 'items.category_id', '=', 'categories.id')
                ->select('items.*', 'categories.name as category_name', 'categories.name_ar as category_name_ar')
                ->get();

        return response()->json([
            'status' => 'success',
            'items' => $items
        ]);
    }
    public function items($categoryId)
    {
         $user = auth()->user();
         $userId=$user->id;
        $items = Item::where('category_id', $categoryId)
        ->leftJoin('favorites', function ($join) use ($userId) {
            $join->on('items.id', '=', 'favorites.item_id')
                ->where('favorites.user_id', '=', $userId);
        })
        ->select(
            'items.*',
            \DB::raw('CASE WHEN favorites.id IS NULL THEN 0 ELSE 1 END as is_favorite')
        )->get();

        return response()->json([
            'status' => 'success',
            'items' => ItemResource::collection($items)
        ],200);
    }
     public function FavItems()
    {
         $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
    }

    return response()->json([
        'status' => 'success',
        'items' => $user->favoriteItems
    ]);
    }

     public function discountItems(Request $request){
       $userId=$request->userId;
       $items = Item::where('discount', '>',0)
        ->leftJoin('favorites', function ($join) use ($userId): void {
            $join->on('items.id', '=', 'favorites.item_id')
                ->where('favorites.user_id', '=', $userId);
        })
        ->select(
            'items.*',
            \DB::raw('CASE WHEN favorites.id IS NULL THEN 0 ELSE 1 END as is_favorite')
        )
        ->get();

          return response()->json([
            'status' => 'success',
            'items' => ItemResource::collection($items)
        ],200);
    }

public function topSellingItems(Request $request)
{
    $userId = auth()->id();

    $items = \DB::table('carts')
        ->join('items', 'carts.item_id', '=', 'items.id')
        ->leftJoin('favorites', function ($join) use ($userId) {
            $join->on('items.id', '=', 'favorites.item_id')
                 ->where('favorites.user_id', '=', $userId);
        })
        ->where('carts.order_id', '!=', 0)
        ->select(
            'items.id',
            'items.category_id',
            'items.name',
            'items.name_ar',
            'items.desc',
            'items.desc_ar',
            'items.price',
            'items.count',
            'items.discount',
            'items.is_active',
            'items.image',
            'items.created_at',
            'items.updated_at',
            \DB::raw('COUNT(carts.item_id) as total_sold'),
            \DB::raw('CASE WHEN favorites.id IS NULL THEN 0 ELSE 1 END as is_favorite')
        )
        ->groupBy(
            'items.id',
            'items.category_id',
            'items.name',
            'items.name_ar',
            'items.desc',
            'items.desc_ar',
            'items.price',
            'items.count',
            'items.discount',
            'items.is_active',
            'items.image',
            'items.created_at',
            'items.updated_at',
            'favorites.id'
        )
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();

    return response()->json([
        'status' => 'success',
        'items' => ItemResource::collection($items)
    ], 200);
}


    public function toggleFavorite(Request $request)
    {
   $userId = auth()->id();
    $itemId = $request->itemId;

    $favorite = favorite::where('user_id', $userId)->where('item_id', $itemId)->first();

    if ($favorite) {
        $favorite->delete();
        return response()->json(['status' => 'removed']);
    } else {
        favorite::create([
            'user_id' => $userId,
            'item_id' => $itemId,
        ]);
        return response()->json(['status' => 'added']);
    }
    }

    public function search(Request $request){
      $search=$request->search;
      $items=item::where('name','like',"%$search%")->get();
      if($items){
         return response()->json([
        'status' => 'success',
        'items' => $items
        ]);
      }
      return response()->json([
            'status' => 'error',
            'message' => 'items not found'
        ], 404);
    }
    public function searchOffers(Request $request){
      $search=$request->search;
      $items=item::where('name','like',"%$search%")->where('discount', '>',0)->get();
      if($items){
         return response()->json([
        'status' => 'success',
        'items' => $items
        ]);
      }
      return response()->json([
            'status' => 'error',
            'message' => 'items not found'
        ], 404);
    }



    ///////////
    //  Admin Functions
     public function store(Request $request)
    {
        try {
             $request->validate([
                'categoryName'=>'required',
                'name' => 'required',
                'nameAr' => 'required',
                'desc'=>'required',
                'descAr'=>'required',
                'price'=>'required',
                'count'=>'required',
                'image' => 'nullable|mimes:jpg,jpeg,png'
            ]);
            $category = category::where("name", $request->categoryName)->first();
             if($request->image)
            {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/items'), $imageName);

              $item = item::create([
                'category_id'=>$category->id,
                'name' => $request->name,
                'name_ar' => $request->nameAr,
                'desc'=>$request->desc,
                'desc_ar'=>$request->descAr,
                'price'=>$request->price,
                'count'=>$request->count,
                'discount'=>$request->discount,
                'image' =>$imageName,
                // 'image' => "images/items/".$imageName,
                'is_active'=>1,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Item added to the app successfully',
                'Item' => $item
            ], 201);
        }
          $item = item::create([
                'category_id'=>$category->id,
                'name' => $request->name,
                'name_ar' => $request->nameAr,
                'desc'=>$request->desc,
                'desc_ar'=>$request->descAr,
                'price'=>$request->price,
                'count'=>$request->count,
                'discount'=>$request->discount,
                'image' =>null,
                'is_active'=>1,

            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Item added to the app successfully',
                'Item' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add Item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
     public function update(Request $request,$id)
    {
        try {
           $item = item::find($id);

            if (!$item) {
                return response()->json(['status' => 'error', 'message' => 'Not found']);
            }
            $category=category::where('name',$request->categoryName)->first();
            if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }
            $isActive=$request->isActive=="0"?false:True;
            $item->update([
                'category_id'=>$category->id,
                'name' => $request->name,
                'name_ar' => $request->nameAr,
                'desc'=>$request->desc,
                'desc_ar'=>$request->descAr,
                'price'=>$request->price,
                'count'=>$request->count,
                'discount'=>$request->discount,
                'is_active'=>$isActive
            ]);

              if ($request->hasFile('image')) {
                $extension = $request->file('image')->getClientOriginalExtension();
                $imageName = time().'.'.$extension;
                $request->image->move(public_path('images/items'), $imageName);
                $item->image = "images/items/".$imageName;
            }

            $item->save();
            return response()->json([
                'status' => 'success',
                'Messsage' => 'Item Updated  successfully',
                'Item' => $item
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to Update Category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {

              $item = item::find($id);


            if ($item) {
                $item->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item removed from the app successfully'
                ],200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found '
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove Item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

