<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storefront deferred CSS (reference)
    |--------------------------------------------------------------------------
    | Vendor CSS (jQuery UI, Owl, Slick, animate, lightbox2) is not bundled in
    | tailwind-storefront-deferred*.css when scripts are disabled in
    | resources/views/templates/basic/layouts/app.blade.php ($disableLegacy*).
    | To use a legacy widget again: set the flag false for that page and add the
    | matching @import to resources/css/tailwind-storefront-deferred.css, then npm run production.
    */

    /*
    |--------------------------------------------------------------------------
    | Admin dashboard live refresh
    |--------------------------------------------------------------------------
    | poll_interval_ms: client polling interval for dashboard stats endpoint.
    | live_stats_cache_ttl: server cache TTL (seconds) for live stats payload.
    | Keep these low for near-real-time but avoid hammering DB.
    */
    'admin' => [
        'dashboard_poll_interval_ms' => (int) env('ADMIN_DASHBOARD_POLL_INTERVAL_MS', 10000),
        'live_stats_cache_ttl' => (int) env('ADMIN_LIVE_STATS_CACHE_TTL', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storefront real-time fallback tuning
    |--------------------------------------------------------------------------
    | Used when websocket is unavailable; lower values are more real-time but
    | increase request load.
    */
    'storefront' => [
        'realtime_poll_interval_ms' => (int) env('STOREFRONT_RT_POLL_INTERVAL_MS', 8000),
        'realtime_ws_dead_before_poll_ms' => (int) env('STOREFRONT_RT_WS_DEAD_MS', 12000),
    ],
];
