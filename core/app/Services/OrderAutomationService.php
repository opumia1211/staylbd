<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\OrderAutomationLog;
use App\Models\OrderAutomationSetting;
use Illuminate\Support\Facades\Schema;

class OrderAutomationService
{
    public function isAvailable(): bool
    {
        return Schema::hasTable('order_automation_settings');
    }

    /**
     * @return array{confirmed: int, processing: int, cancelled: int, errors: int}
     */
    public function run(OrderAutomationSetting $settings, bool $manual = false): array
    {
        $result = ['confirmed' => 0, 'processing' => 0, 'cancelled' => 0, 'errors' => 0];

        if (!$settings->is_enabled && !$manual) {
            $this->log('skipped', null, __('Automation is disabled.'));
            return $result;
        }

        if ($settings->auto_confirm_paid) {
            $result['confirmed'] = $this->autoConfirmPaidOrders($settings);
        }

        if ($settings->auto_processing_after_confirm) {
            $result['processing'] = $this->autoMoveConfirmedToProcessing($settings);
        }

        if ($settings->auto_cancel_unpaid_enabled && $settings->auto_cancel_unpaid_days > 0) {
            $result['cancelled'] = $this->autoCancelStalePending($settings);
        }

        if ($settings->sla_alerts_enabled ?? true) {
            $overdue = app(OrderOperationsService::class)->slaOverdueCount();
            if ($overdue > 0) {
                $this->log('sla_alert', null, __(':n order(s) exceed SLA thresholds.', ['n' => $overdue]), ['count' => $overdue]);
            }
        }

        app(OrderOperationsService::class)->clearCountCache();

        $settings->last_run_at = now();
        $settings->save();

        $this->log('run_complete', null, __('Automation run finished.'), $result);

        return $result;
    }

    protected function autoConfirmPaidOrders(OrderAutomationSetting $settings): int
    {
        $count = 0;
        Order::pending()
            ->where('payment_status', Status::ORDER_PAYMENT_SUCCESS)
            ->orderBy('id')
            ->limit(100)
            ->get()
            ->each(function (Order $order) use ($settings, &$count) {
                try {
                    $order->order_status = Status::ORDER_CONFIRMED;
                    $order->save();
                    $this->maybeNotifyCustomer($settings, $order, Status::ORDER_CONFIRMED);
                    $this->log('auto_confirm', $order->id, __('Auto-confirmed paid order :no', ['no' => $order->order_no]));
                    $count++;
                } catch (\Throwable $e) {
                    $this->log('error', $order->id, $e->getMessage());
                }
            });

        return $count;
    }

    protected function autoMoveConfirmedToProcessing(OrderAutomationSetting $settings): int
    {
        $count = 0;
        Order::confirmed()
            ->where('updated_at', '<=', now()->subHour())
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->each(function (Order $order) use ($settings, &$count) {
                try {
                    $order->order_status = Status::ORDER_PROCESSING;
                    $order->save();
                    $this->maybeNotifyCustomer($settings, $order, Status::ORDER_PROCESSING);
                    $this->log('auto_processing', $order->id, __('Auto-processing order :no', ['no' => $order->order_no]));
                    $count++;
                } catch (\Throwable $e) {
                    $this->log('error', $order->id, $e->getMessage());
                }
            });

        return $count;
    }

    protected function autoCancelStalePending(OrderAutomationSetting $settings): int
    {
        $count = 0;
        $cutoff = now()->subDays($settings->auto_cancel_unpaid_days);

        Order::pending()
            ->where('created_at', '<', $cutoff)
            ->where(function ($q) {
                $q->where('payment_status', '!=', Status::ORDER_PAYMENT_SUCCESS)
                    ->orWhereNull('payment_status');
            })
            ->orderBy('id')
            ->limit(50)
            ->with('orderDetail')
            ->get()
            ->each(function (Order $order) use ($settings, &$count) {
                try {
                    $this->cancelOrderStock($order);
                    $order->order_status = Status::ORDER_CANCEL;
                    $order->save();
                    $this->maybeNotifyCustomer($settings, $order, Status::ORDER_CANCEL);
                    $this->log('auto_cancel', $order->id, __('Auto-cancelled unpaid order :no', ['no' => $order->order_no]));
                    $count++;
                } catch (\Throwable $e) {
                    $this->log('error', $order->id, $e->getMessage());
                }
            });

        return $count;
    }

    protected function maybeNotifyCustomer(OrderAutomationSetting $settings, Order $order, int $status): void
    {
        if (!$settings->notify_customer_on_auto || !$order->user) {
            return;
        }

        $labels = [
            Status::ORDER_CONFIRMED => __('Your order has been confirmed.'),
            Status::ORDER_PROCESSING => __('Your order is now being processed.'),
            Status::ORDER_CANCEL => __('Your order has been cancelled.'),
        ];

        notify($order->user, 'ORDER_STATUS', [
            'method_name' => $labels[$status] ?? __('Order updated'),
            'user_name' => $order->user->username ?? '',
            'order_no' => $order->order_no,
            'total' => showAmount($order->total),
            'link' => route('user.order.detail', $order->id),
        ]);
    }

    protected function cancelOrderStock(Order $order): void
    {
        $order->payment_status = Status::ORDER_PAYMENT_CANCEL;
        $order->save();

        foreach ($order->orderDetail as $detail) {
            if ($detail->variant_id) {
                ProductVariant::where('id', $detail->variant_id)
                    ->where('product_id', $detail->product_id)
                    ->increment('quantity', $detail->quantity);
            }
            Product::where('id', $detail->product_id)->increment('quantity', $detail->quantity);
        }
    }

    public function log(string $action, ?int $orderId, string $message, ?array $meta = null): void
    {
        if (!Schema::hasTable('order_automation_logs')) {
            return;
        }

        OrderAutomationLog::create([
            'action' => $action,
            'order_id' => $orderId,
            'message' => $message,
            'meta' => $meta,
        ]);
    }
}
