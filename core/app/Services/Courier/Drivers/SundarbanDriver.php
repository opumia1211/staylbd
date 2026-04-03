<?php

namespace App\Services\Courier\Drivers;

use App\Models\Courierapi;
use App\Models\Order;
use App\Services\Courier\CourierDriverInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SundarbanDriver implements CourierDriverInterface
{
    public function getType(): string
    {
        return 'sundarban';
    }

    public function getName(): string
    {
        return 'Sundarban Courier';
    }

    public function getCountryCode(): string
    {
        return 'BD';
    }

    public function isConfigured(Courierapi $api): bool
    {
        return !empty(trim($api->url ?? '')) && (!empty(trim($api->token ?? '')) || !empty(trim($api->api_key ?? '')));
    }

    public function testConnection(Courierapi $api): array
    {
        try {
            $url = rtrim($api->url ?? '', '/');
            $token = $api->token ?? $api->api_key ?? '';
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->get($url . '/api/v1/status');
            if ($response->successful() || $response->status() === 404) {
                return [true, __('Connection successful.')];
            }
            return [false, 'HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 150)];
        } catch (\Throwable $e) {
            return [false, $e->getMessage()];
        }
    }

    public function sendOrder(Courierapi $api, Order $order, array $formData): array
    {
        return [false, null, __('Sundarban API integration: configure URL and credentials, then implement send endpoint.')];
    }

    public function getOptions(Courierapi $api, string $key, Request $request): array
    {
        return [];
    }

    public function getTrackingUrl(string $courierOrderId): string
    {
        return 'https://sundarbancourierltd.com/check_track/' . $courierOrderId;
    }

    public function trackOrder(Courierapi $api, string $courierOrderId): array
    {
        return ['status' => 'pending', 'message' => 'Tracking info not implemented yet', 'raw' => []];
    }

    public function getIcon(): string
    {
        return 'las la-truck';
    }
}
