@php
    use App\Constants\Status;
    $price = productPrice($product);
    $saveAmount = $product->price - $price;
    $savePercent = $product->price > 0 ? round(($saveAmount / $product->price) * 100) : 0;
    $hasDiscount = ($product->discount != 0 || $product->today_deals == 1) && $saveAmount > 0;
    $offerLabel = null;
    if ($product->today_deals == 1) {
        $offerLabel = $general->discount_type == 1 ? $general->cur_sym . showAmount($general->discount) . ' ' . __('off') : showAmount($general->discount) . '% ' . __('off');
    } elseif ($product->discount != 0) {
        $offerLabel = $product->discount_type == 1 ? $general->cur_sym . showAmount($product->discount) . ' ' . __('off') : showAmount($product->discount) . '% ' . __('off');
    }
    $reviewsCount = $product->reviews_count ?? (isset($product->reviews) ? $product->reviews->count() : 0);
    $avgRate = $product->avg_rate ?? 0;
    $secondImg = null;
    $galleryUrls = [ getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product')) ];
    if (is_array($product->gallery ?? null)) {
        foreach ($product->gallery as $g) {
            $galleryUrls[] = getImageWebP(getFilePath('productGallery') . '/' . $g, getFileSize('productGallery'));
        }
    }
    if (count($galleryUrls) > 1) {
        $secondImg = $galleryUrls[1];
    }
    $bestSellerThreshold = config('product_upload.best_seller_threshold', 10);
    $newProductDays = config('product_upload.new_product_days', 7);
    $inStockMin = config('product_upload.in_stock_min', 20);
    $lowStockMin = config('product_upload.low_stock_min', 5);
    $lowStockMax = config('product_upload.low_stock_max', 20);
    $isBestSeller = ($product->sale_count ?? 0) >= $bestSellerThreshold;
    $isNew = $product->created_at && $product->created_at->diffInDays(now()) <= $newProductDays;
    $qty = (int) ($product->quantity ?? 0);
    $stockStatus = 'out';
    $stockLabel = __('Out Of Stock');
    if ($qty > $lowStockMax) {
        $stockStatus = 'in';
        $stockLabel = __('In Stock');
    } elseif ($qty >= 1) {
        $stockStatus = 'low';
        $stockLabel = __('Low Stock');
    }
    $isCustomizable = stripos($product->name ?? '', 'custom') !== false;
    $displayName = __($product->name);
    $fetchpriority = $fetchpriority ?? 'low';

    // Promo text (short Bangla)
    $promoText = null;
    if ($product->today_deals == 1) {
        $promoText = 'ফ্ল্যাশ সেল';
    } elseif ($hasDiscount && $savePercent >= 25) {
        $promoText = 'পলো অফার';
    }

    // Admin-controlled icon for Add To Cart CTA (header_icons.content)
    static $buyNowIconConfig = null;
    if ($buyNowIconConfig === null) {
        $headerIconsContent = getContent('header_icons.content', true);
        $iconValues = (array) ($headerIconsContent->data_values ?? []);
        $buyNowIconConfig = [
            'name' => trim((string) ($iconValues['buy_now_icon'] ?? 'cart-grid')) ?: 'cart-grid',
            'image' => trim((string) ($iconValues['buy_now_icon_image'] ?? '')),
        ];
    }

@endphp

{{-- Click behavior: image/upper area → Product Details; CTA button → Add to Cart --}}
<div class="product-card rounded-xl overflow-hidden" data-gallery="{{ json_encode($galleryUrls) }}" data-product_id="{{ $product->id }}" data-has_variants="{{ $product->has_variants ? '1' : '0' }}" data-product_url="{{ product_detail_url($product) }}">
    <div class="product-card__img-wrap product-card__img-wrap--zoom relative product-card__img-wrap--skeleton">
        <a href="{{ product_detail_url($product) }}" class="product-card__img-link" aria-label="{{ __('View product details') }}">
            <img src="{{ getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product')) }}" alt="{{ __($product->name) }}"
                class="product-card__img product-card__img--main product-card__img--cycle product-image aspect-square object-contain"
                width="800" height="800"
                loading="lazy"
                decoding="async"
                fetchpriority="low"
                sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 20vw"
                data-cycle-index="0">
            @if($secondImg && count($galleryUrls) <= 2)
                <img src="{{ $secondImg }}" alt="" class="product-card__img product-card__img--hover aspect-square object-contain" width="800" height="800" loading="lazy" decoding="async" sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 20vw" aria-hidden="true">
            @endif
        </a>

        {{-- ছোট ব্যাজ এক লাইনে: ডিসকাউন্ট/অফার + নিউ/বেস্ট + কাস্টম – স্টক নিচে দেখানো হয় --}}
        <div class="product-card__badges-top">
            @if($qty == 0)
            <span class="product-card__badge product-card__stock-badge--out">@lang('Out Of Stock')</span>
            @endif
            @if($promoText)
                <span class="product-card__badge product-card__promo-badge">{{ $promoText }}</span>
            @endif
            @if($hasDiscount && $savePercent > 0)
                <span class="product-card__badge product-card__badge--discount-banner">
                    -{{ $savePercent }}% {{ $general->cur_sym }}{{ showAmount($price) }}
                </span>
            @elseif($product->discount != 0 || $product->today_deals == 1)
                @php
                    $d = $product->today_deals == 1 ? $general->discount : $product->discount;
                    $dType = $product->today_deals == 1 ? $general->discount_type : $product->discount_type;
                    $dLabel = $dType == 1 ? $general->cur_sym . showAmount($d) : showAmount($d) . '%';
                @endphp
                <span class="product-card__badge product-card__badge--discount-banner">{{ $dLabel }} @lang('off')</span>
            @endif
            @if($isBestSeller)<span class="product-card__badge product-card__badge--best">@lang('Best Seller')</span>@endif
            @if($isNew)<span class="product-card__badge product-card__badge--new">@lang('New')</span>@endif
            @if($isCustomizable)<span class="product-card__badge product-card__badge--custom">@lang('Customizable')</span>@endif
        </div>
        {{-- Hover actions: Cart, Compare, Wishlist, View – always anchored inside image area --}}
        <div class="product-card__actions flex flex-col gap-2">
            <button type="button" class="product-card__action product-card__action--cart add-to-cart btn-cart" data-product_id="{{ $product->id }}" data-qty="{{ $qty }}" aria-label="@lang('Add to cart')">@include($activeTemplate . 'partials.icon', ['name' => 'cart-grid'])</button>
            <button type="button" class="product-card__action product-card__action--compare add-to-compare btn-compare" data-product_id="{{ $product->id }}" aria-label="@lang('Compare')">@include($activeTemplate . 'partials.icon', ['name' => 'exchange-alt'])</button>
            <button type="button" class="product-card__action add-wishlist btn-wishlist" data-product_id="{{ $product->id }}" aria-label="@lang('Add to wishlist')">@include($activeTemplate . 'partials.icon', ['name' => 'heart'])</button>
            <button type="button" class="product-card__action quickView" data-product_id="{{ $product->id }}" aria-label="@lang('Quick view')">@include($activeTemplate . 'partials.icon', ['name' => 'eye'])</button>
        </div>
    </div>
    {{-- Bottom: compact info (name, price, stock, rating) + Add to Cart CTA --}}
    <div class="product-card__info flex flex-col">
        <div class="product-card__info-inner">
            <h3 class="product-card__title">
                <a href="{{ product_detail_url($product) }}">{{ $displayName }}</a>
            </h3>
            {{-- Line 2: Price + Discount + Old Price + Sold Count --}}
            <div class="product-card__row product-card__row--price product-card__row--oneline flex items-center gap-1">
                <span class="product-card__price">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
                @if($product->discount != 0 || $product->today_deals == 1)
                    @if($savePercent > 0)
                        <span class="product-card__sep">|</span>
                        <span class="product-card__discount-once">-{{ $savePercent }}%</span>
                    @endif
                    <span class="product-card__price-old">{{ $general->cur_sym }}{{ showAmount($product->price) }}</span>
                @endif
                @if(($saleCount = (int)($product->sale_count ?? 0)) > 0)
                    <span class="product-card__sep">|</span>
                    <span class="product-card__sold-count">{{ shortNumber($saleCount) }} @lang('Sold')</span>
                @endif
            </div>
            {{-- Line 3: Rating + Review Count + Stock --}}
            <div class="product-card__row product-card__row--reviews product-card__row--footer flex items-center gap-1">
                <span class="product-card__stars">{!! showProductRatings($avgRate) !!}</span>
                <span class="product-card__reviews-count">({{ $reviewsCount }})</span>
                <span class="product-card__sep">|</span>
                <span class="product-card__stock product-card__stock--{{ $stockStatus }}">
                    @if($qty == 0)@lang('Out Of Stock')@else{{ $stockLabel }}@endif
                </span>
            </div>
        </div>
        @if($qty > 0)
        <button type="button"
            class="product-card__cta product-card__cta--cart add-to-cart inline-flex items-center justify-center gap-2 font-sans"
            title="@lang('Add to cart')"
            data-product_id="{{ $product->id }}"
            data-qty="{{ $qty }}">
            <span class="buy-now-cta__icon" aria-hidden="true">
                @if(!empty($buyNowIconConfig['image']))
                    <img src="{{ asset('assets/images/frontend/header_icons/' . $buyNowIconConfig['image']) }}" alt="" loading="lazy" decoding="async" width="20" height="20" class="buy-now-cta__icon-img">
                @else
                    @include($activeTemplate . 'partials.icon', ['name' => $buyNowIconConfig['name'], 'class' => 'buy-now-cta__icon-svg'])
                @endif
            </span>
            <span class="product-card__cta-label">@lang('Add to Cart')</span>
        </button>
        @else
        <span class="product-card__cta product-card__cta--disabled inline-flex items-center justify-center" title="@lang('Out of stock')">@lang('Out Of Stock')</span>
        @endif
    </div>
</div>