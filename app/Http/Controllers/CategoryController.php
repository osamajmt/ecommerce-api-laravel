<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index(){
        return response()->json(['categories' => CategoryResource::collection(Category::all()),
            'status'=>"success",],200);
    }
     public function store(StoreCategoryRequest $request)
    {
        try {
            if($request->image)
            {
             $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = time().'.'.$extension;
             $request->image->move(public_path('images/categories'), $imageName);

              $category = category::create([
                ...$request->validated(),
                // 'image' => "images/categories/".$imageName
                'image' => $imageName
            ]);
               return response()->json([
                'status' => 'success',
                'message' => 'Category added to the app successfully',
                'category' => new CategoryResource($category)
            ], 201);
            }
              $category = category::create([
                ...$request->validated(),
                'image' => null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category added to the app successfully',
                'category' => new CategoryResource($category)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add Category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
     public function update(UpdateCategoryRequest $request,$id)
    {
        try {
           $category = category::find($id);

            if (!$category) {
                return response()->json(['status' => 'error', 'message' => 'Not found']);
            }

            $category->update([
               $request->validated()
            ]);

              if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = time().'.'.$extension;
             $request->image->move(public_path('images/categories'), $imageName);
             $category->image = "images/categories/".$imageName;
            }

            $category->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Category Edited  successfully',
                 'category' => new CategoryResource($category)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to Edit Category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
              $category = category::find($id);


            if ($category) {
                $category->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Category removed from the app successfully'
                ],200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found '
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove Category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
