<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Constants\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AbandonedCartService
{
    /**
     * Get or create a stable identifier for guest (session, cookie, or new).
     */
    public function getGuestIdentifiers(Request $request): array
    {
        $sessionId = $request->session()->getId();
        $cookieId = $request->cookie('abandoned_cart_id') ?: null;
        $localStorageId = $request->header('X-Cart-Id') ?: $request->input('cart_id'); // optional from frontend
        if (!$cookieId && !$localStorageId) {
            $cookieId = Str::random(40);
        }
        return [
            'session_id' => $sessionId,
            'cookie_id' => $cookieId,
            'local_storage_id' => $localStorageId,
        ];
    }

    /**
     * Build cart snapshot from session cart (guest).
     */
    public function buildSnapshotFromSession(): array
    {
        $cart = session()->get('cart', []);
        if (!is_array($cart) || empty($cart)) {
            return [];
        }
        $snapshot = [];
        foreach ($cart as $key => $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            if ($productId <= 0) {
                continue;
            }
            $product = Product::active()->find($productId);
            if (!$product) {
                continue;
            }
            $price = $item['price'] ?? $product->price;
            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty <= 0) {
                continue;
            }
            $snapshot[] = [
                'product_id' => $productId,
                'product_name' => $item['name'] ?? $product->name,
                'product_price' => $price,
                'quantity' => $qty,
                'variant_id' => $item['variant_id'] ?? null,
                'variant_details' => $item['variant_details'] ?? null,
                'image' => $item['image'] ?? $product->image,
            ];
        }
        return $snapshot;
    }

    /**
     * Build cart snapshot from DB carts (logged-in user).
     */
    public function buildSnapshotFromUserCart(int $userId, ?array $cartIds = null): array
    {
        $query = Cart::where('user_id', $userId)->with('product');
        if (!empty($cartIds) && is_array($cartIds)) {
            $query->whereIn('id', $cartIds);
        }
        $carts = $query->get();
        $snapshot = [];
        foreach ($carts as $cart) {
            if (!$cart->product || !$cart->product->exists) {
                continue;
            }
            $product = $cart->product;
            $price = productPrice($product);
            if ($cart->variant_id) {
                $variant = ProductVariant::find($cart->variant_id);
                if ($variant) {
                    $price = showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1);
                }
            }
            $snapshot[] = [
                'product_id' => $cart->product_id,
                'product_name' => $product->name,
                'product_price' => $price,
                'quantity' => $cart->quantity,
                'variant_id' => $cart->variant_id,
                'variant_details' => $cart->variant_details,
                'image' => $product->image,
            ];
        }
        return $snapshot;
    }

    /**
     * Calculate cart value from snapshot.
     */
    public function calculateCartValue(array $snapshot): float
    {
        $total = 0;
        foreach ($snapshot as $item) {
            $total += ((float) ($item['product_price'] ?? 0)) * ((int) ($item['quantity'] ?? 0));
        }
        return round($total, 2);
    }

    /**
     * Detect device type from user agent.
     */
    public function getDeviceType(Request $request): string
    {
        $ua = $request->userAgent() ?? '';
        if (preg_match('/mobile|android|iphone|ipad|tablet/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Record or update abandoned cart (guest).
     */
    public function recordGuestCart(Request $request): ?AbandonedCart
    {
        $snapshot = $this->buildSnapshotFromSession();
        if (empty($snapshot)) {
            return null;
        }
        $ids = $this->getGuestIdentifiers($request);
        $cartValue = $this->calculateCartValue($snapshot);
        $sessionId = $ids['session_id'];

        $abandoned = AbandonedCart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])
            ->first();

        if ($abandoned) {
            $abandoned->cart_snapshot = $snapshot;
            $abandoned->cart_value = $cartValue;
            $abandoned->last_activity_at = now();
            $abandoned->ip_address = $request->ip();
            $abandoned->device_type = $this->getDeviceType($request);
            $abandoned->cookie_id = $ids['cookie_id'] ?? $abandoned->cookie_id;
            $abandoned->local_storage_id = $ids['local_storage_id'] ?? $abandoned->local_storage_id;
            if (!$abandoned->recovery_token) {
                $abandoned->recovery_token = Str::random(48);
            }
            $abandoned->save();
            return $abandoned;
        }

        $abandoned = new AbandonedCart();
        $abandoned->session_id = $sessionId;
        $abandoned->cookie_id = $ids['cookie_id'];
        $abandoned->local_storage_id = $ids['local_storage_id'];
        $abandoned->cart_snapshot = $snapshot;
        $abandoned->cart_value = $cartValue;
        $abandoned->last_activity_at = now();
        $abandoned->ip_address = $request->ip();
        $abandoned->device_type = $this->getDeviceType($request);
        $abandoned->status = AbandonedCart::STATUS_PENDING;
        $abandoned->recovery_token = Str::random(48);
        $abandoned->save();
        return $abandoned;
    }

    /**
     * Record or update abandoned cart (logged-in user).
     */
    public function recordUserCart(int $userId, Request $request, bool $checkoutStarted = false, ?array $cartIds = null): ?AbandonedCart
    {
        $snapshot = $this->buildSnapshotFromUserCart($userId, $cartIds);
        if (empty($snapshot)) {
            return null;
        }
        $cartValue = $this->calculateCartValue($snapshot);
        $user = \App\Models\User::find($userId);
        $email = $user ? $user->email : null;
        $mobile = $user ? $user->mobile : null;

        $abandoned = AbandonedCart::where('user_id', $userId)
            ->whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])
            ->first();

        if ($abandoned) {
            $abandoned->cart_snapshot = $snapshot;
            $abandoned->cart_value = $cartValue;
            $abandoned->last_activity_at = now();
            $abandoned->ip_address = $request->ip();
            $abandoned->device_type = $this->getDeviceType($request);
            $abandoned->email = $email;
            $abandoned->mobile = $mobile;
            if ($checkoutStarted) {
                $abandoned->checkout_started_at = $abandoned->checkout_started_at ?? now();
            }
            if (!$abandoned->recovery_token) {
                $abandoned->recovery_token = Str::random(48);
            }
            $abandoned->save();
            return $abandoned;
        }

        $abandoned = new AbandonedCart();
        $abandoned->user_id = $userId;
        $abandoned->session_id = $request->session()->getId();
        $abandoned->cart_snapshot = $snapshot;
        $abandoned->cart_value = $cartValue;
        $abandoned->last_activity_at = now();
        $abandoned->ip_address = $request->ip();
        $abandoned->device_type = $this->getDeviceType($request);
        $abandoned->email = $email;
        $abandoned->mobile = $mobile;
        $abandoned->status = AbandonedCart::STATUS_PENDING;
        $abandoned->recovery_token = Str::random(48);
        if ($checkoutStarted) {
            $abandoned->checkout_started_at = now();
        }
        $abandoned->save();
        return $abandoned;
    }

    /**
     * Mark abandoned cart as recovered (order placed).
     */
    public function markRecoveredByUser(int $userId): void
    {
        AbandonedCart::where('user_id', $userId)
            ->whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])
            ->update(['status' => AbandonedCart::STATUS_RECOVERED]);
    }

    /**
     * Mark abandoned cart as recovered by session (guest completed order after login or recovery).
     */
    public function markRecoveredBySession(string $sessionId): void
    {
        AbandonedCart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])
            ->update(['status' => AbandonedCart::STATUS_RECOVERED]);
    }

    /**
     * Mark recovered by recovery token (after user uses recovery link and completes order).
     */
    public function markRecoveredByToken(string $token): void
    {
        AbandonedCart::where('recovery_token', $token)
            ->whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])
            ->update(['status' => AbandonedCart::STATUS_RECOVERED]);
    }

    /**
     * Restore cart from abandoned cart snapshot (for recovery link).
     * For guest: merge into session. For logged-in: merge into DB carts.
     */
    public function restoreCartFromAbandoned(AbandonedCart $abandoned, $user = null): void
    {
        $snapshot = $abandoned->cart_snapshot ?? [];
        if (empty($snapshot)) {
            return;
        }
        if ($user && $user->id) {
            foreach ($snapshot as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 1);
                if ($productId <= 0) {
                    continue;
                }
                $product = Product::active()->find($productId);
                if (!$product) {
                    continue;
                }
                $existing = Cart::where('user_id', $user->id)
                    ->where('product_id', $productId)
                    ->where('variant_id', $item['variant_id'] ?? null)
                    ->first();
                if ($existing) {
                    $existing->quantity = min($existing->quantity + $quantity, $product->quantity);
                    $existing->save();
                } else {
                    $cart = new Cart();
                    $cart->user_id = $user->id;
                    $cart->product_id = $productId;
                    $cart->variant_id = $item['variant_id'] ?? null;
                    $cart->variant_details = $item['variant_details'] ?? null;
                    $cart->quantity = min($quantity, $product->quantity);
                    $cart->save();
                }
            }
            return;
        }
        $sessionCart = session()->get('cart', []);
        $general = gs();
        foreach ($snapshot as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 1);
            if ($productId <= 0) {
                continue;
            }
            $product = Product::active()->find($productId);
            if (!$product) {
                continue;
            }
            $variantId = $item['variant_id'] ?? null;
            $variantDetails = $item['variant_details'] ?? null;
            $cartKey = $variantId ? $productId . '_' . $variantId . '_' . ($variantDetails ?? '') : $productId . '_';
            $price = $item['product_price'] ?? $product->price;
            if (isset($sessionCart[$cartKey])) {
                $sessionCart[$cartKey]['quantity'] = min(($sessionCart[$cartKey]['quantity'] ?? 0) + $quantity, $product->quantity);
            } else {
                $sessionCart[$cartKey] = [
                    'name' => $item['product_name'] ?? $product->name,
                    'price' => $price,
                    'discount' => $product->discount ?? 0,
                    'discount_type' => $product->discount_type ?? 1,
                    'image' => $item['image'] ?? $product->image,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'variant_details' => $variantDetails,
                    'quantity' => min($quantity, $product->quantity),
                ];
            }
        }
        session()->put('cart', $sessionCart);
    }
}
