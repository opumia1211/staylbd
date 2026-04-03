<?php

/**
 * Location Management Module configuration.
 * Optional: use a separate DB for locations by setting LOCATIONS_DB_CONNECTION in .env
 * (e.g. ecommerce_locations_db). Default uses the main application connection.
 */
return [
    'database_connection' => env('LOCATIONS_DB_CONNECTION', null),
    'cache_ttl_seconds' => env('LOCATION_CACHE_TTL', 300),
    'cache_key_prefix' => 'location_',
];
