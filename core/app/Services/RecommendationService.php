<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * AI-Powered Recommendation Engine: frequently bought together, you may also like, recently viewed.
 * Uses purchase history and activity logs for high-precision suggestions.
 */
class RecommendationService
{
    /**
     * "Frequently Bought Together" for a product.
     * Logic: Find orders containing this product, then find other products in those same orders.
     */
    public function frequentlyBoughtTogether(int $productId, int $limit = 4)
    {
        $cacheKey = 'recommend:bought_together:' . $productId;
        return Cache::remember($cacheKey, 3600, function () use ($productId, $limit) {
            $orderIds = OrderDetail::where('product_id', $productId)->pluck('order_id');

            if ($orderIds->isEmpty()) {
                return collect();
            }

            $suggestedIds = OrderDetail::whereIn('order_id', $orderIds)
                ->where('product_id', '!=', $productId)
                ->select('product_id', DB::raw('count(*) as count'))
                ->groupBy('product_id')
                ->orderByDesc('count')
                ->limit($limit)
                ->pluck('product_id');

            return Product::available()
                ->whereIn('id', $suggestedIds)
                ->with(['category', 'brand'])
                ->get();
        });
    }

    /**
     * "You May Also Like" - Similar products based on category, brand, and user viewing patterns.
     */
    public function youMayAlsoLike(Product $product, int $limit = 8)
    {
        $cacheKey = 'recommend:similar:' . $product->id;
        return Cache::remember($cacheKey, 3600, function () use ($product, $limit) {
            // Find products in same category or brand
            $query = Product::available()
                ->where('id', '!=', $product->id)
                ->where(function($q) use ($product) {
                    $q->where('category_id', $product->category_id)
                      ->orWhere('brand_id', $product->brand_id);
                });

            // Also include products that users viewed along with this one (Collaborative Filtering Lite)
            $usersWhoViewed = UserActivityLog::where('action_type', UserActivityLog::PRODUCT_VIEW)
                ->where('model_type', 'product')
                ->where('model_id', $product->id)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique();

            if ($usersWhoViewed->isNotEmpty()) {
                $otherViewedIds = UserActivityLog::whereIn('user_id', $usersWhoViewed)
                    ->where('action_type', UserActivityLog::PRODUCT_VIEW)
                    ->where('model_type', 'product')
                    ->where('model_id', '!=', $product->id)
                    ->select('model_id', DB::raw('count(*) as count'))
                    ->groupBy('model_id')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->pluck('model_id');
                
                if ($otherViewedIds->isNotEmpty()) {
                    $query->orWhereIn('id', $otherViewedIds);
                }
            }

            return $query->with(['category', 'brand'])
                ->orderByDesc('sale_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Personal Recommendations for a specific user.
     * Logic: Based on their past purchases and view history.
     */
    public function personalRecommendations($userId, int $limit = 12)
    {
        if (!$userId) return collect();

        $cacheKey = 'recommend:personal:' . $userId;
        return Cache::remember($cacheKey, 1800, function () use ($userId, $limit) {
            // 1. Get categories of past purchases
            $purchasedCategoryIds = OrderDetail::whereHas('order', fn($q) => $q->where('user_id', $userId))
                ->with('product')
                ->get()
                ->pluck('product.category_id')
                ->filter()
                ->unique();

            // 2. Get viewed categories
            $viewedCategoryIds = UserActivityLog::where('user_id', $userId)
                ->where('action_type', UserActivityLog::PRODUCT_VIEW)
                ->where('model_type', 'product')
                ->with('product')
                ->get()
                ->pluck('product.category_id')
                ->filter()
                ->unique();

            $relevantCategories = $purchasedCategoryIds->concat($viewedCategoryIds)->unique();

            return Product::available()
                ->when($relevantCategories->isNotEmpty(), fn($q) => $q->whereIn('category_id', $relevantCategories))
                ->orderByRaw('COALESCE(sale_count, 0) DESC, avg_rate DESC')
                ->limit($limit)
                ->get();
        });
    }
}
