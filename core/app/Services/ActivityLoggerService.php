<?php

namespace App\Services;

use App\Events\UserActivityOccurred;
use Illuminate\Http\Request;

/**
 * Builds activity payload and dispatches UserActivityOccurred (→ Queue → Worker → DB).
 * Request is not slowed; worker does the insert.
 */
class ActivityLoggerService
{
    /**
     * Dispatch activity to be logged via queue. Safe to call from anywhere.
     * @param int|null $userIdOverride When provided (e.g. gateway callback), use instead of auth()->id()
     */
    public static function log(
        string $actionType,
        ?string $description = null,
        ?string $modelType = null,
        ?int $modelId = null,
        ?Request $request = null,
        ?int $userIdOverride = null
    ): void {
        try {
            $request = $request ?? request();
            $userAgent = $request->userAgent() ?? '';
            $osBrowser = function_exists('osBrowser') ? osBrowser() : ['browser' => null, 'os_platform' => null];
            $ip = function_exists('getRealIP') ? getRealIP() : $request->ip();

            $city = null;
            $country = null;
            $latitude = null;
            $longitude = null;

            if (function_exists('getIpInfo')) {
                $cacheKey = 'ip_info_' . md5($ip);
                $ipInfo = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($ip) {
                    try {
                        return json_decode(json_encode(getIpInfo()), true);
                    } catch (\Throwable $e) {
                        return [];
                    }
                });
                if (is_array($ipInfo)) {
                    $country = is_array($ipInfo['country'] ?? null) ? implode(',', $ipInfo['country']) : ($ipInfo['country'] ?? null);
                    $city = is_array($ipInfo['city'] ?? null) ? implode(',', $ipInfo['city']) : ($ipInfo['city'] ?? null);
                    $lat = $ipInfo['lat'] ?? null;
                    $long = $ipInfo['long'] ?? null;
                    if (is_array($lat)) {
                        $latitude = !empty($lat) ? (float) implode('', $lat) : null;
                    } else {
                        $latitude = $lat !== null && $lat !== '' ? (float) $lat : null;
                    }
                    if (is_array($long)) {
                        $longitude = !empty($long) ? (float) implode('', $long) : null;
                    } else {
                        $longitude = $long !== null && $long !== '' ? (float) $long : null;
                    }
                }
            }

            $device = self::detectDevice($userAgent);
            $userId = $userIdOverride !== null ? $userIdOverride : auth('web')->id();

            $payload = [
                'user_id' => $userId,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'action_type' => $actionType,
                'description' => $description ? mb_substr($description, 0, 1000) : null,
                'model_type' => $modelType ? mb_substr($modelType, 0, 100) : null,
                'model_id' => $modelId,
                'ip_address' => $ip,
                'device' => $device,
                'browser' => $osBrowser['browser'] ?? null,
                'os' => $osBrowser['os_platform'] ?? null,
                'country' => $country ? mb_substr($country, 0, 100) : null,
                'city' => $city ? mb_substr($city, 0, 100) : null,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'url' => $request->fullUrl() ? mb_substr($request->fullUrl(), 0, 500) : null,
            ];

            event(new UserActivityOccurred($payload));
        } catch (\Throwable $e) {
            \Log::channel('daily')->warning('Activity dispatch failed: ' . $e->getMessage(), [
                'action' => $actionType,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected static function detectDevice(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        $ua = strtolower($userAgent);
        if (strpos($ua, 'mobile') !== false || strpos($ua, 'android') !== false || strpos($ua, 'iphone') !== false) {
            return strpos($ua, 'ipad') !== false ? 'tablet' : 'mobile';
        }
        if (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false) {
            return 'tablet';
        }
        return 'desktop';
    }
}
