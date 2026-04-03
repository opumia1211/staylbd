<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Cart;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\Wishlist;
use App\Jobs\SendOrderCompleteNotificationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait OrderConfirmation
{
    /**
     * Validate that all cart items have sufficient stock before placing order.
     * When a user has items in cart/compare/wishlist and another user completes an order,
     * stock decreases – at order placement we show "Stock Out" if any item is out of stock.
     *
     * @param int $userId
     * @param array|null $cartIds
     * @return array ['valid' => bool, 'message' => string, 'out_of_stock_names' => string[]]
     */
    public static function validateCartStockForOrder($userId, $cartIds = null)
    {
        $query = Cart::where('user_id', $userId)->with('product');
        if (!empty($cartIds) && is_array($cartIds)) {
            $query->whereIn('id', $cartIds);
        }
        $carts = $query->get();
        $outOfStockNames = [];
        $outOfStockProductIds = [];

        foreach ($carts as $cart) {
            if ($cart->product === null) {
                continue;
            }
            $product = Product::where('id', $cart->product_id)->first();
            if (!$product) {
                $outOfStockNames[] = $cart->product->name ?? __('Product');
                $outOfStockProductIds[] = $cart->product_id;
                continue;
            }
            $required = (int) $cart->quantity;
            if ($cart->variant_id) {
                $variant = ProductVariant::where('id', $cart->variant_id)->where('product_id', $product->id)->first();
                if (!$variant || (int) $variant->quantity < $required) {
                    $outOfStockNames[] = __($product->name);
                    $outOfStockProductIds[] = $product->id;
                }
            } else {
                $available = (int) $product->quantity;
                if ($available < $required) {
                    $outOfStockNames[] = __($product->name);
                    $outOfStockProductIds[] = $product->id;
                }
            }
        }

        $valid = empty($outOfStockNames);
        $defaultUserMsg = __('This product is currently out of stock. Please wait—we are restocking soon. You can keep it in your cart and try again later.');
        $message = $valid ? '' : (static::getStockOutUserMessage() ?: $defaultUserMsg);

        return [
            'valid' => $valid,
            'message' => $message,
            'out_of_stock_names' => $outOfStockNames,
            'out_of_stock_product_ids' => array_values(array_unique($outOfStockProductIds)),
        ];
    }

    /** Editable from admin: Stock & Order Messages. */
    public static function getStockOutUserMessage(): ?string
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('general_settings', 'stock_out_user_message')) {
            return null;
        }
        $g = gs();
        $msg = $g ? trim((string) ($g->stock_out_user_message ?? '')) : '';
        return $msg !== '' ? $msg : null;
    }

    /** Editable from admin: Stock & Order Messages (admin notification suffix). */
    public static function getStockOutAdminMessage(): ?string
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('general_settings', 'stock_out_admin_message')) {
            return null;
        }
        $g = gs();
        $msg = $g ? trim((string) ($g->stock_out_admin_message ?? '')) : '';
        return $msg !== '' ? $msg : null;
    }

    /**
     * Validate session cart (guest) has sufficient stock before placing order.
     *
     * @param array $sessionCart
     * @return array ['valid' => bool, 'message' => string, 'out_of_stock_names' => string[]]
     */
    public static function validateSessionCartStock(array $sessionCart)
    {
        $outOfStockNames = [];
        $outOfStockProductIds = [];

        foreach ($sessionCart as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $product = Product::where('id', $productId)->active()->first();
            if (!$product) {
                $outOfStockNames[] = __('Product');
                $outOfStockProductIds[] = $productId;
                continue;
            }
            $variantId = isset($item['variant_id']) ? (int) $item['variant_id'] : null;
            if ($variantId) {
                $variant = ProductVariant::where('id', $variantId)->where('product_id', $productId)->first();
                if (!$variant || (int) $variant->quantity < $quantity) {
                    $outOfStockNames[] = __($product->name);
                    $outOfStockProductIds[] = $productId;
                }
            } else {
                $available = (int) $product->quantity;
                if ($available < $quantity) {
                    $outOfStockNames[] = __($product->name);
                    $outOfStockProductIds[] = $productId;
                }
            }
        }

        $valid = empty($outOfStockNames);
        $defaultUserMsg = __('This product is currently out of stock. Please wait—we are restocking soon. You can keep it in your cart and try again later.');
        $message = $valid ? '' : (static::getStockOutUserMessage() ?: $defaultUserMsg);

        return [
            'valid' => $valid,
            'message' => $message,
            'out_of_stock_names' => $outOfStockNames,
            'out_of_stock_product_ids' => array_values(array_unique($outOfStockProductIds)),
        ];
    }

    /**
     * Notify admin that product(s) are out of stock but users are trying to order.
     */
    public static function notifyAdminStockOutAttempt(array $outOfStockNames, array $outOfStockProductIds = [])
    {
        if (empty($outOfStockNames)) {
            return;
        }
        $productList = implode(', ', array_slice($outOfStockNames, 0, 3));
        if (count($outOfStockNames) > 3) {
            $productList .= ' (+' . (count($outOfStockNames) - 3) . ' more)';
        }
        $suffix = static::getStockOutAdminMessage() ?: __('Out of stock but customers are trying to order. Please add stock soon.');
        $title = $productList . ' — ' . $suffix;
        $firstId = !empty($outOfStockProductIds) ? $outOfStockProductIds[0] : null;
        $clickUrl = $firstId ? urlPath('admin.product.edit', $firstId) : urlPath('admin.product.index');

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->id();
        $adminNotification->title = $title;
        $adminNotification->click_url = $clickUrl;
        $adminNotification->save();
    }

    public static function confirmOrder($order, $cartIds = null)
    {
        try {
            $user = auth()->user();
            $validation = static::validateCartStockForOrder($user->id, $cartIds);
            if (!$validation['valid']) {
                static::notifyAdminStockOutAttempt(
                    $validation['out_of_stock_names'] ?? [],
                    $validation['out_of_stock_product_ids'] ?? []
                );
                throw new \RuntimeException($validation['message'] ?: __('Stock Out - one or more products are no longer available.'));
            }

            $query = Cart::where('user_id', $user->id)->with('product');
            if (!empty($cartIds) && is_array($cartIds)) {
                $query->whereIn('id', $cartIds);
            }
            $carts = $query->get();
            $general = gs();

            $orderDetailsData = [];
            $productStockUpdate = [];

            foreach ($carts as $cart) {
                if ($cart->product === null) {
                    continue;
                }
                $price = productPrice($cart->product);
                if ($cart->variant_id) {
                    $variant = ProductVariant::find($cart->variant_id);
                    if ($variant) {
                        $price = showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1);
                    }
                }
                $orderDetailsData[] = [
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $price,
                    'variant_id' => $cart->variant_id,
                    'variant_details' => $cart->variant_details,
                ];

                $productStockUpdate[$cart->product_id] = ['quantity' => $cart->quantity, 'variant_id' => $cart->variant_id];
            }

            if (!empty($orderDetailsData)) {
                OrderDetail::insert($orderDetailsData);
            }

            if (!empty($cartIds) && is_array($cartIds)) {
                Cart::where('user_id', $user->id)->whereIn('id', $cartIds)->delete();
            } else {
                $carts->toQuery()->delete();
            }
            session()->forget('checkout_cart_ids');

            if (!empty($productStockUpdate)) {
                foreach ($productStockUpdate as $productId => $data) {
                    $quantity = $data['quantity'];
                    $variantId = $data['variant_id'] ?? null;
                    if ($variantId) {
                        ProductVariant::where('id', $variantId)->where('product_id', $productId)
                            ->decrement('quantity', $quantity);
                    }
                    Product::where('id', $productId)->decrement('quantity', $quantity);
                }
            }

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'Order successfully placed.';
            $adminNotification->click_url = urlPath('admin.orders.detail', $order->id);
            $adminNotification->save();

            // Queue notification so UI is not blocked (high concurrency)
            SendOrderCompleteNotificationJob::dispatch($order, $user);

            // Award loyalty points for the order
            try {
                $loyaltyService = new \App\Services\LoyaltyPointsService();
                $loyaltyService->awardPointsForOrder($order);
            } catch (\Exception $e) {
                Log::channel('single')->warning('Loyalty points award failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            Log::channel('single')->info('Order confirmed', [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'user_id' => $user->id,
            ]);

            try {
                app(\App\Services\AbandonedCartService::class)->markRecoveredByUser($user->id);
            } catch (\Throwable $e) {
                Log::channel('single')->debug('Abandoned cart mark recovered failed', ['message' => $e->getMessage()]);
            }

            // Meta Conversions API (server-side Purchase) – module may be disabled
            try {
                if (class_exists(\App\Modules\Tracking\MetaConversionApiService::class)) {
                    $capi = app(\App\Modules\Tracking\MetaConversionApiService::class);
                    $capi->firePurchase(
                        $order->order_no,
                        (float) $order->total,
                        $general->cur_sym ?? 'BDT',
                        [
                            'em' => $user->email,
                            'ph' => $user->mobile ?? null,
                            'external_id' => (string) $user->id,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::channel('single')->debug('Meta CAPI fire failed', ['order_id' => $order->id, 'message' => $e->getMessage()]);
            }
        } catch (\Throwable $e) {
            Log::channel('single')->error('Order confirmation failed', [
                'order_id' => $order->id ?? null,
                'order_no' => $order->order_no ?? null,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Confirm guest order: create order details from session cart, decrement stock, notify admin.
     * Does not require a logged-in user. Session cart format: [ key => [ product_id, quantity, price, variant_id?, variant_details? ], ... ]
     */
    public static function confirmGuestOrder($order, array $sessionCart): void
    {
        $orderDetailsData = [];
        $productStockUpdate = [];

        foreach ($sessionCart as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $product = Product::where('id', $productId)->active()->first();
            if (!$product) {
                continue;
            }
            $price = productPrice($product);
            $variantId = isset($item['variant_id']) ? (int) $item['variant_id'] : null;
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant && $variant->product_id == $productId) {
                    $price = showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1);
                }
            }
            $variantDetails = $item['variant_details'] ?? null;
            $orderDetailsData[] = [
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'variant_id' => $variantId ?: null,
                'variant_details' => is_string($variantDetails) ? $variantDetails : json_encode($variantDetails ?? []),
            ];
            $productStockUpdate[$productId] = ['quantity' => $quantity, 'variant_id' => $variantId];
        }

        if (!empty($orderDetailsData)) {
            OrderDetail::insert($orderDetailsData);
        }

        foreach ($productStockUpdate as $productId => $data) {
            $quantity = $data['quantity'];
            $variantId = $data['variant_id'] ?? null;
            if ($variantId) {
                ProductVariant::where('id', $variantId)->where('product_id', $productId)
                    ->decrement('quantity', $quantity);
            }
            Product::where('id', $productId)->decrement('quantity', $quantity);
        }

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = null;
        $adminNotification->title = 'Guest order placed: ' . $order->order_no;
        $adminNotification->click_url = urlPath('admin.orders.detail', $order->id);
        $adminNotification->save();

        session()->forget('cart');
        session()->forget('total');

        Log::channel('single')->info('Guest order confirmed', [
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'guest_phone' => $order->guest_phone ?? null,
        ]);
    }

    protected static function transactionCreate($order, $user, $deposit)
    {
        $order->payment_status = Status::ORDER_PAYMENT_SUCCESS;
        $order->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $order->total;
        $transaction->post_balance = 0;
        $transaction->charge = $deposit->charge;
        $transaction->trx_type = '-';
        $transaction->details = 'Order confirmation via ' . $deposit->gatewayCurrency()->name;
        $transaction->trx = $order->order_no;
        $transaction->remark = 'Payment';
        $transaction->save();
    }

    protected static function orderCancel($order)
    {
        $order->payment_status = Status::ORDER_PAYMENT_CANCEL;
        $order->save();

        foreach ($order->orderDetail as $detail) {
            if ($detail->variant_id) {
                ProductVariant::where('id', $detail->variant_id)->where('product_id', $detail->product_id)
                    ->increment('quantity', $detail->quantity);
            }
            Product::where('id', $detail->product_id)->increment('quantity', $detail->quantity);
        }
    }

    protected static function createCart($user)
    {
        $sessionCart = session()->get('cart');
        if (!$sessionCart || !is_array($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $key => $item) {
            $productId = (int) (isset($item['product_id']) ? $item['product_id'] : $key);
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $variantId = isset($item['variant_id']) && $item['variant_id'] ? (int) $item['variant_id'] : null;
            $variantDetails = isset($item['variant_details']) ? $item['variant_details'] : null;
            $product = Product::where('id', $productId)->active()->first();
            if (!$product) {
                continue;
            }
            $maxQty = $product->quantity;
            if ($variantId) {
                $variant = ProductVariant::where('product_id', $productId)->where('id', $variantId)->where('status', 1)->first();
                if ($variant) {
                    $maxQty = $variant->quantity;
                }
            }
            $existing = Cart::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->where(function ($q) use ($variantId) {
                    if ($variantId) {
                        $q->where('variant_id', $variantId);
                    } else {
                        $q->whereNull('variant_id');
                    }
                })
                ->where(function ($q) use ($variantDetails) {
                    if ($variantDetails === null || $variantDetails === '') {
                        $q->whereNull('variant_details');
                    } else {
                        $q->where('variant_details', $variantDetails);
                    }
                })
                ->first();
            if ($existing) {
                $newQty = min($existing->quantity + $quantity, $maxQty > 0 ? $maxQty : 999);
                $existing->quantity = $newQty;
                $existing->save();
            } else {
                $createCart = new Cart();
                $createCart->user_id = $user->id;
                $createCart->product_id = $productId;
                $createCart->variant_id = $variantId;
                $createCart->variant_details = $variantDetails;
                $createCart->quantity = min($quantity, $maxQty > 0 ? $maxQty : 999);
                $createCart->save();
            }
        }
        session()->forget('cart');
        session()->forget('total');
    }

    /**
     * Merge guest wishlist (session) into user account after login/register.
     */
    protected static function migrateWishlist($user)
    {
        $sessionWishlist = session()->get('wishlist');
        if (!$sessionWishlist || !is_array($sessionWishlist)) {
            return;
        }
        foreach ($sessionWishlist as $productId => $item) {
            $productId = (int) (is_array($item) ? ($item['product_id'] ?? $productId) : $productId);
            if ($productId <= 0) {
                continue;
            }
            if (!Product::where('id', $productId)->active()->exists()) {
                continue;
            }
            if (Wishlist::where('user_id', $user->id)->where('product_id', $productId)->exists()) {
                continue;
            }
            $w = new Wishlist();
            $w->user_id = $user->id;
            $w->product_id = $productId;
            $w->save();
        }
        session()->forget('wishlist');
    }
}
