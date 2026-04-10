<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;

/**
 * Monetization Service: manages revenue streams, vendor commissions, and paid promotions.
 */
class MonetizationService
{
    /**
     * Calculate commission for a sale (for multi-vendor preparedness).
     */
    public function calculateCommission(float $totalAmount, float $rate = 10.0): float
    {
        return ($totalAmount * $rate) / 100;
    }

    /**
     * Mark a product for Featured Placement (Paid Promotion).
     */
    public function promoteProduct(int $productId, int $days = 7)
    {
        $product = Product::findOrFail($productId);
        $product->is_promoted = 1;
        $product->promotion_expires_at = now()->addDays($days);
        $product->save();
        
        return $product;
    }

    /**
     * Get Daily Profit (Revenue minus estimated COGS and expenses).
     */
    public function getEstimatedProfit(\DateTime $date): float
    {
        $revenue = Order::whereDate('created_at', $date)->sum('total_amount');
        // Assuming 60% COGS + OpEx for estimation
        return $revenue * 0.40; 
    }

    /**
     * Generate ROI report for a specific promotion.
     */
    public function getPromotionROI(int $productId): array
    {
        $sales = OrderDetail::where('product_id', $productId)
            ->whereHas('order', fn($q) => $q->where('created_at', '>', now()->subWeek()))
            ->sum('price');
            
        $cost = 50.0; // Assume flat cost for 1 week promotion
        
        return [
            'total_sales' => $sales,
            'cost' => $cost,
            'roi_percent' => $cost > 0 ? (($sales - $cost) / $cost) * 100 : 0
        ];
    }
}
