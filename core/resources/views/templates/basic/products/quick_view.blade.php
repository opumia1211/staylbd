@php
    $qvPricing = productDisplayPricing($product);
    $detailPrice = $qvPricing['effective'];
    $qvCompareAt = $qvPricing['compare_at'];
    $showStrike = $qvPricing['show_strike'];
    $saveAmount = $qvPricing['save_amount'];
    $savePercent = $qvPricing['save_percent'];
    $reviewsCount = (int) ($product->reviews_count ?? 0);
    $avgRate = (float) ($product->avg_rate ?? 0);
    $galleryImages = array_merge([$product->image], $product->gallery ?? []);
    $quickViewUrl = url()->current() . '?quick_view=' . $product->id;
    $buyNowUrl = storefront_route('cart.list.buy.now', ['id' => $product->id]);
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

                <div class="qv-price-row">
                    <span class="qv-price staylbd-rt-price notranslate" data-base-price="{{ $detailPrice }}">{{ $general->cur_sym }}{{ showAmount($detailPrice) }}</span>
                    @if($showStrike && $qvCompareAt !== null)
                        <del class="qv-price-old staylbd-rt-price-compare notranslate" data-base-price="{{ $qvCompareAt }}">{{ $general->cur_sym }}{{ showAmount($qvCompareAt) }}</del>
                    @endif
                    @if($savePercent >= 1)
                        <span class="qv-badge notranslate">-{{ $savePercent }}%</span>
                    @endif
                    @if($saveAmount > 0)
                        <span class="qv-save-amount notranslate">{{ __('Save') }} {{ $general->cur_sym }}{{ showAmount($saveAmount) }}</span>
                    @endif
                </div>

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
                    {!! showProductRatings($avgRate) !!}
                    <span class="qv-rating-value notranslate">{{ number_format($avgRate, 1) }}</span>
                    <span class="qv-reviews">({{ $reviewsCount }} @lang('reviews'))</span>
                </div>

                @if($product->summary)
                    <div class="qv-summary">
                        <h6 class="qv-heading">@lang('Summary')</h6>
                        <p class="qv-summary-text">{{ \Illuminate\Support\Str::limit(__($product->summary), 200) }}</p>
                    </div>
                @endif

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
                        <button type="button" class="add-to-cart btn-cart qv-btn-cart staylbd-rt-atc" data-product_id="{{ $product->id }}" data-qty="1">@lang('Add To Cart')</button>
                        <a href="{{ $buyNowUrl }}" class="buy-now qv-btn-buy" data-no-ajax data-product_id="{{ $product->id }}">@lang('Buy Now')</a>
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
