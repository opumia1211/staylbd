<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Constants\Status;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected ShippingService $shippingService
    ) {}

    /**
     * Get cart subtotal for a user (sum of cart items × price).
     */
    public function getCartSubtotal(int $userId): float
    {
        $carts = Cart::where('user_id', $userId)->with('product')->get();
        $subtotal = 0;
        foreach ($carts as $cart) {
            $product = Product::active()->find($cart->product_id);
            if (!$product) {
                continue;
            }
            $price = productPrice($product);
            if ($cart->variant_id) {
                $variant = \App\Models\ProductVariant::find($cart->variant_id);
                if ($variant) {
                    $price = showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1);
                }
            }
            $subtotal += $price * $cart->quantity;
        }
        return (float) $subtotal;
    }

    /**
     * Create order from current user cart. Validates shipping, applies coupon discount, saves address.
     * Returns the created Order. Does not confirm (call confirmOrder for offline; redirect to deposit for online).
     */
    public function createOrderFromCheckout(\Illuminate\Contracts\Auth\Authenticatable $user, array $validated): Order
    {
        $userId = $user->id;
        $subtotal = $this->getCartSubtotal($userId);

        $shipping = ShippingMethod::where('id', $validated['shipping_method'])->where('status', Status::ENABLE)->firstOrFail();
        $shippingCalc = $this->shippingService->calculateCost(
            $shipping,
            $subtotal,
            (int) $validated['payment_type'],
            0
        );
        $shippingCost = $shippingCalc['cost'];

        $grandTotal = $subtotal + $shippingCost;
        $discount = 0;
        $couponId = 0;
        if (!empty($validated['session_total'])) {
            $discount = $validated['session_total']['discount'] ?? 0;
            $couponId = $validated['session_total']['coupon_id'] ?? 0;
            $grandTotal = $grandTotal - $discount;
        }

        $address = [
            'address' => $validated['address'],
            'state' => $validated['state'],
            'zip' => $validated['zip'],
            'country' => $validated['country'],
            'city' => $validated['city'],
        ];

        if (!empty($validated['save_address'])) {
            $user->address = $address;
            $user->save();
        }

        $zone = $this->shippingService->resolveZone(
            getCountryIsoByName($validated['country']),
            $validated['city'] ?? '',
            $validated['state'] ?? ''
        );

        $order = new Order();
        $order->user_id = $userId;
        $order->order_no = getTrx();
        $order->subtotal = $subtotal;
        $order->discount = $discount;
        $order->shipping_charge = $shippingCost;
        $order->total = $grandTotal;
        $order->coupon_id = $couponId;
        $order->shipping_method_id = $shipping->id;
        $order->shipping_zone_id = $zone?->id;
        $order->delivery_estimate = $shippingCalc['estimated_days'] ?? null;
        $order->courier_name = $shippingCalc['courier_name'] ?? null;
        $order->address = json_encode($address);
        $order->payment_type = (int) $validated['payment_type'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'ad_source')) {
            $order->ad_source = $validated['ad_source'] ?? $validated['utm_source'] ?? null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'utm_source')) {
            $order->utm_source = $validated['utm_source'] ?? null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'utm_medium')) {
            $order->utm_medium = $validated['utm_medium'] ?? null;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'utm_campaign')) {
            $order->utm_campaign = $validated['utm_campaign'] ?? null;
        }
        $order->save();

        Log::channel('single')->info('Order placed', [
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'user_id' => $userId,
            'total' => $grandTotal,
        ]);

        return $order;
    }
}
