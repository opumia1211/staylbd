<?php

namespace App\Modules\StaffAudit;

use App\Models\AdminActivityLog;
use App\Models\Order;

class OrderAuditObserver
{
    public function updating(Order $order): void
    {
        $dirty = $order->getDirty();
        if (empty($dirty)) {
            return;
        }

        $toLog = [];
        if (array_key_exists('order_status', $dirty)) {
            $toLog['order_status'] = ['old' => $order->getOriginal('order_status'), 'new' => $dirty['order_status']];
        }
        if (array_key_exists('address', $dirty)) {
            $toLog['address'] = ['old' => $order->getOriginal('address'), 'new' => $dirty['address']];
        }
        if (array_key_exists('staff_notes', $dirty)) {
            $toLog['staff_notes'] = ['old' => $order->getOriginal('staff_notes'), 'new' => $dirty['staff_notes']];
        }
        if (array_key_exists('advance_payment', $dirty)) {
            $toLog['advance_payment'] = ['old' => $order->getOriginal('advance_payment'), 'new' => $dirty['advance_payment']];
        }

        if (empty($toLog)) {
            return;
        }

        try {
            AdminActivityLog::logAction('update', 'Order', $order->id, $order->getOriginal(), array_merge($order->getOriginal(), $dirty));
        } catch (\Throwable $e) {
            \Log::debug('StaffAudit: log failed ' . $e->getMessage());
        }
    }
}
