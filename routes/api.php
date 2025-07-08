<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\front\CategoryBrandsController;
use App\Http\Controllers\front\HomeProductController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\TempImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/admin/login', [AuthController::class, 'authenticat']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/user', function (Request $request) {

        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'role' => $request->user()->role,

        ]);
    });



    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    });

    Route::get('/categorys', [CategoryController::class, 'index']);
    Route::get('/categorys/{id}', [CategoryController::class, 'show']);
    Route::put('/categorys/{id}', [CategoryController::class, 'update']);
    Route::delete('/categorys/{id}', [CategoryController::class, 'destroy']);
    Route::post('/categorys', [CategoryController::class, 'store']);

    Route::resource('/brands', BrandsController::class);

    Route::get('/sizes', [SizeController::class, 'index']);
    Route::resource('/products', ProductController::class);

    Route::delete('/image/{id}', [ImageController::class, 'destroy']);
});

Route::get('leatest-products', [HomeProductController::class, 'letestproducts']);
Route::get('featured-products', [HomeProductController::class, 'featuredproducts']);

Route::get('/shop/category', [CategoryBrandsController::class, 'category']);
Route::get('/shop/brand', [CategoryBrandsController::class, 'brands']);
Route::get('/get-product', [HomeProductController::class, 'getProduct']);
Route::get('/get-product/{id}', [HomeProductController::class, 'showProduct']);
Route::get('/get-cart-items', [HomeProductController::class, 'getCartItems']);


