<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class HomeProductController extends Controller
{
    public function letestproducts()
    {
        $product = Product::with(['category', 'brand', 'images'])
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();
        return response()->json([
            'status' => 200,
            'message' => 'Latest Products',
            'products' => $product
        ]);
    }
    public function featuredproducts()
    {
        $product = Product::with(['category', 'brand', 'images'])
            ->where('status', 1)
            ->where('featured', 'yes')
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Featured Products',
            'products' => $product
        ]);
    }

    public function getProduct(Request $request)
    {
        $productQuery = Product::with('category', 'brand', 'images', 'sizes')
            ->where('status', 1);

        if ($request->filled('category')) {
            $catIds = explode(',', $request->category);
            $productQuery->whereIn('category_id', $catIds);
        }

        if ($request->filled('brand')) {
            $brandIds = explode(',', $request->brand);
            $productQuery->whereIn('brand_id', $brandIds);
        }

        $products = $productQuery->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'No products found for this filter.',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'data' => $products
        ]);
    }
    public function showProduct($id)
    {
        $product = Product::with(['category', 'brand', 'images', 'sizes'])
            ->where('status', 1)
            ->find($id);

        if ($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $product
        ]);


    }
}
