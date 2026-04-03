<?php

namespace App\Modules\Tracking;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Server-side Meta Conversions API (CAPI).
 * Call after order placement / payment success for reliable tracking.
 */
class MetaConversionApiService
{
    protected const API_URL = 'https://graph.facebook.com/v18.0/%s/events';

    public function sendEvent(string $eventName, array $payload = []): bool
    {
        $pixelId = app(TrackingScriptService::class)->metaPixelId();
        $token = app(TrackingScriptService::class)->facebookAccessToken();
        if (!$pixelId || !$token) {
            return false;
        }

        try {
            $url = sprintf(self::API_URL, $pixelId);
            $data = array_merge([
                'data' => [
                    array_merge([
                        'event_name' => $eventName,
                        'event_time' => time(),
                        'event_source_url' => request()->url(),
                        'action_source' => 'website',
                        'user_data' => $this->hashUserData($payload['user_data'] ?? []),
                    ], isset($payload['custom_data']) ? ['custom_data' => $payload['custom_data']] : []),
                ],
                'access_token' => $token,
            ], $payload['test_event_code'] ?? []);

            $response = Http::timeout(5)->post($url, $data);
            if (!$response->successful()) {
                Log::channel('daily')->warning('Meta CAPI failed', ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::channel('daily')->warning('Meta CAPI exception: ' . $e->getMessage());
            return false;
        }
    }

    /** Hash user data for Meta (sha256). */
    protected function hashUserData(array $data): array
    {
        $out = [];
        foreach (['em', 'ph', 'fn', 'ln', 'ct', 'st', 'zp', 'country'] as $key) {
            if (!empty($data[$key])) {
                $out[$key] = hash('sha256', trim(strtolower((string) $data[$key])));
            }
        }
        if (!empty($data['external_id'])) {
            $out['external_id'] = hash('sha256', (string) $data['external_id']);
        }
        return $out;
    }

    public function firePurchase(string $orderId, float $value, string $currency = 'BDT', array $userData = []): bool
    {
        return $this->sendEvent('Purchase', [
            'custom_data' => [
                'order_id' => $orderId,
                'value' => $value,
                'currency' => $currency,
            ],
            'user_data' => $userData,
        ]);
    }
}
