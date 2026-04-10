<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\ProductsRealtimeController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes – StayLBD (Mobile / Headless / Third-party)
|--------------------------------------------------------------------------
|
| API Versioning strategy (v1, v2, etc.) for scalability.
|
*/

Route::prefix('v1')->group(function () {

    // Public: Products
    Route::prefix('products')->name('api.v1.products.')->group(function() {
        Route::get('/', [ApiProductController::class, 'index'])->name('index');
        Route::get('featured', [ApiProductController::class, 'featured'])->name('featured');
        Route::get('today-deals', [ApiProductController::class, 'todayDeals'])->name('today_deals');
        Route::get('realtime', [ProductsRealtimeController::class, 'index'])
            ->middleware('throttle:realtime_poll')
            ->name('realtime');
        Route::get('{id}', [ApiProductController::class, 'show'])->name('show')->whereNumber('id');
    });

    // Public: Categories
    Route::prefix('categories')->name('api.v1.categories.')->group(function() {
        Route::get('/', [ApiCategoryController::class, 'index'])->name('index');
        Route::get('{id}/products', [ApiCategoryController::class, 'products'])->name('products')->whereNumber('id');
    });

    // Cart (auth:sanctum)
    Route::middleware('auth:sanctum')->prefix('cart')->name('api.v1.cart.')->group(function () {
        Route::get('/', [ApiCartController::class, 'index'])->name('index');
        Route::post('/', [ApiCartController::class, 'store'])->name('store');
        Route::put('/', [ApiCartController::class, 'update'])->name('update');
        Route::delete('/', [ApiCartController::class, 'destroy'])->name('destroy');
        Route::get('count', [ApiCartController::class, 'count'])->name('count');
        Route::post('coupon', [ApiCartController::class, 'applyCoupon'])->name('coupon');
    });

    // Health / Pulse endpoint
    Route::get('status', function() {
        return response()->json([
            'status' => 'online',
            'version' => '1.0.0',
            'environment' => config('app.env'),
            'timestamp' => now()->toIso8601String()
        ]);
    });

        // Behavioral & Growth Analytics
        Route::post('track/event', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'trackEvent']);

        // Mobile App: Customer Auth
        Route::prefix('auth')->group(function() {
            Route::post('login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
            Route::post('register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
        });

        // Protected Routes
        Route::middleware('auth:sanctum')->group(function() {
            Route::get('user', [\App\Http\Controllers\Api\V1\UserController::class, 'profile']);
            Route::get('orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'index']);
            Route::get('orders/{id}', [\App\Http\Controllers\Api\V1\OrderController::class, 'show']);
        });

        Route::get('products', [ApiProductController::class, 'products']);

});

// Legacy / Compatibility health check
Route::get('health', function () {
    return response()->json(['status' => 'ok']);
});

