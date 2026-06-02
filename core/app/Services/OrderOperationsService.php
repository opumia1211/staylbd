<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Order;
use App\Models\OrderAutomationSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderOperationsService
{
    /** @return array<string, int> */
    public function statusCounts(int $ttlSeconds = 60): array
    {
        return Cache::remember('admin.order.status_counts', $ttlSeconds, function () {
            $rows = Order::query()
                ->select('order_status', DB::raw('COUNT(*) as aggregate'))
                ->groupBy('order_status')
                ->pluck('aggregate', 'order_status');

            return [
                'all' => (int) $rows->sum(),
                'pending' => (int) Order::pending()->count(),
                'confirmed' => (int) ($rows[Status::ORDER_CONFIRMED] ?? 0),
                'processing' => (int) ($rows[Status::ORDER_PROCESSING] ?? 0),
                'packaging' => (int) ($rows[Status::ORDER_PACKAGING] ?? 0),
                'shipped' => (int) ($rows[Status::ORDER_SHIPPED] ?? 0),
                'delivered' => (int) ($rows[Status::ORDER_DELIVERED] ?? 0),
                'cancel' => (int) ($rows[Status::ORDER_CANCEL] ?? 0),
                'returned' => (int) ($rows[Status::ORDER_RETURNED] ?? 0),
                'out_for_delivery' => (int) ($rows[Status::ORDER_OUT_FOR_DELIVERY] ?? 0),
                'delivery_failed' => (int) ($rows[Status::ORDER_DELIVERY_FAILED] ?? 0),
            ];
        });
    }

    public function clearCountCache(): void
    {
        Cache::forget('admin.order.status_counts');
    }

    public function slaSettings(): array
    {
        if (!Schema::hasTable('order_automation_settings')) {
            return ['pending_hours' => 24, 'fulfillment_hours' => 48, 'enabled' => true];
        }

        $s = OrderAutomationSetting::current();

        return [
            'pending_hours' => (int) ($s->sla_pending_hours ?? 24),
            'fulfillment_hours' => (int) ($s->sla_fulfillment_hours ?? 48),
            'enabled' => (bool) ($s->sla_alerts_enabled ?? true),
        ];
    }

    public function slaOverdueCount(): int
    {
        if (!$this->slaSettings()['enabled']) {
            return 0;
        }

        $sla = $this->slaSettings();
        $pendingCut = now()->subHours($sla['pending_hours']);
        $fulfillCut = now()->subHours($sla['fulfillment_hours']);

        return Order::query()->where(function ($q) use ($pendingCut, $fulfillCut) {
            $q->where(function ($q2) use ($pendingCut) {
                $q2->where('order_status', Status::ORDER_PENDING)
                    ->where('created_at', '<', $pendingCut);
            })->orWhere(function ($q3) use ($fulfillCut) {
                $q3->whereIn('order_status', [
                    Status::ORDER_CONFIRMED,
                    Status::ORDER_PROCESSING,
                    Status::ORDER_PACKAGING,
                ])->where('updated_at', '<', $fulfillCut);
            });
        })->count();
    }

    public function fulfillmentQueueCount(): int
    {
        return Order::query()->whereIn('order_status', [
            Status::ORDER_PENDING,
            Status::ORDER_CONFIRMED,
            Status::ORDER_PROCESSING,
            Status::ORDER_PACKAGING,
        ])->count();
    }

    /**
     * Modern OMS capability map (what this install supports).
     *
     * @return array<int, array{key: string, label: string, enabled: bool, route: ?string}>
     */
    public function featureMatrix(): array
    {
        $hasChannels = Schema::hasTable('order_channels');
        $hasAutomation = Schema::hasTable('order_automation_settings');
        $hasTracking = Schema::hasTable('order_shipment_trackings');
        $hasAbandoned = Schema::hasTable('abandoned_carts');
        $hasAdvance = Schema::hasColumn('orders', 'advance_payment');

        return [
            ['key' => 'lifecycle', 'label' => __('Full order lifecycle (9+ statuses)'), 'enabled' => true, 'route' => 'admin.orders.index'],
            ['key' => 'automation', 'label' => __('Workflow automation (cron)'), 'enabled' => $hasAutomation, 'route' => 'admin.orders.automation.index'],
            ['key' => 'channels', 'label' => __('Multi-channel import (API/webhook)'), 'enabled' => $hasChannels, 'route' => 'admin.orders.channels.index'],
            ['key' => 'import_export', 'label' => __('CSV import / export'), 'enabled' => true, 'route' => 'admin.orders.import-export'],
            ['key' => 'bulk', 'label' => __('Bulk status & courier dispatch'), 'enabled' => true, 'route' => 'admin.orders.index'],
            ['key' => 'tracking', 'label' => __('Shipment tracking & maps'), 'enabled' => $hasTracking, 'route' => 'admin.orders.index'],
            ['key' => 'delivery_scan', 'label' => __('QR delivery scan'), 'enabled' => Schema::hasColumn('orders', 'delivery_scan_token'), 'route' => 'admin.notifications.delivery.scan'],
            ['key' => 'abandoned', 'label' => __('Abandoned cart recovery'), 'enabled' => $hasAbandoned, 'route' => 'admin.abandoned-orders.index'],
            ['key' => 'advance', 'label' => __('Advance / partial payment'), 'enabled' => $hasAdvance, 'route' => 'admin.orders.index'],
            ['key' => 'cod', 'label' => __('COD & guest checkout'), 'enabled' => true, 'route' => 'admin.shipping.cod.index'],
            ['key' => 'sla', 'label' => __('SLA overdue alerts'), 'enabled' => $hasAutomation, 'route' => 'admin.orders.fulfillment'],
        ];
    }
}
