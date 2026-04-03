<?php

namespace App\Modules\OrderEnhancements\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderEnhancementsController extends Controller
{
    /**
     * Update advance payment and/or staff notes for an order.
     * Safe when columns are missing.
     */
    public function update(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $data = [];
        if (\Schema::hasColumn('orders', 'advance_payment')) {
            $v = $request->input('advance_payment');
            $data['advance_payment'] = is_numeric($v) ? max(0, (float) $v) : 0;
        }
        if (\Schema::hasColumn('orders', 'staff_notes')) {
            $data['staff_notes'] = $request->input('staff_notes');
        }

        if (empty($data)) {
            return back()->with('notify', [['info', __('No fields to update.')]]);
        }

        foreach ($data as $key => $value) {
            $order->setAttribute($key, $value);
        }
        $order->save();

        if (function_exists('log_admin_activity')) {
            log_admin_activity('update', 'Order', $order->id, null, $data);
        }

        $notify[] = ['success', __('Order updated.')];
        return back()->withNotify($notify);
    }
}
