@php
    $price = productPrice($product);
    $saveAmount = $product->price - $price;
    $savePercent = $product->price > 0 ? (int) round(($saveAmount / $product->price) * 100) : 0;
    $hasDiscount = ($product->discount != 0 || $product->today_deals == 1) && $saveAmount > 0;

    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));
    $variantPreview = collect($product->activeVariants ?? [])
        ->flatMap(function ($v) {
            $attrs = $v->attributes ?? [];
            if (!is_array($attrs)) {
                return [];
            }
            $out = [];
            foreach ($attrs as $key => $val) {
                $k = strtolower((string) $key);
                if (!str_contains($k, 'color') && !str_contains($k, 'colour') && !str_contains($k, 'size')) {
                    continue;
                }
                $s = trim((string) $val);
                if ($s === '') {
                    continue;
                }
                $out[] = ['kind' => str_contains($k, 'size') ? 'size' : 'color', 'val' => $s];
            }

            return $out;
        })
        ->unique(fn ($row) => $row['kind'].'|'.strtolower($row['val']))
        ->take(10)
        ->values();
    $colorPreview = $variantPreview->where('kind', 'color')->pluck('val')->take(5);
    $sizePreview = $variantPreview->where('kind', 'size')->pluck('val')->take(3);
    $isHexSwatch = function ($s) {
        return (bool) preg_match('/^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', trim($s));
    };

    $reviewsCount = $product->reviews_count ?? (isset($product->reviews) ? $product->reviews->count() : 0);
    $avgRate = $product->avg_rate ?? 0;

    $galleryUrls = [getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product'))];
    if (is_array($product->gallery ?? null)) {
        foreach ($product->gallery as $g) {
            $galleryUrls[] = getImageWebP(getFilePath('productGallery') . '/' . $g, getFileSize('productGallery'));
        }
    }
    $primaryImg = $galleryUrls[0] ?? '';
    $hoverImg = $galleryUrls[1] ?? null;

    $qty = $product->has_variants && $product->activeVariants->isNotEmpty()
        ? (int) $product->activeVariants->sum('quantity')
        : (int) ($product->quantity ?? 0);
    $lowStockMax = (int) config('product_upload.low_stock_max', 20);
    $stockTier = $qty > $lowStockMax ? 'in' : ($qty >= 1 ? 'low' : 'out');
    $stockLabel = $stockTier === 'in' ? __('In Stock') : ($stockTier === 'low' ? __('Low Stock') : __('Out of Stock'));
    $canPurchase = $qty > 0 && $stockTier !== 'out';
    $displayName = __($product->name);

    $fetchpriority = $fetchpriority ?? 'auto';
@endphp
<div
    class="home-storefront-card group/card relative flex h-full min-h-[335px] flex-col overflow-hidden rounded-xl border border-white/50 bg-white/80 shadow-lg shadow-slate-900/10 ring-1 ring-slate-900/5 backdrop-blur-md transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-white/90 hover:shadow-xl hover:shadow-slate-900/15 hover:ring-slate-900/10"
    data-product-id="{{ $product->id }}"
    data-product_id="{{ $product->id }}"
>
    <div class="group/img relative aspect-[5/6] w-full shrink-0 overflow-hidden bg-slate-100">
        <div class="absolute left-3 top-3 z-20 flex max-w-[calc(100%-5.5rem)] flex-col gap-1.5">
            <div class="flex flex-wrap gap-1">
                @if($isNew)
                    <span class="inline-flex rounded-md bg-sky-600 px-1.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-white shadow-sm">@lang('New')</span>
                @endif
                @if((int) ($product->hot_deals ?? 0) === \App\Constants\Status::YES)
                    <span class="inline-flex rounded-md bg-gradient-to-r from-orange-600 to-red-500 px-1.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-white shadow-sm">@lang('Hot')</span>
                @endif
                @if((int) ($product->trending_now ?? 0) === \App\Constants\Status::YES)
                    <span class="inline-flex rounded-md bg-violet-600 px-1.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-white shadow-sm">@lang('Trending')</span>
                @endif
                @if($hasDiscount)
                    <span class="inline-flex rounded-md bg-emerald-700 px-1.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-white shadow-sm">@lang('Sale')</span>
                @endif
            </div>
            @if($hasDiscount && $savePercent > 0)
                <span class="inline-flex w-fit rounded-lg bg-gradient-to-r from-rose-600 to-orange-500 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white shadow-md">
                    -{{ $savePercent }}%
                </span>
            @elseif($product->today_deals == 1)
                <span class="inline-flex w-fit rounded-lg bg-gradient-to-r from-amber-500 to-yellow-400 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-900 shadow-md">
                    @lang('Deal')
                </span>
            @endif
        </div>

        <div class="absolute right-3 top-3 z-30 flex flex-col gap-2">
            <button
                type="button"
                class="add-wishlist flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-slate-700 shadow-md backdrop-blur-sm transition hover:scale-105 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                data-product_id="{{ $product->id }}"
                aria-label="{{ __('Add to wishlist') }}"
            >
                @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'wishlist_icon', 'fallback' => 'heart', 'width' => 18, 'height' => 18, 'alt' => '', 'icon3d' => true, 'variant' => 'light', 'icon3dSm' => true])
            </button>
            <button
                type="button"
                class="add-to-compare btn-compare flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-slate-700 shadow-md backdrop-blur-sm transition hover:scale-105 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                data-product_id="{{ $product->id }}"
                aria-label="{{ __('Compare') }}"
                title="{{ __('Compare') }}"
            >
                @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'compare_icon', 'fallback' => 'exchange-alt', 'width' => 18, 'height' => 18, 'alt' => '', 'icon3d' => true, 'variant' => 'light', 'icon3dSm' => true])
            </button>
        </div>

        <button
            type="button"
            class="quickView absolute bottom-3 left-3 z-[25] inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-slate-700 shadow-md backdrop-blur-sm transition duration-200 hover:scale-105 hover:bg-white active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
            data-product_id="{{ $product->id }}"
            aria-label="{{ __('Quick view') }}"
            title="{{ __('Quick view') }}"
        >
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'quick_view_icon', 'fallback' => 'eye', 'width' => 18, 'height' => 18, 'alt' => '', 'icon3d' => true, 'variant' => 'light', 'icon3dSm' => true])
        </button>

        <a href="{{ product_detail_url($product) }}" class="absolute inset-0 z-10 block focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-emerald-500/60" aria-label="{{ $displayName }}">
            <div class="relative h-full w-full">
                <img
                    src="{{ $primaryImg }}"
                    alt=""
                    role="presentation"
                    loading="lazy"
                    decoding="async"
                    fetchpriority="{{ $fetchpriority }}"
                    width="400"
                    height="500"
                    class="h-full w-full object-cover object-center {{ $hoverImg ? 'transition-opacity duration-500 ease-out motion-reduce:transition-none group-hover/card:opacity-0 motion-reduce:group-hover/card:opacity-100' : 'transition-transform duration-500 ease-out motion-reduce:transition-none group-hover/card:scale-105 motion-reduce:group-hover/card:scale-100' }}"
                    onerror="this.onerror=null;this.removeAttribute('src');this.removeAttribute('srcset');this.style.display='none';"
                >
                @if($hoverImg)
                    <img
                        src="{{ $hoverImg }}"
                        alt=""
                        role="presentation"
                        loading="lazy"
                        decoding="async"
                        width="400"
                        height="500"
                        class="pointer-events-none absolute inset-0 h-full w-full object-cover object-center opacity-0 transition-opacity duration-500 ease-out motion-reduce:opacity-0 motion-reduce:transition-none group-hover/card:opacity-100"
                        onerror="this.onerror=null;this.style.display='none';"
                    >
                @endif
            </div>
        </a>
    </div>

    <div class="flex min-h-0 flex-1 flex-col p-2.5 sm:p-3">
        <h3 class="line-clamp-1 min-h-[1.35rem] text-[13px] font-semibold leading-snug text-slate-900">
            <a href="{{ product_detail_url($product) }}" class="text-inherit hover:text-emerald-700 focus:outline-none focus-visible:text-emerald-700">{{ $displayName }}</a>
        </h3>

        <div class="mt-1.5 flex items-center gap-1.5">
            <span class="flex shrink-0 items-center text-amber-400">{!! showProductRatings($avgRate) !!}</span>
            <span class="text-[11px] font-medium text-slate-500">({{ $reviewsCount }})</span>
        </div>

        @if($colorPreview->isNotEmpty() || $sizePreview->isNotEmpty())
            <div class="mt-2 flex flex-wrap items-center gap-2">
                @if($colorPreview->isNotEmpty())
                    <div class="flex items-center gap-1" title="{{ __('Colors') }}">
                        @foreach($colorPreview as $cval)
                            @php $hex = $isHexSwatch($cval) ? (str_starts_with($cval, '#') ? $cval : '#'.$cval) : null; @endphp
                            <span
                                class="inline-block h-4 w-4 shrink-0 rounded-full border border-slate-200/80 shadow-sm ring-1 ring-black/5"
                                style="{{ $hex ? 'background:'.$hex.';' : 'background:linear-gradient(135deg,#e2e8f0,#94a3b8);' }}"
                                title="{{ $cval }}"
                            ></span>
                        @endforeach
                    </div>
                @endif
                @if($sizePreview->isNotEmpty())
                    <div class="flex flex-wrap gap-1">
                        @foreach($sizePreview as $sz)
                            <span class="rounded border border-slate-200/90 bg-slate-50 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">{{ $sz }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-2 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
            <span class="staylbd-rt-price text-lg font-extrabold tabular-nums text-emerald-600">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
            @if($hasDiscount)
                <span class="staylbd-rt-price-compare text-sm font-medium text-slate-400 line-through">{{ $general->cur_sym }}{{ showAmount($product->price) }}</span>
            @else
                <span class="staylbd-rt-price-compare hidden text-sm font-medium text-slate-400 line-through" aria-hidden="true"></span>
            @endif
        </div>

        <p class="staylbd-rt-stock mt-1 text-[11px] font-semibold {{ $stockTier === 'in' ? 'text-emerald-600' : ($stockTier === 'low' ? 'text-amber-600' : 'text-rose-600') }}" data-stock-tier="{{ $stockTier }}">{{ $stockLabel }}</p>

        <div class="mt-auto flex flex-col gap-2 pt-3">
            <div class="grid grid-cols-2 gap-2">
                <button
                    type="button"
                    class="add-to-cart staylbd-rt-atc inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-slate-900 px-2 text-xs font-bold text-white shadow-md transition duration-200 hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-50"
                    data-product_id="{{ $product->id }}"
                    data-qty="1"
                    @if(!$canPurchase) disabled aria-disabled="true" @endif
                    aria-label="{{ __('Add to Cart') }}"
                >
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'cart_icon', 'fallback' => 'shopping-cart', 'class' => 'text-white shrink-0', 'width' => 20, 'height' => 20, 'alt' => ''])
                    <span class="product-card-atc-label truncate">{{ $canPurchase ? __('Add to Cart') : __('Out of Stock') }}</span>
                </button>
                <a
                    href="{{ route('cart.list.buy.now', $product->id) }}"
                    class="staylbd-rt-buynow inline-flex h-10 items-center justify-center gap-1 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 px-2 text-center text-xs font-bold text-white shadow-md transition duration-200 hover:from-emerald-500 hover:to-teal-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 active:scale-[0.97] {{ $canPurchase ? '' : 'pointer-events-none opacity-50' }}"
                    data-no-ajax
                    @if(!$canPurchase) aria-disabled="true" @endif
                >
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'buy_now_icon', 'fallback' => 'bolt', 'class' => 'text-white shrink-0', 'width' => 20, 'height' => 20, 'alt' => ''])
                    <span class="truncate">{{ __('Buy Now') }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
