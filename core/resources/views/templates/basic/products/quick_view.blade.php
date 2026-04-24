@php
    $qvPricing = productDisplayPricing($product);
    $detailPrice = $qvPricing['effective'];
    $hasDiscount = $qvPricing['has_savings'];
    $qvCompareAt = $qvPricing['compare_at'];
    $savePercent = $qvPricing['save_percent'];
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
                    <span class="qv-price staylbd-rt-price">{{ $general->cur_sym }}{{ showAmount($detailPrice) }}</span>
                    @if($hasDiscount && $qvCompareAt !== null)
                        <del class="qv-price-old">{{ $general->cur_sym }}{{ showAmount($qvCompareAt) }}</del>
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
                        <span class="qv-stock staylbd-rt-stock qv-stock--{{ $product->quantity ? 'ok' : 'out' }}">
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
                        <a href="#0" class="cmn--btn add-to-cart btn-cart qv-btn-cart staylbd-rt-atc" data-product_id="{{ $product->id }}">@lang('Add To Cart')</a>
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


{{-- inline style moved to critical-storefront.css --}}


{{-- Thumb, copy link ও quantity +/- লেআউটের ডেলিগেটেড হ্যান্ডলারে (frontend.blade.php) বাইন্ড করা আছে --}}
