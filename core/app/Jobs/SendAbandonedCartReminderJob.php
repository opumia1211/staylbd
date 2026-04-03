<?php

namespace App\Jobs;

use App\Models\AbandonedCart;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAbandonedCartReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    public function __construct(
        public AbandonedCart $abandonedCart
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $general = gs();
        $recoveryUrl = $this->abandonedCart->recovery_url;
        $cartValue = $this->abandonedCart->cart_value;
        $productNames = [];
        if (!empty($this->abandonedCart->cart_snapshot)) {
            $productNames = array_map(function ($item) {
                return $item['product_name'] ?? '';
            }, array_slice($this->abandonedCart->cart_snapshot, 0, 5));
        }

        $user = null;
        if ($this->abandonedCart->user_id) {
            $user = User::find($this->abandonedCart->user_id);
        }
        if (!$user) {
            $user = (object) [
                'id' => 0,
                'email' => $this->abandonedCart->email,
                'mobile' => $this->abandonedCart->mobile,
                'username' => $this->abandonedCart->email ?: ('Guest-' . $this->abandonedCart->id),
            ];
        }
        $userName = $user->username ?? $user->email ?? __('Customer');
        $shortCodes = [
            'user_name' => $userName,
            'recovery_link' => $recoveryUrl,
            'cart_value' => ($general->cur_sym ?? '৳') . number_format($cartValue, 2),
            'product_list' => implode(', ', array_filter($productNames)),
        ];

        $sendVia = [];
        if ($general->abandoned_cart_reminder_email ?? true) {
            if ($user->email ?? $this->abandonedCart->email) {
                $sendVia[] = 'email';
            }
        }
        if ($general->abandoned_cart_reminder_sms ?? false) {
            if ($user->mobile ?? $this->abandonedCart->mobile) {
                $sendVia[] = 'sms';
            }
        }
        if (empty($sendVia)) {
            return;
        }

        notify($user, 'ABANDONED_CART', $shortCodes, $sendVia, true);
    }
}
