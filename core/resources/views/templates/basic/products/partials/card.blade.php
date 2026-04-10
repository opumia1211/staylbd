@php
    $price = productPrice($product);
    $saveAmount = $product->price - $price;
    $savePercent = $product->price > 0 ? round(($saveAmount / $product->price) * 100) : 0;
    $hasDiscount = ($product->discount != 0 || $product->today_deals == 1) && $saveAmount > 0;
    
    $reviewsCount = $product->reviews_count ?? (isset($product->reviews) ? $product->reviews->count() : 0);
    $avgRate = $product->avg_rate ?? 0;
    
    $galleryUrls = [ getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product')) ];
    if (is_array($product->gallery ?? null)) {
        foreach ($product->gallery as $g) {
            $galleryUrls[] = getImageWebP(getFilePath('productGallery') . '/' . $g, getFileSize('productGallery'));
        }
    }
    
    $qty = $product->has_variants && $product->activeVariants->isNotEmpty()
        ? (int) $product->activeVariants->sum('quantity')
        : (int) ($product->quantity ?? 0);
    $lowStockMax = (int) config('product_upload.low_stock_max', 20);
    $stockTier = $qty > $lowStockMax ? 'in' : ($qty >= 1 ? 'low' : 'out');
    $stockLabel = $stockTier === 'in' ? __('In Stock') : ($stockTier === 'low' ? __('Low Stock') : __('Out of Stock'));
    $displayName = __($product->name);
    
    $promoText = null;
    if ($product->today_deals == 1) {
        $promoText = 'Flash Sale';
    } elseif ($hasDiscount) {
        $promoText = 'Save ' . $general->cur_sym . showAmount($saveAmount);
    }

    $fetchpriority = $fetchpriority ?? 'auto';
    $galleryAttr = '';
    if (count($galleryUrls) > 1) {
        $galleryAttr = htmlspecialchars(
            json_encode($galleryUrls, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ENT_QUOTES,
            'UTF-8',
            true
        );
    }
    $lqipUrl = getImageLQIP(getFilePath('product') . '/' . $product->image);
    $srcset = getImageSrcset($product->image, 'product');
@endphp

{{-- Full-bleed image; bottom info gradient; actions fixed 6px from card bottom --}}
<div
    class="product-card product-card-glass group relative h-full min-h-[320px] overflow-hidden rounded-lg border-0 bg-slate-100 p-0 shadow-none ring-0 outline-none ring-offset-0 transition-[box-shadow] duration-300 ease-out [--pc-glass-action-stack:2.85rem]"
    data-product-id="{{ $product->id }}"
    data-product_id="{{ $product->id }}"
    @if($galleryAttr !== '') data-gallery="{{ $galleryAttr }}" @endif
>
    <div class="product-card-glass__media product-card-glass__media--fullbleed pointer-events-none absolute inset-0 z-0 overflow-hidden">

        @if($promoText)
            <div class="pointer-events-none absolute left-3 top-3 z-30 rounded-full bg-gradient-to-r from-orange-500 to-rose-500 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-white shadow-md shadow-orange-500/20">
                {{ $promoText }}
            </div>
        @endif

        <div class="product-card-glass__photo-zone absolute inset-0 z-0 box-border bg-slate-100" style="background-image: url('{{ $lqipUrl }}'); background-size: cover; background-position: center;">
            <a href="{{ product_detail_url($product) }}" class="product-card-glass__media-link pointer-events-auto flex h-full w-full min-h-0 min-w-0 items-stretch justify-stretch bg-slate-100/10 p-0 outline-none" aria-label="{{ $displayName }}">
                <img
                    src="{{ $galleryUrls[0] }}"
                    data-src="{{ $galleryUrls[0] }}"
                    @if($srcset) srcset="{{ $srcset }}" sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 250px" @endif
                    alt=""
                    role="presentation"


                    loading="lazy"
                    decoding="async"
                    fetchpriority="{{ $fetchpriority }}"
                    class="product-card-glass__photo product-card-glass__cycle-img main-img-card h-full w-full bg-slate-100 object-cover object-center transition-transform duration-[600ms] ease-in-out motion-reduce:transition-none"
                    onerror="this.onerror=null;this.removeAttribute('src');this.removeAttribute('srcset');this.style.display='none';"
                >
            </a>
        </div>
    </div>

    <div class="product-card-glass__info pointer-events-none absolute bottom-0 left-0 right-0 z-10 flex min-w-0 flex-col bg-gradient-to-t from-white from-50% via-white/92 to-transparent px-2.5 pb-[var(--pc-glass-action-stack)] pt-6 outline-none">

        <div class="info-view-default product-card-glass__details pointer-events-auto flex min-h-0 flex-col gap-1 transition-all duration-300">

            <h3 class="product-card-glass__title mb-0.5 line-clamp-1 overflow-hidden text-[13px] font-semibold leading-snug tracking-tight text-slate-900 drop-shadow-sm">
                {{ $displayName }}
            </h3>

            <div class="mb-0 flex items-center gap-1">
                <div class="flex items-center text-[11px] text-amber-400 drop-shadow-sm">
                    {!! showProductRatings($avgRate) !!}
                </div>
                <span class="text-[10px] font-semibold text-slate-500">({{ $reviewsCount }})</span>
            </div>

            {{-- Price/stock hides on hover (desktop); actions are a separate layer --}}
            <div class="product-card-glass__meta-row relative mt-1">
                <div class="product-card-glass__price-stock flex flex-col gap-0.5 transition-opacity duration-300 ease-out group-hover:pointer-events-none group-hover:opacity-0 group-hover:invisible">
                    <div class="flex flex-wrap items-baseline gap-2">
                        <span class="staylbd-rt-price text-base font-extrabold tabular-nums text-sky-600">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
                        <span class="staylbd-rt-price-compare text-[13px] text-slate-400 line-through {{ $hasDiscount ? '' : 'hidden' }}">{{ $general->cur_sym }}{{ showAmount($product->price) }}</span>
                    </div>
                    <p class="staylbd-rt-stock text-[11px] font-semibold {{ $stockTier === 'in' ? 'text-emerald-600' : ($stockTier === 'low' ? 'text-amber-600' : 'text-rose-600') }}" data-stock-tier="{{ $stockTier }}">{{ $stockLabel }}</p>
                </div>
            </div>
        </div>
    </div>

    <div
        class="hover-view-actions product-card-glass__actions pointer-events-none absolute bottom-[6px] left-2 right-2 z-20 flex flex-nowrap items-center justify-evenly gap-3 overflow-x-auto px-0.5 opacity-0 transition-opacity duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] group-hover:pointer-events-auto group-hover:opacity-100 sm:gap-4 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden"
    >
        <button
            type="button"
            class="product-card-glass__act-btn add-to-cart staylbd-rt-atc pointer-events-auto flex h-10 min-w-0 max-w-[min(100%,11rem)] shrink items-center justify-center gap-2 rounded-full px-4 text-xs font-semibold leading-none tracking-wide text-white transition-transform duration-200 active:scale-[0.98] {{ $qty > 0 ? 'cursor-pointer' : 'cursor-not-allowed' }}"
            data-product_id="{{ $product->id }}"
            data-qty="1"
            @if($qty <= 0) disabled aria-disabled="true" @endif
            aria-label="{{ $qty > 0 ? __('Cart') : __('Out of Stock') }}"
        >
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'cart_icon', 'fallback' => 'shopping-cart', 'svgClass' => 'h-[18px] w-[18px] shrink-0 drop-shadow-sm', 'class' => 'h-[18px] w-[18px] shrink-0 object-contain drop-shadow-sm', 'width' => 18, 'height' => 18, 'alt' => ''])
            <span class="product-card-atc-label whitespace-nowrap">{{ $qty > 0 ? __('Cart') : __('Out of Stock') }}</span>
        </button>

        <button
            type="button"
            class="product-card-glass__act-btn add-to-compare btn-compare pointer-events-auto flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-full transition-transform duration-200 active:scale-[0.96]"
            data-product_id="{{ $product->id }}"
            aria-label="{{ __('Compare') }}"
            title="{{ __('Compare') }}"
        >
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'compare_icon', 'fallback' => 'exchange-alt', 'svgClass' => 'h-5 w-5', 'class' => 'h-5 w-5 object-contain', 'width' => 22, 'height' => 22, 'alt' => ''])
        </button>

        <button
            type="button"
            class="product-card-glass__act-btn add-wishlist pointer-events-auto flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-full transition-transform duration-200 active:scale-[0.96]"
            data-product_id="{{ $product->id }}"
            aria-label="{{ __('Add to wishlist') }}"
        >
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'wishlist_icon', 'fallback' => 'heart', 'svgClass' => 'h-5 w-5', 'class' => 'h-5 w-5 object-contain', 'width' => 22, 'height' => 22, 'alt' => ''])
        </button>

        <button
            type="button"
            class="product-card-glass__act-btn quickView pointer-events-auto flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-full transition-transform duration-200 active:scale-[0.96]"
            data-product_id="{{ $product->id }}"
            aria-label="{{ __('Quick view') }}"
            title="{{ __('Quick view') }}"
        >
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'quick_view_icon', 'fallback' => 'eye', 'svgClass' => 'h-5 w-5', 'class' => 'h-5 w-5 object-contain', 'width' => 22, 'height' => 22, 'alt' => ''])
        </button>
    </div>
</div>
