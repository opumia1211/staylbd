<?php

return [
    'log_queue' => env('ACTIVITY_LOG_QUEUE', 'default'),
    'cache_ttl' => (int) env('ACTIVITY_CACHE_TTL', 600),
    'archive_after_days' => (int) env('ACTIVITY_ARCHIVE_AFTER_DAYS', 30),
    'delete_archive_after_days' => (int) env('ACTIVITY_DELETE_ARCHIVE_AFTER_DAYS', 365),
    'fraud' => [
        'failed_logins_2min' => (int) env('ACTIVITY_FRAUD_FAILED_LOGINS', 5),
        'payment_failures_5min' => (int) env('ACTIVITY_FRAUD_PAYMENT_FAILURES', 3),
        'cart_spam_per_min' => (int) env('ACTIVITY_FRAUD_CART_SPAM', 15),
    ],
];
