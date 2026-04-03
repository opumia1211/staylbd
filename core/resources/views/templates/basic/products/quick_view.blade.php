@php
    $detailPrice = productPrice($product);
    $hasDiscount = $product->price > 0 && $detailPrice < $product->price;
    $savePercent = $product->price > 0 ? round((($product->price - $detailPrice) / $product->price) * 100) : 0;
    $reviewsCount = $product->reviews_count ?? 0;
    $galleryImages = array_merge([$product->image], $product->gallery ?? []);
    $quickViewUrl = url()->current() . '?quick_view=' . $product->id;
@endphp
<div class="qv-modal mx-auto w-full max-w-storefront" data-product-id="{{ $product->id }}">
    <div class="row g-3">
        <div class="col-md-5">
            <div class="qv-gallery">
                <div class="qv-main-img-wrap">
                    <img src="{{ $product->imageShow() }}" alt="{{ __($product->name) }}" class="qv-main-img" id="qvMainImg" loading="lazy" width="500" height="500" decoding="async">
                </div>
                @if(count($galleryImages) > 1)
                    <div class="qv-thumbs">
                        <button type="button" class="qv-thumb active" data-src="{{ $product->imageShow() }}" aria-label="@lang('Image') 1">
                            <img src="{{ $product->imageShow() }}" alt="" loading="lazy" width="44" height="44" decoding="async">
                        </button>
                        @foreach($product->gallery ?? [] as $gallery)
                            @php $src = getImage(getFilePath('productGallery') . '/' . $gallery, getFileSize('productGallery')); @endphp
                            <button type="button" class="qv-thumb" data-src="{{ $src }}" aria-label="@lang('Image')"><img src="{{ $src }}" alt="" loading="lazy" width="44" height="44" decoding="async"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-7">
            <div class="qv-info">
                <div class="qv-title-wrap">
                    <h2 class="qv-title" id="qvProductName">{{ __($product->name) }}</h2>
                </div>

                {{-- লাইন ১: দাম ও ডিসকাউন্ট --}}
                <div class="qv-price-row">
                    <span class="qv-price">{{ $general->cur_sym }}{{ showAmount($detailPrice) }}</span>
                    @if($hasDiscount)
                        <del class="qv-price-old">{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                        <span class="qv-badge">{{ $savePercent }}% @lang('OFF')</span>
                    @endif
                </div>

                {{-- লাইন ২: প্রোডাক্ট আইডি ও স্টক --}}
                <div class="qv-meta">
                    @if($product->product_sku)
                        <span class="qv-sku">@lang('Product ID'): {{ $product->product_sku }}</span>
                    @else
                        <span class="qv-sku">@lang('Product ID'): {{ $product->id }}</span>
                    @endif
                    @if(isset($general->display_stock) && $general->display_stock == \App\Constants\Status::ENABLE)
                        <span class="qv-stock qv-stock--{{ $product->quantity ? 'ok' : 'out' }}">
                            {{ $product->quantity ? __('In Stock') : __('Out of Stock') }}
                            @if($product->quantity) ({{ $product->quantity }} @lang('available')) @endif
                        </span>
                    @endif
                </div>

                <div class="qv-ratings">
                    {!! showProductRatings($product->avg_rate ?? 0) !!}
                    <span class="qv-reviews">({{ $reviewsCount }} @lang('reviews'))</span>
                </div>

                @if($product->summary)
                    <div class="qv-summary">
                        <h6 class="qv-heading">@lang('Summary')</h6>
                        <p class="qv-summary-text">{{ \Illuminate\Support\Str::limit(__($product->summary), 200) }}</p>
                    </div>
                @endif

                {{-- এক লাইনে: Quantity + Add To Cart + Buy Now --}}
                <div class="qv-actions qv-actions--one-line">
                    <div class="qv-qty-inline">
                        <span class="qv-qty-label">@lang('Quantity')</span>
                        <div class="cart-plus-minus qv-qty">
                            <button type="button" class="cart-decrease qtybutton" aria-label="@lang('Decrease')">@include($activeTemplate . 'partials.icon', ['name' => 'minus'])</button>
                            <span class="qv-qty-display" id="qvQtyDisplay" aria-live="polite">1</span>
                            <input type="number" class="form-control productQuantity" name="quantity" value="1" min="1" max="{{ max(1, (int)$product->quantity) }}" aria-label="@lang('Quantity')" tabindex="-1">
                            <button type="button" class="cart-increase qtybutton" aria-label="@lang('Increase')">@include($activeTemplate . 'partials.icon', ['name' => 'plus'])</button>
                        </div>
                    </div>
                    @if($product->quantity)
                        <a href="#0" class="cmn--btn add-to-cart btn-cart qv-btn-cart" data-product_id="{{ $product->id }}">@lang('Add To Cart')</a>
                        <a href="#0" class="cmn--btn buy-now qv-btn-buy" data-product_id="{{ $product->id }}">@include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1'])@lang('Buy Now')</a>
                    @endif
                </div>
                <a href="{{ product_detail_url($product) }}" class="qv-detail-link" target="_blank" rel="noopener">@include($activeTemplate . 'partials.icon', ['name' => 'info-circle']) @lang('Details')</a>

                <div class="qv-share">
                    <span class="qv-share-label">@lang('Share'):</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($quickViewUrl) }}" target="_blank" rel="noopener" class="qv-share-icon" title="Facebook">@include($activeTemplate . 'partials.icon', ['name' => 'facebook-f'])</a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode(__($product->name)) }}&url={{ urlencode($quickViewUrl) }}" target="_blank" rel="noopener" class="qv-share-icon" title="Twitter">@include($activeTemplate . 'partials.icon', ['name' => 'twitter'])</a>
                    <a href="https://wa.me/?text={{ urlencode(__($product->name) . ' ' . $quickViewUrl) }}" target="_blank" rel="noopener" class="qv-share-icon" title="WhatsApp">@include($activeTemplate . 'partials.icon', ['name' => 'whatsapp'])</a>
                    <button type="button" class="qv-share-icon qv-copy-qv-link" data-url="{{ $quickViewUrl }}" title="@lang('Copy this page link')">@include($activeTemplate . 'partials.icon', ['name' => 'link'])</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.qv-modal { padding: 0.25rem 0; max-width: 100%; }
