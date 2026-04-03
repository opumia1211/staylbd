<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\GatewayCurrency;
use App\Models\UserSavedAddress;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Services\AbandonedCartService;
use App\Services\CodChargeService;
use App\Services\CodEligibilityService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use App\Traits\OrderConfirmation;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    use OrderConfirmation;

	public function checkout(Request $request)
	{
		$pageTitle = 'Checkout';
		$total     = session()->get('total');
		$userId    = auth()->user()->id;

		if ($total) {
			$data['subtotal'] = $total['subtotal'];
			$data['discount'] = $total['discount'];
			$data['total']    = $total['totalAmount'];
		} else {
			$cartIds = session('checkout_cart_ids');
			$subtotal = $this->cartSubTotal($userId, $cartIds);

			if ($subtotal == 0) {
				$notify[] = ['info', __('Your cart is empty or you have already placed your order. View your orders or continue shopping.')];
				return redirect()->route('user.order.index')->withNotify($notify);
			}

			$data['subtotal'] = $subtotal;
			$data['discount'] = showAmount(0.00);
			$data['total']    = $subtotal;
		}

		$countries   = json_decode(file_get_contents(resource_path('views/partials/country.json')));
		$user        = auth()->user();
		$userAddress = $user->address ?? (object)['address' => '', 'address_2' => '', 'state' => '', 'city' => '', 'zip' => '', 'country' => '', 'thana' => '', 'division' => ''];
		$cartIds = session('checkout_cart_ids');
		$cartsQuery = Cart::where('user_id', $userId)->with('product')->orderBy('id');
		if (!empty($cartIds) && is_array($cartIds)) {
			$cartsQuery->whereIn('id', $cartIds);
		}
		$carts = $cartsQuery->get()->filter(function ($cart) {
			return $cart->product !== null;
		})->values();
		$shippingService = new ShippingService();
		$shippingRules   = $shippingService->getRules();
		// Database-driven options only: no static "Dhaka — ৳0.00". Initial list from saved address or empty.
		$countryIso = getCountryIsoByName($userAddress->country ?? '');
		$city       = $userAddress->city ?? '';
		$state      = $userAddress->state ?? '';
		$shippingMethodOptions = [];
		if ($countryIso || ($userAddress->country ?? '')) {
			$shippingMethodOptions = $shippingService->getMethodsForCheckout(
				$countryIso ?: getCountryIsoByName($userAddress->country ?? ''),
				$city,
				$state,
				$data['subtotal'],
				1,
				0
			);
		}

		$thanasByDistrict = getThanaListByDistrict();
		$divisionList = getDivisionList();
		$districtsByDivision = getDistrictsByDivision();
		$savedAddresses = UserSavedAddress::where('user_id', $userId)->with(['division', 'district', 'thana'])->orderByDesc('is_default')->orderBy('id')->get();

		// Pre-fill from default saved address if available
		$defaultSaved = $savedAddresses->firstWhere('is_default', true) ?? $savedAddresses->first();
		if ($defaultSaved) {
			$userAddress = (object)[
				'address' => $defaultSaved->address_line,
				'address_2' => $defaultSaved->address_line_2 ?? '',
				'state' => $defaultSaved->state ?? '',
				'zip' => $defaultSaved->postal_code ?? '',
				'country' => $defaultSaved->country,
				'city' => $defaultSaved->district->name_en ?? $defaultSaved->city ?? '',
				'thana' => $defaultSaved->thana->name_en ?? '',
				'division' => $defaultSaved->division->name_en ?? '',
			];
		}

		$codEligibility = (new CodEligibilityService())->check(
			$data['subtotal'],
			$userAddress->country ?? '',
			$userAddress->city ?? '',
			$userAddress->state ?? '',
			$user,
			$cartIds,
			$user->mobile ?? null,
			$userAddress->address ?? null,
			$request->ip()
		);
		$cartCodCheck = (new CodEligibilityService())->isCartEligibleForCod($userId, $cartIds);
		$codEligible = $codEligibility['eligible'] && $cartCodCheck['eligible'];
		$codReason = $codEligibility['reason'] ?: $cartCodCheck['reason'];
		$codChargeService = new CodChargeService();
		$codChargeResult = $codChargeService->calculate($data['subtotal']);
		$codCharge = $codChargeResult['charge'];
		$codSettings = \App\Models\CodSettings::getCached();
		$codOtpRequired = $codSettings && $codSettings->cod_otp_required;

		$gatewayNames = GatewayCurrency::whereHas('method', function ($q) {
			$q->where('status', Status::ENABLE);
		})->with('method')->get()->pluck('method.name')->unique()->filter()->values()->toArray();
		$onlinePaymentSubtitle = count($gatewayNames) > 0 ? implode(', ', $gatewayNames) : __('Card, bKash, Nagad, etc.');

		try {
			app(AbandonedCartService::class)->recordUserCart($userId, $request, true, $cartIds ?? null);
		} catch (\Throwable $e) {
			Log::channel('single')->debug('Abandoned cart checkout record failed', ['message' => $e->getMessage()]);
		}

		return response()
			->view($this->activeTemplate . 'checkout', compact('pageTitle', 'data', 'countries', 'shippingMethodOptions', 'user', 'userAddress', 'carts', 'shippingRules', 'thanasByDistrict', 'divisionList', 'districtsByDivision', 'savedAddresses', 'codEligible', 'codReason', 'codCharge', 'codOtpRequired', 'codSettings', 'onlinePaymentSubtitle'))
			->withHeaders([
				'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
				'Pragma'         => 'no-cache',
				'Expires'        => '0',
			]);
	}

	/**
	 * AJAX: return shipping options for given address + payment type (for zone-based dynamic shipping).
	 */
	public function shippingOptions(Request $request)
	{
		$country = $request->get('country', '');
		$city    = $request->get('city', '');
		$state   = $request->get('state', '');
		$paymentType = (int) $request->get('payment_type', 1);
		$countryIso   = getCountryIsoByName($country);
		$subtotal     = (float) $request->get('subtotal', 0);
		if ($subtotal <= 0) {
			$subtotal = $this->cartSubTotal(auth()->id(), session('checkout_cart_ids'));
		}
		$service = new ShippingService();
		$methods = $service->getMethodsForCheckout($countryIso, $city, $state, $subtotal, $paymentType, 0);
		return response()->json(['success' => true, 'methods' => $methods]);
	}

	public function order(Request $request)
	{
		// FraudGuard: block blocked IP/phone when module is present
		if (class_exists(\App\Modules\FraudGuard\FraudGuardService::class)) {
			$fg = app(\App\Modules\FraudGuard\FraudGuardService::class);
			if ($fg->isBlockedIp($request->ip())) {
				$notify[] = ['error', __('Access denied.')];
				return back()->withNotify($notify)->withInput();
			}
			$user = auth()->user();
			if ($user && $fg->isBlockedPhone($request->mobile ?? $user->mobile)) {
				$notify[] = ['error', __('Account restricted. Contact support.')];
				return back()->withNotify($notify)->withInput();
			}
		}

		$request->validate([
			'firstname'       => 'required|string|max:100',
			'lastname'        => 'required|string|max:100',
			'mobile'          => 'required|string|max:50',
			'email'           => 'required|email',
			'country'         => 'required|string|max:100',
			'address'         => 'required|string|max:500',
			'address_2'       => 'nullable|string|max:500',
			'state'           => 'nullable|string|max:100',
			'city'            => 'required|string|max:100',
			'zip'             => 'required|string|max:20',
			'shipping_method' => 'required|integer|exists:shipping_methods,id',
			'payment_type'    => 'required|integer|in:1,2',
		]);

		try {
			$user     = auth()->user();
			$cartIds  = session('checkout_cart_ids');
			$subtotal = $this->cartSubTotal($user->id, $cartIds);
			$shipping = ShippingMethod::where('id', $request->shipping_method)->where('status', Status::ENABLE)->first();

			if (!$shipping) {
				$notify[] = ['error', 'Shipping method unable to locate.'];
				return back()->withNotify($notify)->withInput();
			}

			$shippingService = new ShippingService();
			$shippingCalc   = $shippingService->calculateCost($shipping, $subtotal, (int) $request->payment_type, 0);
			$shippingCost   = $shippingCalc['cost'];

			$codCharge = 0;
			if ($request->payment_type == Status::PAYMENT_OFFLINE) {
				$codEligibility = (new CodEligibilityService())->check(
					$subtotal,
					$request->country ?? '',
					$request->city ?? '',
					$request->state ?? '',
					$user,
					$cartIds,
					$request->mobile ?? null,
					$request->address ?? null,
					$request->ip()
				);
				$cartCodCheck = (new CodEligibilityService())->isCartEligibleForCod($user->id, $cartIds);
				if (!$codEligibility['eligible'] || !$cartCodCheck['eligible']) {
					$notify[] = ['error', $codEligibility['reason'] ?: $cartCodCheck['reason']];
					return back()->withNotify($notify)->withInput();
				}
				$codChargeResult = (new CodChargeService())->calculate($subtotal);
				$codCharge = $codChargeResult['charge'];
			}

			$grandTotal = $subtotal + $shippingCost + $codCharge;
			$total = session()->get('total');
			$discount = 0;
			$coupon_id = 0;
			if ($total && !empty($total['coupon_id'])) {
				$coupon = \App\Models\Coupon::find((int) $total['coupon_id']);
				if (!$coupon || !$coupon->isCurrentlyActive()) {
					session()->forget('total');
					$notify[] = ['error', 'Coupon expired or invalid. Please remove it and try again.'];
					return back()->withNotify($notify)->withInput();
				}
				$discount   = $total['discount'];
				$coupon_id  = $total['coupon_id'];
				$grandTotal = $grandTotal - $discount;
			}

			$address = [
				'address'   => $request->address,
				'address_2' => $request->input('address_2', ''),
				'state'     => $request->state,
				'zip'       => $request->zip,
				'country'   => $request->country,
				'city'      => $request->city,
				'thana'     => $request->input('thana', ''),
				'division'  => $request->input('division', ''),
			];

			if ($request->boolean('save_address', true)) {
				$user->address = $address;
				$user->save();
				// Also save to user_saved_addresses for "saved addresses" list (with division_id, district_id, thana_id)
				$divisionId = null;
				$districtId = null;
				$thanaId = null;
				if (!empty($request->division) && !empty($request->city)) {
					$div = Division::where('name_en', $request->division)->first();
					if ($div) {
						$divisionId = $div->id;
						$dist = District::where('division_id', $div->id)->where('name_en', $request->city)->first();
						if ($dist) {
							$districtId = $dist->id;
							if (!empty($request->thana)) {
								$th = Thana::where('district_id', $dist->id)->where('name_en', $request->thana)->first();
								if ($th) $thanaId = $th->id;
							}
						}
					}
				}
				$isFirst = UserSavedAddress::where('user_id', $user->id)->count() === 0;
				UserSavedAddress::create([
					'user_id' => $user->id,
					'country' => $request->country,
					'division_id' => $divisionId,
					'district_id' => $districtId,
					'thana_id' => $thanaId,
					'postal_code' => $request->zip,
					'address_line' => $request->address,
					'address_line_2' => $request->input('address_2', ''),
					'state' => $request->state ?? '',
					'city' => $request->city ?? '',
					'is_default' => $isFirst ? 1 : 0,
				]);
			}

			$zone = $shippingService->resolveZone(getCountryIsoByName($request->country), $request->city ?? '', $request->state ?? '');

			// Stock check: if another user bought and stock is now 0, show friendly message and notify admin
			$stockValidation = static::validateCartStockForOrder($user->id, $cartIds);
			if (!$stockValidation['valid']) {
				static::notifyAdminStockOutAttempt(
					$stockValidation['out_of_stock_names'] ?? [],
					$stockValidation['out_of_stock_product_ids'] ?? []
				);
				$notify[] = ['error', $stockValidation['message'] ?: __('This product is currently out of stock. Please wait—we are restocking soon. You can keep it in your cart and try again later.')];
				return back()->withNotify($notify)->withInput();
			}

			$order                      = new Order();
			$order->user_id             = $user->id;
			$order->order_no            = getTrx();
			$order->subtotal            = $subtotal;
			$order->discount            = $discount ?? 0;
			$order->shipping_charge     = $shippingCost;
			$order->cod_charge          = $codCharge;
			$order->total               = $grandTotal;
			$order->coupon_id           = $coupon_id ?? 0;
			$order->shipping_method_id  = $shipping->id;
			$order->shipping_zone_id    = $zone?->id;
			$order->delivery_estimate   = $shippingCalc['estimated_days'] ?? null;
			$order->courier_name        = $shippingCalc['courier_name'] ?? null;
			$order->address             = json_encode($address);
			$order->payment_type        = $request->payment_type;
			$order->ip_address          = $request->ip();
			$order->device_lat          = $request->filled('device_lat') ? $request->device_lat : null;
			$order->device_lng          = $request->filled('device_lng') ? $request->device_lng : null;
			$order->location_risk_score = $this->computeLocationRiskScore($request);
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

			activity_log(\App\Models\UserActivityLog::ORDER_PLACE, 'Order placed: ' . $order->order_no, 'order', $order->id);

			Log::channel('single')->info('Order placed', [
				'order_id' => $order->id,
				'order_no' => $order->order_no,
				'user_id' => $user->id,
				'total' => $grandTotal,
			]);

			if ($request->payment_type == Status::PAYMENT_ONLINE) {
				return redirect()->route('user.deposit.index', $order->id);
			}

			static::confirmOrder($order, $cartIds);

			$notify[] = ['success', 'Order successfully completed.'];
			return redirect()->route('user.order.index')->withNotify($notify);
		} catch (\Throwable $e) {
			Log::channel('single')->error('Checkout order failed', [
				'user_id' => auth()->id(),
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
			]);
			$notify[] = ['error', 'Something went wrong while placing your order. Please try again or contact support.'];
			return back()->withNotify($notify)->withInput();
		}
	}

	/**
	 * Simple location risk score 0-100 for admin (device + IP presence; can be extended with distance check).
	 */
	protected function computeLocationRiskScore(Request $request): ?int
	{
		$hasDevice = $request->filled('device_lat') && $request->filled('device_lng');
		$hasIp = $request->ip();
		if (!$hasIp) {
			return null;
		}
		if (!$hasDevice) {
			return 0;
		}
		return 25;
	}

	protected function cartSubTotal($user_id, $cartIds = null)
	{
		$query = Cart::where('user_id', $user_id)->with('product');
		if (!empty($cartIds) && is_array($cartIds)) {
			$query->whereIn('id', $cartIds);
		}
		$carts = $query->get();
		$total = [0];

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
			$total[] = $price * $cart->quantity;
		}

		$subtotal = array_sum($total);
		return $subtotal;
	}
}
