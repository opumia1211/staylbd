<?php

namespace App\Http\Controllers\Storefront;

use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

/**
 * JSON snapshot for storefront polling fallback when WebSockets are down.
 */
class RealtimePollController extends Controller
{
    public function product(int $id): JsonResponse
    {
        $product = Product::query()->with(['activeVariants'])->find($id);

        if (! $product) {
            return response()->json(['ok' => false, 'error' => 'not_found'], 404);
        }

        $stillAvailable = Product::available()->whereKey($id)->exists();

        $payload = $stillAvailable
            ? (new ProductUpdated($product, 'updated'))->broadcastWith()
            : (new ProductUpdated($product, 'deleted'))->broadcastWith();

        return response()
            ->json($payload)
            ->header('Cache-Control', 'no-store, private');
    }
}
