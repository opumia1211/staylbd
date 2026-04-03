<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Services\CodChargeService;
use App\Services\CodEligibilityService;
use App\Services\ShippingService;
use App\Traits\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuestCheckoutController extends Controller
{
    use OrderConfirmation;

    /**
     * Return location data (divisions, districts by division, thanas by district) for the guest order modal.
     * Public route – no auth required.
     */
    public function locationData()
    {
        $divisionList = getDivisionList();
        $districtsByDivision = getDistrictsByDivision();
        $thanasByDistrict = getThanaListByDistrict();
        $countriesRaw = json_decode(file_get_contents(resource_path('views/partials/country.json')), true) ?: [];
        $countries = [];
        foreach ($countriesRaw as $code => $item) {
            if (is_array($item) && !empty($item['country'])) {
                $countries[] = ['code' => $code, 'name' => $item['country']];
            }
        }
        return response()->json([
            'success' => true,
            'divisions' => $divisionList,
            'districts_by_division' => $districtsByDivision,
            'thanas_by_district' => $thanasByDistrict,
            'countries' => $countries,
        ]);
    }

    /**
     * Full-page guest checkout (order + delivery form). Replaces cart?open_guest_checkout=1 for Buy Now / Quick Order.
     */
    public function showOrderPage()
    {
        if (auth()->check()) {
            return redirect()->route('user.checkout.index');
        }

        $sessionCart = session('cart', []);
        if (!is_array($sessionCart) || empty($sessionCart)) {
            $notify[] = ['info', __('Your cart is empty.')];

            return redirect()->route('user.cart')->withNotify($notify);
        }

        $subtotal = $this->guestCartSubtotal($sessionCart);
        if ($subtotal <= 0) {
            $notify[] = ['error', __('No valid items in cart.')];

            return redirect()->route('user.cart')->withNotify($notify);
        }

        $productIds = array_unique(array_map(static function ($item) {
            return (int) ($item['product_id'] ?? 0);
        }, array_values($sessionCart)));
        $productIds = array_values(array_filter($productIds));

        $products = Product::active()->whereIn('id', $productIds)
            ->with(['category', 'brand'])
            ->get()
            ->keyBy('id');

        $cartLines = [];
        foreach ($sessionCart as $raw) {
            if (!is_array($raw)) {
                continue;
            }
            $item = (object) $raw;
            $pid = (int) ($item->product_id ?? 0);
            $product = $products->get($pid);
            if (!$product) {
                continue;
            }
            $qty = max(1, (int) ($item->quantity ?? 1));
            $price = productPrice($product);
            $variantId = !empty($item->variant_id) ? (int) $item->variant_id : null;
            if ($variantId) {
                $variant = ProductVariant::where('product_id', $product->id)->where('id', $variantId)->where('status', 1)->first();
                if ($variant) {
                    $price = showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1);
                }
            }
            $cartLines[] = (object) [
                'product' => $product,
                'quantity' => $qty,
                'unit_price' => $price,
                'line_total' => $price * $qty,
                'variant_id' => $variantId,
                'variant_details' => $item->variant_details ?? null,
            ];
        }

        if (empty($cartLines)) {
            $notify[] = ['error', __('No valid items in cart.')];

            return redirect()->route('user.cart')->withNotify($notify);
        }

        $pageTitle = __('Checkout');
        $general = gs();

        return response()
            ->view($this->activeTemplate . 'guest_order', compact('pageTitle', 'cartLines', 'subtotal', 'general'))
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    /**
     * Submit guest order. Expects session cart. Validates guest fields, creates order with user_type=guest.
     * For quick_order source, validation rules follow admin-configured Quick Order enabled fields.
     */
    public function submit(Request $request)
    {
        $isQuickOrder = $request->input('order_source') === 'quick_order';
        $enabled = $isQuickOrder && function_exists('getQuickOrderEnabledFields')
            ? getQuickOrderEnabledFields()
            : null;

        $rules = [
            'country'     => 'required|string|max:100',
            'division'    => 'nullable|string|max:100',
            'district'    => 'nullable|string|max:100',
            'city'        => 'nullable|string|max:100',
            'thana'       => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ];
        $messages = [];

        if ($enabled !== null) {
            $requiredFields = ['guest_phone', 'guest_name', 'guest_address', 'guest_area_city'];
            $optionalFields = [
                'guest_delivery_note', 'guest_email', 'guest_preferred_delivery_time',
                'guest_alternate_phone', 'guest_landmark', 'guest_order_note',
                'guest_preferred_contact_time', 'postal_code',
            ];
            foreach ($requiredFields as $f) {
                if (in_array($f, $enabled, true)) {
                    if ($f === 'guest_phone') {
                        $rules[$f] = 'required|string|max:50|regex:/^[0-9+\-\s]{10,20}$/';
                        $messages[$f . '.required'] = __('Mobile number is required.');
                        $messages[$f . '.regex'] = __('Please enter a valid mobile number.');
                    } elseif ($f === 'guest_area_city') {
                        $rules[$f] = 'required|string|max:200';
                    } else {
                        $rules[$f] = 'required|string|max:' . ($f === 'guest_name' ? '200' : '1000');
                        $messages[$f . '.required'] = $f === 'guest_name' ? __('Full name is required.') : __('Delivery address is required.');
                    }
                }
            }
            foreach ($optionalFields as $f) {
                if (in_array($f, $enabled, true)) {
                    if ($f === 'guest_email') {
                        $rules[$f] = 'nullable|email|max:100';
                    } elseif ($f === 'guest_alternate_phone') {
                        $rules[$f] = 'nullable|string|max:50|regex:/^[0-9+\-\s]{0,20}$/';
                    } elseif ($f === 'postal_code') {
                        $rules[$f] = 'nullable|string|max:20';
                    } elseif (in_array($f, ['guest_delivery_note', 'guest_order_note'], true)) {
                        $rules[$f] = 'nullable|string|max:500';
                    } else {
                        $rules[$f] = 'nullable|string|max:200';
                    }
                }
            }
        } else {
            $rules['guest_phone'] = 'required|string|max:50|regex:/^[0-9+\-\s]{10,20}$/';
            $rules['guest_email'] = 'nullable|email|max:100';
            $rules['guest_name'] = 'required|string|max:200';
            $rules['guest_address'] = 'required|string|max:1000';
            $rules['guest_delivery_note'] = 'nullable|string|max:500';
            $rules['guest_preferred_delivery_time'] = 'nullable|string|max:200';
            $messages = [
                'guest_phone.required' => __('Mobile number is required.'),
                'guest_phone.regex'    => __('Please enter a valid mobile number.'),
                'guest_name.required' => __('Full name is required.'),
                'guest_address.required' => __('Delivery address is required.'),
            ];
        }

        $request->validate($rules, $messages);

        $sessionCart = session('cart', []);
        if (empty($sessionCart) || !is_array($sessionCart)) {
            return response()->json(['success' => false, 'message' => __('Your cart is empty. Add items before placing order.')], 422);
        }

        if (class_exists(\App\Modules\FraudGuard\FraudGuardService::class)) {
            $fg = app(\App\Modules\FraudGuard\FraudGuardService::class);
            if ($fg->isBlockedIp($request->ip())) {
                return response()->json(['success' => false, 'message' => __('Access denied.')], 403);
            }
            if ($fg->isBlockedPhone($request->guest_phone)) {
                return response()->json(['success' => false, 'message' => __('This number is restricted. Please contact support.')], 403);
            }
        }

        try {
            $subtotal = $this->guestCartSubtotal($sessionCart);
            if ($subtotal <= 0) {
                return response()->json(['success' => false, 'message' => __('No valid items in cart.')], 422);
            }

            $stockValidation = static::validateSessionCartStock($sessionCart);
            if (!$stockValidation['valid']) {
                static::notifyAdminStockOutAttempt(
                    $stockValidation['out_of_stock_names'] ?? [],
                    $stockValidation['out_of_stock_product_ids'] ?? []
                );
                return response()->json([
                    'success' => false,
                    'message' => $stockValidation['message'] ?: __('This product is currently out of stock. Please wait—we are restocking soon. You can keep it in your cart and try again later.'),
                ], 422);
            }

            $country = $request->country;
            $city = $request->district ?: $request->city ?: $request->input('guest_area_city');
            $state = $request->thana ?: $request->division;
            $countryIso = getCountryIsoByName($country);
            $shippingService = new ShippingService();
            $methods = $shippingService->getMethodsForCheckout($countryIso ?: 'BD', $city ?? '', $state ?? '', $subtotal, Status::PAYMENT_OFFLINE, 0);
            $shipping = null;
            $shippingCost = 0;
            $shippingMethodId = 0;
            $zone = null;
            $deliveryEstimate = null;
            $courierName = null;

            if (!empty($methods) && is_array($methods)) {
                $first = $methods[0];
                $methodId = (int) ($first['id'] ?? 0);
                if ($methodId) {
                    $shipping = ShippingMethod::where('id', $methodId)->where('status', Status::ENABLE)->first();
                    if ($shipping) {
                        $shippingMethodId = $shipping->id;
                        $calc = $shippingService->calculateCost($shipping, $subtotal, Status::PAYMENT_OFFLINE, 0);
                        $shippingCost = $calc['cost'];
                        $deliveryEstimate = $calc['estimated_days'] ?? null;
                        $courierName = $calc['courier_name'] ?? null;
                    }
                }
            }
            if (!$shipping && $shippingMethodId === 0) {
                $shipping = ShippingMethod::where('status', Status::ENABLE)->orderBy('id')->first();
                if ($shipping) {
                    $shippingMethodId = $shipping->id;
                    $calc = $shippingService->calculateCost($shipping, $subtotal, Status::PAYMENT_OFFLINE, 0);
                    $shippingCost = $calc['cost'];
                    $deliveryEstimate = $calc['estimated_days'] ?? null;
                    $courierName = $calc['courier_name'] ?? null;
                }
            }

            $zone = $shippingService->resolveZone($countryIso ?: 'BD', $city ?? '', $state ?? '');
            $codCharge = 0;
            $codEligibility = (new CodEligibilityService())->check(
                $subtotal,
                $country ?? '',
                $city ?? '',
                $state ?? '',
                null,
                null,
                $request->input('guest_phone'),
                $request->input('guest_address'),
                $request->ip()
            );
            if (!$codEligibility['eligible']) {
                return response()->json(['success' => false, 'message' => $codEligibility['reason'] ?? __('Cash on delivery is not available for this order.')], 422);
            }
            $codChargeResult = (new CodChargeService())->calculate($subtotal);
            $codCharge = $codChargeResult['charge'];

            $discount = 0;
            $couponId = 0;
            $totalSession = session('total');
            if ($totalSession && !empty($totalSession['coupon_id'])) {
                $coupon = \App\Models\Coupon::find((int) $totalSession['coupon_id']);
                if ($coupon && $coupon->isCurrentlyActive()) {
                    $discount = $totalSession['discount'] ?? 0;
                    $couponId = $totalSession['coupon_id'];
                }
            }

            $grandTotal = $subtotal + $shippingCost + $codCharge - $discount;
            $guestLocation = implode(', ', array_filter([
                $request->division,
                $request->district,
                $request->city,
                $request->input('guest_area_city'),
                $request->thana,
                $request->country,
            ]));
            if (trim($guestLocation) === '') {
                $guestLocation = $request->country;
            }

            $deliveryNote = $request->input('guest_delivery_note') ?: '';
            $orderNote = $request->input('guest_order_note') ?: '';
            if ($orderNote !== '') {
                $deliveryNote = $deliveryNote === '' ? $orderNote : $deliveryNote . "\n" . __('Order note') . ': ' . $orderNote;
            }
            $address = [
                'address'   => $request->input('guest_address', ''),
                'address_2' => '',
                'state'     => $request->thana ?? $request->state ?? '',
                'zip'       => $request->input('postal_code') ?? $request->postal_code ?? '',
                'country'   => $country,
                'city'      => $request->district ?: $request->city ?: $request->input('guest_area_city'),
                'thana'     => $request->thana ?? '',
                'division'  => $request->division ?? '',
                'landmark'  => $request->input('guest_landmark') ?: '',
                'alternate_phone' => $request->input('guest_alternate_phone') ?: '',
                'preferred_contact_time' => $request->input('guest_preferred_contact_time') ?: '',
            ];

            $order = new Order();
            $order->user_id = null;
            $order->user_type = 'guest';
            $order->guest_name = $request->input('guest_name');
            $order->guest_phone = $request->input('guest_phone');
            $order->guest_email = $request->input('guest_email') ?: null;
            $order->guest_address = $request->input('guest_address');
            $order->guest_location = $guestLocation;
            $order->guest_delivery_note = $deliveryNote ?: null;
            $order->guest_preferred_delivery_time = $request->input('guest_preferred_delivery_time') ?: null;
            $order->order_no = getTrx();
            $order->subtotal = $subtotal;
            $order->discount = $discount;
            $order->shipping_charge = $shippingCost;
            $order->cod_charge = $codCharge;
            $order->total = $grandTotal;
            $order->coupon_id = $couponId;
            $order->shipping_method_id = $shippingMethodId ?: null;
            $order->shipping_zone_id = $zone?->id;
            $order->delivery_estimate = $deliveryEstimate;
            $order->courier_name = $courierName;
            $order->address = json_encode($address);
            $order->payment_type = Status::PAYMENT_OFFLINE;
            $order->order_status = Status::ORDER_PENDING;
            $order->payment_status = Status::ORDER_PAYMENT_PENDING;
            $order->ip_address = $request->ip();
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'order_source')) {
                $order->order_source = $request->input('order_source') === 'quick_order' ? 'quick_order' : 'guest_checkout';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'ad_source')) {
                $order->ad_source = $request->input('ad_source') ?: $request->input('utm_source');
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'utm_source')) {
                $order->utm_source = $request->input('utm_source');
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'utm_medium')) {
                $order->utm_medium = $request->input('utm_medium');
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'utm_campaign')) {
                $order->utm_campaign = $request->input('utm_campaign');
            }
            $order->save();

            static::confirmGuestOrder($order, $sessionCart);

            $message = __('Your order has been successfully placed. Order no: :order_no. Our team will contact you shortly.', ['order_no' => $order->order_no]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'order_no' => $order->order_no,
            ]);
        } catch (\Throwable $e) {
            Log::channel('single')->error('Guest checkout failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again or contact support.'),
            ], 500);
        }
    }

    /**
     * Calculate subtotal from session cart (same logic as frontend cart total).
     */
    protected function guestCartSubtotal(array $sessionCart): float
    {
        $subtotal = 0;
        foreach ($sessionCart as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            $product = \App\Models\Product::where('id', $productId)->active()->first();
            if (!$product) {
                continue;
            }
            $price = productPrice($product);
            $variantId = isset($item['variant_id']) ? (int) $item['variant_id'] : null;
            if ($variantId) {
                $variant = \App\Models\ProductVariant::find($variantId);
                if ($variant && $variant->product_id == $productId) {
                    $price = showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1);
                }
            }
            $subtotal += $price * $quantity;
        }
        return (float) $subtotal;
    }
}
