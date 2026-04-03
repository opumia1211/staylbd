<?php

namespace App\Services\Courier\Drivers;

use App\Models\Courierapi;
use App\Models\Order;
use App\Services\Courier\CourierDriverInterface;
use Illuminate\Http\Request;

class GenericDriver implements CourierDriverInterface
{
    public function getType(): string
    {
        return 'generic';
    }

    public function getName(): string
    {
        return 'Custom/Global Courier';
    }

    public function getCountryCode(): string
    {
        return 'GLOBAL';
    }

    public function isConfigured(Courierapi $api): bool
    {
        return !empty(trim($api->url ?? ''));
    }

    public function testConnection(Courierapi $api): array
    {
        return [true, __('Generic driver initialized for URL: ') . ($api->url ?? 'N/A')];
    }

    public function sendOrder(Courierapi $api, Order $order, array $formData): array
    {
        return [false, null, __('Generic driver: please implement specific API endpoint in a new driver class.')];
    }

    public function getOptions(Courierapi $api, string $key, Request $request): array
    {
        return [];
    }

    public function getTrackingUrl(string $courierOrderId): string
    {
        return '#';
    }

    public function trackOrder(Courierapi $api, string $courierOrderId): array
    {
        return ['status' => 'pending', 'message' => 'Tracking info not available for generic driver', 'raw' => []];
    }

    public function getIcon(): string
    {
        return 'las la-globe';
    }
}
