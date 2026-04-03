<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fault Isolation (Modularity)
    |--------------------------------------------------------------------------
    | When true, homepage and other aggregate views load each section in
    | try-catch so one failing module (e.g. hot_deal, featured) does not
    | break the entire page. Failed sections log and return empty data.
    */
    'fault_isolation' => env('OPTIMIZATION_FAULT_ISOLATION', true),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    | Homepage and listing cache TTL in seconds. Use Redis/Memcached in
    | production for best performance; set CACHE_DRIVER=redis in .env.
    | Cache tags (e.g. for invalidation) require Redis.
    */
    'homepage_cache_ttl' => (int) env('HOMEPAGE_CACHE_TTL', 600),
    'product_detail_cache_ttl' => (int) env('PRODUCT_DETAIL_CACHE_TTL', 600),
    'use_cache_tags' => env('CACHE_DRIVER') === 'redis',

    /*
    |--------------------------------------------------------------------------
    | Asset Versioning
    |--------------------------------------------------------------------------
    | Bump asset_version (e.g. in AppServiceProvider or on deploy) so
    | browsers fetch new CSS/JS after deploy. Prevents FOUC on refresh.
    */
    'asset_version_bump_on_clear_cache' => true,

    /*
    |--------------------------------------------------------------------------
    | Responsive Breakpoints (reference for CSS/JS)
    |--------------------------------------------------------------------------
    | Used in docs and for consistent mobile/tablet/desktop behavior.
    | Values in pixels; match Bootstrap 5 / custom media queries.
    */
    'breakpoints' => [
        'xs' => 0,
        'sm' => 576,
        'md' => 768,
        'lg' => 992,
        'xl' => 1200,
        'xxl' => 1400,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defer Non-Critical Scripts
    |--------------------------------------------------------------------------
    | Scripts that can be deferred (carousel, lightbox, wow) are loaded
    | with defer in layout. jQuery loads without defer for cart/search.
    */
    'defer_non_critical_js' => true,

    /*
    |--------------------------------------------------------------------------
    | Lazy Load Images
    |--------------------------------------------------------------------------
    | Product card and listing images use loading="lazy" and decoding="async".
    */
    'lazy_load_images' => true,

    /*
    |--------------------------------------------------------------------------
    | Homepage: defer below-the-fold HTML (guests)
    |--------------------------------------------------------------------------
    | First paint: header + hero + Hot Deals only. Categories, Power Zone,
    | Featured, etc. load via GET /home-below-fold (cached). Add ?full_home=1
    | to disable. Logged-in users always get full page (no defer).
    */
    'homepage_defer_below_fold' => env('HOMEPAGE_DEFER_BELOW_FOLD', true),

    /*
    |--------------------------------------------------------------------------
    | Guest banner query cache (seconds) — reduces DB on every homepage hit
    */
    'homepage_banner_guest_cache_ttl' => (int) env('HOMEPAGE_BANNER_GUEST_CACHE_TTL', 300),

    /*
    |--------------------------------------------------------------------------
    | On homepage, defer non-critical CSS until after load (main path only)
    */
    'homepage_defer_heavy_css' => env('HOMEPAGE_DEFER_HEAVY_CSS', true),

];
