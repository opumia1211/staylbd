<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Clothing product categories
    |--------------------------------------------------------------------------
    | Category slugs or names that are considered "clothing" for the dedicated
    | clothing product create page (/product/create). Products in these
    | categories use clothing-specific fields (fabric, material, season, etc.).
    */
    'clothing_category_slugs' => [
        'shirts',
        't-shirts',
        'pants',
        'jeans',
        'jackets',
        'traditional-clothing',
        'fashion-apparel',
        'clothing-accessories',
        'clothing',
        'apparel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Category name keywords (fallback if slug not in list)
    |--------------------------------------------------------------------------
    */
    'clothing_category_keywords' => [
        'shirt', 't-shirt', 'pants', 'jeans', 'jacket', 'traditional', 'fashion',
        'apparel', 'clothing', 'accessories', 'wear', 'dress', 'top', 'bottom',
    ],

    /*
    |--------------------------------------------------------------------------
    | Seasons (for clothing)
    |--------------------------------------------------------------------------
    */
    'seasons' => [
        'all' => 'All Season',
        'spring' => 'Spring',
        'summer' => 'Summer',
        'fall' => 'Fall',
        'winter' => 'Winter',
    ],

    /*
    |--------------------------------------------------------------------------
    | Image optimization (general product upload)
    |--------------------------------------------------------------------------
    */
    'image_convert_webp' => true,
    'image_quality' => 85,
    'thumbnail_sizes' => [150, 500, 1000],

    /*
    |--------------------------------------------------------------------------
    | Stock status badges (admin + frontend product cards)
    |--------------------------------------------------------------------------
    | In Stock: quantity > in_stock_min
    | Low Stock: quantity between low_stock_min and low_stock_max (inclusive)
    | Out Of Stock: quantity = 0
    */
    'in_stock_min' => 20,
    'low_stock_min' => 5,
    'low_stock_max' => 20,

    /*
    |--------------------------------------------------------------------------
    | New Product badge (show if product created within this many days)
    |--------------------------------------------------------------------------
    */
    'new_product_days' => 7,

    /*
    |--------------------------------------------------------------------------
    | Best Seller badge (show if product sale_count >= this threshold)
    |--------------------------------------------------------------------------
    */
    'best_seller_threshold' => 10,
];
