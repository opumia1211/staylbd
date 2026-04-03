<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Constants\Status;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get current user's cart (items with product info).
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $carts = Cart::where('user_id', $userId)->with('product')->orderBy('id')->get();

        $items = [];
        $subtotal = 0;
        foreach ($carts as $cart) {
            $price = $this->getCartItemPrice($cart);
            $total = $price * $cart->quantity;
            $subtotal += $total;
            $items[] = [
                'cart_id' => $cart->id,
                'product_id' => $cart->product_id,
                'variant_id' => $cart->variant_id,
                'name' => $cart->product->name ?? '',
                'image' => $cart->product ? getImage(getFilePath('product') . '/' . $cart->product->image, getFileSize('product')) : null,
                'price' => $price,
                'quantity' => $cart->quantity,
                'total' => $total,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'subtotal' => round($subtotal, 2),
                'count' => $carts->count(),
            ],
        ]);
    }

    /**
     * Add to cart.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product = Product::where('id', $request->product_id)->active()->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $variantId = $request->variant_id ? (int) $request->variant_id : null;
        $maxQty = $product->quantity;
        $price = $product->price;
        $discount = $product->discount ?? 0;
        $discountType = $product->discount_type ?? 1;
        $variantDetails = null;

        if ($product->has_variants) {
            if (!$variantId) {
                return response()->json(['success' => false, 'message' => 'Please select a variant.'], 422);
            }
            $variant = ProductVariant::where('product_id', $product->id)->where('id', $variantId)->where('status', 1)->first();
            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Variant not available.'], 422);
            }
            $maxQty = $variant->quantity;
            $price = $variant->price;
            $discount = $variant->discount ?? 0;
            $discountType = $variant->discount_type ?? 1;
            $variantDetails = $variant->attributes ? json_encode($variant->attributes) : null;
        }

        if ($request->quantity > $maxQty) {
            return response()->json(['success' => false, 'message' => 'Requested quantity not available in stock.'], 422);
        }

        $userId = $request->user()->id;
        $cart = Cart::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->where(function ($q) use ($variantId) {
                if ($variantId) {
                    $q->where('variant_id', $variantId);
                } else {
                    $q->whereNull('variant_id');
                }
            })
            ->when(!$variantId, function ($q) use ($variantDetails) {
                if ($variantDetails === null || $variantDetails === '') {
                    $q->whereNull('variant_details');
                } else {
                    $q->where('variant_details', $variantDetails);
                }
            })
            ->first();

        if ($cart) {
            $newQty = $cart->quantity + $request->quantity;
            if ($newQty > $maxQty) {
                return response()->json(['success' => false, 'message' => 'Requested quantity not available in stock.'], 422);
            }
            $cart->quantity = $newQty;
            $cart->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'variant_id' => $variantId,
                'variant_details' => $variantDetails,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Product added to cart.']);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
            'variant_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $userId = $request->user()->id;
        $variantId = $request->variant_id ? (int) $request->variant_id : null;

        $cart = Cart::where('user_id', $userId)->where('product_id', $request->product_id)
            ->where(function ($q) use ($variantId) {
                if ($variantId) {
                    $q->where('variant_id', $variantId);
                } else {
                    $q->whereNull('variant_id');
                }
            })
            ->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart item not found.'], 404);
        }

        if ($request->quantity === 0) {
            $cart->delete();
            return response()->json(['success' => true, 'message' => 'Item removed from cart.']);
        }

        $maxQty = $cart->product->quantity ?? 0;
        if ($cart->variant_id) {
            $v = ProductVariant::where('id', $cart->variant_id)->where('product_id', $cart->product_id)->first();
            if ($v) {
                $maxQty = $v->quantity;
            }
        }
        if ($request->quantity > $maxQty) {
            return response()->json(['success' => false, 'message' => 'Requested quantity not available in stock.'], 422);
        }

        $cart->quantity = $request->quantity;
        $cart->save();

        return response()->json(['success' => true, 'message' => 'Cart updated.']);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $userId = $request->user()->id;
        $variantId = $request->variant_id ? (int) $request->variant_id : null;

        $deleted = Cart::where('user_id', $userId)->where('product_id', $request->product_id)
            ->where(function ($q) use ($variantId) {
                if ($variantId) {
                    $q->where('variant_id', $variantId);
                } else {
                    $q->whereNull('variant_id');
                }
            })
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Cart item not found.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Item removed from cart.']);
    }

    /**
     * Cart items count.
     */
    public function count(Request $request): JsonResponse
    {
        $count = Cart::where('user_id', $request->user()->id)->count();
        return response()->json(['success' => true, 'count' => $count]);
    }

    /**
     * Apply coupon (returns discount info; actual application at checkout).
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'coupon' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $coupon = Coupon::where('name', $request->coupon)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->active()
            ->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon.'], 422);
        }

        $userId = $request->user()->id;
        $subtotal = Cart::where('user_id', $userId)->with('product')->get()->sum(function ($cart) {
            return $this->getCartItemPrice($cart) * $cart->quantity;
        });

        $discount = 0;
        if ($coupon->type == 1) {
            $discount = ($subtotal * $coupon->discount) / 100;
        } else {
            $discount = min($coupon->discount, $subtotal);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'coupon' => $coupon->name,
                'discount' => round($discount, 2),
                'subtotal' => round($subtotal, 2),
                'total_after_discount' => round($subtotal - $discount, 2),
            ],
        ]);
    }

    private function getCartItemPrice($cart): float
    {
        $product = $cart->product;
        if (!$product) {
            return 0;
        }
        $price = $product->price;
        $discount = $product->discount ?? 0;
        $discountType = $product->discount_type ?? 1;
        if ($cart->variant_id) {
            $variant = ProductVariant::find($cart->variant_id);
            if ($variant) {
                $price = $variant->price;
                $discount = $variant->discount ?? 0;
                $discountType = $variant->discount_type ?? 1;
            }
        }
        return (float) showDiscountPrice($price, $discount, $discountType);
    }
}
