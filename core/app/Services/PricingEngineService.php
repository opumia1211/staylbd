<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

/**
 * Pricing Strategy Engine: handles demand-based pricing, flash sales, and time-based rules.
 */
class PricingEngineService
{
    /**
     * Calculate dynamic price for a product based on demand (views) and stock.
     */
    public function getDynamicPrice(Product $product): float
    {
        $basePrice = $product->price;
        $views = $product->views ?? 0;
        $stock = $product->quantity ?? 0;

        // Demand-based increase: if views are high (>1000) and stock is low (<10)
        $surgeFactor = 1.0;
        if ($views > 1000 && $stock < 10 && $stock > 0) {
            $surgeFactor = 1.10; // 10% surge pricing
        }

        // Time-based rules: Night owl discount (1 AM - 5 AM)
        $hour = now()->hour;
        if ($hour >= 1 && $hour <= 5) {
            $surgeFactor *= 0.95; // 5% discount
        }

        return round($basePrice * $surgeFactor, 2);
    }

    /**
     * Check if a flash sale is active for the product.
     */
    public function checkFlashSale(Product $product): ?float
    {
        // Logic to check if product is part of an active OfferTimer or Flash sale
        // This is a placeholder for the integrated offer system
        return $product->offer_price > 0 ? $product->offer_price : null;
    }
}
