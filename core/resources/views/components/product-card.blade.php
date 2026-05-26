@props(['product', 'general', 'activeTemplate'])

@php
    $pricing = productDisplayPricing($product);
    $price = $pricing['effective'];
    $compareAt = $pricing['compare_at'];
    $savePercent = $pricing['save_percent'];
    $showStrike = $pricing['show_strike'];
    $hasSavings = $pricing['has_savings'];

    $listPrice = ($compareAt !== null && $compareAt > $price + 0.000001) ? $compareAt : null;
    if ($listPrice === null) {
        $listCandidates = array_filter([
            (float) ($product->original_price ?? 0),
            (float) ($product->price ?? 0),
        ], static fn ($p) => $p > $price + 0.000001);
        $listPrice = empty($listCandidates) ? null : max($listCandidates);
    }
    if ($listPrice === null && $savePercent >= 1 && $price > 0) {
        $listPrice = round($price / (1 - ($savePercent / 100)), 2);
    }

    $strikePrice = $listPrice;
    if ($strikePrice === null && $showStrike && $compareAt !== null) {
        $strikePrice = $compareAt;
    }
    if ($strikePrice === null && $hasSavings && $savePercent >= 1 && $price > 0) {
        $strikePrice = round($price / (1 - ($savePercent / 100)), 2);
    }

    $primaryImg = getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product'));
    $qty = $product->has_variants && $product->activeVariants->isNotEmpty()
        ? (int) $product->activeVariants->sum('quantity')
        : (int) ($product->quantity ?? 0);
    $canPurchase = $qty > 0;
    $displayName = __($product->name);
    $buyNowUrl = storefront_route('cart.list.buy.now', ['id' => $product->id]);

    $avgRate = (float) ($product->avg_rate ?? 0);
    $reviewCount = (int) ($product->reviews_count ?? 0);

    $showStock = isset($general->display_stock) && (int) $general->display_stock === \App\Constants\Status::ENABLE;
    $lowStockMax = (int) config('product_upload.low_stock_max', 20);
    $stockTier = $qty > $lowStockMax ? 'in' : ($qty >= 1 ? 'low' : 'out');
    $stockLabel = $stockTier === 'in' ? __('In Stock') : ($stockTier === 'low' ? __('Low Stock') : __('Out of Stock'));
    $hasFreeDelivery = isset($product->delivery_type) && $product->delivery_type === 'free';
@endphp

<article class="stayl-product-card" data-product-id="{{ $product->id }}">
    @if(in_array($product->product_type, ['digital', 'service']))
        <div class="stayl-card-type-badge absolute top-2 right-2 z-10 pointer-events-none">
            <span class="inline-block bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide">{{ ucfirst($product->product_type) }}</span>
        </div>
    @endif

    <div class="stayl-card-media">
        @if($hasFreeDelivery)
            <span class="stayl-card-free-delivery">{{ __('Free Delivery') }}</span>
        @endif
        <a href="{{ product_detail_url($product) }}" title="{{ $displayName }}">
            <img src="{{ $primaryImg }}" alt="{{ $displayName }}" class="stayl-card-img" loading="lazy" decoding="async" width="320" height="320">
        </a>

        <div class="stayl-card-side-actions" aria-label="{{ __('Product actions') }}">
            <button type="button"
                    class="add-wishlist btn-wishlist stayl-card-side-btn stayl-card-side-btn--wish"
                    data-product_id="{{ $product->id }}"
                    title="{{ __('Wishlist') }}"
                    aria-label="{{ __('Add to wishlist') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            </button>

            <button type="button"
                    class="add-to-compare btn-compare stayl-card-side-btn stayl-card-side-btn--compare"
                    data-product_id="{{ $product->id }}"
                    title="{{ __('Compare') }}"
                    aria-label="{{ __('Add to compare') }}">
                @include($activeTemplate . 'partials.icons.compare', ['size' => 16, 'class' => 'shrink-0'])
            </button>

            <button type="button"
                    class="stayl-card-side-btn stayl-card-side-btn--view quickView"
                    data-product_id="{{ $product->id }}"
                    title="{{ __('Quick view') }}"
                    aria-label="{{ __('Quick view') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
    </div>

    <div class="stayl-card-bezel">
        {{-- Line 1: product name (max 2 lines) --}}
        <h3 class="stayl-card-title">
            <a href="{{ product_detail_url($product) }}" title="{{ $displayName }}">{{ $displayName }}</a>
        </h3>

        {{-- Line 2: price + old price + save % + stock --}}
        <div class="stayl-card-price-line">
            <span class="stayl-card-price-group">
                <span class="stayl-card-price staylbd-rt-price notranslate" data-base-price="{{ $price }}">{{ currency_symbol() }}{{ showAmount($price) }}</span>
                @if($showStrike && $strikePrice !== null && $strikePrice > $price + 0.000001)
                    <del class="stayl-card-price-old staylbd-rt-price-compare notranslate" data-base-price="{{ $strikePrice }}" data-has-savings="1">{{ currency_symbol() }}{{ showAmount($strikePrice) }}</del>
                @endif
                @if($showStrike && $savePercent >= 1)
                    <span class="stayl-card-save-percent staylbd-rt-save-percent notranslate">-{{ $savePercent }}%</span>
                @endif
                @if($showStock)
                    <span class="stayl-card-stock staylbd-rt-stock stayl-card-stock--{{ $stockTier }}" data-stock-tier="{{ $stockTier }}">{{ $stockLabel }}</span>
                @endif
            </span>
        </div>

        {{-- Line 3: review stars beside Add to Cart + Buy Now --}}
        <div class="stayl-card-footer">
            <div class="stayl-card-rating" aria-label="{{ __('Customer rating') }}">
                <span class="stayl-card-stars-wrap">{!! showProductRatings($avgRate) !!}</span>
                <span class="stayl-card-rating-meta">
                    <span class="stayl-card-rating-value notranslate">{{ number_format($avgRate, 1) }}</span>
                    <span class="stayl-card-review-count">({{ $reviewCount }})</span>
                </span>
            </div>

            <div class="stayl-card-actions" role="group" aria-label="{{ __('Purchase actions') }}">
                <button type="button"
                        class="add-to-cart staylbd-rt-atc stayl-card-cta stayl-card-cta--cart"
                        data-product_id="{{ $product->id }}"
                        data-qty="1"
                        {{ $canPurchase ? '' : 'disabled' }}
                        title="{{ __('Add to Cart') }}"
                        aria-label="{{ __('Add to Cart') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span class="stayl-card-cta-text">{{ __('Cart') }}</span>
                </button>

                <a href="{{ $buyNowUrl }}"
                   class="buy-now staylbd-rt-buynow stayl-card-cta stayl-card-cta--buy {{ $canPurchase ? '' : 'is-disabled' }}"
                   data-no-ajax
                   data-product_id="{{ $product->id }}"
                   title="{{ __('Buy Now') }}"
                   aria-label="{{ __('Buy Now') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span class="stayl-card-cta-text">{{ __('Buy') }}</span>
                </a>
            </div>
        </div>
    </div>
</article>
