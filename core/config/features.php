<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Runtime Resilience
    |--------------------------------------------------------------------------
    | Ensure one module failure doesn't break other modules/pages.
    */
    'resilience' => [
        'module_isolation' => (bool) env('FEATURE_MODULE_ISOLATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Loading Policy
    |--------------------------------------------------------------------------
    | When library_only_mode=true, block third-party remote scripts and
    | allow only first-party/local assets.
    */
    'assets' => [
        'library_only_mode' => (bool) env('LIBRARY_ONLY_MODE', true),
        'allow_marketing_pixels' => (bool) env('ALLOW_MARKETING_PIXELS', false),
        'allow_live_chat_embed' => (bool) env('ALLOW_LIVE_CHAT_EMBED', false),
        'allow_external_observability' => (bool) env('ALLOW_EXTERNAL_OBSERVABILITY', false),
    ],
];

