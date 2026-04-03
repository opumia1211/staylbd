<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\AbandonedCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'   => 'required|integer',
            'quantity'     => 'required|integer|gt:0',
            'variant_id'   => 'nullable|integer|exists:product_variants,id',
            'size'         => 'nullable|string|max:20',
            'custom_size'  => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()]);
        }

        $product = Product::where('id', $request->product_id)->active()->first();

        if (!$product) {
            return response()->json(['error' => __('Product not found or something went wrong')]);
        }

        $variantId = $request->variant_id ? (int) $request->variant_id : null;
        $variantDetails = null;
        $maxQty = $product->quantity;
        $price = $product->price;
        $discount = $product->discount ?? 0;
        $discountType = $product->discount_type ?? 1;

        if ($product->has_variants) {
            if ($variantId) {
                $variant = ProductVariant::where('product_id', $product->id)->where('id', $variantId)->where('status', 1)->first();
                if (!$variant) {
                    return response()->json(['error' => __('Selected option is not available.')]);
                }
                $maxQty = $variant->quantity;
                $attrs = $variant->attributes ?? [];
                $sizeKey = is_array($attrs) ? ($attrs['size'] ?? null) : null;
                if ($sizeKey === 'NO') {
                    $customSize = $request->filled('custom_size') ? trim($request->custom_size) : '';
                    if ($customSize === '') {
                        return response()->json(['error' => __('Please enter your custom size.')]);
                    }
                    $variantDetails = json_encode(['size' => 'NO', 'custom_size' => $customSize]);
                } else {
                    $variantDetails = $variant->attributes ? json_encode($variant->attributes) : null;
                }
                $price = $variant->price;
                $discount = $variant->discount ?? 0;
                $discountType = $variant->discount_type ?? 1;
            } else {
                // Size optional: add without selecting size – use main product price/quantity
                $variantId = null;
                $variantDetails = null;
                if ($request->filled('custom_size')) {
                    $variantDetails = json_encode(['size' => 'NO', 'custom_size' => trim($request->custom_size)]);
                } elseif ($request->filled('size')) {
                    $variantDetails = json_encode(['size' => trim($request->size)]);
                } else {
                    $variantDetails = json_encode(['size' => 'NO_SIZE']);
                }
                $maxQty = $product->quantity;
                $price = $product->price;
                $discount = $product->discount ?? 0;
                $discountType = $product->discount_type ?? 1;
            }
        } else {
            $size = $request->filled('size') ? trim($request->size) : null;
            $customSize = $request->filled('custom_size') ? trim($request->custom_size) : null;
            if ($size === 'NO' && $customSize !== null) {
                $variantDetails = json_encode(['size' => 'NO', 'custom_size' => $customSize]);
            } elseif ($size) {
                $variantDetails = json_encode(['size' => $size]);
            } else {
                $variantDetails = null;
            }
        }

        $userId        = auth()->id();
        $notInStockMsz = __('Requested quantity is not available in our stock');

        // When stock is 0, still allow add to cart (user can keep for later); only block when stock > 0 but requested qty exceeds it
        if ($maxQty > 0 && $request->quantity > $maxQty) {
            return response()->json(['error' => $notInStockMsz]);
        }
        $qtyToAdd = $maxQty > 0 ? min($request->quantity, $maxQty) : $request->quantity;

        if ($userId) {
            $cart = Cart::where('user_id', $userId)
                ->where('product_id', $request->product_id)
                ->where(function ($q) use ($variantId) {
                    if ($variantId) $q->where('variant_id', $variantId);
                    else $q->whereNull('variant_id');
                })
                ->where(function ($q) use ($variantDetails) {
                    if ($variantDetails === null || $variantDetails === '') {
                        $q->whereNull('variant_details');
                    } else {
                        $q->where('variant_details', $variantDetails);
                    }
                })
                ->first();

            if ($cart) {
                if ($maxQty > 0 && $cart->quantity >= $maxQty) {
                    return response()->json(['error' => $notInStockMsz]);
                }
                $cart->quantity += $qtyToAdd;
            } else {
                if (Cart::where('user_id', $userId)->count() >= Cart::CART_MAX) {
                    return response()->json(['error' => __('Maximum :max items allowed in cart. Remove some to add more.', ['max' => Cart::CART_MAX])]);
                }
                $cart = new Cart();
                $cart->user_id = auth()->id();
                $cart->product_id = $request->product_id;
                $cart->variant_id = $variantId;
                $cart->variant_details = $variantDetails;
                $cart->quantity = $qtyToAdd;
            }
            $cart->save();

        } else {
            $cartKey = $variantId ? $product->id . '_' . $variantId . '_' . (isset($variantDetails) ? $variantDetails : '') : $product->id . '_' . (isset($variantDetails) ? $variantDetails : '');
            $cart = session()->get('cart', []);

            if (isset($cart[$cartKey])) {
                if ($maxQty > 0 && $cart[$cartKey]['quantity'] >= $maxQty) {
                    return response()->json(['error' => $notInStockMsz]);
                }
                $cart[$cartKey]['quantity'] += $qtyToAdd;
            } else {
                if (count($cart) >= Cart::CART_MAX) {
                    return response()->json(['error' => __('Maximum :max items allowed in cart. Remove some to add more.', ['max' => Cart::CART_MAX])]);
                }
                $general = gs();
                $cart[$cartKey] = [
                    'name'           => $product->name,
                    'price'          => $price,
                    'discount'       => ($product->today_deals == Status::YES) ? $general->discount : $discount,
                    'discount_type'  => ($product->today_deals == Status::YES) ? $general->discount_type : $discountType,
                    'image'          => $product->image,
                    'product_id'     => $product->id,
                    'variant_id'     => $variantId,
                    'variant_details'=> $variantDetails,
                    'quantity'       => $qtyToAdd,
                ];
            }
            session()->put('cart', $cart);
        }

        activity_log(\App\Models\UserActivityLog::CART_ADD, 'Added to cart: ' . $product->name, 'product', $product->id);

        try {
            $abandonedService = app(AbandonedCartService::class);
            if ($userId) {
                $abandonedService->recordUserCart($userId, $request);
            } else {
                $abandonedService->recordGuestCart($request);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::channel('single')->debug('Abandoned cart record failed', ['message' => $e->getMessage()]);
        }

        return response()->json(['success' => __('Product added to shopping cart')]);
    }

    /**
     * Order Now / Buy Now – কার্টে যোগ করে চেকআউটে রিডাইরেক্ট (JS ছাড়াই কাজ করে)
     * Products with variants: redirect to product detail to select size.
     */
    public function buyNow($id)
    {
        $product = Product::where('id', (int) $id)->active()->first();
        if (!$product) {
            return redirect()->route('products')->with('error', __('Product not found.'));
        }
        if ($product->has_variants) {
            return redirect()->to(product_detail_url($product), 302);
        }
        $quantity  = 1;
        $variantId = null;
        $variantDetails = null;
        $price = $product->price;
        $discount = $product->discount ?? 0;
        $discountType = $product->discount_type ?? 1;
        $maxQty = $product->quantity;
        $userId = auth()->id();
        $general = gs();

        if ($quantity > $maxQty) {
            return redirect()->back()->with('error', __('Requested quantity is not available in our stock'));
        }

        if ($userId) {
            $cart = Cart::where('user_id', $userId)->where('product_id', $product->id)->whereNull('variant_id')->whereNull('variant_details')->first();
            if ($cart) {
                if ($cart->quantity >= $maxQty) {
                    return redirect()->back()->with('error', __('Requested quantity is not available in our stock'));
                }
                $cart->quantity += $quantity;
            } else {
                if (Cart::where('user_id', $userId)->count() >= Cart::CART_MAX) {
                    return redirect()->back()->with('error', __('Maximum :max items allowed in cart. Remove some to add more.', ['max' => Cart::CART_MAX]));
                }
                $cart = new Cart();
                $cart->user_id = $userId;
                $cart->product_id = $product->id;
                $cart->variant_id = null;
                $cart->variant_details = null;
                $cart->quantity = $quantity;
            }
            $cart->save();
        } else {
            $cartKey = $product->id . '_';
            $cart = session()->get('cart', []);
            if (isset($cart[$cartKey])) {
                if ($cart[$cartKey]['quantity'] >= $maxQty) {
                    return redirect()->back()->with('error', __('Requested quantity is not available in our stock'));
                }
                $cart[$cartKey]['quantity'] += $quantity;
            } else {
                if (count($cart) >= Cart::CART_MAX) {
                    return redirect()->back()->with('error', __('Maximum :max items allowed in cart. Remove some to add more.', ['max' => Cart::CART_MAX]));
                }
                $cart[$cartKey] = [
                    'name' => $product->name,
                    'price' => $price,
                    'discount' => ($product->today_deals == Status::YES) ? $general->discount : $discount,
                    'discount_type' => ($product->today_deals == Status::YES) ? $general->discount_type : $discountType,
                    'image' => $product->image,
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'variant_details' => null,
                    'quantity' => $quantity,
                ];
            }
            session()->put('cart', $cart);
        }

        if ($userId) {
            return redirect()->route('user.checkout.index');
        }

        return redirect()->route('user.guest.order');
    }

    /**
     * Restore guest cart from localStorage so it persists after browser refresh/clear.
     * Only for guests; logged-in users use DB. Payload: { "items": [ { "product_id", "quantity", "variant_id?", "variant_details?" } ] }
     */
    public function restoreGuestCart(Request $request)
    {
        if (auth()->id()) {
            return response()->json(['success' => true, 'message' => 'User cart is in database']);
        }
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|min:1',
            'items.*.quantity' => 'nullable|integer|min:1|max:999',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.variant_details' => 'nullable|string|max:500',
        ]);
        $items = $request->input('items', []);
        if (count($items) > Cart::CART_MAX) {
            $items = array_slice($items, 0, Cart::CART_MAX);
        }
        $cart = session()->get('cart', []);
        if (!is_array($cart)) {
            $cart = [];
        }
        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            $product = Product::where('id', $productId)->active()->first();
            if (!$product) {
                continue;
            }
            $qty = isset($item['quantity']) ? max(1, (int) $item['quantity']) : 1;
            $variantId = isset($item['variant_id']) && $item['variant_id'] ? (int) $item['variant_id'] : null;
            $variantDetails = isset($item['variant_details']) ? trim((string) $item['variant_details']) : null;
            $cartKey = $variantId ? $productId . '_' . $variantId . '_' . ($variantDetails ?? '') : $productId . '_' . ($variantDetails ?? '');
            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] = min($cart[$cartKey]['quantity'] + $qty, 999);
            } else {
                $price = $product->price;
                $discount = $product->discount ?? 0;
                $discountType = $product->discount_type ?? 1;
                if ($variantId) {
                    $variant = ProductVariant::where('product_id', $product->id)->where('id', $variantId)->where('status', 1)->first();
                    if ($variant) {
                        $price = $variant->price;
                        $discount = $variant->discount ?? 0;
                        $discountType = $variant->discount_type ?? 1;
                    }
                }
                $cart[$cartKey] = [
                    'name' => $product->name,
                    'price' => $price,
                    'discount' => $discount,
                    'discount_type' => $discountType,
                    'image' => $product->image,
                    'product_id' => $product->id,
                    'variant_id' => $variantId,
                    'variant_details' => $variantDetails,
                    'quantity' => $qty,
                ];
            }
        }
        session()->put('cart', $cart);
        return response()->json(['success' => true, 'count' => count($cart)]);
    }

    public function getCartCount()
    {
        $userId = auth()->id();
        $items = [];

        if ($userId) {
            $rows = Cart::where('user_id', $userId)->get(['product_id', 'variant_id']);
            $items = $rows->map(function ($r) {
                return ['product_id' => (int) $r->product_id, 'variant_id' => $r->variant_id ? (int) $r->variant_id : null];
            })->toArray();
            return response()->json(['count' => count($items), 'items' => $items]);
        }

        $cart = session()->get('cart');
        if ($cart && is_array($cart)) {
            foreach ($cart as $key => $row) {
                $pid = (int) ($row['product_id'] ?? 0);
                if ($pid <= 0) {
                    continue;
                }
                $items[] = [
                    'product_id' => $pid,
                    'quantity' => (int) ($row['quantity'] ?? 1),
                    'variant_id' => isset($row['variant_id']) && $row['variant_id'] ? (int) $row['variant_id'] : null,
                    'variant_details' => isset($row['variant_details']) ? (string) $row['variant_details'] : null,
                ];
            }
            return response()->json(['count' => count($items), 'items' => $items]);
        }

        return response()->json(['count' => 0, 'items' => []]);
    }

    public function cartProducts()
    {
        $pageTitle = 'My Cart';
        $userId    = auth()->id();
        $carts     = [];

        $cart = session()->get('cart');

        if ($userId) {
            $carts = Cart::where('user_id', $userId)->with('product')->orderBy('id', 'asc')->get()->filter(function ($cart) {
                return $cart->product !== null;
            })->values();
        } else {
            $carts = is_array($cart) ? array_values(array_map(function ($item) {
                return (object) $item;
            }, $cart)) : [];
        }

        session()->forget('total');
        try {
            $abandonedService = app(AbandonedCartService::class);
            if ($userId) {
                $abandonedService->recordUserCart($userId, request());
            } else {
                $abandonedService->recordGuestCart(request());
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::channel('single')->debug('Abandoned cart record failed', ['message' => $e->getMessage()]);
        }
        $emptyMessage = 'Your cart is empty';
        return view($this->activeTemplate . 'cart', compact('pageTitle', 'carts', 'emptyMessage'));
    }

    /** Cart inside user dashboard (sidebar + menu bar stay). */
    public function cartProductsDashboard()
    {
        $pageTitle = 'My Cart';
        $userId    = auth()->id();
        $carts     = [];

        if (!$userId && request()->boolean('open_guest_checkout')) {
            $guestCart = session('cart', []);
            if (is_array($guestCart) && $guestCart !== []) {
                return redirect()->route('user.guest.order');
            }
        }

        $cart = session()->get('cart');

        if ($userId) {
            $carts = Cart::where('user_id', $userId)->with(['product' => function ($q) {
                $q->with(['category', 'brand', 'activeVariants'])->withCount('reviews');
            }])->orderBy('id', 'asc')->get()->filter(function ($cart) {
                return $cart->product !== null;
            })->values();
        } else {
            if (is_array($cart) && !empty($cart)) {
                $productIds = array_unique(array_map(function ($item) {
                    return (int) ($item['product_id'] ?? 0);
                }, array_values($cart)));
                $productIds = array_filter($productIds);
                $products = Product::active()->whereIn('id', $productIds)
                    ->with(['category', 'brand', 'activeVariants'])->withCount('reviews')->get()->keyBy('id');
                $carts = collect();
                foreach ($cart as $key => $item) {
                    $item = (object) $item;
                    $product = $products->get($item->product_id ?? 0);
                    if (!$product) continue;
                    $item->product = $product;
                    $item->id = $key;
                    $carts->push($item);
                }
                $carts = $carts->values();
            } else {
                $carts = collect();
            }
        }

        session()->forget('total');
        try {
            $abandonedService = app(AbandonedCartService::class);
            if ($userId) {
                $abandonedService->recordUserCart($userId, request());
            } else {
                $abandonedService->recordGuestCart(request());
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::channel('single')->debug('Abandoned cart record failed', ['message' => $e->getMessage()]);
        }
        $emptyMessage = 'Your cart is empty';
        return view($this->activeTemplate . 'user.cart', compact('pageTitle', 'carts', 'emptyMessage'));
    }

    public function removeCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'     => 'required|integer',
            'variant_id'     => 'nullable|integer',
            'variant_details'=> 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $userId = auth()->id();
        $variantId = $request->variant_id ? (int) $request->variant_id : null;
        $variantDetails = $request->filled('variant_details') ? trim($request->variant_details) : null;

        if ($userId) {
            $cart = Cart::where('user_id', $userId)->where('product_id', $request->product_id)
                ->where(function ($q) use ($variantId) {
                    if ($variantId) $q->where('variant_id', $variantId);
                    else $q->whereNull('variant_id');
                })->first();
            if ($cart) {
                $cart->delete();
            }
        } else {
            $cart = session()->get('cart', []);
            if (is_array($cart)) {
                if ($variantId) {
                    $key = $request->product_id . '_' . $variantId . ($variantDetails ? '_' . $variantDetails : '');
                    unset($cart[$key]);
                } else {
                    unset($cart[$request->product_id]);
                    unset($cart[$request->product_id . '_']);
                    if ($variantDetails) {
                        unset($cart[$request->product_id . '_' . $variantDetails]);
                    }
                }
                session()->put('cart', $cart);
            }
        }

        $product = Product::find($request->product_id);
        activity_log(\App\Models\UserActivityLog::CART_REMOVE, $product ? 'Removed from cart: ' . $product->name : 'Removed from cart', 'product', $request->product_id);

        try {
            $abandonedService = app(AbandonedCartService::class);
            if ($userId) {
                $abandonedService->recordUserCart($userId, $request);
            } else {
                $abandonedService->recordGuestCart($request);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::channel('single')->debug('Abandoned cart record failed', ['message' => $e->getMessage()]);
        }

        return response()->json(['success' => 'Product was successfully removed.']);
    }

    /**
     * Clear all items from cart (logged-in user or session).
     */
    public function clearCart(Request $request)
    {
        $userId = auth()->id();
        if ($userId) {
            Cart::where('user_id', $userId)->delete();
        } else {
            session()->forget('cart');
        }
        activity_log(\App\Models\UserActivityLog::CART_REMOVE, 'Cart cleared', null, null);
        return response()->json(['success' => __('Cart cleared successfully.')]);
    }

    public function updateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'      => 'required|integer',
            'quantity'        => 'required|integer|gt:0',
            'variant_id'      => 'nullable|integer',
            'variant_details' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $product = Product::findOrFail($request->product_id);
        $userId  = auth()->id();
        $variantId = $request->variant_id ? (int) $request->variant_id : null;
        $variantDetails = $request->filled('variant_details') ? trim($request->variant_details) : null;

        $maxQty = $product->quantity;
        if ($variantId) {
            $variant = ProductVariant::where('product_id', $product->id)->where('id', $variantId)->where('status', 1)->first();
            if ($variant) {
                $maxQty = $variant->quantity;
            }
        }

        if ($request->quantity > $maxQty) {
            return response()->json(['error' => __('Requested quantity is not available in our stock.')]);
        }

        if ($userId) {
            $cart = Cart::where('user_id', $userId)->where('product_id', $request->product_id)
                ->where(function ($q) use ($variantId) {
                    if ($variantId) $q->where('variant_id', $variantId);
                    else $q->whereNull('variant_id');
                })->first();
            if ($cart) {
                $cart->quantity = $request->quantity;
                $cart->save();
            }
        } else {
            $cart = session()->get('cart');
            if ($variantId) {
                $key = $request->product_id . '_' . $variantId . ($variantDetails ? '_' . $variantDetails : '');
            } else {
                $key = $variantDetails ? $request->product_id . '_' . $variantDetails : $request->product_id;
                if (!isset($cart[$key])) $key = $request->product_id . '_';
            }
            if (isset($cart[$key])) {
                $cart[$key]['quantity'] = $request->quantity;
                session()->put('cart', $cart);
            }
        }

        try {
            $abandonedService = app(AbandonedCartService::class);
            if ($userId) {
                $abandonedService->recordUserCart($userId, $request);
            } else {
                $abandonedService->recordGuestCart($request);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::channel('single')->debug('Abandoned cart record failed', ['message' => $e->getMessage()]);
        }

        return response()->json(['success' => __('Cart was successfully updated.')]);
    }

    public function couponApply(Request $request)
    {
        $coupon = Coupon::where('name', $request->coupon)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->active()->first();

        if (!$coupon) {
            return response()->json(['error' => __('No coupon found.')]);
        }

        // Usage limit check
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['error' => __('This coupon has reached its usage limit.')]);
        }

        $userId  = auth()->id();
        $general = gs();

        // First-order only coupon: allow only if user has no successful/non-cancelled orders yet.
        if ($coupon->is_first_order_only ?? false) {
            if (!$userId) {
                return response()->json(['error' => __('Please login to use this coupon.')]);
            }
            $hasOrder = \App\Models\Order::where('user_id', $userId)
                ->where('order_status', '!=', Status::ORDER_CANCEL)
                ->exists();
            if ($hasOrder) {
                return response()->json(['error' => __('This coupon is valid only on your first order.')]);
            }
        }

        // Per-user limit check (for logged-in users)
        if ($userId && $coupon->per_user_limit !== null) {
            $userUsage = \App\Models\Order::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();
            if ($userUsage >= $coupon->per_user_limit) {
                return response()->json(['error' => __('You have already used this coupon the maximum number of times.')]);
            }
        }

        if ($userId) {
            $carts = Cart::where('user_id', $userId)->with('product')->get();

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
        } else {
            $carts = session()->get('cart');
            if (!is_array($carts)) {
                $carts = [];
            }
            foreach ($carts as $cart) {
                $price = isset($cart['price']) ? showDiscountPrice($cart['price'], $cart['discount'] ?? 0, $cart['discount_type'] ?? 1) : 0;
                $total[] = $price * ($cart['quantity'] ?? 0);
            }
        }

        $subtotal = array_sum($total);

        if ($coupon->min_order > $subtotal) {
            return response()->json(['error' => 'Sorry, you have to order a minimum amount of ' . $general->cur_sym . showAmount($coupon->min_order)]);
        }

        if ($coupon->discount_type == 1) {
            $discount = $coupon->discount;
        } else {
            $discount = $subtotal * $coupon->discount / 100;
            // Max discount cap (for % coupons)
            if ($coupon->max_discount !== null && $discount > $coupon->max_discount) {
                $discount = $coupon->max_discount;
            }
        }

        $totalAmount = $subtotal - $discount;

        $total = [
            'coupon_name'   => $coupon->name,
            'coupon_id'     => $coupon->id,
            'discount_type' => $coupon->discount_type,
            'subtotal'      => $subtotal,
            'discount'      => $discount,
            'totalAmount'   => $totalAmount,
        ];

        session()->put('total', $total);

        return response()->json([
            'success'     => 'Coupon has been successfully added.',
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * Store selected cart IDs for checkout (only these will be used on checkout page and order).
     */
    public function setCheckoutSelection(Request $request)
    {
        $request->validate([
            'cart_ids' => 'required|array',
            'cart_ids.*' => 'integer',
        ]);

        $userId = auth()->id();
        $ids = array_map('intval', $request->cart_ids);
        $ids = array_values(array_unique(array_filter($ids)));

        $validIds = Cart::where('user_id', $userId)->whereIn('id', $ids)->pluck('id')->toArray();

        if (empty($validIds)) {
            return redirect()->route('user.cart')->with('error', __('Please select at least one item to checkout.'));
        }

        session()->put('checkout_cart_ids', $validIds);
        session()->forget('total'); // Recalculate on checkout from selected items only

        return redirect()->route('user.checkout.index');
    }
}
