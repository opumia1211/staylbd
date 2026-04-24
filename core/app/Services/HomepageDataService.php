<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\HomepageCustomProductRow;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Cached homepage data – single cache, minimal columns for fast load.
 * Fault isolation: each section loaded independently; failure in one does not break others.
 */
class HomepageDataService
{
    public const CACHE_BASE_KEY = 'homepage.sections.data';
    public const TTL = 600; // 10 min

    /** Locale-aware cache key */
    public static function getCacheKey(): string
    {
        return self::CACHE_BASE_KEY . '.' . app()->getLocale();
    }

    /** Columns needed for product cards and available() scope (smaller payload, faster load) */
    private const PRODUCT_SELECT_BASE = [
        'id', 'name', 'image', 'gallery', 'price', 'discount', 'discount_type', 'today_deals',
        'sale_count', 'quantity', 'has_variants', 'created_at', 'avg_rate', 'category_id', 'brand_id', 'subcategory_id', 'status',
    ];

    /** Max products per section in cache (10 = light, single row) */
    private const SECTION_LIMIT = 10;

    /** Initial products per section on first paint (lazy load rest via AJAX) */
    public const INITIAL_PAGE_SIZE = 8;

    private static function productSelect(): array
    {
        $cols = self::PRODUCT_SELECT_BASE;
        if (\Illuminate\Support\Facades\Schema::hasColumn((new Product)->getTable(), 'trending_now')) {
            $cols[] = 'trending_now';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn((new Product)->getTable(), 'home_section_override')) {
            $cols[] = 'home_section_override';
            $cols[] = 'home_section_rank';
            $cols[] = 'home_exclude_from_auto';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn((new Product)->getTable(), 'delivery_type')) {
            $cols[] = 'delivery_type';
            $cols[] = 'delivery_charge';
        }
        return $cols;
    }

    /** Only allow known override keys (prevent SQL injection in raw ordering). */
    private static function isAllowedOverrideKey(string $key): bool
    {
        return in_array($key, ['new_arrivals', 'best_selling', 'recommended', 'trending'], true);
    }

