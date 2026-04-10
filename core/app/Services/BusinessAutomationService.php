<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Admin;
use App\Models\Coupon;
use App\Notify\Notify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Business Automation: abandoned cart recovery, exit-intent discounts, order follow-ups.
 * Optimizes for revenue and customer retention.
 */
class BusinessAutomationService
{
    /**
     * Identify abandoned carts (created > 2 hours ago, no order placed since).
     * Sends automatic reminders and optional discount codes.
     */
    public function processAbandonedCarts()
    {
        $cutoff = now()->subHours(2);
        
        // Find users with items in cart who haven't placed an order in the last 24 hours
        $abandonedUsers = User::whereHas('cart', function($q) use ($cutoff) {
            $q->where('updated_at', '<', $cutoff);
        })->whereDoesntHave('orders', function($q) {
            $q->where('created_at', '>', now()->subDay());
        })->get();

        foreach ($abandonedUsers as $user) {
            $this->sendAbandonedCartReminder($user);
        }

        return count($abandonedUsers);
    }

    /**
     * Send email/SMS reminder for abandoned cart.
     */
    protected function sendAbandonedCartReminder(User $user)
    {
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        if ($cartItems->isEmpty()) return;

        $itemsText = $cartItems->take(3)->map(fn($item) => $item->product->name)->implode(', ');
        
        // Generate a limited-time 5% discount to encourage completion
        $coupon = $this->createDynamicDiscount($user, 5, 'ABANDONED');

        notify($user, 'CART_ABANDONED', [
            'username' => $user->username,
            'items' => $itemsText,
            'coupon_code' => $coupon->coupon_code,
            'discount' => '5%',
            'link' => url('/cart-list')
        ]);
        
        Log::info("Abandoned cart notification sent to user {$user->id}");
    }

    /**
     * Generate dynamic, one-time use coupon.
     */
    public function createDynamicDiscount(User $user, int $percent, string $prefix = 'WIN')
    {
        $code = strtoupper($prefix . '-' . Str::random(6));
        
        $coupon = new Coupon();
        $coupon->coupon_code = $code;
        $coupon->discount_type = 1; // percentage
        $coupon->coupon_amount = $percent;
        $coupon->minimum_spend = 100;
        $coupon->maximum_spend = 10000;
        $coupon->valid_from = now();
        $coupon->valid_to = now()->addDays(2);
        $coupon->limit_per_user = 1;
        $coupon->total_usage_limit = 1;
        $coupon->status = 1;
        $coupon->save();

        return $coupon;
    }

    /**
     * Automated Order Follow-up: Send review request 3 days after delivery.
     */
    public function orderFollowUp()
    {
        $deliveredOrders = Order::delivered()
            ->where('updated_at', '<', now()->subDays(3))
            ->where('follow_up_sent', 0)
            ->with('user')
            ->get();

        foreach ($deliveredOrders as $order) {
            if (!$order->user) continue;

            notify($order->user, 'ORDER_FOLLOW_UP', [
                'order_number' => $order->id,
                'link' => product_detail_url_for_id($order->orderDetail->first()->product_id ?? 0)
            ]);

            $order->follow_up_sent = 1;
            $order->save();
        }
    }
}
