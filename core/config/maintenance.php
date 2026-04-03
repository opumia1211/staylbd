<?php

return [
    'clean_temp_cache_schedule' => env('MAINTENANCE_CLEAN_TEMP_SCHEDULE', 'daily'),
    'clean_logs_schedule'        => env('MAINTENANCE_CLEAN_LOGS_SCHEDULE', 'daily'),
    'run_full_schedule'          => env('MAINTENANCE_RUN_FULL_SCHEDULE', 'weekly'),
    'log_keep_days'              => (int) env('MAINTENANCE_LOG_KEEP_DAYS', 7),
    'auto_clean_temp'            => env('MAINTENANCE_AUTO_CLEAN_TEMP', true),
];
