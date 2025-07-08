<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategoryBrandsController extends Controller
{
      public function category()
    {
       
        $categories = Categorie::where("status", 1)->get();

        if ($categories->isEmpty()) {
            return response()->json([
                'status' => 404, 
                'message' => 'No categories found for this user.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $categories
        ]);
    }

      public function brands()
    {
        $brands = Brand::where("status", 1 )->get();

        if ($brands->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No brands found for this user.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $brands
        ]);
    }

    
}
