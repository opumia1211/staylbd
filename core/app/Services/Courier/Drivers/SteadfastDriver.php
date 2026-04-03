<?php

namespace App\Services\Courier\Drivers;

use App\Models\Courierapi;
use App\Models\Order;
use App\Services\Courier\CourierDriverInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SteadfastDriver implements CourierDriverInterface
{
    public function getType(): string
    {
        return 'steadfast';
    }

    public function getName(): string
    {
        return 'Steadfast Courier';
    }

    public function getCountryCode(): string
    {
        return 'BD';
    }

    public function isConfigured(Courierapi $api): bool
    {
        $url = trim($api->url ?? '');
        $hasAuth = !empty(trim($api->token ?? '')) || !empty(trim($api->api_key ?? ''));
        return $url !== '' && $hasAuth;
    }

    public function testConnection(Courierapi $api): array
    {
        try {
            $url = rtrim($api->url ?? '', '/');
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . ($api->token ?? $api->api_key ?? ''),
                    'Content-Type' => 'application/json',
                ])
                ->get($url . '/api/consignment');
            if ($response->successful() || $response->status() === 404) {
                return [true, __('Connection successful.')];
            }
            return [false, __('API returned') . ' HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 200)];
        } catch (\Throwable $e) {
            return [false, $e->getMessage()];
        }
    }

    public function sendOrder(Courierapi $api, Order $order, array $formData): array
    {
        $url = rtrim($api->url ?? '', '/');
        $apiData = [
            'consignment_type' => $formData['consignment_type'] ?? 1,
            'delivery_type' => $formData['delivery_type'] ?? 1,
            'city' => $formData['city'] ?? '',
            'area' => $formData['area'] ?? '',
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'customer_name' => $order->user->username ?? 'N/A',
            'customer_phone' => $order->user->mobile ?? optional($order->shipping)->phone ?? 'N/A',
            'customer_address' => $order->shipping_address ?? optional($order->shipping)->address ?? 'N/A',
            'amount' => (float) $order->total,
            'weight' => (float) ($formData['weight'] ?? 1),
            'notes' => $formData['notes'] ?? 'Order from website',
        ];
        if (empty($apiData['city']) || empty($apiData['area'])) {
            return [false, null, __('Missing city or area.')];
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . ($api->token ?? $api->api_key ?? ''),
                'Content-Type' => 'application/json',
            ])->post($url . '/api/consignment', $apiData);
            $body = $response->json();
            if ($response->successful()) {
                $consignmentId = $body['consignment_id'] ?? $body['id'] ?? null;
                return [true, $consignmentId, null];
            }
            return [false, null, 'HTTP ' . $response->status() . ': ' . $response->body()];
        } catch (\Throwable $e) {
            return [false, null, $e->getMessage()];
        }
    }

    public function getOptions(Courierapi $api, string $key, Request $request): array
    {
        return [];
    }

    public function getTrackingUrl(string $courierOrderId): string
    {
        return 'https://steadfast.com.bd/tracking/' . $courierOrderId;
    }

    public function trackOrder(Courierapi $api, string $courierOrderId): array
    {
        return ['status' => 'pending', 'message' => 'Tracking info not implemented yet', 'raw' => []];
    }

    public function getIcon(): string
    {
        return 'las la-truck-moving';
    }
}
