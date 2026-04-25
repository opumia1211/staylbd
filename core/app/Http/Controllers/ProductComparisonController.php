<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComparison;
use Illuminate\Http\Request;

class ProductComparisonController extends Controller
{
    /** Maximum products allowed in compare list (must match ProductComparison::COMPARE_MAX). */
    private const COMPARE_MAX = \App\Models\ProductComparison::COMPARE_MAX;

    /**
     * Show comparison page
     */
    public function index(Request $request)
    {
        $pageTitle = 'Product Comparison';
        $products = ProductComparison::getItems()
            ->filter(function ($item) {
                return $item->product !== null;
            })
            ->values()
            ->take(self::COMPARE_MAX);

        // Guest: if no items by cookie but we have items by session_id, use them and set cookie
        if (!auth()->check() && $products->isEmpty()) {
            $sessionId = session()->getId();
            if ($sessionId !== null && $sessionId !== '') {
                $bySession = ProductComparison::with(['product' => function($q) {
                        $q->with(['category', 'brand', 'activeVariants'])->withCount(['reviews' => fn($r) => $r->visibleOnProduct()]);
                    }])
                    ->where('session_id', $sessionId)
                    ->latest()
                    ->get()
                    ->filter(fn ($item) => $item->product !== null)
                    ->values()
                    ->take(self::COMPARE_MAX);
                if ($bySession->isNotEmpty()) {
                    $products = $bySession;
                }
            }
        }

        $userId = auth()->id();
        $wishListProductIds = [];
        if ($userId) {
            $wishListProductIds = \App\Models\Wishlist::where('user_id', $userId)->pluck('product_id')->toArray();
        } else {
            $wishlist = session('wishlist', []);
            $wishListProductIds = is_array($wishlist) ? array_map('intval', array_keys($wishlist)) : [];
        }

        $viewName = request()->routeIs('user.compare') ? 'user.compare' : 'compare.index';

        $response = response()
            ->view($this->activeTemplate . $viewName, compact('pageTitle', 'products', 'wishListProductIds'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

        // Guest: ensure cookie is set when we have items
        if (!auth()->check() && $products->isNotEmpty()) {
            $guestId = $request->cookie(ProductComparison::GUEST_COOKIE_NAME) ?: ProductComparison::getGuestCompareId();
            if ($guestId !== null) {
                $response->cookie(
                    ProductComparison::GUEST_COOKIE_NAME,
                    $guestId,
                    ProductComparison::GUEST_COOKIE_TTL,
                    ProductComparison::getCookiePath(),
                    null,
                    $request->secure(),
                    false,
                    false,
                    'lax'
                );
            }
        }

        return $response;
    }

    /**
     * Add product to comparison (AJAX)
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::find($request->product_id);

        if (!$product || $product->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or inactive'
            ]);
        }

        $result = ProductComparison::addProduct($request->product_id);

        $guestToken = null;
        if (is_array($result) && isset($result['guest_token'])) {
            $guestToken = $result['guest_token'];
            $result = $result['result'];
        }

        // For guests: set long-lived cookie so compare list survives reload and /user/compare page shows items
        $guestCookie = null;
        if ($result && !auth()->check()) {
            session()->save();
            $guestId = $guestToken ?? ProductComparison::getGuestCompareId();
            if ($guestId !== null) {
                $cookiePath = ProductComparison::getCookiePath();
                $guestCookie = cookie(
                    ProductComparison::GUEST_COOKIE_NAME,
                    $guestId,
                    ProductComparison::GUEST_COOKIE_TTL,
                    $cookiePath,
                    null,
                    $request->secure(),
                    false,
                    false,
                    'lax'
                );
            }
        }

        if ($result === false || $result === null) {
            $count = ProductComparison::getCount();
            if ($count >= self::COMPARE_MAX) {
                return response()->json([
                    'success' => false,
                    'message' => __('Maximum :max products can be compared. Remove one to add another.', ['max' => self::COMPARE_MAX])
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Product already in comparison'
            ]);
        }

        activity_log(\App\Models\UserActivityLog::COMPARE_ADD, 'Added to compare: ' . $product->name, 'product', $product->id);

        $count = ProductComparison::getCount();
        $response = response()->json([
            'success' => true,
            'message' => 'Product added to comparison',
            'count' => min(max(0, $count), self::COMPARE_MAX)
        ]);
        if ($guestCookie !== null) {
            $response->cookie($guestCookie);
        }
        return $response;
    }

    /**
     * Remove product from comparison (AJAX)
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        ProductComparison::removeProduct($request->product_id);

        activity_log(\App\Models\UserActivityLog::COMPARE_REMOVE, 'Removed from compare', 'product', (int) $request->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from comparison',
            'count' => ProductComparison::getCount()
        ]);
    }

    /**
     * Remove multiple products from comparison (AJAX) – tick/checkbox bulk delete
     */
    public function removeBulk(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|exists:products,id'
        ]);

        $removed = 0;
        foreach ($request->product_ids as $productId) {
            ProductComparison::removeProduct($productId);
            $removed++;
        }

        if ($removed > 0) {
            activity_log(\App\Models\UserActivityLog::COMPARE_REMOVE, 'Bulk removed ' . $removed . ' from compare', 'product', 0);
        }

        return response()->json([
            'success' => true,
            'message' => $removed > 0 ? __(':count product(s) removed from comparison', ['count' => $removed]) : __('No products removed'),
            'count' => ProductComparison::getCount()
        ]);
    }

    /**
     * Clear all comparisons (AJAX)
     */
    public function clear()
    {
        ProductComparison::clearAll();

        return response()->json([
            'success' => true,
            'message' => 'Comparison list cleared',
            'count' => 0,
        ]);
    }

    /**
     * Get comparison count (AJAX). Real-time, no cache. Returns count + product_ids for badge and button state.
     */
    public function count()
    {
        $count = ProductComparison::getCount();
        $count = min(max(0, (int) $count), self::COMPARE_MAX);
        $productIds = ProductComparison::getCompareProductIds();

        return response()
            ->json([
                'count' => $count,
                'product_ids' => $productIds,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Get comparison data (AJAX). Returns valid JSON; safe when category/brand or helpers throw.
     */
    public function getData()
    {
        try {
            $products = ProductComparison::getItems()
                ->filter(function ($item) {
                    return $item->product !== null;
                })
                ->take(self::COMPARE_MAX)
                ->values();

            $data = $products->map(function ($item) {
                $product = $item->product;
                return [
                    'id' => $product->id,
                    'name' => $product->name ?? '',
                    'image' => method_exists($product, 'imageShow') ? $product->imageShow() : '',
                    'price' => productPrice($product),
                    'original_price' => $product->price ?? 0,
                    'category' => optional($product->category)->name ?? 'N/A',
                    'brand' => optional($product->brand)->name ?? 'N/A',
                    'rating' => $product->avg_rate ?? 0,
                    'url' => product_detail_url($product)
                ];
            });

            return response()->json([
                'success' => true,
                'products' => $data,
                'count' => min($data->count(), self::COMPARE_MAX)
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'products' => [],
                'count' => 0,
                'message' => 'Unable to load comparison data.'
            ], 200);
        }
    }
}
