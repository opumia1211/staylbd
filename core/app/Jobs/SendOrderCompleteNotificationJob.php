<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderCompleteNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 60;

    public function __construct(
        public Order $order,
        public User $user
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $general = gs();
        notify($this->user, 'ORDER_COMPLETE', [
            'method_name' => 'Order successfully placed.',
            'user_name' => $this->user->username,
            'subtotal' => showAmount($this->order->subtotal),
            'shipping_charge' => showAmount($this->order->shipping_charge),
            'total' => showAmount($this->order->total),
            'currency' => $general->cur_text,
            'order_no' => $this->order->order_no,
            'link' => route('user.order.detail', $this->order->id),
        ]);
    }
}
