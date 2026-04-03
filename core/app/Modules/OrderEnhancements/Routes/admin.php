<?php

use Illuminate\Support\Facades\Route;

Route::post('order/details/{orderId}/enhancements', [\App\Modules\OrderEnhancements\Http\Controllers\OrderEnhancementsController::class, 'update'])
    ->name('orders.enhancements.update');
