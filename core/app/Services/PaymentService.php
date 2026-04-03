<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Order;
use App\Models\Transaction;

class PaymentService
{
    /**
     * Record successful online payment for an order (update order, create transaction).
     */
    public function recordOrderPaymentSuccess(Order $order, $deposit): void
    {
        $order->payment_status = Status::ORDER_PAYMENT_SUCCESS;
        $order->save();

        $transaction = new Transaction();
        $transaction->user_id = $order->user_id;
        $transaction->amount = $order->total;
        $transaction->post_balance = 0;
        $transaction->charge = $deposit->charge ?? 0;
        $transaction->trx_type = '-';
        $transaction->details = 'Order confirmation via ' . (optional(optional($deposit)->gatewayCurrency())->name ?? 'Online');
        $transaction->trx = $order->order_no;
        $transaction->remark = 'Payment';
        $transaction->save();
    }

    /**
     * Cancel order payment and restore product stock.
     */
    public function cancelOrder(Order $order): void
    {
        $order->payment_status = Status::ORDER_PAYMENT_CANCEL;
        $order->save();

        foreach ($order->orderDetail as $detail) {
            if ($detail->variant_id) {
                \App\Models\ProductVariant::where('id', $detail->variant_id)
                    ->where('product_id', $detail->product_id)
                    ->increment('quantity', $detail->quantity);
            }
            \App\Models\Product::where('id', $detail->product_id)->increment('quantity', $detail->quantity);
        }
    }
}
