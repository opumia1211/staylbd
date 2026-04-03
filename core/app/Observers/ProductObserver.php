<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\ProductCacheService;
use App\Services\RestockNotificationService;

/**
 * Auto-invalidates product caches when Product is created/updated/deleted.
 * When quantity goes from 0 to >0, notifies users who have product in cart/wishlist/compare.
 */
class ProductObserver
{
    public function created(Product $product): void
    {
        $this->invalidate($product);
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
    }

    public function deleted(Product $product): void
    {
        $this->invalidate($product);
    }

    protected function invalidate(Product $product): void
    {
        ProductCacheService::clearProductDetail($product->id);
        ProductCacheService::clearProductListings();
    }
}