    /**
     * Filter: allow products that are either not overridden OR overridden to this section.
     * Also supports a strict exclusion: if product explicitly set "exclude from auto", it won't show in auto sections unless overridden here.
     */
    private static function applyAutoSectionFilters($query, string $sectionKey, array $excludeIds = [])
    {
        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn((new Product)->getTable(), 'home_section_override') && self::isAllowedOverrideKey($sectionKey)) {
            $query->where(function ($q) use ($sectionKey) {
                $q->whereNull('home_section_override')
                    ->orWhere('home_section_override', $sectionKey);
            });
            $query->where(function ($q) use ($sectionKey) {
                $q->where('home_exclude_from_auto', 0)
                    ->orWhere('home_section_override', $sectionKey);
            });
        }
        return $query;
    }

    /** Get override-first ordering (manual picks appear first). */
    private static function applyOverrideOrdering($query, string $sectionKey)
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn((new Product)->getTable(), 'home_section_override') && self::isAllowedOverrideKey($sectionKey)) {
            $query->orderByRaw("CASE WHEN home_section_override = '" . $sectionKey . "' THEN 0 ELSE 1 END")
                ->orderByDesc('home_section_rank');
        }
        return $query;
    }

    public static function getCachedData(): array
    {
        return Cache::remember(self::getCacheKey(), self::TTL, function () {
            return self::loadAll();
        });
    }

    public static function clearCache(): void
    {
        $locales = self::getAllLocales();
        foreach ($locales as $l) {
            Cache::forget(self::CACHE_BASE_KEY . '.' . $l);
        }
        self::clearBelowFoldFragmentCache();
    }

    /** Invalidate deferred homepage HTML (all locales). */
    public static function clearBelowFoldFragmentCache(): void
    {
        $locales = self::getAllLocales();
        foreach ($locales as $l) {
            Cache::forget('home.below_fold.v2.' . $l);
        }
    }

    private static function getAllLocales(): array
    {
        $locales = [config('app.locale', 'en')];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('languages')) {
                $codes = \App\Models\Language::query()->pluck('code')->filter()->values()->all();
                if (!empty($codes)) {
                    $locales = array_values(array_unique(array_merge($locales, $codes)));
                }
            }
        } catch (\Throwable $e) {
        }
        return $locales;
    }

    /**
     * Get products for a section with offset/limit (for lazy "load more" on homepage).
     * Eager loads category, brand, reviews count.
     */
    public static function getSectionProducts(string $section, int $offset = 0, int $limit = 8)
    {
        $baseWith = ['category:id,name', 'brand:id,name', 'activeVariants'];
        $reviewCount = fn ($r) => $r->visibleOnProduct();

        if (preg_match('/^custom_row_(\d+)$/', $section, $m)) {
            return self::getCustomRowProducts((int) $m[1], $offset, $limit, $baseWith, $reviewCount);
        }

        switch ($section) {
            case 'today_deals':
                return self::newProductQuery()
                    ->todayDeal()
                    ->with($baseWith)
                    ->withCount(['reviews' => $reviewCount])
                    ->latest('id')
                    ->skip($offset)
                    ->take($limit)
                    ->get();
            case 'hot_deal':
                $q = self::newProductQuery()->hotDeal()->latest('id');
                $fallback = self::newProductQuery()->notSpotlight()->latest('id');
                $products = $q->with($baseWith)->withCount(['reviews' => $reviewCount])->skip($offset)->take($limit)->get();
                if ($products->count() < $limit && $offset === 0) {
                    $products = $fallback->with($baseWith)->withCount(['reviews' => $reviewCount])->take($limit)->get();
                } elseif ($products->count() < $limit && $offset > 0) {
                    $extra = $fallback->with($baseWith)->withCount(['reviews' => $reviewCount])->skip(0)->take($limit - $products->count())->get();
                    $products = $products->merge($extra);
                }
                return $products;
            case 'featured':
                $q = self::newProductQuery()->featured()->latest('id')->with($baseWith)->withCount(['reviews' => $reviewCount]);
                return $q->skip($offset)->take($limit)->get();
            case 'best_selling':
                $q = self::newProductQuery()->notSpotlight()->where('sale_count', '>', 0)->orderBy('sale_count', 'desc');
                $q = self::applyAutoSectionFilters($q, 'best_selling');
                $q = self::applyOverrideOrdering($q, 'best_selling');
                $q->with($baseWith)->withCount(['reviews' => $reviewCount]);
                return $q->skip($offset)->take($limit)->get();
            case 'trending':
                if (\Illuminate\Support\Facades\Schema::hasColumn((new Product)->getTable(), 'trending_now')) {
                    $idsQ = self::newProductQuery()->notSpotlight()->where('trending_now', 1)->orderBy('updated_at', 'desc');
                    $idsQ = self::applyAutoSectionFilters($idsQ, 'trending');
                    $ids = $idsQ->skip($offset)->take($limit)->pluck('id')->toArray();
                    if (!empty($ids)) {
                        return self::newProductQuery()->whereIn('id', $ids)->with($baseWith)->withCount(['reviews' => $reviewCount])->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $ids)) . ')')->get();
                    }
                }
                $q = self::newProductQuery()->notSpotlight()->where('sale_count', '>', 0)->orderBy('sale_count', 'desc');
                $q = self::applyAutoSectionFilters($q, 'trending');
                $q = self::applyOverrideOrdering($q, 'trending');
                return $q->with($baseWith)->withCount(['reviews' => $reviewCount])->skip($offset)->take($limit)->get();
            case 'new_arrivals':
                $q = self::newProductQuery()->notSpotlight()->latest('id');
                $q = self::applyAutoSectionFilters($q, 'new_arrivals');
                $q = self::applyOverrideOrdering($q, 'new_arrivals');
                return $q->with($baseWith)->withCount(['reviews' => $reviewCount])->skip($offset)->take($limit)->get();
            case 'recommended':
                $q = self::newProductQuery()->notSpotlight()->where('sale_count', '>', 0)->orderBy('sale_count', 'desc');
                $q = self::applyAutoSectionFilters($q, 'recommended');
                $q = self::applyOverrideOrdering($q, 'recommended');
                return $q->with($baseWith)->withCount(['reviews' => $reviewCount])->skip($offset)->take($limit)->get();
            default:
                return self::newProductQuery()->notSpotlight()->latest('id')->with($baseWith)->withCount(['reviews' => $reviewCount])->skip($offset)->take($limit)->get();
        }
    }

    /**
     * Products for admin-defined homepage row (category or hand-picked IDs).
     */
    public static function getCustomRowProducts(int $rowId, int $offset, int $limit, ?array $baseWith = null, $reviewCount = null): \Illuminate\Support\Collection
    {
        $baseWith = $baseWith ?? ['category:id,name', 'brand:id,name', 'activeVariants'];
        $reviewCount = $reviewCount ?? fn ($r) => $r->visibleOnProduct();
        $row = HomepageCustomProductRow::query()->where('id', $rowId)->where('is_active', true)->first();
        if (!$row) {
            return collect();
        }
        $limit = max(1, min(24, $limit));
        $offset = max(0, $offset);
        $maxTotal = max(1, min(24, (int) $row->product_limit));

        if ($row->source_type === 'category' && $row->category_id) {
            return self::newProductQuery()
                ->where('category_id', $row->category_id)
                ->latest('id')
                ->with($baseWith)
                ->withCount(['reviews' => $reviewCount])
                ->skip($offset)
                ->take(min($limit, max(0, $maxTotal - $offset)))
                ->get();
        }

        if ($row->source_type === 'manual') {
            $ids = array_values(array_unique(array_filter(array_map('intval', (array) ($row->product_ids ?? [])))));
            $ids = array_slice($ids, 0, $maxTotal);
            if ($ids === []) {
                return collect();
            }
            $pageIds = array_slice($ids, $offset, $limit);
            if ($pageIds === []) {
                return collect();
            }
            $orderSql = 'FIELD(id,' . implode(',', array_map('intval', $pageIds)) . ')';

            return self::newProductQuery()
                ->whereIn('id', $pageIds)
                ->with($baseWith)
                ->withCount(['reviews' => $reviewCount])
                ->orderByRaw($orderSql)
                ->get();
        }

        return collect();
    }

    protected static function loadCustomRowProductsForCache(HomepageCustomProductRow $row, array $baseWith, $reviewCount): \Illuminate\Support\Collection
    {
        $lim = max(1, min(24, (int) $row->product_limit));

        return self::getCustomRowProducts((int) $row->id, 0, $lim, $baseWith, $reviewCount);
    }

    protected static function newProductQuery()
    {
        return Product::available()->select(self::productSelect());
    }

    /** Wrap a section loader so one failure does not break the whole homepage (fault isolation). */
    private static function loadSection(string $module, callable $loader)
    {
        if (!config('optimization.fault_isolation', true)) {
            return $loader();
        }
        try {
            return $loader();
        } catch (\Throwable $e) {
            Log::warning('HomepageDataService: section failed', [
                'module' => $module,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected static function loadAll(): array
    {
        $baseWith = ['category:id,name', 'brand:id,name', 'activeVariants'];
        $reviewCount = fn ($r) => $r->visibleOnProduct();
        $sectionLimit = self::SECTION_LIMIT;
        $usedIds = [];

        $hotDeal = self::loadSection('hot_deal', function () use ($baseWith, $reviewCount, $sectionLimit) {
            $q = self::newProductQuery()->hotDeal()->latest('id')->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get();
            return $q->isEmpty() ? self::newProductQuery()->notSpotlight()->latest('id')->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get() : $q;
        }) ?? collect();
        $usedIds = array_values(array_unique(array_merge($usedIds, $hotDeal->pluck('id')->all())));

        $featured = self::loadSection('featured', function () use ($baseWith, $reviewCount, $sectionLimit) {
            $q = self::newProductQuery()->featured()->latest('id')->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get();
            return $q->isEmpty() ? self::newProductQuery()->notSpotlight()->latest('id')->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get() : $q;
        }) ?? collect();
        $usedIds = array_values(array_unique(array_merge($usedIds, $featured->pluck('id')->all())));

        $bestSelling = self::loadSection('best_selling', function () use ($baseWith, $reviewCount, $sectionLimit, &$usedIds) {
            $q = self::newProductQuery()->notSpotlight()->where('sale_count', '>', 0)->orderBy('sale_count', 'desc');
            $q = self::applyAutoSectionFilters($q, 'best_selling', $usedIds);
            $q = self::applyOverrideOrdering($q, 'best_selling');
            $q = $q->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get();
            if ($q->isEmpty()) {
                $fallback = self::newProductQuery()->notSpotlight()->latest('id');
                $fallback = self::applyAutoSectionFilters($fallback, 'best_selling', $usedIds);
                $fallback = self::applyOverrideOrdering($fallback, 'best_selling');
                $q = $fallback->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get();
            }
            $usedIds = array_values(array_unique(array_merge($usedIds, $q->pluck('id')->all())));
            return $q;
        }) ?? collect();

        $recommended = self::loadSection('recommended', function () use ($baseWith, $reviewCount, $sectionLimit, &$usedIds) {
            $recommended = self::newProductQuery()->notSpotlight()->where('sale_count', '>', 0)->orderBy('sale_count', 'desc');
            $recommended = self::applyAutoSectionFilters($recommended, 'recommended', $usedIds);
            $recommended = self::applyOverrideOrdering($recommended, 'recommended');
            $recommended = $recommended->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get();
            if ($recommended->count() < $sectionLimit) {
                $ids = $recommended->pluck('id')->toArray();
                $extraQ = self::newProductQuery()->notSpotlight()->whereNotIn('id', array_values(array_unique(array_merge($usedIds, $ids))))->latest('id');
                $extraQ = self::applyAutoSectionFilters($extraQ, 'recommended');
                $extra = $extraQ->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit - $recommended->count())->get();
                $recommended = $recommended->merge($extra);
            }
            if ($recommended->count() < $sectionLimit) {
                $ids = $recommended->pluck('id')->toArray();
                $extraQ = self::newProductQuery()->whereNotIn('id', array_values(array_unique(array_merge($usedIds, $ids))))->latest('id');
                $extraQ = self::applyAutoSectionFilters($extraQ, 'recommended');
                $extra = $extraQ->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit - $recommended->count())->get();
                $recommended = $recommended->merge($extra);
            }
            $usedIds = array_values(array_unique(array_merge($usedIds, $recommended->pluck('id')->all())));
            return $recommended;
        }) ?? collect();

        $reviews = self::loadSection('reviews', function () {
            return Review::visibleOnProduct()->with(['user:id,username', 'product:id,name'])->whereHas('product')->latest('id')->take(6)->get();
        }) ?? collect();

        $topRatedProducts = self::loadSection('top_rated', function () use ($baseWith, $reviewCount) {
            return self::newProductQuery()->where('avg_rate', '>=', 1)->orderBy('avg_rate', 'desc')->withCount(['reviews' => $reviewCount])->with($baseWith)->take(8)->get();
        }) ?? collect();

        $trendingBest = self::loadSection('trending', function () use ($baseWith, $reviewCount, $sectionLimit, &$usedIds) {
            $trendingBest = collect();
            if (\Illuminate\Support\Facades\Schema::hasColumn((new Product)->getTable(), 'trending_now')) {
                $trendingIdsQ = self::newProductQuery()->notSpotlight()->where('trending_now', 1)->orderBy('updated_at', 'desc');
                $trendingIdsQ = self::applyAutoSectionFilters($trendingIdsQ, 'trending', $usedIds);
                $trendingIds = $trendingIdsQ->pluck('id')->take($sectionLimit)->toArray();
                if (!empty($trendingIds)) {
                    $trendingBest = self::newProductQuery()->whereIn('id', $trendingIds)->with($baseWith)->withCount(['reviews' => $reviewCount])->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $trendingIds)) . ')')->get();
                }
            }
            if ($trendingBest->count() < $sectionLimit) {
                $excludeIds = $trendingBest->pluck('id')->toArray();
                $fillQuery = self::newProductQuery()->notSpotlight()->where('sale_count', '>', 0)->orderBy('sale_count', 'desc');
                $fillQuery = self::applyAutoSectionFilters($fillQuery, 'trending', array_values(array_unique(array_merge($usedIds, $excludeIds))));
                $trendingBest = $trendingBest->merge($fillQuery->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit - $trendingBest->count())->get());
            }
            if ($trendingBest->isEmpty()) {
                $fillQuery = self::newProductQuery()->notSpotlight()->where('sale_count', '>', 0)->orderBy('sale_count', 'desc');
                $fillQuery = self::applyAutoSectionFilters($fillQuery, 'trending', $usedIds);
                $fillQuery = self::applyOverrideOrdering($fillQuery, 'trending');
                $trendingBest = $fillQuery->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get();
            }
            if ($trendingBest->isEmpty()) {
                $fillQuery = self::newProductQuery()->notSpotlight()->latest('id');
                $fillQuery = self::applyAutoSectionFilters($fillQuery, 'trending', $usedIds);
                $fillQuery = self::applyOverrideOrdering($fillQuery, 'trending');
                $trendingBest = $fillQuery->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get();
            }
            $usedIds = array_values(array_unique(array_merge($usedIds, $trendingBest->pluck('id')->all())));
            return $trendingBest;
        }) ?? collect();

        $newArrivals = self::loadSection('new_arrivals', function () use ($baseWith, $reviewCount, $sectionLimit, &$usedIds) {
            $q = self::newProductQuery()->notSpotlight()->latest('id');
            $q = self::applyAutoSectionFilters($q, 'new_arrivals', $usedIds);
            $q = self::applyOverrideOrdering($q, 'new_arrivals');
            $q = $q->with($baseWith)->withCount(['reviews' => $reviewCount])->take($sectionLimit)->get();
            $usedIds = array_values(array_unique(array_merge($usedIds, $q->pluck('id')->all())));
            return $q;
        }) ?? collect();

        $categories = self::loadSection('categories', function () {
            // একাধিক লাইন সাপোর্ট: home_line ১, ২, ৩... অনুযায়ী আলাদা রো; সব লাইনের ক্যাটাগরি লোড
            $catTable = (new Category)->getTable();
            $query = Category::active()->select(['id', 'name', 'image']);
            if (\Illuminate\Support\Facades\Schema::hasColumn($catTable, 'home_line')) {
                $query->addSelect('home_line')->orderBy('home_line')->orderBy('home_order')->orderBy('id', 'desc');
            } else {
                $query->latest();
            }
            return $query->take(60)->get();
        }) ?? collect();

        $topCategories = self::loadSection('top_categories', function () {
            $q = Category::active()->featured()->select(['id', 'name', 'image'])->latest()->limit(6)->get();
            return $q->isEmpty() ? Category::active()->select(['id', 'name', 'image'])->latest()->take(6)->get() : $q;
        }) ?? collect();

        $topBrands = self::loadSection('top_brands', function () {
            $q = Brand::active()->featured()->select(['id', 'name', 'image'])->latest()->take(6)->get();
            return $q->isEmpty() ? Brand::active()->select(['id', 'name', 'image'])->latest()->take(6)->get() : $q;
        }) ?? collect();

        $customProductRows = self::loadSection('custom_product_rows', function () use ($baseWith, $reviewCount) {
            $rows = HomepageCustomProductRow::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
            $out = [];
            foreach ($rows as $row) {
                $out[] = [
                    'row' => $row,
                    'products' => self::loadCustomRowProductsForCache($row, $baseWith, $reviewCount),
                ];
            }

            return $out;
        }) ?? [];

        return [
            'hotDealProducts'     => $hotDeal,
            'featuredProducts'    => $featured,
            'bestSellingProducts' => $bestSelling,
            'recommendedProducts' => $recommended,
            'reviews'             => $reviews,
            'topRatedProducts'    => $topRatedProducts,
            'trendingBest'        => $trendingBest,
            'newArrivals'         => $newArrivals,
            'categories'          => $categories,
            'topCategories'       => $topCategories,
            'topBrands'           => $topBrands,
            'customProductRows'   => $customProductRows,
        ];
    }
}