.qv-gallery { border-radius: 8px; overflow: hidden; background: #f8f9fa; }
.qv-main-img-wrap { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; padding: 10px; min-height: 220px; }
.qv-main-img { max-width: 100%; max-height: 100%; object-fit: contain; }
.qv-thumbs { display: flex; gap: 5px; padding: 6px; flex-wrap: wrap; }
.qv-thumb { padding: 2px; border: 2px solid transparent; border-radius: 6px; background: #fff; cursor: pointer; transition: border-color 0.2s; }
.qv-thumb.active { border-color: var(--base, #0d9488); }
.qv-thumb img { width: 44px; height: 44px; object-fit: cover; border-radius: 4px; display: block; }
.qv-info { padding-left: 0.5rem; }
.qv-title-wrap { margin-bottom: 0.6rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0; }
.qv-title { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.3; display: block; letter-spacing: -0.02em; }
.qv-price-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 0.35rem; }
.qv-price { font-size: 1.25rem; font-weight: 700; color: #111827; }
.qv-price-old { font-size: 0.95rem; color: #9ca3af; }
.qv-badge { background: #dc2626; color: #fff; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; font-weight: 600; }
.qv-meta { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 0.5rem; font-size: 0.85rem; color: #6b7280; align-items: center; }
.qv-sku { font-weight: 500; }
.qv-stock--ok { color: #059669; font-weight: 600; }
.qv-stock--out { color: #dc2626; font-weight: 600; }
.qv-ratings { margin-bottom: 0.5rem; display: flex; align-items: center; gap: 4px; }
.qv-ratings i { color: #f59e0b; font-size: 12px; }
.qv-reviews { font-size: 0.8rem; color: #6b7280; }
.qv-summary { margin-bottom: 0.6rem; }
.qv-heading { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 0.25rem; }
.qv-summary-text { font-size: 0.85rem; color: #4b5563; line-height: 1.45; margin: 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.qv-actions--one-line { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 0.5rem; }
.qv-qty-inline { display: inline-flex; align-items: center; gap: 6px; }
.qv-qty-label { font-size: 0.85rem; font-weight: 600; color: #374151; white-space: nowrap; }
.qv-qty { display: inline-flex !important; align-items: stretch; border: 1px solid #d1d5db; border-radius: 6px; overflow: hidden; background: #fff; position: relative; }
.qv-qty .qtybutton { position: static !important; left: auto !important; right: auto !important; width: 28px; height: 28px; border: none; background: #f1f5f9; cursor: pointer; padding: 0; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; color: #334155; flex-shrink: 0; }
.qv-qty .qtybutton:hover { background: #e2e8f0; }
.qv-qty-display { width: 46px; min-width: 46px; height: 28px; display: inline-flex !important; align-items: center; justify-content: center; font-size: 1.1rem !important; font-weight: 700 !important; color: #0f172a !important; background: #fff !important; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; line-height: 1; user-select: none; visibility: visible !important; opacity: 1 !important; }
.qv-qty input.productQuantity { position: absolute !important; width: 1px; height: 1px; opacity: 0; pointer-events: none; margin: -1px; overflow: hidden; left: 0; top: 0; }
.qv-actions--one-line .qv-btn-cart, .qv-actions--one-line .qv-btn-buy { height: 28px; padding: 0 8px; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center; flex: 1; min-width: 72px; max-width: 115px; }
.qv-detail-link { font-size: 0.85rem; color: var(--base, #0d9488); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-bottom: 0.5rem; }
.qv-detail-link:hover { text-decoration: underline; color: #0f766e; }
.qv-share { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.qv-share-label { font-size: 0.85rem; font-weight: 600; color: #374151; }
.qv-share-icon { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #e5e7eb; background: #fff; color: #374151; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s; font-size: 14px; }
.qv-share-icon:hover { background: #f3f4f6; border-color: var(--base, #0d9488); color: var(--base, #0d9488); }

/* ট্যাবে ছোট ও কমপ্যাক্ট */
@media (max-width: 991px) {
    .qv-modal { padding: 0.15rem 0; }
    .qv-info { padding-left: 0; padding-top: 0.5rem; }
    .qv-main-img-wrap { min-height: 160px; padding: 6px; aspect-ratio: 1; }
    .qv-thumb img { width: 36px; height: 36px; }
    .qv-thumbs { gap: 4px; padding: 4px; }
    .qv-title { font-size: 1.1rem; }
    .qv-title-wrap { margin-bottom: 0.4rem; padding-bottom: 0.35rem; }
    .qv-price { font-size: 1.05rem; }
    .qv-price-old { font-size: 0.85rem; }
    .qv-meta { font-size: 0.75rem; gap: 8px; margin-bottom: 0.35rem; }
    .qv-ratings { margin-bottom: 0.35rem; }
    .qv-ratings i { font-size: 10px; }
    .qv-reviews { font-size: 0.7rem; }
    .qv-summary { margin-bottom: 0.4rem; }
    .qv-heading { font-size: 0.75rem; margin-bottom: 0.2rem; }
    .qv-summary-text { font-size: 0.75rem; -webkit-line-clamp: 2; }
    .qv-actions--one-line { gap: 6px; margin-bottom: 0.4rem; }
    .qv-qty-label { font-size: 0.75rem; }
    .qv-qty .qtybutton { width: 26px; height: 26px; font-size: 11px; }
    .qv-qty-display { width: 38px; min-width: 38px; height: 26px; font-size: 0.95rem !important; }
    .qv-actions--one-line .qv-btn-cart, .qv-actions--one-line .qv-btn-buy { height: 26px; font-size: 0.7rem; padding: 0 6px; min-width: 60px; max-width: 100px; }
    .qv-detail-link { font-size: 0.75rem; margin-bottom: 0.35rem; }
    .qv-share-label { font-size: 0.75rem; }
    .qv-share-icon { width: 28px; height: 28px; font-size: 12px; }
}
/* মোবাইলে আরও ছোট – ভালোভাবে দেখা যাবে */
@media (max-width: 767px) {
    .qv-modal .row { margin-left: -0.2rem; margin-right: -0.2rem; }
    .qv-modal .row.g-3 { --bs-gutter-y: 0.5rem; --bs-gutter-x: 0.5rem; }
    .qv-main-img-wrap { min-height: 140px; padding: 5px; }
    .qv-thumb img { width: 32px; height: 32px; }
    .qv-title { font-size: 1rem; }
    .qv-title-wrap { margin-bottom: 0.3rem; padding-bottom: 0.3rem; }
    .qv-price { font-size: 1rem; }
    .qv-price-old { font-size: 0.8rem; }
    .qv-badge { font-size: 0.65rem; padding: 1px 6px; }
    .qv-meta { font-size: 0.7rem; gap: 6px; margin-bottom: 0.3rem; }
    .qv-ratings { margin-bottom: 0.3rem; }
    .qv-ratings i { font-size: 9px; }
    .qv-reviews { font-size: 0.65rem; }
    .qv-summary { margin-bottom: 0.35rem; }
    .qv-heading { font-size: 0.7rem; }
    .qv-summary-text { font-size: 0.7rem; -webkit-line-clamp: 2; }
    .qv-actions--one-line { gap: 6px; margin-bottom: 0.35rem; flex-direction: row; flex-wrap: wrap; }
    .qv-qty-inline { width: 100%; margin-bottom: 4px; }
    .qv-qty-label { font-size: 0.7rem; }
    .qv-qty .qtybutton { width: 24px; height: 24px; font-size: 10px; }
    .qv-qty-display { width: 34px; min-width: 34px; height: 24px; font-size: 0.85rem !important; }
    .qv-actions--one-line .qv-btn-cart, .qv-actions--one-line .qv-btn-buy { height: 26px; font-size: 0.7rem; padding: 0 6px; min-width: 0; flex: 1 1 auto; max-width: none; }
    .qv-detail-link { font-size: 0.7rem; margin-bottom: 0.3rem; }
    .qv-share { gap: 4px; }
    .qv-share-label { font-size: 0.7rem; }
    .qv-share-icon { width: 26px; height: 26px; font-size: 11px; }
}
@media (max-width: 575px) {
    .qv-modal .row { margin-left: -0.15rem; margin-right: -0.15rem; }
    .qv-main-img-wrap { min-height: 120px; padding: 4px; }
    .qv-thumb img { width: 28px; height: 28px; }
    .qv-title { font-size: 0.95rem; }
    .qv-price { font-size: 0.95rem; }
    .qv-actions--one-line .qv-btn-cart, .qv-actions--one-line .qv-btn-buy { height: 24px; font-size: 0.65rem; }
}
</style>

{{-- Thumb, copy link ও quantity +/- লেআউটের ডেলিগেটেড হ্যান্ডলারে (frontend.blade.php) বাইন্ড করা আছে --}}
