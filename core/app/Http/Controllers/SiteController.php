<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\HomepageAdSlot;
use App\Models\Language;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\SearchLog;
use App\Models\Subcategory;
use App\Models\Subscriber;
use App\Models\BannerAnalytics;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\AutoResponse;
use App\Services\ContactChannelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    protected ContactChannelService $contactChannelService;

    public function __construct(ContactChannelService $contactChannelService)
    {
        parent::__construct();
        $this->contactChannelService = $contactChannelService;
    }

    public function index()
    {
        $pageTitle = 'Home';

        $todayDealProducts = Cache::remember('homepage.today_deals.' . app()->getLocale(), 600, function () {
            return Product::available()
                ->todayDeal()
                ->with(['category:id,name', 'brand:id,name', 'activeVariants'])
                ->withCount(['reviews' => fn($r) => $r->visibleOnProduct()])
                ->latest('id')
                ->take(8)
                ->get();
        });

        // Allow forcing banner cache refresh (e.g. after admin upload)
        if (request()->has('refresh_banner')) {
            $locales = Language::pluck('code')->toArray() ?: ['en', 'bn'];
            foreach ($locales as $l) {
                Cache::forget('homepage.banner.guest.' . $l);
                Cache::forget('homepage.banner.auth.' . $l);
            }
        }

        $bannerData = $this->getBannerDataForHomepage();

        $bannerPrecomputed = $this->precomputeBannerForView($bannerData);

        $bannerModuleService = app(\App\Modules\Banner\BannerModuleService::class);
        $allForHomepage = $bannerModuleService->getBannersForHomepage();
        $bannersWithImage = $bannerModuleService->getBannersWithImage($allForHomepage);
        if ($bannersWithImage->isEmpty()) {
            $anyWithImage = Frontend::where('data_keys', 'banner.element')->orderBy('id', 'asc')->get()->filter(function ($b) {
                $dv = $b->data_values;
                if (!$dv)
                    return false;
                $img = is_object($dv) ? ($dv->image ?? null) : (is_array($dv) ? ($dv['image'] ?? null) : null);
                if (is_array($img))
                    $img = $img['desktop'] ?? $img['image'] ?? null;
                return is_string($img) && trim($img) !== '';
            })->values();
            if ($anyWithImage->isNotEmpty()) {
                $bannersWithImage = $anyWithImage;
            }
        }
        $bannerModuleData = [
            'banners' => $bannersWithImage,
            'settings' => $bannerModuleService->getSettings(),
        ];

        $homeDataRaw = \App\Services\HomepageDataService::getCachedData();
        $initialSize = \App\Services\HomepageDataService::INITIAL_PAGE_SIZE;
        $homeData = [
            'hotDealProducts' => $homeDataRaw['hotDealProducts']->take($initialSize),
            'featuredProducts' => $homeDataRaw['featuredProducts']->take($initialSize),
            'bestSellingProducts' => $homeDataRaw['bestSellingProducts']->take($initialSize),
            'recommendedProducts' => $homeDataRaw['recommendedProducts']->take($initialSize),
            'trendingBest' => $homeDataRaw['trendingBest']->take($initialSize),
            'newArrivals' => $homeDataRaw['newArrivals']->take($initialSize),
            'reviews' => $homeDataRaw['reviews'],
            'topRatedProducts' => $homeDataRaw['topRatedProducts'],
            'categories' => $homeDataRaw['categories'],
            'topCategories' => $homeDataRaw['topCategories'],
            'topBrands' => $homeDataRaw['topBrands'],
            'customProductRows' => collect($homeDataRaw['customProductRows'] ?? [])->map(function ($item) use ($initialSize) {
                return [
                    'row' => $item['row'],
                    'products' => $item['products']->take($initialSize),
                ];
            })->all(),
        ];
        $homeSectionData = getCachedHomeSectionData();

        $customRowsById = [];
        foreach ($homeDataRaw['customProductRows'] ?? [] as $block) {
            $customRowsById[$block['row']->id] = $block;
        }

        $adSlotsById = [];
        try {
            if (Schema::hasTable('homepage_ad_slots')) {
                $adSlotsById = HomepageAdSlot::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->keyBy('id')
                    ->all();
            }
        } catch (\Throwable $e) {
            $adSlotsById = [];
        }

        $response = response()->view($this->activeTemplate . 'home', array_merge(
            compact('pageTitle', 'todayDealProducts', 'bannerData', 'homeData', 'homeSectionData', 'bannerModuleData', 'customRowsById', 'adSlotsById'),
            $bannerPrecomputed,
            ['storefrontDeferredBundle' => 'tailwind-storefront-deferred-home']
        ));
        if (request()->get('open') === 'login' || request()->get('open') === 'register') {
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');
        } else {
            $response->header('Cache-Control', 'public, max-age=0, must-revalidate');
        }
        return $response;
    }

    /**
     * Lazy load more products for a homepage section (AJAX). Returns HTML fragment.
     */
    public function homeSectionProducts(Request $request)
    {
        $section = (string) $request->input('section', '');
        $isCustom = (bool) preg_match('/^custom_row_\d+$/', $section);
        $request->validate([
            'section' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($isCustom) {
                    if ($isCustom) {
                        return;
                    }
                    $allowed = ['hot_deal', 'featured', 'best_selling', 'trending', 'new_arrivals', 'recommended', 'today_deals'];
                    if (!in_array($value, $allowed, true)) {
                        $fail(__('Invalid section.'));
                    }
                },
            ],
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:16',
        ]);
        $offset = (int) $request->get('offset', 8);
        $limit = (int) $request->get('limit', 8);
        $products = \App\Services\HomepageDataService::getSectionProducts($section, $offset, $limit);
        $general = gs();
        $activeTemplate = $this->activeTemplate;
        $html = view($activeTemplate . 'partials.product_cards_fragment', compact('products', 'general', 'activeTemplate'))->render();
        return response()
            ->json(['html' => $html, 'count' => $products->count()])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Load banner data for homepage. Always from DB (no cache) so user page always shows current banners.
     */
    protected function getBannerDataForHomepage(): array
    {
        $now = now()->format('Y-m-d');
        $locale = app()->getLocale();
        $bannerCacheKey = auth()->check() ? "homepage.banner.auth.{$locale}" : "homepage.banner.guest.{$locale}";
        $cached = Cache::remember($bannerCacheKey, 120, function () {
            return [
                'elements' => Frontend::where('data_keys', 'banner.element')->orderBy('id', 'asc')->get(),
                'settings' => Frontend::where('data_keys', 'banner.content')->orderBy('id', 'desc')->first(),
            ];
        });

        $elements = $cached['elements'] ?? collect();
        $settings = $cached['settings'] ?? null;
        return ['elements' => $elements, 'settings' => $settings, 'now' => $now];
    }

    /** Precompute banner elements and settings for view. Pass all elements; view filters by image. */
    protected function precomputeBannerForView(array $bannerData): array
    {
        $now = $bannerData['now'] ?? now()->format('Y-m-d');
        $userLoggedIn = auth()->check();
        $elements = collect($bannerData['elements'] ?? [])->filter(function ($b) use ($now, $userLoggedIn) {
            $dv = $b->data_values ?? (object) [];
            if (is_array($dv)) {
                $dv = (object) $dv;
            }
            $isActive = $dv->is_active ?? 1;
            if ((string) $isActive === '0' || (int) $isActive === 0) {
                return false;
            }
            if (!empty($dv->start_date) && $now < $dv->start_date) {
                return false;
            }
            if (!empty($dv->end_date) && $now > $dv->end_date) {
                return false;
            }
            $vis = isset($dv->visibility) ? strtolower(trim((string) $dv->visibility)) : 'public';
            if ($vis === 'logged_in_only' && !$userLoggedIn) {
                return false;
            }
            if ($vis === 'guest_only' && $userLoggedIn) {
                return false;
            }
            if ($vis === 'campaign_only') {
                return false;
            }
            return true;
        })->values();

        $settings = $bannerData['settings'] ?? null;
        $settingsValues = null;
        if ($settings && isset($settings->data_values)) {
            $settingsValues = is_array($settings->data_values) ? (object) $settings->data_values : $settings->data_values;
        }
        $slideIntervalSeconds = $settingsValues ? (int) ($settingsValues->slide_interval_seconds ?? 5) : 5;
        $slideIntervalSeconds = max(1, min(60, $slideIntervalSeconds));
        $bannerAutoplay = $settingsValues ? (int) ($settingsValues->autoplay ?? 1) : 1;
        $bannerAutoplay = $bannerAutoplay !== 0 ? 1 : 0;
        $bannerWidth = $settingsValues ? (int) ($settingsValues->banner_width ?? 2560) : 2560;
        $bannerHeight = $settingsValues ? (int) ($settingsValues->banner_height ?? 400) : 400;
        $bannerWidth = $bannerWidth < 100 ? 2560 : $bannerWidth;
        $bannerHeight = $bannerHeight < 50 ? 400 : $bannerHeight;

        return [
            'bannerElement' => $elements,
            'slideIntervalSeconds' => $slideIntervalSeconds,
            'bannerAutoplay' => $bannerAutoplay,
            'bannerWidth' => $bannerWidth,
            'bannerHeight' => $bannerHeight,
            'bannerSize' => $bannerWidth . 'x' . $bannerHeight,
        ];
    }

    /**
     * Newsletter subscribe page (GET). Form on this page uses same AJAX as footer.
     */
    public function subscribePage()
    {
        $pageTitle = __('Subscribe to Newsletter');
        return view($this->activeTemplate . 'subscribe', compact('pageTitle'));
    }

    public function subscribe(Request $request)
    {
        $email = trim((string) $request->input('email', ''));
        $validator = Validator::make(['email' => $email], ['email' => 'required|email']);

        $isAjax = $request->ajax() || $request->wantsJson();

        if ($validator->fails()) {
            $msg = __('Please enter a valid email address.');
            if ($isAjax) {
                return response()->json(['error' => ['email' => $msg]], 422);
            }
            $notify[] = ['error', $msg];
            return back()->withNotify($notify)->withInput();
        }

        $exists = Subscriber::where('email', $email)->exists();
        if ($exists) {
            $msg = __('You are already subscribed to our newsletter.');
            if ($isAjax) {
                return response()->json(['success' => $msg]);
            }
            $notify[] = ['success', $msg];
            return back()->withNotify($notify);
        }

        $subscribe = new Subscriber();
        $subscribe->email = $email;
        $subscribe->save();

        $msg = __('Subscribed successfully.');
        if ($isAjax) {
            return response()->json(['success' => $msg]);
        }
        $notify[] = ['success', $msg];
        return back()->withNotify($notify);
    }

    public function trackOrder()
    {
        $pageTitle = "Track Your Order";
        $recentOrders = collect();
        if (auth()->check()) {
            $recentOrders = Order::where('user_id', auth()->id())
                ->where('order_status', '!=', Status::ORDER_CANCEL)
                ->latest()
                ->take(10)
                ->get(['id', 'order_no', 'order_status', 'total', 'created_at']);
        }
        return view($this->activeTemplate . 'track.track_order', compact('pageTitle', 'recentOrders'));
    }

    /** Track Order inside user dashboard (sidebar + menu bar stay). */
    public function trackOrderDashboard()
    {
        $pageTitle = "Track Your Order";
        $recentOrders = collect();
        if (auth()->check()) {
            $recentOrders = Order::where('user_id', auth()->id())
                ->where('order_status', '!=', Status::ORDER_CANCEL)
                ->latest()
                ->take(10)
                ->get(['id', 'order_no', 'order_status', 'total', 'created_at']);
        }
        return view($this->activeTemplate . 'user.track_order', compact('pageTitle', 'recentOrders'));
    }

    public function getTrackOrder(Request $request)
    {
        $orderNumber = trim((string) ($request->input('orderNo') ?? $request->input('order_no') ?? ''));

        $validator = Validator::make(
            ['order_number' => $orderNumber],
            ['order_number' => 'required|string|max:100']
        );

        if ($validator->fails()) {
            $msg = $validator->errors()->first('order_number') ?: __('Please enter a valid order number.');
            return response()->json(['error' => $msg]);
        }

        try {
            $order = Order::with(['orderDetail.product', 'shipping', 'shipmentTrackings'])
                ->where('order_no', $orderNumber)
                ->first();

            if (!$order) {
                return response()->json(['error' => __('Sorry! The order number was not found.')]);
            }

            if (class_exists(\App\Models\UserActivityLog::class)) {
                activity_log(\App\Models\UserActivityLog::TRACK_ORDER, 'Tracked order: ' . $order->order_no, 'order', $order->id);
            }

            $emptyMessage = __('Your order has been cancelled.');
            $isOwner = auth()->check() && (int) $order->user_id === (int) auth()->id();

            return view($this->activeTemplate . 'track.show_track', compact('order', 'emptyMessage', 'isOwner'));
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'error' => __('Something went wrong. Try again.'),
            ]);
        }
    }

    public function productDetailsBySlug(?string $locale, string $slug)
    {
        $slug = (string) ($slug ?: request()->route('slug', ''));
        $id = Product::parseIdFromProductSlug($slug);
        abort_if($id === null, 404);

        $product = Product::available()->findOrFail($id);

        $canonical = trim((string) ($product->slug ?? ''));
        if ($canonical === '' || !preg_match('/-\d+$/', $canonical)) {
            $canonical = Product::buildShortSlugForProduct($product);
            $product->slug = $canonical;
            $product->saveQuietly();
        }
        if ($slug !== $canonical) {
            return redirect()->to(storefront_route('product.detail', ['slug' => $canonical]), 301);
        }

        return $this->productDetailPage($id);
    }

    /**
     * Redirect legacy /product/details/{id} URLs to canonical slug route.
     */
    public function productDetailsLegacy(\Illuminate\Http\Request $request)
    {
        $id = (int) $request->route('id');
        abort_if($id <= 0, 404);

        $product = Product::query()->where('id', $id)->first(['id', 'slug', 'name']);
        abort_if($product === null, 404);

        $slug = trim((string) ($product->slug ?? ''));
        if ($slug === '' || !preg_match('/-\d+$/', $slug)) {
            $slug = Product::buildShortSlugForProduct($product);
        }

        return redirect()->to(storefront_route('product.detail', ['slug' => $slug]), 301);
    }

    protected function productDetailPage(int $id)
    {
        $cacheKey = 'product.detail.' . $id . '.' . app()->getLocale();
        $cacheTtl = config('optimization.product_detail_cache_ttl', 600);

        $data = Cache::remember($cacheKey, $cacheTtl, function () use ($id) {
            $product = Product::available()
                ->with([
                    'category:id,name',
                    'brand:id,name',
                    'reviews' => fn($q) => $q->visibleOnProduct()->latest()->limit(10)->with('user:id,username,firstname,lastname,image'),
                    'activeVariants',
                ])
                ->findOrFail($id);

            $categoryId = $product->category_id;
            $brandId = $product->brand_id;
            $productSelect = ['id', 'name', 'slug', 'image', 'price', 'discount', 'discount_type', 'today_deals', 'category_id', 'brand_id', 'sale_count', 'avg_rate', 'quantity', 'created_at', 'gallery', 'product_sku'];

            // 1) Related: same category, up to 12 (for carousel + sidebar)
            $relatedProduct = Product::active()
                ->select($productSelect)
                ->withCount(['reviews' => fn($q) => $q->visibleOnProduct()])
                ->with(['category:id,name', 'brand:id,name'])
                ->where('id', '!=', $id)
                ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
                ->latest()
                ->take(12)
                ->get();
            if ($relatedProduct->isEmpty()) {
                $relatedProduct = Product::active()
                    ->select($productSelect)
                    ->withCount(['reviews' => fn($q) => $q->visibleOnProduct()])
                    ->with(['category:id,name', 'brand:id,name'])
                    ->where('id', '!=', $id)
                    ->latest()
                    ->take(12)
                    ->get();
            }

            $excludeIds = $relatedProduct->pluck('id')->push($id)->unique()->values()->all();

            // 2) Same brand (exclude current + already in related)
            $sameBrandProducts = collect();
            if ($brandId) {
                $sameBrandProducts = Product::active()
                    ->select($productSelect)
                    ->withCount(['reviews' => fn($q) => $q->visibleOnProduct()])
                    ->with(['category:id,name', 'brand:id,name'])
                    ->where('brand_id', $brandId)
                    ->whereNotIn('id', $excludeIds)
                    ->latest()
                    ->take(12)
                    ->get();
            }

            // 3) You may also like: other categories, best selling / trending
            $youMayAlsoLike = Product::active()
                ->select($productSelect)
                ->withCount(['reviews' => fn($q) => $q->visibleOnProduct()])
                ->with(['category:id,name', 'brand:id,name'])
                ->whereNotIn('id', array_merge($excludeIds, $sameBrandProducts->pluck('id')->all()))
                ->when($categoryId, fn($q) => $q->where('category_id', '!=', $categoryId))
                ->orderByRaw('COALESCE(sale_count, 0) DESC, created_at DESC')
                ->take(12)
                ->get();

            // Single query: rating breakdown + total (one DB round-trip)
            $reviewStats = Review::where('product_id', $id)->visibleOnProduct()
                ->selectRaw('stars, count(*) as cnt')
                ->groupBy('stars')
                ->get();
            $ratingBreakdown = $reviewStats->pluck('cnt', 'stars')->toArray();
            $reviewsTotal = (int) $reviewStats->sum('cnt');

            return [
                'product' => $product,
                'reviews' => $product->reviews,
                'relatedProduct' => $relatedProduct,
                'sameBrandProducts' => $sameBrandProducts,
                'youMayAlsoLike' => $youMayAlsoLike,
                'ratingBreakdown' => $ratingBreakdown,
                'reviewsTotal' => $reviewsTotal,
            ];
        });

        $product = $data['product'];
        $reviews = $data['reviews'];
        $relatedProduct = $data['relatedProduct'];
        $sameBrandProducts = $data['sameBrandProducts'] ?? collect();
        $youMayAlsoLike = $data['youMayAlsoLike'] ?? collect();
        $ratingBreakdown = $data['ratingBreakdown'] ?? [];
        $reviewsTotal = $data['reviewsTotal'] ?? 0;
        $pageTitle = $product->meta_title ?: $product->name;

        $canReview = false;
        $hasPurchased = false;
        $userReview = null;
        $reviewBlockedReason = null; // 'profile_incomplete' when logged in but profile not complete
        if (auth()->check()) {
            $userId = auth()->id();
            $user = auth()->user();
            $userReview = Review::where('product_id', $product->id)->where('user_id', $userId)->first();
            $hasPurchased = $userReview ? false : hasPurchasedProduct($userId, $product->id);
            $profileComplete = $user->profile_complete ?? false;
            if ($profileComplete) {
                $canReview = true;
            } else {
                $reviewBlockedReason = 'profile_incomplete';
            }
        }

        $wishListProductIds = [];
        if (auth()->check()) {
            $userId = (int) auth()->id();
            $wishlistHasProduct = Cache::remember(
                'wishlist.has_product:' . $userId . ':' . $product->id,
                60,
                fn() => \App\Models\Wishlist::where('user_id', $userId)->where('product_id', $product->id)->exists()
            );
            $wishListProductIds = $wishlistHasProduct ? [$product->id] : [];
        } else {
            $wishlist = session()->get('wishlist', []);
            $wishListProductIds = is_array($wishlist) ? array_map('intval', array_keys($wishlist)) : [];
        }

        $seoContents['keywords'] = $product->meta_keywords ?? [];
        $seoContents['social_title'] = $product->meta_title ?: $product->name;
        $seoContents['social_description'] = $product->meta_description ?: $product->summary;
        $seoContents['description'] = $product->meta_description ?: $product->summary;
        $seoContents['meta_description'] = $product->meta_description ?: $product->summary;
        $seoContents['image'] = $product->imageShow();
        $seoContents['image_size'] = getFileSize('product');

        // Precompute for view (avoids repeated getImage/route in blade)
        $productUrl = product_detail_url($product);
        $seoContents['canonical_url'] = $productUrl;
        $productImages = $this->productDetailImageList($product);
        $breadcrumbList = $this->productDetailBreadcrumb($product, $productUrl);
        $detailPrice = productPrice($product);

        // Defer activity log so response is sent first (non-blocking)
        if (function_exists('activity_log')) {
            $productId = $product->id;
            $productName = $product->name;
            app()->terminating(function () use ($productId, $productName) {
                try {
                    activity_log(\App\Models\UserActivityLog::PRODUCT_VIEW, 'Viewed product: ' . $productName, 'product', $productId);
                } catch (\Throwable $e) {
                    // non-blocking
                }
            });
        }

        // Social proof: view count in last 24h (outside cache so it stays fresh)
        $productViews24h = 0;
        if (Schema::hasTable('user_activity_logs')) {
            $productViews24h = (int) Cache::remember('product.views24h:' . $id, 60, function () use ($id) {
                return \App\Models\UserActivityLog::where('action_type', \App\Models\UserActivityLog::PRODUCT_VIEW)
                    ->where('model_type', 'product')
                    ->where('model_id', $id)
                    ->where('created_at', '>=', now()->subDay())
                    ->count();
            });
        }

        $emptyMessage = __('No reviews yet. Be the first to review!');
        $bottomProducts = $this->buildProductDetailBottomProducts($product->id);
        $response = response()->view($this->activeTemplate . 'products.details', compact('product', 'pageTitle', 'relatedProduct', 'sameBrandProducts', 'youMayAlsoLike', 'reviews', 'reviewsTotal', 'ratingBreakdown', 'canReview', 'hasPurchased', 'userReview', 'seoContents', 'wishListProductIds', 'productUrl', 'productImages', 'breadcrumbList', 'detailPrice', 'emptyMessage', 'reviewBlockedReason', 'productViews24h', 'bottomProducts'));
        if (!auth()->check()) {
            $response->header('Cache-Control', 'public, max-age=30, stale-while-revalidate=60');
        }
        return $response;
    }

    /**
     * Product details bottom list:
     * - prioritize recently viewed product IDs from cookie (excluding current product)
     * - fill remaining slots with latest active products
     */
    protected function buildProductDetailBottomProducts(int $currentProductId, int $limit = 24)
    {
        $limit = max(8, min(48, $limit));

        $productSelect = [
            'id',
            'name',
            'slug',
            'image',
            'price',
            'discount',
            'discount_type',
            'today_deals',
            'category_id',
            'brand_id',
            'sale_count',
            'avg_rate',
            'quantity',
            'created_at',
            'gallery'
        ];

        $recentCookie = (string) request()->cookie('recently_viewed_ids', '');
        $recentIds = [];
        if ($recentCookie !== '') {
            $decoded = json_decode(urldecode($recentCookie), true);
            if (is_array($decoded)) {
                $recentIds = collect($decoded)
                    ->map(fn($v) => (int) $v)
                    ->filter(fn($v) => $v > 0 && $v !== $currentProductId)
                    ->unique()
                    ->take($limit)
                    ->values()
                    ->all();
            }
        }

        $recentProducts = collect();
        if (!empty($recentIds)) {
            $recentProducts = Product::active()
                ->select($productSelect)
                ->withCount(['reviews' => fn($q) => $q->visibleOnProduct()])
                ->with(['category:id,name', 'brand:id,name'])
                ->whereIn('id', $recentIds)
                ->get()
                ->sortBy(fn($p) => array_search((int) $p->id, $recentIds, true))
                ->values();
        }

        $remaining = max(0, $limit - $recentProducts->count());
        $fillerProducts = collect();
        if ($remaining > 0) {
            $excludeIds = $recentProducts->pluck('id')->push($currentProductId)->unique()->values()->all();
            $fillerProducts = Product::active()
                ->select($productSelect)
                ->withCount(['reviews' => fn($q) => $q->visibleOnProduct()])
                ->with(['category:id,name', 'brand:id,name'])
                ->whereNotIn('id', $excludeIds)
                ->latest()
                ->take($remaining)
                ->get();
        }

        return $recentProducts->concat($fillerProducts)->take($limit)->values();
    }

    /** Precompute product image list for JSON-LD and view (avoids getImage in loop). */
    protected function productDetailImageList(Product $product): array
    {
        $path = getFilePath('product');
        $size = getFileSize('product');
        $images = array_merge([$product->image], $product->gallery ?? []);
        $list = [];
        foreach ($images as $img) {
            if ($img) {
                $list[] = getImage($path . '/' . $img, $size);
            }
        }
        if (empty($list)) {
            $list[] = $product->imageShow();
        }
        return array_values($list);
    }

    /** Precompute breadcrumb for product detail (avoids route/slug in view). */
    protected function productDetailBreadcrumb(Product $product, string $productUrl): array
    {
        $list = [
            ['name' => __('Home'), 'url' => url('/')],
            ['name' => __($product->name), 'url' => $productUrl],
        ];
        if ($product->category) {
            array_splice($list, 1, 0, [
                [
                    'name' => __($product->category->name),
                    'url' => route('category.products', [slug($product->category->name), $product->category->id]),
                ]
            ]);
        }
        return $list;
    }

    public function fetchReviews(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'skip' => 'required|integer|min:0',
            'sort' => 'nullable|string|in:recent,highest,lowest,helpful',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'error' => $validate->errors()->first()]);
        }

        $product = Product::where('id', $id)->first();
        if (!$product) {
            return response()->json(['success' => false, 'error' => __('Product not found')]);
        }

        $query = Review::where('product_id', $product->id)->visibleOnProduct()->with('user:id,username,firstname,lastname,image');

        switch ($request->get('sort', 'recent')) {
            case 'highest':
                $query->orderBy('stars', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'lowest':
                $query->orderBy('stars', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'helpful':
                $query->orderBy('helpful_count', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->latest();
        }

        $reviews = $query->skip((int) $request->skip)->limit(10)->get();

        if ($reviews->isEmpty()) {
            return response()->json(['success' => true, 'html' => '', 'has_more' => false]);
        }

        $view = view($this->activeTemplate . 'products.load_reviews', compact('reviews'))->render();
        return response()->json([
            'success' => true,
            'html' => $view,
            'has_more' => $reviews->count() === 10,
        ]);
    }

    public function reviewHelpful($id)
    {
        $review = Review::where('id', $id)->visibleOnProduct()->first();
        if (!$review) {
            return response()->json(['success' => false, 'message' => __('Review not found')]);
        }
        $review->increment('helpful_count');
        return response()->json([
            'success' => true,
            'helpful_count' => $review->fresh()->helpful_count,
            'message' => __('Thanks for your feedback!'),
        ]);
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        $user = auth()->user();
        $contactContent = Cache::remember('contact_us:content', now()->addMinutes(30), function () {
            return getContent('contact_us.content', true);
        });
        $contactChannels = $this->contactChannelService->getActiveIntegrations();

        return view($this->activeTemplate . 'contact', compact('pageTitle', 'user', 'contactContent', 'contactChannels'));
    }

    /** ভাসমান লাইভ চ্যাট – আলাদা পেজ নেই; হোমে রিডাইরেক্ট + প্যানেল অটো-ওপেন, চ্যাট বন্ধ করলে পিছনের পেজই দেখা যাবে */
    public function contactLive()
    {
        return redirect()->route('home', ['open_contact' => 1]);
    }

    public function contactSubmit(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:500',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();
        $random = getNumber();

        $user = auth()->user();
        $ticket = new SupportTicket();
        $ticket->user_id = $user->id;
        $ticket->name = $user->fullname ?? $request->name;
        $ticket->email = $user->email ?? $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $msgPreview = \Illuminate\Support\Str::limit(strip_tags($request->message ?? ''), 60);
        $adminNotification->title = auth()->user() ? ('New message from ' . auth()->user()->username . ': ' . $msgPreview) : 'A new contact message has been submitted';
        $adminNotification->click_url = (auth()->user() && auth()->user()->id) ? urlPath('admin.ticket.view.user', auth()->user()->id) : urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        activity_log(\App\Models\UserActivityLog::CONTACT_SUBMIT, 'Contact form / ticket: ' . $request->subject, null, $ticket->id);

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    /** Submit from global floating contact panel (AJAX) - শুধুমাত্র লগইন ইউজার, অ্যাটাচমেন্ট সাপোর্ট */
    public function contactPanelSubmit(Request $request)
    {

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'mp3', 'm4a', 'mp4', 'webm'];
        $maxSizeMb = (int) substr(ini_get('upload_max_filesize'), 0, -1) ?: 4;
        $maxFiles = 5;

        $request->merge(['subject' => trim((string) $request->input('subject', ''))]);
        $allowedSubjects = ['Live Chat Message', 'General Inquiry', 'Report a Problem', 'Order Support'];
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|in:' . implode(',', $allowedSubjects),
            'message' => 'required|string|max:500',
            'attachments' => 'nullable',
            'attachments.*' => 'file|max:' . ($maxSizeMb * 1024),
        ];

        $validator = Validator::make($request->all(), $rules, [
            'subject.in' => __('Please select a valid subject: Live Chat Message, General Inquiry, Report a Problem, or Order Support.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $files = $request->file('attachments');
        if ($files) {
            $files = is_array($files) ? $files : [$files];
            if (count($files) > $maxFiles) {
                return response()->json([
                    'success' => false,
                    'message' => __('Maximum :count files allowed.', ['count' => $maxFiles]),
                ], 422);
            }
            foreach ($files as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, $allowedExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Invalid file type. Allowed: images, PDF, DOC, audio, video.'),
                    ], 422);
                }
            }
        }

        if (!verifyCaptcha()) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid captcha provided.'),
            ], 422);
        }

        if (auth()->check() && auth()->user()->status == Status::USER_BAN) {
            return response()->json([
                'success' => false,
                'message' => __('Your account is restricted. You cannot send messages.'),
            ], 403);
        }

        if (messageContainsBlockedContent($request->message ?? '')) {
            return response()->json([
                'success' => false,
                'message' => __('Message blocked. Do not send links or inappropriate content.'),
            ], 422);
        }

        $userId = auth()->id() ?? 0;
        $channelCol = Schema::hasColumn('support_tickets', 'channel');
        $subject = $request->subject ?? 'Live Chat Message';
        $ticket = null;

        // One ticket per subject per user (Live Chat Message, General Inquiry, Report a Problem, Order Support) so admin sees by subject
        if ($userId && $channelCol) {
            $ticket = SupportTicket::where('user_id', $userId)
                ->where('channel', SupportTicket::CHANNEL_WEB)
                ->where('subject', $subject)
                ->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY, Status::TICKET_ANSWER])
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->orderBy('id', 'desc')
                ->first();
        } else if (!$userId && $channelCol) {
            $guestTicket = $request->cookie('stayl_guest_ticket');
            if ($guestTicket) {
                $ticket = SupportTicket::where('ticket', $guestTicket)
                    ->where('channel', SupportTicket::CHANNEL_WEB)
                    ->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY, Status::TICKET_ANSWER])
                    ->first();
            }
        }

        if (!$ticket) {
            $random = getNumber();
            $ticket = new SupportTicket();
            $ticket->user_id = $userId;
            $ticket->name = $request->name;
            $ticket->email = $request->email;
            $ticket->priority = Status::PRIORITY_MEDIUM;
            $ticket->ticket = $random;
            $ticket->subject = $request->subject;
            if ($channelCol) {
                $ticket->channel = SupportTicket::CHANNEL_WEB;
            }
            $ticket->last_reply = Carbon::now();
            $ticket->status = Status::TICKET_OPEN;
            $ticket->save();

            $msgPreview = \Illuminate\Support\Str::limit(strip_tags($request->message ?? ''), 60);
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $userId;
            $adminNotification->title = $userId ? ('New message from ' . auth()->user()->username . ': ' . $msgPreview) : 'A new contact message has been submitted';
            $adminNotification->click_url = $userId ? urlPath('admin.ticket.view.user', $userId) : urlPath('admin.ticket.view', $ticket->id);
            $adminNotification->save();
        } else {
            $ticket->last_reply = Carbon::now();
            $ticket->status = Status::TICKET_REPLY;
            $ticket->subject = $request->subject;
            $ticket->save();

            $msgPreview = \Illuminate\Support\Str::limit(strip_tags($request->message ?? ''), 60);
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $userId;
            $adminNotification->title = $userId ? ('New message from ' . auth()->user()->username . ': ' . $msgPreview) : __('New message in ticket #:ticket', ['ticket' => $ticket->ticket]);
            $adminNotification->click_url = $userId ? urlPath('admin.ticket.view.user', $userId) : urlPath('admin.ticket.view', $ticket->id);
            $adminNotification->save();
        }

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        activity_log(\App\Models\UserActivityLog::LIVE_CHAT, 'Live chat: ' . $subject, null, $ticket->id);

        if ($files) {
            $path = getFilePath('ticket');
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            foreach ($files as $file) {
                try {
                    $savedName = fileUploader($file, $path);
                    $ext = strtolower($file->getClientOriginalExtension() ?? '');
                    $fullPath = null;
                    foreach ([public_path($path . '/' . $savedName), base_path($path . '/' . $savedName), base_path('../' . $path . '/' . $savedName)] as $candidate) {
                        if ($candidate && file_exists($candidate)) {
                            $fullPath = $candidate;
                            break;
                        }
                    }
                    if ($fullPath && in_array($ext, $imageExtensions)) {
                        try {
                            $optimizer = app(\App\Services\ImageOptimizationService::class);
                            $webpPath = $optimizer->convertToWebP($fullPath, 85);
                            if ($webpPath && file_exists($webpPath)) {
                                @unlink($fullPath);
                                $savedName = basename($webpPath);
                            }
                        } catch (\Throwable $e) {
                            // Keep original if WebP conversion fails
                        }
                    }
                    $attachment = new SupportAttachment();
                    $attachment->support_message_id = $message->id;
                    $attachment->attachment = $savedName;
                    $attachment->save();
                } catch (\Exception $e) {
                    // Log and continue; one failed upload does not rollback ticket
                }
            }
        }

        try {
            if (Schema::hasTable('auto_responses')) {
                $query = AutoResponse::keyword()->active();
                if (Schema::hasColumn('auto_responses', 'is_public')) {
                    $query->where('is_public', true);
                }
                $autoReplies = $query->get();
                $userMessage = mb_strtolower(trim($request->message));
                foreach ($autoReplies as $ar) {
                    $keywords = $ar->getKeywordsList();
                    $matched = false;
                    foreach ($keywords as $kw) {
                        if ($kw !== '' && mb_strpos($userMessage, mb_strtolower($kw)) !== false) {
                            $matched = true;
                            break;
                        }
                    }
                    if ($matched) {
                        $adminId = Admin::first()?->id ?? 0;
                        $autoMsg = new SupportMessage();
                        $autoMsg->support_ticket_id = $ticket->id;
                        $autoMsg->admin_id = $adminId;
                        $autoMsg->message = $ar->message;
                        $autoMsg->save();
                        $ticket->last_reply = Carbon::now();
                        $ticket->status = Status::TICKET_ANSWER;
                        $ticket->save();
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Auto-response is optional - message already saved
        }

        $messages = [];
        $msgs = SupportMessage::where('support_ticket_id', $ticket->id)->with(['admin', 'attachments'])->orderBy('id', 'asc')->get();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        foreach ($msgs as $m) {
            $dt = $m->created_at;
            if ($dt->isSameDay($today)) {
                $dateLabel = __('Today');
            } elseif ($dt->isSameDay($yesterday)) {
                $dateLabel = __('Yesterday');
            } else {
                $dateLabel = $dt->format('d/m/Y');
            }
            $messages[] = [
                'id' => $m->id,
                'message' => $m->message,
                'is_admin' => (bool) $m->admin_id,
                'name' => $m->admin_id ? ($m->admin->name ?? 'Staff') : ($ticket->name ?? 'You'),
                'created_at' => $dt->format('g:i A'),
                'created_at_full' => $dt->format('M d, H:i'),
                'date_label' => $dateLabel,
                'attachments' => $m->attachments->map(fn($a) => route('ticket.download', encrypt($a->id)))->toArray(),
            ];
        }

        if ($userId) {
            Cache::forget('contact_chat_feed_' . $userId);
        }

        $response = response()->json([
            'success' => true,
            'message' => __('Message sent! We will reply soon.'),
            'ticket' => $ticket->ticket,
            'messages' => $messages,
            'new_msg_id' => $message->id,
        ]);

        if (!$userId) {
            $response->cookie('stayl_guest_ticket', $ticket->ticket, 43200); // 30 days
        }

        return $response;
    }

    /**
     * All messages for live chat / contactlive: from ALL web tickets of this user, any subject.
     * When user fetches messages we mark "chat seen" so unread (badge) becomes 0.
     */
    public function getChatMessages(Request $request)
    {
        $user = auth()->user();
        $ticketIds = collect();
        $lastSeen = null;

        if ($user) {
            $ticketIds = SupportTicket::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->pluck('id');
            $lastSeen = Schema::hasColumn('users', 'last_chat_seen_at') ? $user->last_chat_seen_at : null;
        } else {
            $guestTicket = $request->cookie('stayl_guest_ticket');
            if ($guestTicket) {
                $ticketIds = SupportTicket::where('ticket', $guestTicket)->pluck('id');
            }
        }

        $unreadCount = 0;
        if ($ticketIds->isNotEmpty()) {
            $q = SupportMessage::whereIn('support_ticket_id', $ticketIds)
                ->whereNotNull('admin_id')
                ->where('admin_id', '!=', 0);
            $unreadCount = $lastSeen
                ? (clone $q)->where('created_at', '>', $lastSeen)->count()
                : (clone $q)->count();
        }

        if ($user) {
            $cacheKey = 'contact_chat_feed_' . $user->id;
            $messages = Cache::remember($cacheKey, 12, function () use ($user) {
                return $this->contactChannelService->buildChatFeedForUser($user);
            });
            $latestTicket = SupportTicket::where('user_id', $user->id)->latest('id')->first();
            if (Schema::hasColumn('users', 'last_chat_seen_at')) {
                \DB::table('users')->where('id', $user->id)->update(['last_chat_seen_at' => now()]);
            }
        } else {
            $messages = [];
            if ($ticketIds->isNotEmpty()) {
                $msgs = SupportMessage::whereIn('support_ticket_id', $ticketIds)->with(['admin', 'attachments'])->orderBy('id', 'asc')->get();
                $today = Carbon::today();
                $yesterday = Carbon::yesterday();
                foreach ($msgs as $m) {
                    $dt = $m->created_at;
                    $dateLabel = $dt->isSameDay($today) ? __('Today') : ($dt->isSameDay($yesterday) ? __('Yesterday') : $dt->format('d/m/Y'));
                    $messages[] = [
                        'id' => $m->id,
                        'message' => $m->message,
                        'is_admin' => (bool) $m->admin_id,
                        'name' => $m->admin_id ? ($m->admin->name ?? 'Staff') : __('You'),
                        'created_at' => $dt->format('g:i A'),
                        'created_at_full' => $dt->format('M d, H:i'),
                        'date_label' => $dateLabel,
                        'attachments' => $m->attachments->map(fn($a) => route('ticket.download', encrypt($a->id)))->toArray(),
                    ];
                }
            }
            $latestTicket = SupportTicket::whereIn('id', $ticketIds)->latest('id')->first();
        }

        return response()->json([
            'messages' => $messages,
            'ticket' => $latestTicket ? $latestTicket->ticket : null,
            'unread_count' => 0,
        ]);
    }

    /**
     * Unread admin-reply count for live chat badge. Does not mark as seen.
     */
    public function getChatUnreadCount(Request $request)
    {
        $user = auth()->user();
        $ticketIds = collect();
        $lastSeen = null;

        if ($user) {
            $ticketIds = SupportTicket::where('user_id', $user->id)->where('created_at', '>=', now()->subDays(30))->pluck('id');
            $lastSeen = Schema::hasColumn('users', 'last_chat_seen_at') ? $user->last_chat_seen_at : null;
        } else {
            $guestTicket = $request->cookie('stayl_guest_ticket');
            if ($guestTicket) {
                $ticketIds = SupportTicket::where('ticket', $guestTicket)->pluck('id');
            }
        }

        $unreadCount = 0;
        if ($ticketIds->isNotEmpty()) {
            $q = SupportMessage::whereIn('support_ticket_id', $ticketIds)
                ->whereNotNull('admin_id')
                ->where('admin_id', '!=', 0);
            $unreadCount = $lastSeen
                ? (clone $q)->where('created_at', '>', $lastSeen)->count()
                : (clone $q)->count();
        }
        return response()->json(['unread_count' => $unreadCount], 200);
    }

    /**
     * Get redirect URL for WhatsApp/Telegram or submit email as ticket.
     * Admin phone/email/username never sent to frontend – used only server-side.
     */
    public function getContactChannelRedirect(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:whatsapp,telegram,email',
            'message' => 'required|string|max:2000',
            'name' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'email' => 'nullable|email',
        ]);

        $contactContent = getContent('contact_us.content', true);
        $contactEmail = trim(@$contactContent->data_values->contact_email ?? '');
        $wa = preg_replace('/[^0-9]/', '', @$contactContent->data_values->whatsapp_number ?? '');
        $tg = trim(@$contactContent->data_values->telegram_username ?? '');
        $tg = $tg ? ltrim($tg, '@') : '';

        $channel = $request->channel;
        $message = $request->message;

        if ($channel === 'email') {
            if (!$contactEmail) {
                return response()->json(['success' => false, 'message' => __('This channel is not configured.')], 400);
            }
            $name = $request->name ?: __('Visitor');
            $subject = $request->subject ?: __('Message from ') . $name;
            $random = getNumber();
            $ticket = new SupportTicket();
            $ticket->user_id = auth()->id() ?? 0;
            $ticket->name = $name;
            $ticket->email = $request->email ?? auth()->user()?->email ?? 'noreply@' . parse_url(url('/'), PHP_URL_HOST);
            $ticket->priority = Status::PRIORITY_MEDIUM;
            $ticket->ticket = $random;
            $ticket->subject = $subject;
            $ticket->last_reply = Carbon::now();
            $ticket->status = Status::TICKET_OPEN;
            if (Schema::hasColumn('support_tickets', 'channel')) {
                $ticket->channel = 'email';
            }
            $ticket->save();
            $uid = auth()->user()?->id ?? 0;
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $uid;
            $adminNotification->title = $uid ? ('New message from ' . auth()->user()->username . ': ' . \Illuminate\Support\Str::limit(strip_tags($message ?? ''), 60)) : 'New contact (Email channel)';
            $adminNotification->click_url = $uid ? urlPath('admin.ticket.view.user', $uid) : urlPath('admin.ticket.view', $ticket->id);
            $adminNotification->save();
            $msg = new SupportMessage();
            $msg->support_ticket_id = $ticket->id;
            $msg->message = $message;
            $msg->save();
            $this->contactChannelService->logMessage([
                'channel' => 'email',
                'direction' => 'inbound',
                'message' => $message,
                'user_id' => auth()->id(),
                'sender_name' => $name,
                'sender_handle' => strtolower($request->email ?? $ticket->email),
                'subject' => $subject,
                'remote_chat_id' => strtolower($request->email ?? $ticket->email),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Message sent! We will reply soon.'),
            ]);
        }

        if ($channel === 'whatsapp') {
            if (!$wa) {
                return response()->json(['success' => false, 'message' => __('This channel is not configured.')], 400);
            }
            $this->contactChannelService->logMessage([
                'channel' => 'whatsapp',
                'direction' => 'inbound',
                'message' => $message,
                'user_id' => auth()->id(),
                'sender_name' => auth()->user()?->fullname ?? $request->name ?? __('Visitor'),
                'sender_handle' => $wa,
                'remote_chat_id' => $wa,
                'subject' => $request->subject,
            ]);
            $redirect = 'https://wa.me/' . $wa . '?text=' . rawurlencode($message);
            return response()->json(['success' => true, 'redirect' => $redirect]);
        }

        if ($channel === 'telegram') {
            if (!$tg) {
                return response()->json(['success' => false, 'message' => __('This channel is not configured.')], 400);
            }
            $this->contactChannelService->logMessage([
                'channel' => 'telegram',
                'direction' => 'inbound',
                'message' => $message,
                'user_id' => auth()->id(),
                'sender_name' => auth()->user()?->fullname ?? $request->name ?? __('Visitor'),
                'sender_handle' => $tg,
                'remote_chat_id' => $tg,
                'subject' => $request->subject,
            ]);
            $redirect = 'https://t.me/' . $tg . '?text=' . rawurlencode($message);
            return response()->json(['success' => true, 'redirect' => $redirect]);
        }

        return response()->json(['success' => false, 'message' => __('Invalid channel.')], 400);
    }

    /**
     * Short policy URL: /policy/42 → redirect to canonical /policy/slug/42
     */
    public function policyPageShort($id)
    {
        $policy = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->first();
        if (!$policy) {
            $pageTitle = __('Policy');
            $safeDetails = '';
            return view($this->activeTemplate . 'policy', compact('pageTitle', 'safeDetails'))->with('policy', null);
        }
        $slug = \Illuminate\Support\Str::slug($policy->data_values->title ?? 'policy');
        return redirect()->route('policy.pages', ['slug' => $slug, 'id' => $policy->id], 301);
    }

    /**
     * Policy page: validate slug (prevents wrong-slug access), output sanitized HTML
     */
    public function policyPages($slug, $id)
    {
        $policy = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->first();
        if (!$policy) {
            $pageTitle = __('Policy');
            $safeDetails = '';
            return view($this->activeTemplate . 'policy', compact('pageTitle', 'safeDetails'))->with('policy', null);
        }
        $expectedSlug = \Illuminate\Support\Str::slug($policy->data_values->title ?? '');
        if ($expectedSlug !== $slug) {
            abort(404);
        }
        $pageTitle = $policy->data_values->title ?? __('Policy');
        $safeDetails = safe_policy_html($policy->data_values->details ?? '');
        $safeDetails2 = safe_policy_html($policy->data_values->details_2 ?? '');
        return view($this->activeTemplate . 'policy', compact('policy', 'pageTitle', 'safeDetails', 'safeDetails2'));
    }

    public function changeLanguage(Request $request, $lang = null)
    {
        $allowed = ['en', 'bn', 'hi', 'ar', 'ur', 'ru', 'zh', 'es', 'fr', 'de', 'pt', 'ja'];
        $inputCode = strtolower(trim((string) $lang));

        if (!in_array($inputCode, $allowed)) {
            $inputCode = 'en';
        }

        // Persist in session for middleware fallback
        session(['locale' => $inputCode, 'lang' => $inputCode]);
        app()->setLocale($inputCode);

        $previousUrl = url()->previous();
        $rootUrl = $request->getSchemeAndHttpHost() . $request->getBaseUrl();

        // Check if previous URL is internal to site
        if (str_starts_with($previousUrl, $rootUrl)) {
            $path = trim(substr($previousUrl, strlen($rootUrl)), '/');
            $segments = explode('/', $path);

            // If the first segment is an existing locale, replace it; otherwise unshift
            if (isset($segments[0]) && in_array($segments[0], $allowed)) {
                $segments[0] = $inputCode;
            } else {
                array_unshift($segments, $inputCode);
            }

            $targetPath = implode('/', $segments);
            return redirect()->to($targetPath);
        }

        return redirect()->to($inputCode);
    }

    private function normalizeLocaleCode(string $code): string
    {
        $code = strtolower(trim($code));
        return $code !== '' ? $code : 'en';
    }

    private function localeExists(string $code): bool
    {
        return File::exists(resource_path("lang/{$code}.json")) || File::isDirectory(resource_path("lang/{$code}"));
    }

    public function cookieAccept()
    {
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        $expiryDays = $cookie && isset($cookie->data_values->cookie_expiry_days) ? (int) $cookie->data_values->cookie_expiry_days : 365;
        $minutes = min(525600, max(1440, $expiryDays * 24 * 60)); // 1 day to 1 year
        Cookie::queue('gdpr_cookie', gs('site_name'), $minutes);
        Cookie::queue('gdpr_cookie_declined', '', -1);
        return response()->json(['ok' => 1]);
    }

    public function cookieDecline()
    {
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        $expiryDays = $cookie && isset($cookie->data_values->cookie_expiry_days) ? (int) $cookie->data_values->cookie_expiry_days : 365;
        $minutes = min(525600, max(1440, $expiryDays * 24 * 60));
        Cookie::queue('gdpr_cookie_declined', '1', $minutes);
        Cookie::queue('gdpr_cookie', '', -1);
        return response()->json(['ok' => 1]);
    }

    public function cookieRevoke()
    {
        Cookie::queue('gdpr_cookie', '', -1);
        Cookie::queue('gdpr_cookie_declined', '', -1);
        return redirect()->back();
    }

    public function cookiePolicy()
    {
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie'));
    }

    /**
     * Submit product return request from footer form (guest or logged-in).
     * Creates a support ticket with subject "Product Return Request".
     */
    public function submitReturnRequest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'order_number' => 'nullable|string|max:50',
            'reason' => 'nullable|string|max:200',
            'message' => 'required|string|max:2000',
        ], [], [
            'name' => __('Name'),
            'email' => __('Email'),
            'order_number' => __('Order number'),
            'reason' => __('Reason'),
            'message' => __('Message'),
        ]);

        $body = __('Order') . ': ' . ($request->order_number ?: '—') . "\n";
        $body .= __('Reason') . ': ' . ($request->reason ?: '—') . "\n\n";
        $body .= $request->message;

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->ticket = getNumber();
        $ticket->subject = __('Product Return Request');
        $ticket->last_reply = Carbon::now();
        $ticket->status = Status::TICKET_OPEN;
        $ticket->priority = Status::PRIORITY_MEDIUM;
        if (Schema::hasColumn('support_tickets', 'channel')) {
            $ticket->channel = SupportTicket::CHANNEL_WEB;
        }
        $ticket->save();

        $msg = new SupportMessage();
        $msg->support_ticket_id = $ticket->id;
        $msg->message = $body;
        $msg->save();

        $uid = auth()->id() ?? 0;
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $uid;
        $adminNotification->title = __('Product Return Request from ') . $request->name;
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $returnData = Frontend::where('data_keys', 'footer.return_policy')->orderBy('id', 'desc')->first();
        $successMessage = $returnData && !empty($returnData->data_values->success_message)
            ? $returnData->data_values->success_message
            : __('We have received your return request. Our team will contact you shortly.');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $successMessage]);
        }
        return back()->withNotify([['success', $successMessage]]);
    }

    public function placeholderImage($size = null)
    {
        if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
            return redirect(asset('assets/images/default.png'));
        }

        $imgWidth = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize = round(($imgWidth - 50) / 8);

        if ($fontSize <= 9) {
            $fontSize = 9;
        }

        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX = ($imgWidth - $textWidth) / 2;
        $textY = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        $general = gs();

        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }

        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view($this->activeTemplate . 'maintenance', compact('pageTitle', 'maintenance'));
    }

    public function products(Request $request)
    {
        $pageTitle = "All Products";
        $emptyMessage = 'No products found';
        $isFirstPageNoFilter = !$request->filled('categories') && !$request->filled('subcategories')
            && !$request->filled('search') && (int) $request->get('page', 1) <= 1;

        if ($isFirstPageNoFilter && config('cache.default') !== 'array') {
            $cacheKey = 'response.products.index.first_page';
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return view($this->activeTemplate . 'products.index', $cached + ['pageTitle' => $pageTitle, 'emptyMessage' => $emptyMessage]);
            }
        }

        $data = $this->getProductData();
        $data['subcategoryList'] = collect();
        $variantLists = $this->getVariantFilterLists($data['products']);
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $productList = $data['products'];

        $productList = $this->productsQuery($productList, $request);

        if ($request->categories && is_array($request->categories) && count($request->categories) > 0) {
            $categories = array_filter($request->categories);
            if (count($categories) > 0) {
                $productList = $productList->whereIn('category_id', $categories);
            }
        }

        if ($request->subcategories && is_array($request->subcategories) && count($request->subcategories) > 0) {
            $subcategories = array_filter($request->subcategories);
            if (count($subcategories) > 0) {
                $productList = $productList->whereIn('subcategory_id', $subcategories);
            }
        }

        $data['products'] = $productList;
        $products = $productList->paginate(getPaginate());

        if ((int) $request->get('page', 1) <= 1) {
            $this->logPublicSearchOrFilter($request, $products->total());
        }

        if ($isFirstPageNoFilter && config('cache.default') !== 'array') {
            Cache::put('response.products.index.first_page', [
                'products' => $products,
                'data' => [
                    'minPrice' => $data['minPrice'],
                    'maxPrice' => $data['maxPrice'],
                    'brands' => $data['brands'],
                    'categoryList' => $data['categoryList'],
                    'subcategoryList' => $data['subcategoryList'] ?? collect(),
                    'sizeList' => $data['sizeList'] ?? collect(),
                    'colorList' => $data['colorList'] ?? collect(),
                ],
            ], 120);
        }

        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data', 'emptyMessage'));
    }

    /**
     * Log search or filter from public products page for admin analytics.
     */
    protected function logPublicSearchOrFilter(Request $request, int $resultsCount): void
    {
        try {
            $search = trim((string) $request->get('search', ''));
            if ($search !== '') {
                $query = mb_substr($search, 0, 500);
                $source = 'products_page';
            } else {
                $parts = [];
                if ($request->filled('categories') && is_array($request->categories)) {
                    $ids = array_filter($request->categories);
                    if (!empty($ids)) {
                        $names = Category::whereIn('id', $ids)->pluck('name')->take(5)->implode(', ');
                        $parts[] = 'Cat: ' . $names;
                    }
                }
                if ($request->filled('brands') && is_array($request->brands)) {
                    $ids = array_filter($request->brands);
                    if (!empty($ids)) {
                        $names = Brand::whereIn('id', $ids)->pluck('name')->take(5)->implode(', ');
                        $parts[] = 'Brand: ' . $names;
                    }
                }
                if ($request->filled('min_price') || $request->filled('max_price')) {
                    $min = $request->get('min_price') ?: $request->get('min');
                    $max = $request->get('max_price') ?: $request->get('max');
                    if ($min || $max)
                        $parts[] = 'Price: ' . ($min ?: '0') . '-' . ($max ?: '∞');
                }
                if (empty($parts))
                    return;
                $query = mb_substr(implode(' | ', $parts), 0, 500);
                $source = 'filter';
            }
            SearchLog::create([
                'query' => $query,
                'user_id' => auth('web')->id(),
                'ip' => $request->ip(),
                'user_agent' => mb_substr($request->userAgent() ?? '', 0, 512),
                'results_count' => $resultsCount,
                'source' => $source,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Search log (products/filter) failed: ' . $e->getMessage());
        }
    }

    public function hotDeal()
    {
        $pageTitle = "Hot Deal Product";
        $data = $this->getProductData('hotDeal');
        $data['subcategoryList'] = collect();
        $variantLists = $this->getVariantFilterLists($data['products']);
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $products = $data['products']->paginate(getPaginate());
        $productScope = 'hotDeal';
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data', 'productScope'));
    }

    public function featured()
    {
        $pageTitle = "Featured Product";
        $data = $this->getProductData('featured');
        $data['subcategoryList'] = collect();
        $variantLists = $this->getVariantFilterLists($data['products']);
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $products = $data['products']->paginate(getPaginate());
        $productScope = 'featured';
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data', 'productScope'));
    }

    public function todayDeal()
    {
        $pageTitle = __('Today\'s Deal');
        $data = $this->getProductData('todayDeal');
        $data['subcategoryList'] = collect();
        $variantLists = $this->getVariantFilterLists($data['products']);
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $products = $data['products']->paginate(getPaginate());
        $productScope = 'todayDeal';
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data', 'productScope'));
    }

    public function bestSelling()
    {
        $pageTitle = "Best Selling Product";
        $data = $this->getProductData('bestSelling');
        $data['products'] = $data['products']->notSpotlight();
        $data['subcategoryList'] = collect();
        $variantLists = $this->getVariantFilterLists($data['products']);
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $products = $data['products']->paginate(getPaginate());
        $productScope = 'bestSelling';
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data', 'productScope'));
    }

    public function newArrival()
    {
        $pageTitle = __('New Arrival');
        $data = $this->getProductData();
        $data['subcategoryList'] = collect();
        $variantLists = $this->getVariantFilterLists($data['products']);
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $products = $data['products']->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }

    public function discount()
    {
        $pageTitle = __('Discount & Offers');
        $data = $this->getProductData();
        $data['subcategoryList'] = collect();
        $variantLists = $this->getVariantFilterLists($data['products']);
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $products = $data['products']->where(function ($q) {
            $q->where('discount', '>', 0)->orWhere('today_deals', Status::YES);
        })->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data'));
    }

    /**
     * All Categories – advanced listing with server cache, sort, search, per-page.
     * Browser cache: CacheHeaders middleware. Server cache: 10 min, invalidated on admin category change.
     */
    public function categoryAll(Request $request)
    {
        $pageTitle = __('All Categories');
        $perPage = (int) $request->get('per_page', 24);
        $perPage = in_array($perPage, [12, 24, 48, 96], true) ? $perPage : 24;
        $sort = $request->get('sort', 'name_asc');
        $allowedSort = ['name_asc', 'name_desc', 'products_desc', 'featured_first'];
        $sort = in_array($sort, $allowedSort, true) ? $sort : 'name_asc';
        $search = trim((string) $request->get('q', ''));
        $featuredOnly = $request->has('featured') && $request->get('featured') === '1';

        $cacheVersion = Cache::get('category_all_updated', 0);
        $cacheKey = 'category_all.' . $cacheVersion . '.' . md5($request->fullUrl());

        $categories = Cache::remember($cacheKey, 600, function () use ($sort, $search, $perPage, $featuredOnly, $request) {
            $query = Category::query()
                ->active()
                ->publicPublished()
                ->withCount([
                    'product' => function ($q) {
                        $q->where('status', Status::ENABLE);
                    }
                ])
                ->with([
                    'subcategories' => function ($q) {
                        $q->active()->orderBy('name')->limit(6);
                    }
                ]);

            if ($featuredOnly) {
                $query->where('featured', 1);
            }
            if ($search !== '') {
                $query->where('name', 'LIKE', '%' . $search . '%');
            }

            if ($sort === 'name_asc') {
                $query->orderBy('name', 'asc');
            } elseif ($sort === 'name_desc') {
                $query->orderBy('name', 'desc');
            } elseif ($sort === 'products_desc') {
                $query->orderBy('product_count', 'desc')->orderBy('name', 'asc');
            } elseif ($sort === 'featured_first') {
                $query->orderByRaw('CASE WHEN featured = 1 THEN 0 ELSE 1 END')->orderBy('name', 'asc');
            }

            return $query->paginate($perPage)->withQueryString();
        });

        $emptyMessage = __('No categories found.');

        $categoriesNav = Cache::remember('category_all_nav.' . $cacheVersion, 600, function () {
            return Category::query()
                ->active()
                ->publicPublished()
                ->orderBy('name')
                ->with([
                    'subcategories' => function ($q) {
                        $q->active()->orderBy('name');
                    }
                ])
                ->get();
        });

        return view($this->activeTemplate . 'all_category', compact('pageTitle', 'categories', 'categoriesNav', 'emptyMessage', 'sort', 'search', 'perPage', 'featuredOnly'));
    }

    /**
     * Guest account menu (same options as the old bottom-nav modal), opens in a new tab from mobile nav.
     */
    public function guestAccountMenu()
    {
        if (auth()->check()) {
            return redirect()->route('user.home');
        }
        $pageTitle = __('My Account');

        return view($this->activeTemplate . 'guest_account_page', compact('pageTitle'));
    }

    public function allBrand()
    {
        $pageTitle = 'All Brands';
        $brands = Brand::active()->orderBy('name')->paginate(getPaginate());
        return view($this->activeTemplate . 'all_brands', compact('pageTitle', 'brands'));
    }

    public function categoryProduct($slug, $id)
    {
        $id = (int) $id;
        $category = Category::find($id);
        $name = $category ? $category->name : keyToTitle($slug);
        $pageTitle = __('Products in') . ' ' . $name;
        $data = $this->getProductData();
        $products = $data['products']->where('category_id', $id)->latest()->paginate(getPaginate());
        $currentCategoryId = $id;
        $currentSubcategoryId = null;
        $data['subcategoryList'] = Subcategory::where('category_id', $id)->where('status', Status::ENABLE)
            ->whereIn('id', (clone $data['products'])->where('category_id', $id)->distinct()->pluck('subcategory_id')->filter())
            ->withCount(['products' => fn($q) => $q->where('status', Status::ENABLE)])
            ->orderBy('name')
            ->get();
        $variantLists = $this->getVariantFilterLists($data['products']->where('category_id', $id));
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $seoContents = [
            'description' => __('Browse') . ' ' . __($name) . ' - ' . __('quality products at best prices.') . ' | ' . (gs()->sitename ?? ''),
            'keywords' => array_filter([$name, __('products'), __('buy'), __('online')]),
            'social_title' => $pageTitle,
            'social_description' => __('Shop') . ' ' . __($name) . ' ' . __('products') . '.',
        ];
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data', 'currentCategoryId', 'currentSubcategoryId', 'seoContents'));
    }

    public function brandProduct($slug, $id)
    {
        $brand = Brand::find($id);
        $name = $brand ? $brand->name : keyToTitle($slug);
        $pageTitle = $name . ' - ' . __('Products');
        $data = $this->getProductData();
        $products = $data['products']->where('brand_id', $id)->latest()->paginate(getPaginate());
        $currentCategoryId = null;
        $currentSubcategoryId = null;
        $data['subcategoryList'] = collect();
        $variantLists = $this->getVariantFilterLists($data['products']->where('brand_id', $id));
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $seoContents = [
            'description' => __('Shop') . ' ' . __($name) . ' ' . __('products') . ' - ' . __('official collection.') . ' | ' . (gs()->sitename ?? ''),
            'keywords' => array_filter([$name, __('products'), __('brand')]),
            'social_title' => $pageTitle,
            'social_description' => __('Explore') . ' ' . __($name) . ' ' . __('products') . '.',
        ];
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data', 'currentCategoryId', 'currentSubcategoryId', 'seoContents'));
    }

    public function subCategoryProduct($slug, $id)
    {
        $id = (int) $id;
        $subcategory = Subcategory::find($id);
        $name = $subcategory ? $subcategory->name : keyToTitle($slug);
        $pageTitle = __('Products in') . ' ' . $name;
        $data = $this->getProductData();
        $products = $data['products']->where('subcategory_id', $id)->latest()->paginate(getPaginate());
        $currentCategoryId = $subcategory ? (int) $subcategory->category_id : null;
        $currentSubcategoryId = $id;
        $data['subcategoryList'] = $currentCategoryId
            ? Subcategory::where('category_id', $currentCategoryId)->where('status', Status::ENABLE)
                ->whereIn('id', (clone $data['products'])->where('category_id', $currentCategoryId)->distinct()->pluck('subcategory_id'))
                ->withCount(['products' => fn($q) => $q->where('status', Status::ENABLE)])
                ->orderBy('name')
                ->get()
            : collect();
        $variantLists = $this->getVariantFilterLists($data['products']->where('subcategory_id', $id));
        $data['sizeList'] = $variantLists['sizes'];
        $data['colorList'] = $variantLists['colors'];
        $seoContents = [
            'description' => __('Browse') . ' ' . __($name) . ' - ' . __('products') . '. ' . (gs()->sitename ?? ''),
            'keywords' => array_filter([$name, __('products'), __('subcategory')]),
            'social_title' => $pageTitle,
            'social_description' => __('Shop') . ' ' . __($name) . ' ' . __('products') . '.',
        ];
        return view($this->activeTemplate . 'products.index', compact('pageTitle', 'products', 'data', 'currentCategoryId', 'currentSubcategoryId', 'seoContents'));
    }


    public function filterProduct(Request $request)
    {
        $scope = $request->get('product_scope');
        $allowedScopes = ['featured', 'hotDeal', 'bestSelling', 'todayDeal'];
        if ($scope && in_array($scope, $allowedScopes, true)) {
            $data = $this->getProductData($scope);
            $productList = $data['products'];
            if ($scope === 'bestSelling') {
                $productList = $productList->notSpotlight();
            }
        } else {
            $productList = $this->getProductData()['products'];
        }

        // Enhanced filter handling - Support both old and new parameter names
        if ($request->brandId) {
            $productList = $productList->where('brand_id', $request->brandId);
        }

        // New filter parameters (from sidebar)
        if ($request->brands && is_array($request->brands) && count($request->brands) > 0) {
            $brands = array_filter($request->brands);
            if (count($brands) > 0) {
                $productList = $productList->whereIn('brand_id', $brands);
            }
        }

        if ($request->categoryId) {
            if ($request->categoryId != 0) {
                $productList = $productList->where('category_id', $request->categoryId);
                $productFilter = $this->subcategoriesQuery($productList, $request);
            }
        } else {
            $productFilter = $this->categoriesQuery($productList, $request);
        }

        // New categories filter (from sidebar)
        if ($request->categories && is_array($request->categories) && count($request->categories) > 0) {
            $categories = array_filter($request->categories);
            if (count($categories) > 0) {
                $productFilter = $productFilter->whereIn('category_id', $categories);
            }
        }

        if ($request->subcategoryId) {
            $productFilter = $productList->where('subcategory_id', $request->subcategoryId);
        }

        // New subcategories filter (from sidebar)
        if ($request->subcategories && is_array($request->subcategories) && count($request->subcategories) > 0) {
            $subcategories = array_filter($request->subcategories);
            if (count($subcategories) > 0) {
                $productFilter = $productFilter->whereIn('subcategory_id', $subcategories);
            }
        }

        $productFilter = $this->productsQuery($productFilter, $request);

        if ($request->paginate == null) {
            $paginate = getPaginate();
        } else {
            $paginate = $request->paginate;
        }

        $products = $productFilter->latest()->paginate($paginate);
        $emptyMessage = 'No products found';
        return view($this->activeTemplate . 'products.show_products', compact('products', 'emptyMessage'));
    }

    protected function categoriesQuery($productList, $request)
    {
        if ($request->categories) {
            $productList = $productList->whereIn('category_id', $request->categories);
        }

        return $productList;
    }

    protected function subcategoriesQuery($productList, $request)
    {
        if ($request->subcategories) {
            $productList = $productList->whereIn('subcategory_id', $request->subcategories);
        }

        return $productList;
    }

    protected function productsQuery($productFilter, $request)
    {
        // Brands filter
        if ($request->brands && is_array($request->brands) && count($request->brands) > 0) {
            $brands = array_filter($request->brands); // Remove empty values
            if (count($brands) > 0) {
                $productFilter = $productFilter->whereIn('brand_id', $brands);
            }
        }

        // Categories filter
        if ($request->categories && is_array($request->categories) && count($request->categories) > 0) {
            $categories = array_filter($request->categories); // Remove empty values
            if (count($categories) > 0) {
                $productFilter = $productFilter->whereIn('category_id', $categories);
            }
        }

        // Subcategories filter
        if ($request->subcategories && is_array($request->subcategories) && count($request->subcategories) > 0) {
            $subcategories = array_filter($request->subcategories); // Remove empty values
            if (count($subcategories) > 0) {
                $productFilter = $productFilter->whereIn('subcategory_id', $subcategories);
            }
        }

        // Price filter - Enhanced
        $minPrice = $request->min_price ?? $request->min;
        $maxPrice = $request->max_price ?? $request->max;

        if ($minPrice && $maxPrice) {
            $minPrice = (float) $minPrice;
            $maxPrice = (float) $maxPrice;
            if ($minPrice > 0 || $maxPrice > 0) {
                $productFilter = $productFilter->whereBetween('price', [$minPrice, $maxPrice]);
            }
        } elseif ($minPrice) {
            $minPrice = (float) $minPrice;
            if ($minPrice > 0) {
                $productFilter = $productFilter->where('price', '>=', $minPrice);
            }
        } elseif ($maxPrice) {
            $maxPrice = (float) $maxPrice;
            if ($maxPrice > 0) {
                $productFilter = $productFilter->where('price', '<=', $maxPrice);
            }
        }

        // Sort filter
        if ($request->sort) {
            $sort = explode('_', $request->sort);
            $productFilter = $productFilter->orderBy(@$sort[0], @$sort[1]);
        }

        // Special offers / discount filter (percentage only: 50%+, 30-50%, 1-30%)
        $discountOffers = $request->discount_offers;
        if ($discountOffers && is_array($discountOffers) && count($discountOffers) > 0) {
            $discountOffers = array_filter($discountOffers);
            if (count($discountOffers) > 0) {
                $productFilter = $productFilter->where(function ($q) use ($discountOffers) {
                    foreach ($discountOffers as $offer) {
                        if ($offer === '50+') {
                            $q->orWhere(function ($q2) {
                                $q2->where('discount_type', 2)->where('discount', '>=', 50);
                            });
                        } elseif ($offer === '30-50') {
                            $q->orWhere(function ($q2) {
                                $q2->where('discount_type', 2)->whereBetween('discount', [30, 50]);
                            });
                        } elseif ($offer === '1-30') {
                            $q->orWhere(function ($q2) {
                                $q2->where('discount_type', 2)->whereBetween('discount', [1, 30]);
                            });
                        }
                    }
                });
            }
        }

        // Size filter (products that have at least one variant with selected size)
        $sizes = $request->sizes;
        if ($sizes && is_array($sizes) && count($sizes) > 0) {
            $sizes = array_filter($sizes);
            if (count($sizes) > 0) {
                $productFilter = $productFilter->whereHas('variants', function ($v) use ($sizes) {
                    $v->where(function ($v2) use ($sizes) {
                        foreach ($sizes as $s) {
                            $v2->orWhereJsonContains('attributes->size', $s);
                        }
                    });
                });
            }
        }

        // Color filter (products that have at least one variant with selected color)
        $colors = $request->colors;
        if ($colors && is_array($colors) && count($colors) > 0) {
            $colors = array_filter($colors);
            if (count($colors) > 0) {
                $productFilter = $productFilter->whereHas('variants', function ($v) use ($colors) {
                    $v->where(function ($v2) use ($colors) {
                        foreach ($colors as $c) {
                            $v2->orWhereJsonContains('attributes->color', $c);
                        }
                    });
                });
            }
        }

        return $productFilter;
    }

    public function quickView(Request $request)
    {
        $productId = $request->integer('product_id') ?: $request->input('product_id');
        if (!$productId || !is_numeric($productId)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => __('Product not found.')], 400);
            }
            abort(404);
        }

        try {
            $product = Product::active()
                ->with(['category:id,name'])
                ->withCount('reviews')
                ->findOrFail((int) $productId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => __('Product not found.')], 404);
            }
            abort(404);
        }

        $general = gs();
        return view($this->activeTemplate . 'products.quick_view', compact('product', 'general'));
    }

    protected function getProductData($scope = null)
    {
        if ($scope) {
            $products = Product::$scope();
        } else {
            $products = Product::query();
        }

        // Apply search if search parameter exists - Enhanced search across all fields
        if (request()->has('search') && !empty(trim(request()->search))) {
            $searchTerm = trim(request()->search);
            if (strlen($searchTerm) >= 2) {
                $products = $products->where(function ($query) use ($searchTerm) {
                    $query->where('products.name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('products.description', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('products.summary', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('products.product_sku', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('category', function ($q) use ($searchTerm) {
                            $q->where('categories.name', 'LIKE', "%{$searchTerm}%");
                        })
                        ->orWhereHas('brand', function ($q) use ($searchTerm) {
                            $q->where('brands.name', 'LIKE', "%{$searchTerm}%");
                        })
                        ->orWhereHas('subcategory', function ($q) use ($searchTerm) {
                            $q->where('subcategories.name', 'LIKE', "%{$searchTerm}%");
                        });
                })->where('products.status', Status::ENABLE);
            } else {
                $products = $products->available();
            }
        } else {
            // Use searchable trait for other cases
            $products = $products->available()->searchable(['name', 'description', 'features', 'summary', 'category:name', 'subcategory:name', 'brand:name']);
        }

        // Cache min/max & filter data 15 min - invalidates on product/category/brand change
        $hasSearch = request()->has('search') && strlen(trim((string) request()->search)) >= 2;
        $data = $hasSearch ? $this->computeProductFilterData($products) : Cache::remember(
            'product_data.' . ($scope ?? 'all'),
            900,
            function () use ($products) {
                return $this->computeProductFilterData($products);
            }
        );

        // Eager load - minimal relations (fast even with lakhs of products)
        $products = $products
            ->withCount('reviews')
            ->with(['category:id,name', 'brand:id,name']);

        return [
            'products' => $products,
            'minPrice' => $data['minPrice'],
            'maxPrice' => $data['maxPrice'],
            'brands' => $data['brands'],
            'categoryList' => $data['categoryList'],
        ];
    }

    /**
     * Compute min/max price and category/brand lists for product filters (used by getProductData).
     */
    protected function computeProductFilterData($products)
    {
        $minPrice = $products->min('price');
        $maxPrice = $products->max('price');
        $categoryList = Category::where('categories.status', Status::ENABLE)
            ->publicPublished()
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->where('products.status', Status::ENABLE)
            ->select('categories.*')
            ->distinct()
            ->get();
        $productQuery = clone $products;
        $brandIds = $productQuery->distinct()->pluck('brand_id')->filter();
        $counts = (clone $products)->selectRaw('brand_id, count(*) as products_count')
            ->groupBy('brand_id')
            ->pluck('products_count', 'brand_id');
        $brands = Brand::whereIn('id', $brandIds)->where('status', Status::ENABLE)
            ->orderBy('name')
            ->get()
            ->map(function ($brand) use ($counts) {
                $brand->products_count = (int) ($counts[$brand->id] ?? 0);
                return $brand;
            });
        return [
            'minPrice' => $minPrice ?? 0,
            'maxPrice' => $maxPrice ?? 0,
            'brands' => $brands,
            'categoryList' => $categoryList,
        ];
    }

    /**
     * Get distinct size and color values from product variants for the given product query (e.g. for filter sidebar).
     */
    protected function getVariantFilterLists($productQuery)
    {
        $ids = (clone $productQuery)->limit(5000)->pluck('id');
        if ($ids->isEmpty()) {
            return ['sizes' => collect(), 'colors' => collect()];
        }
        $variants = ProductVariant::whereIn('product_id', $ids)->get(['attributes']);
        $sizes = $variants->pluck('attributes')->filter()->map(function ($a) {
            return is_array($a) ? ($a['size'] ?? $a['Size'] ?? null) : null;
        })->filter()->unique()->sort()->values();
        $colors = $variants->pluck('attributes')->filter()->map(function ($a) {
            return is_array($a) ? ($a['color'] ?? $a['Color'] ?? null) : null;
        })->filter()->unique()->sort()->values();
        return ['sizes' => $sizes, 'colors' => $colors];
    }

    public function download($id, $fileName)
    {
        Product::where('id', $id)->where('digital_item', Status::YES)->where('file_type', 1)->where('file', $fileName)->firstOrFail();
        $path = fileManager()->productFile()->path . '/' . $fileName;
        return response()->download($path);
    }

    /** Record banner impression or click for analytics (public API). */
    public function recordBannerAnalytics(Request $request)
    {
        $request->validate([
            'banner_id' => 'required|integer|min:1',
            'event' => 'required|in:impression,click',
        ]);
        $banner = Frontend::where('id', $request->banner_id)->where('data_keys', 'banner.element')->first();
        if (!$banner) {
            return response()->json(['success' => false], 400);
        }
        try {
            BannerAnalytics::record(
                (int) $request->banner_id,
                $request->event,
                $request->input('device'),
                $request->input('campaign_source')
            );
        } catch (\Throwable $e) {
            return response()->json(['success' => false], 200);
        }
        return response()->json(['success' => true]);
    }

    /**
     * Serve banner image from project root or public so it loads regardless of server static path.
     */
    public function serveBannerImage($localeOrFilename = null, $filename = null)
    {
        $resolvedFilename = $filename;
        if ($resolvedFilename === null) {
            $resolvedFilename = $localeOrFilename;
        }
        $resolvedFilename = basename((string) $resolvedFilename);
        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $resolvedFilename)) {
            abort(404);
        }
        $base = \App\Services\BannerService::UPLOAD_BASE . '/' . \App\Services\BannerService::DESKTOP_DIR;
        $paths = [
            base_path('../' . $base . '/' . $resolvedFilename),
            public_path($base . '/' . $resolvedFilename),
            base_path('../' . \App\Services\BannerService::UPLOAD_BASE . '/' . $resolvedFilename),
        ];
        foreach ($paths as $path) {
            if (is_file($path) && is_readable($path)) {
                $mime = mime_content_type($path) ?: 'image/jpeg';
                return response()->file($path, ['Content-Type' => $mime]);
            }
        }
        abort(404);
    }

    /**
     * Serve row split promo images from project root assets or Laravel public (same reliability as hero banners).
     */
    public function serveRowSplitBanner($localeOrFilename = null, $filename = null)
    {
        $resolvedFilename = $filename;
        if ($resolvedFilename === null) {
            $resolvedFilename = $localeOrFilename;
        }
        $resolvedFilename = basename((string) $resolvedFilename);
        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $resolvedFilename)) {
            abort(404);
        }
        $rel = \App\Services\BannerService::ROW_SPLIT_RELATIVE;
        $paths = [
            base_path('../' . $rel . '/' . $resolvedFilename),
            public_path($rel . '/' . $resolvedFilename),
        ];
        foreach ($paths as $path) {
            if (is_file($path) && is_readable($path)) {
                $mime = mime_content_type($path) ?: 'image/jpeg';
                return response()->file($path, ['Content-Type' => $mime]);
            }
        }
        abort(404);
    }
}
