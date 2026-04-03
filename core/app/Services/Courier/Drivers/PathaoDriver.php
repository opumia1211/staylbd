<?php

namespace App\Services\Courier\Drivers;

use App\Models\Courierapi;
use App\Models\Order;
use App\Services\Courier\CourierDriverInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PathaoDriver implements CourierDriverInterface
{
    public function getType(): string
    {
        return 'pathao';
    }

    public function getName(): string
    {
        return 'Pathao';
    }

    public function getCountryCode(): string
    {
        return 'BD';
    }

    public function isConfigured(Courierapi $api): bool
    {
        return !empty(trim($api->url ?? '')) && !empty(trim($api->token ?? ''));
    }

    public function testConnection(Courierapi $api): array
    {
        try {
            $url = rtrim($api->url ?? '', '/');
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . ($api->token ?? ''),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get($url . '/api/v1/stores');
            if ($response->successful()) {
                return [true, __('Connection successful. Stores loaded.')];
            }
            return [false, __('API returned') . ' HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 200)];
        } catch (\Throwable $e) {
            return [false, $e->getMessage()];
        }
    }

    public function sendOrder(Courierapi $api, Order $order, array $formData): array
    {
        $url = rtrim($api->url ?? '', '/');
        $payload = [
            'store_id' => $formData['pathaostore'] ?? $formData['store_id'] ?? null,
            'merchant_order_id' => $order->order_no,
            'sender_name' => 'Store Owner',
            'sender_phone' => optional($order->shipping)->phone ?? $order->user->mobile ?? '',
            'recipient_name' => optional($order->shipping)->name ?? $order->user->username ?? 'N/A',
            'recipient_phone' => optional($order->shipping)->phone ?? $order->user->mobile ?? '',
            'recipient_address' => optional($order->shipping)->address ?? $order->shipping_address ?? '',
            'recipient_city' => $formData['pathaocity'] ?? $formData['recipient_city'] ?? '',
            'recipient_zone' => $formData['pathaozone'] ?? $formData['recipient_zone'] ?? '',
            'recipient_area' => $formData['pathaoarea'] ?? $formData['recipient_area'] ?? '',
            'delivery_type' => (int) ($formData['delivery_type'] ?? 48),
            'item_type' => (int) ($formData['item_type'] ?? 2),
            'special_instruction' => $formData['special_instruction'] ?? 'Please handle with care',
            'item_quantity' => 1,
            'item_weight' => (float) ($formData['item_weight'] ?? 0.5),
            'amount_to_collect' => round((float) $order->total),
            'item_description' => 'Product delivery',
        ];
        if (empty($payload['store_id']) || empty($payload['recipient_city']) || empty($payload['recipient_zone']) || empty($payload['recipient_area'])) {
            return [false, null, __('Missing store, city, zone or area.')];
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . ($api->token ?? ''),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url . '/api/v1/orders', $payload);
            $body = $response->json();
            if ($response->successful()) {
                $consignmentId = $body['data']['consignment_id'] ?? $body['consignment_id'] ?? null;
                return [true, $consignmentId, null];
            }
            return [false, null, 'HTTP ' . $response->status() . ': ' . $response->body()];
        } catch (\Throwable $e) {
            return [false, null, $e->getMessage()];
        }
    }

    public function getOptions(Courierapi $api, string $key, Request $request): array
    {
        $url = rtrim($api->url ?? '', '/');
        $headers = [
            'Authorization' => 'Bearer ' . ($api->token ?? ''),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        if ($key === 'cities') {
            $response = Http::timeout(10)->withHeaders($headers)->get($url . '/api/v1/countries/1/city-list');
            $data = $response->successful() ? $response->json() : [];
            return $data['data'] ?? $data ?? [];
        }
        if ($key === 'zones' && $request->filled('city_id')) {
            $response = Http::timeout(10)->withHeaders($headers)->get($url . '/api/v1/cities/' . $request->city_id . '/zone-list');
            $data = $response->successful() ? $response->json() : [];
            return $data['data'] ?? $data ?? [];
        }
        if ($key === 'stores') {
            $response = Http::timeout(10)->withHeaders($headers)->get($url . '/api/v1/stores');
            $data = $response->successful() ? $response->json() : [];
            return $data['data'] ?? $data ?? [];
        }
        return [];
    }

    public function getTrackingUrl(string $courierOrderId): string
    {
        return 'https://pathao.com/courier/tracking?consignment_id=' . $courierOrderId;
    }

    public function trackOrder(Courierapi $api, string $courierOrderId): array
    {
        // Simple mock/placeholder for real tracking API call
        return ['status' => 'pending', 'message' => 'Tracking info not implemented yet', 'raw' => []];
    }

    public function getIcon(): string
    {
        return 'las la-motorcycle';
    }
}
