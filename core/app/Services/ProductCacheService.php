<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Central service for product-related cache keys.
 * Call clearAll() when product/category/brand/settings change - ensures user & admin pages show fresh data immediately.
 */
class ProductCacheService
{
    public const KEYS = [
        'homepage_today_deals'  => 'homepage.today_deals',
        'homepage_flash_deals'  => 'homepage.flash_deals',
        'product_data_all'      => 'product_data.all',
        'product_data_featured' => 'product_data.featured',
        'product_data_hot_deal' => 'product_data.hotDeal',
        'product_data_today'    => 'product_data.todayDeal',
        'product_data_best'     => 'product_data.bestSelling',
        'category_all'          => 'category_all_updated',
    ];

    /**
     * Clear all product-related caches. Call when:
     * - Product created/updated/deleted
     * - Category/Brand/Subcategory changed
     * - Settings changed
     */
    public static function clearAll(): void
    {
        self::clearProductListings();
        Cache::forget('homepage.flash_deals');
        Cache::forget('product_data.bestSelling');
        // Bump category_all version so category listing cache invalidates
        Cache::put('category_all_updated', time());
    }

    /**
     * Clear product detail cache for a specific product
     */
    public static function clearProductDetail(int $productId): void
    {
        Cache::forget('product.detail.' . $productId);
    }

    /**
     * Clear product listing caches (featured, hot deal, today deal, etc.)
     */
    public static function clearProductListings(): void
    {
        Cache::forget('homepage.today_deals');
        Cache::forget('homepage.flash_deals');
        Cache::forget('response.products.index.first_page');
        \App\Services\HomepageDataService::clearCache();
        Cache::forget('product_data.all');
        Cache::forget('product_data.featured');
        Cache::forget('product_data.hotDeal');
        Cache::forget('product_data.todayDeal');
        Cache::forget('product_data.bestSelling');
    }
}
