<?php

namespace App\Observers;

use App\Events\ProductUpdated;
use App\Models\Product;
use App\Services\ProductCacheService;
use App\Services\RestockNotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Auto-invalidates product caches when Product is created/updated/deleted.
 * When quantity goes from 0 to >0, notifies users who have product in cart/wishlist/compare.
 */
class ProductObserver
{
    public function created(Product $product): void
    {
        $this->invalidate($product);
        $this->broadcastProductChange($product, 'created');
    }

    public function updated(Product $product): void
    {
        $this->invalidate($product);
        $oldQty = (int) $product->getOriginal('quantity');
        $newQty = (int) $product->quantity;
        if ($oldQty === 0 && $newQty > 0) {
            try {
                RestockNotificationService::notifyUsersForRestock($product);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::channel('single')->warning('Restock notification failed', [
                    'product_id' => $product->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }
        $this->broadcastProductChange($product, 'updated');
    }

    public function deleted(Product $product): void
    {
        $this->invalidate($product);
        $this->broadcastProductChange($product, 'deleted');
    }

    protected function invalidate(Product $product): void
    {
        ProductCacheService::clearProductDetail($product->id);
        ProductCacheService::clearProductListings();
    }

    protected function broadcastProductChange(Product $product, string $action): void
    {
        if (config('broadcasting.default') === 'null') {
            return;
        }
        try {
            ProductUpdated::dispatch($product, $action);
        } catch (\Throwable $e) {
            Log::channel('single')->warning('Product broadcast dispatch failed', [
                'product_id' => $product->id,
                'action' => $action,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
