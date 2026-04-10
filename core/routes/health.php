<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Health Check Routes (for monitoring / load balancers)
|--------------------------------------------------------------------------
| Only enable in production if needed. Consider protecting with IP allowlist.
*/

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()], 200);
});

Route::get('/health/queue', function () {
    try {
        if (config('queue.default') === 'redis') {
            \Illuminate\Support\Facades\Redis::ping();
        }
        $failed = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
        return response()->json([
            'status'  => 'ok',
            'failed_jobs' => $failed,
        ], 200);
    } catch (\Throwable $e) {
        if (config('app.debug')) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'error',
        ], 500);
    }
});
