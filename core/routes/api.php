<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes – StayLBD (Mobile / Headless / Third-party)
|--------------------------------------------------------------------------
|
| Public endpoints (no auth): products, categories, product detail.
| Cart & checkout require authentication (Bearer token via Laravel Sanctum).
|
*/

// Public: Products
Route::get('products', [ApiProductController::class, 'index'])->name('api.products.index');
Route::get('products/featured', [ApiProductController::class, 'featured'])->name('api.products.featured');
Route::get('products/today-deals', [ApiProductController::class, 'todayDeals'])->name('api.products.today_deals');
Route::get('products/{id}', [ApiProductController::class, 'show'])->name('api.products.show')->whereNumber('id');

// Public: Categories
Route::get('categories', [ApiCategoryController::class, 'index'])->name('api.categories.index');
Route::get('categories/{id}/products', [ApiCategoryController::class, 'products'])->name('api.categories.products')->whereNumber('id');

// Cart (auth required for persistent cart; use Sanctum token for mobile/app)
Route::middleware('auth:sanctum')->prefix('cart')->name('api.cart.')->group(function () {
    Route::get('/', [ApiCartController::class, 'index'])->name('index');
    Route::post('/', [ApiCartController::class, 'store'])->name('store');
    Route::put('/', [ApiCartController::class, 'update'])->name('update');
    Route::delete('/', [ApiCartController::class, 'destroy'])->name('destroy');
    Route::get('count', [ApiCartController::class, 'count'])->name('count');
    Route::post('coupon', [ApiCartController::class, 'applyCoupon'])->name('coupon');
});

// Health check for monitoring / CI
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'env' => config('app.env'),
    ]);
})->name('api.health');
