<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Order;
use App\Models\OrderChannel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OrderChannelService
{
    public function isAvailable(): bool
    {
        return Schema::hasTable('order_channels');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(OrderChannel $channel, array $payload): array
    {
        if (!$channel->is_active) {
            return ['ok' => false, 'message' => __('Channel is disabled.')];
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('order_automation_settings')) {
            $oa = \App\Models\OrderAutomationSetting::current();
            if (!$oa->channel_import_enabled) {
                return ['ok' => false, 'message' => __('Channel import is disabled in Order Automation settings.')];
            }
        }

        if (!in_array($channel->direction, ['import', 'both'], true)) {
            return ['ok' => false, 'message' => __('Channel is export-only.')];
        }

        $externalRef = (string) ($payload['external_id'] ?? $payload['order_id'] ?? $payload['id'] ?? '');
        if ($externalRef === '') {
            return ['ok' => false, 'message' => __('Missing external order id.')];
        }

        $source = $channel->platform;
        if (Order::query()->where('order_source', $source)->where('external_order_ref', $externalRef)->exists()) {
            return ['ok' => true, 'message' => __('Order already imported.'), 'duplicate' => true];
        }

        $customerName = (string) ($payload['customer_name'] ?? $payload['billing']['name'] ?? 'Guest');
        $phone = (string) ($payload['phone'] ?? $payload['billing']['phone'] ?? '');
        $email = (string) ($payload['email'] ?? $payload['billing']['email'] ?? '');
        $address = (string) ($payload['address'] ?? $payload['billing']['address'] ?? '');
        $total = (float) ($payload['total'] ?? $payload['amount'] ?? 0);

        $paymentType = (int) ($payload['payment_type'] ?? 0);

        $order = DB::transaction(function () use ($channel, $externalRef, $source, $customerName, $phone, $email, $address, $total, $paymentType) {
            $order = new Order();
            $order->user_type = 'guest';
            $order->guest_name = $customerName;
            $order->guest_phone = $phone;
            $order->guest_email = $email;
            $order->guest_address = $address;
            $order->order_no = $this->generateOrderNo();
            $order->external_order_ref = $externalRef;
            $order->order_source = $source;
            $order->subtotal = max(0, $total);
            $order->total = max(0, $total);
            $order->payment_type = $paymentType;
            $order->payment_status = Status::PAYMENT_PENDING;
            $order->order_status = Status::ORDER_PENDING;
            $order->save();

            $channel->increment('imported_count');
            $channel->last_sync_at = now();
            $channel->save();

            return $order;
        });

        return [
            'ok' => true,
            'message' => __('Order imported successfully.'),
            'order_id' => $order->id,
            'order_no' => $order->order_no,
        ];
    }

    protected function generateOrderNo(): string
    {
        do {
            $no = 'CH' . strtoupper(Str::random(8));
        } while (Order::where('order_no', $no)->exists());

        return $no;
    }
}
