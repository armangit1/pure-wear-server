<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category', 'brand', 'images', 'sizes')->get();
        return response()->json([
            'status' => 200,
            'data' => $products
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required',
            'status' => 'required',
            'image.*' => 'image|mimes:jpg,jpeg,png',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        if (!$request->hasFile('image')) {
            return response()->json([
                'status' => 400,
                'message' => 'No images provided.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $product = new Product();
            $product->title = $request->title;
            $product->ex_price = $request->ex_price;
            $product->description = $request->description;
            $product->short_des = $request->short_des;
            $product->price = $request->price;
            $product->qty = $request->qty;
            $product->user_id = Auth::id();
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->sku = $request->sku;
            $product->status = $request->status;
            $product->barcode = $request->barcode;
            $product->featured = $request->featured ?? 'no';
            $product->save();

            $images = [];

            foreach ($request->file('image') as $imageFile) {
                $path = $imageFile->store('products', 'public');
                $productImage = new Image();
                $productImage->product_id = $product->id;
                $productImage->image = $path;
                $productImage->user_id = Auth::id();
                $productImage->save();

                $images[] = $productImage;
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Product added successfully.',
                'data' => [
                    'product' => $product,
                    'images' => $images
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Display the specified resource.
     */
  public function show(string $id)
{
    $product = Product::with('category', 'brand', 'images', 'sizes')->find($id);

    if ($product == null) {
        return response()->json([
            'status' => 400,
            'message' => 'Product not found.',
            'data' => []
        ], 400);
    }

    if ($product->user_id != Auth::id()) {
        return response()->json([
            'status' => 403,
            'message' => 'You do not have permission to view this product.',
        ], 403);
    }

    // ✅ Fix this line
    $productSizes = $product->sizes->pluck('id');

    return response()->json([
        'status' => 200,
        'public_path' => asset("storage/"),
        'data' => $product,
        'sizeIds' => $productSizes
    ], 200);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $product = Product::find($id);
        if ($product == null) {
            return response()->json([
                'status' => 400,
                'message' => 'Product not found.',
                'data' => []
            ], 400);
        }

        if ($product->user_id != Auth::id()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to update this product.',
            ], 403);
        }


        $imageExists = Image::where('product_id', $product->id)->exists();
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required',
            'status' => 'required',
        ];

        if (!$imageExists) {
            $rules['image'] = 'required';
            $rules['image.*'] = 'image|mimes:jpg,jpeg,png';
        } else {
            $rules['image.*'] = 'nullable|image|mimes:jpg,jpeg,png';
        }

        $validator = Validator::make($request->all(), $rules);





        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $product->title = $request->title;
        $product->ex_price = $request->ex_price;
        $product->description = $request->description;
        $product->short_des = $request->short_des;
        $product->price = $request->price;
        $product->qty = $request->qty;
        $product->user_id = Auth::id();
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->sku = $request->sku;
        $product->status = $request->status;
        $product->barcode = $request->barcode;
        $product->featured = $request->featured ?? 'no';





        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Store new images
            foreach ($request->file('image') as $imageFile) {
                $path = $imageFile->store('products', 'public');
                $productImage = new Image();
                $productImage->product_id = $product->id;
                $productImage->image = $path;
                $productImage->user_id = Auth::id();
                $productImage->save();
            }
        }

        // Delete existing sizes
        $sizedelete =  ProductSize::where('product_id', $product->id)->delete();

        if ($sizedelete === false) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete existing sizes.',
            ], 500);
        }


        if ($request->has('sizes') && is_array($request->sizes)) {
            foreach ($request->sizes as $size) {
                $productSize = new ProductSize();
                $productSize->product_id = $product->id;
                $productSize->size_id = $size;
                $productSize->user_id = Auth::id();
                $productSize->save();
            }
        }

        // Save the product
        $product->save();


        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully.',
            'data' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if ($product == null) {
            return response()->json([
                'status' => 400,
                'message' => 'Product not found.',
                'data' => []
            ], 400);
        }

        if ($product->user_id != Auth::id()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to delete this product.',
            ], 403);
        }

        // Delete associated images
        $images = Image::where('product_id', $product->id)->get();

        foreach ($images as $image) {

            $imagePath = storage_path('app/public/' . $image->image);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $image->delete();
        }


        $product->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully.',
        ], 200);
    }



}
