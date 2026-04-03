<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Cart;
use App\Models\CodBlacklist;
use App\Models\CodSettings;
use App\Models\Order;
use App\Models\User;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Schema;

class CodEligibilityService
{
    protected ?CodSettings $settings = null;
    protected ?ShippingService $shippingService = null;

    public function __construct(?CodSettings $settings = null, ?ShippingService $shippingService = null)
    {
        $this->settings = $settings ?? CodSettings::getCached();
        $this->shippingService = $shippingService ?? new ShippingService();
    }

    /**
     * Check if COD is allowed globally and for the given context.
     * Returns ['eligible' => bool, 'reason' => string].
     */
    public function check(
        float $orderSubtotal,
        string $country,
        string $city,
        string $state,
        ?User $user = null,
        ?array $cartIds = null,
        ?string $mobile = null,
        ?string $addressLine = null,
        ?string $ip = null
    ): array {
        if (!$this->settings) {
            return ['eligible' => true, 'reason' => ''];
        }
        if (!$this->settings->cod_enabled) {
            return ['eligible' => false, 'reason' => __('COD is currently disabled.')];
        }

        $minOrder = (float) ($this->settings->cod_min_order ?? 0);
        if ($minOrder > 0 && $orderSubtotal < $minOrder) {
            return ['eligible' => false, 'reason' => __('Minimum order for COD is :amount', ['amount' => showAmount($minOrder)])];
        }

        $maxOrder = (float) ($this->settings->cod_max_order ?? 0);
        if ($maxOrder > 0 && $orderSubtotal > $maxOrder) {
            return ['eligible' => false, 'reason' => __('Maximum order for COD is :amount', ['amount' => showAmount($maxOrder)])];
        }

        if ($user) {
            if (Schema::hasColumn($user->getTable(), 'cod_disabled_until') && $user->cod_disabled_until && \Carbon\Carbon::parse($user->cod_disabled_until)->isFuture()) {
                return ['eligible' => false, 'reason' => __('COD is temporarily disabled for your account.')];
            }
            $failedCount = (int) ($user->cod_failed_count ?? 0);
            $disableAfter = (int) ($this->settings->cod_failed_disable_count ?? 2);
            if ($disableAfter > 0 && $failedCount >= $disableAfter) {
                return ['eligible' => false, 'reason' => __('COD is not available for your account due to previous delivery issues.')];
            }
            $newCustomerMax = (float) ($this->settings->cod_new_customer_max ?? 0);
            if ($newCustomerMax > 0) {
                $orderCount = Order::where('user_id', $user->id)->where('payment_type', Status::PAYMENT_OFFLINE)->whereIn('order_status', [
                    Status::ORDER_CONFIRMED, Status::ORDER_SHIPPED, Status::ORDER_DELIVERED, Status::ORDER_OUT_FOR_DELIVERY
                ])->count();
                if ($orderCount === 0 && $orderSubtotal > $newCustomerMax) {
                    return ['eligible' => false, 'reason' => __('For first COD order, maximum amount is :amount', ['amount' => showAmount($newCustomerMax)])];
                }
            }
        }

        if ($mobile && CodBlacklist::isBlacklisted(CodBlacklist::TYPE_MOBILE, $mobile)) {
            return ['eligible' => false, 'reason' => __('This mobile number is not allowed for COD.')];
        }
        if ($addressLine && CodBlacklist::isBlacklisted(CodBlacklist::TYPE_ADDRESS, $this->hashAddress($addressLine))) {
            return ['eligible' => false, 'reason' => __('This address is not allowed for COD.')];
        }
        if ($ip && CodBlacklist::isBlacklisted(CodBlacklist::TYPE_IP, $ip)) {
            return ['eligible' => false, 'reason' => __('COD is not available.')];
        }

        $countryIso = getCountryIsoByName($country);
        $zone = $this->shippingService->resolveZone($countryIso ?: $country, $city, $state);
        if ($zone && Schema::hasColumn($zone->getTable(), 'cod_enabled')) {
            if (!($zone->cod_enabled ?? true)) {
                return ['eligible' => false, 'reason' => __('COD is not available for this area.')];
            }
        }

        return ['eligible' => true, 'reason' => ''];
    }

    /**
     * Check if any product in cart has COD disabled or is digital/pre-order (no COD).
     */
    public function isCartEligibleForCod(int $userId, ?array $cartIds = null): array
    {
        $query = Cart::where('user_id', $userId)->with('product');
        if (!empty($cartIds) && is_array($cartIds)) {
            $query->whereIn('id', $cartIds);
        }
        $carts = $query->get();
        foreach ($carts as $cart) {
            if (!$cart->product) {
                continue;
            }
            $p = $cart->product;
            if (Schema::hasColumn($p->getTable(), 'cod_disabled') && ($p->cod_disabled ?? false)) {
                return ['eligible' => false, 'reason' => __('Some items in cart do not support COD.')];
            }
            if (!empty($p->digital_item)) {
                return ['eligible' => false, 'reason' => __('Digital or downloadable items are not available for COD.')];
            }
        }
        return ['eligible' => true, 'reason' => ''];
    }

    protected function hashAddress(string $address): string
    {
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $address)));
        return hash('sha256', $normalized);
    }
}
