@php
    $price = productPrice($product);
    $saveAmount = $product->price - $price;
    $savePercent = $product->price > 0 ? (int) round(($saveAmount / $product->price) * 100) : 0;
    $hasDiscount = ($product->discount != 0 || $product->today_deals == 1) && $saveAmount > 0;
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));

    /* Optimized Attribute Preview */
    $colorPreview = [];
    $sizePreview = [];
    $seenValues = [];
    foreach ($product->activeVariants ?? [] as $v) {
        $attrs = (array) ($v->attributes ?? []);
        foreach ($attrs as $key => $val) {
            $k = strtolower((string) $key); $s = trim((string) $val);
            if ($s === '') continue;
            $uniqueKey = $k.'|'.strtolower($s);
            if (isset($seenValues[$uniqueKey])) continue;
            $seenValues[$uniqueKey] = true;
            if (str_contains($k, 'color') || str_contains($k, 'colour')) {
                if (count($colorPreview) < 5) $colorPreview[] = $s;
            } elseif (str_contains($k, 'size')) {
                if (count($sizePreview) < 3) $sizePreview[] = $s;
            }
        }
    }

    $reviewsCount = $product->reviews_count ?? (isset($product->reviews) ? $product->reviews->count() : 0);
    $avgRate = $product->avg_rate ?? 0;

    /* Essential Image Restoration Path */
    $primaryImg = getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product'));
    $hoverImg = null;
    if (is_array($product->gallery ?? null) && count($product->gallery) > 0) {
        $hoverImg = getImageWebP(getFilePath('productGallery') . '/' . $product->gallery[0], getFileSize('productGallery'));
    }

    $qty = $product->has_variants && $product->activeVariants->isNotEmpty() ? (int)$product->activeVariants->sum('quantity') : (int)($product->quantity ?? 0);
    $canPurchase = $qty > 0;
    $displayName = __($product->name);
@endphp

<div class="home-storefront-card group/card relative flex h-full min-h-[380px] flex-col overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:ring-1 hover:ring-slate-100"
     style="background: var(--product-card-bg, #ffffff);"
     data-product-id="{{ $product->id }}">
    {{-- Image Container --}}
    <div class="relative aspect-[4/5] w-full overflow-hidden bg-slate-50">
        {{-- Badges --}}
        <div class="absolute left-2 top-2 z-20 flex flex-col gap-1">
            @if($hasDiscount && $savePercent > 0)
                <span class="rounded px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm" style="background: var(--product-discount-badge, #dc2626);">-{{ $savePercent }}%</span>
            @endif
            @if($isNew)
                <span class="rounded bg-slate-900 px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm">@lang('NEW')</span>
            @endif
        </div>

        {{-- Hot/Trending overlays could go here if needed --}}

        {{-- Action Buttons (Hover) --}}
        <div class="absolute right-2 top-2 z-30 translate-x-12 flex flex-col gap-2 opacity-0 transition-all duration-300 group-hover/card:translate-x-0 group-hover/card:opacity-100">
            <button type="button" class="add-wishlist flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm transition-colors"
                    style="--pc-action-hover: var(--product-button-color, #1f2937);"
                    onmouseover="this.style.background='var(--pc-action-hover)'; this.style.color='#fff';"
                    onmouseout="this.style.background='#fff'; this.style.color='rgb(51 65 85)';"
                    data-product_id="{{ $product->id }}">
                @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'wishlist_icon', 'fallback' => 'heart', 'width' => 14, 'height' => 14])
            </button>
            <button type="button" class="quickView flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm transition-colors"
                    style="--pc-action-hover: var(--product-button-color, #1f2937);"
                    onmouseover="this.style.background='var(--pc-action-hover)'; this.style.color='#fff';"
                    onmouseout="this.style.background='#fff'; this.style.color='rgb(51 65 85)';"
                    data-product_id="{{ $product->id }}">
                @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'quick_view_icon', 'fallback' => 'eye', 'width' => 14, 'height' => 14])
            </button>
        </div>

        <a href="{{ product_detail_url($product) }}" class="block h-full w-full">
            <img src="{{ $primaryImg }}" alt="{{ $displayName }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-700 group-hover/card:scale-110">
            @if($hoverImg)
                <img src="{{ $hoverImg }}" alt="" role="presentation" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-700 group-hover/card:opacity-100">
            @endif
        </a>
    </div>

    {{-- Content --}}
    <div class="flex flex-1 flex-col p-3 text-center">
        <h3 class="line-clamp-2 min-h-[2.5rem] text-[14px] font-medium leading-tight text-slate-800">
            <a href="{{ product_detail_url($product) }}" class="transition-colors"
               onmouseover="this.style.color='var(--product-button-color, #1f2937)'"
               onmouseout="this.style.color=''">{{ $displayName }}</a>
        </h3>

        <div class="mt-2 flex items-center justify-center gap-1.5">
            <span class="flex items-center text-[10px]" style="color: var(--product-rating-color, #f59e0b);">{!! showProductRatings($avgRate) !!}</span>
            <span class="text-[10px] text-slate-400">({{ $reviewsCount }})</span>
        </div>

        <div class="mt-2 flex items-center justify-center gap-2">
            <span class="staylbd-rt-price text-[16px] font-bold leading-none" style="color: var(--product-price-color, #0e9f90);">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
            @if($hasDiscount)
                <span class="staylbd-rt-price-compare text-[12px] text-slate-400 line-through leading-none">{{ $general->cur_sym }}{{ showAmount($product->price) }}</span>
            @endif
        </div>

        {{-- Footer Actions --}}
        <div class="mt-4 grid grid-cols-2 gap-2 opacity-0 transition-opacity duration-300 group-hover/card:opacity-100">
            <button type="button" class="add-to-cart inline-flex h-9 items-center justify-center px-2 text-[11px] font-bold text-white transition-colors"
                    style="background: var(--product-button-color, #1f2937);"
                    onmouseover="this.style.filter='brightness(0.9)';"
                    onmouseout="this.style.filter='';"
                    data-product_id="{{ $product->id }}" data-qty="1" {{ $canPurchase ? '' : 'disabled' }}>
                <span class="truncate">{{ $canPurchase ? __('CART') : __('OUT') }}</span>
            </button>
            <a href="{{ route('cart.list.buy.now', $product->id) }}"
               class="inline-flex h-9 items-center justify-center border px-2 text-[11px] font-bold text-white transition-colors {{ $canPurchase ? '' : 'pointer-events-none opacity-50' }}"
               style="background: var(--product-buy-now-color, #0e9f90); border-color: var(--product-buy-now-color, #0e9f90);"
               onmouseover="this.style.background='var(--product-buy-now-hover, #0c8a7d)'; this.style.borderColor='var(--product-buy-now-hover, #0c8a7d)';"
               onmouseout="this.style.background='var(--product-buy-now-color, #0e9f90)'; this.style.borderColor='var(--product-buy-now-color, #0e9f90)';">
                <span class="truncate">{{ __('BUY') }}</span>
            </a>
        </div>
    </div>
</div>
