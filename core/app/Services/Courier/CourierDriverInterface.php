<?php

namespace App\Services\Courier;

use App\Models\Courierapi;
use App\Models\Order;
use Illuminate\Http\Request;

interface CourierDriverInterface
{
    /** Unique driver key (e.g. pathao, steadfast) */
    public function getType(): string;

    /** Display name */
    public function getName(): string;

    /** Country code (e.g. BD) */
    public function getCountryCode(): string;

    /** Whether the provider config is valid */
    public function isConfigured(Courierapi $api): bool;

    /** Test API connection; return [success, message] */
    public function testConnection(Courierapi $api): array;

    /** Send single order to courier; return [success, courier_order_id?, error?] */
    public function sendOrder(Courierapi $api, Order $order, array $formData): array;

    /** Optional: fetch dynamic options (e.g. cities, zones). Return array. */
    public function getOptions(Courierapi $api, string $key, Request $request): array;

    /** Get tracking URL for a given order ID */
    public function getTrackingUrl(string $courierOrderId): string;

    /** Track order status from API; return [status, message, raw_data?] */
    public function trackOrder(Courierapi $api, string $courierOrderId): array;

    /** Get font-awesome or line-awesome icon class */
    public function getIcon(): string;
}
