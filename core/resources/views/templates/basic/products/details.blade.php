@extends($activeTemplate . 'layouts.frontend')
@php
    $disableLegacyBootstrapBundle = true;
    $disableLegacyJquery = true;
@endphp

@php
    $shareUrl = $productUrl ?? product_detail_url($product);
@endphp
@push('head-meta')
    <style id="pdp-critical-cart">
        .pro-detail-page .pdp-cart-action-row{display:flex;flex-wrap:nowrap;align-items:center;gap:4px;width:100%;min-width:0;overflow:hidden}
        .pro-detail-page .pdp-cart-action-row .action-buttons{display:flex;flex-wrap:nowrap;flex:1;min-width:0;gap:3px;overflow:hidden}
        .pro-detail-page .pdp-cart-action-row .action-buttons .btn{box-sizing:border-box;min-width:0;flex:1 1 0%;height:clamp(30px,7.2vw,38px);display:flex;align-items:center;justify-content:center;gap:3px;font-size:clamp(8px,1.95vw,12px);font-weight:600;border-radius:5px;padding:0 3px;overflow:hidden;-webkit-tap-highlight-color:transparent}
        .pro-detail-page .pdp-cart-action-row .action-buttons .btn>span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0}
        .pro-detail-page .pdp-cart-action-row .action-buttons .cart-btn{flex:1.28 1 0%;background:#16a34a;color:#fff;border:1px solid #15803d;font-weight:700}
        .pro-detail-page .pdp-cart-action-row .action-buttons .cart-btn.in-cart{background:#dc3545;border-color:#b02a37;color:#fff}
        .pro-detail-page .pdp-cart-action-row .action-buttons .compare-btn{background:#e5e7eb;color:#111827;border:1px solid #d1d5db}
        .pro-detail-page .pdp-cart-action-row .action-buttons .compare-btn.in-compare{background:#dc3545;border-color:#b02a37;color:#fff}
        .pro-detail-page .pdp-cart-action-row .action-buttons .wishlist-btn{background:#10b981;color:#fff;border:1px solid #059669;text-decoration:none}
        .pro-detail-page .pdp-cart-action-row .action-buttons .wishlist-btn.added,.pro-detail-page .pdp-cart-action-row .action-buttons .wishlist-btn.active{background:#dc3545;border-color:#b02a37}
        .pro-detail-page .pdp-cart-action-row .action-buttons .chat-btn{background:#f3f4f6;color:#111827;border:1px solid #e5e7eb;text-decoration:none}
        .pro-detail-page .pdp-cart-action-row .action-buttons .chat-btn.pdp-chat-active{background:#dc3545;border-color:#b02a37;color:#fff}
    </style>
@endpush

@push('style')
    <link rel="preconnect" href="{{ url('/') }}" crossorigin>
    <link rel="preload" href="{{ $product->imageShowWebP() }}" as="image" fetchpriority="high">
    <style>
        /* Flexible product-detail: variables so layout won’t break when tuned later */
        .pro-detail-page {
            --pro-detail-container-sm: 1140px;
            --pro-detail-container-xl: 1320px;
            --pro-detail-break-lg: 992px;
            --pro-detail-break-xl: 1400px;
        }
        .pro-detail-size-wrap .pro-detail-size-btn--custom .size-text,
        .pro-detail-size-btns .pro-detail-size-btn--custom .size-text {
            font-size: 0.6rem !important;
            line-height: 1.1 !important;
            font-weight: 600;
        }
        .pro-detail-page .container {
            max-width: var(--pro-detail-container-sm);
        }
        @media (min-width: 1400px) {
            .pro-detail-page .container {
                max-width: var(--pro-detail-container-xl);
            }
        }
        .pro-detail-gallery-col, .pro-detail-info-col { min-width: 0; }
        .pro-detail-size-btns { flex-wrap: wrap; gap: clamp(4px, 1vw, 8px); }
        .pro-detail-size-btn {
            width: clamp(36px, 10vw, 42px);
            height: clamp(36px, 10vw, 42px);
            min-width: clamp(36px, 10vw, 42px);
        }
        #product-detail-tabs {
            content-visibility: auto;
            contain-intrinsic-size: auto 400px;
        }
        .pro-detail-related-section,
        .pro-detail-more-sections .pro-section {
            content-visibility: auto;
            contain-intrinsic-size: auto 300px;
        }
        .pro-detail-more-sections .pro-section__title { font-size: 1.1rem; }
        /* Critical: no horizontal overflow on any device */
        .pro-detail-page { overflow-x: hidden; width: 100%; }
        .pro-detail-page .container { max-width: 100%; box-sizing: border-box; }
        .pro-detail-gallery-col, .pro-detail-info-col { min-width: 0; max-width: 100%; }
        @media (max-width: 575.98px) {
            .pro-detail-page .container { padding-left: 0.5rem; padding-right: 0.5rem; }
            /* cart-action-row: layout from product-details.css @ 768px */
        }
        /* Quantity input: wider so number is clearly visible */
        .pdp-cart-action-row .qty-box input.productQuantity {
            min-width: 44px;
            width: 3rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.95rem;
        }
        /* Mobile: প্রোডাক্ট ছবি নেভ বাটন দুপাশে – Prev বামে, Thumbs মাঝে স্ক্রল, Next ডানে */
        @media (max-width: 575.98px) {
            .pro-detail-thumbs-col {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                gap: 8px;
            }
            .pro-detail-thumb-prev { order: 0; flex-shrink: 0; }
            .pro-detail-thumbs-vertical { order: 1; flex: 1; min-width: 0; -webkit-overflow-scrolling: touch; }
            .pro-detail-thumb-next { order: 2; flex-shrink: 0; }
        }
        /* Mobile/Tablet: Similar Products সাইডবার লুকানো – শুধু Related Products; ক্রম ঠিক */
        @media (max-width: 991.98px) {
            .pro-detail-page .container {
                display: flex;
                flex-direction: column;
            }
            .pro-detail-page .container > .row.g-4 {
                display: contents;
            }
            .pro-detail-page .container > .row.g-4 > .pro-detail-gallery-col { order: 0; }
            .pro-detail-page .container > .row.g-4 > .pro-detail-info-col { order: 1; }
            .pro-detail-page .container > #product-detail-tabs { order: 2; }
            .pro-detail-page .container > .row.g-4 > .col-lg-3.order-lg-3 {
                display: none !important; /* Similar Products: মোবাইল/ট্যাবে দেখাব না, শুধু Related Products */
            }
            .pro-detail-page .container > .pro-detail-more-sections { order: 3; }
        }
    </style>
@endpush

@section('content')
    @php
        $reviewsTotalSeo = $reviewsTotal ?? 0;
        $avgRateSeo = $product->avg_rate ?? 0;
        $detailPrice = $detailPrice ?? productPrice($product);
        $productUrl = $productUrl ?? product_detail_url($product);
        $productImages = $productImages ?? [];
        if (empty($productImages)) {
            $productImages = [$product->imageShow()];
        }
        $breadcrumbList = $breadcrumbList ?? [
            ['name' => __('Home'), 'url' => url('/')],
            ['name' => __($product->name), 'url' => $productUrl],
        ];
        if (isset($product->category) && $product->category && count($breadcrumbList) === 2) {
            array_splice($breadcrumbList, 1, 0, [['name' => __($product->category->name), 'url' => route('category.products', [slug($product->category->name), $product->category->id])]]);
        }
    @endphp
    {{-- BreadcrumbList JSON-LD for rich snippets --}}
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[
        @foreach($breadcrumbList as $i => $b)
        {"@type":"ListItem","position":{{ $i + 1 }},"name":{{ json_encode($b['name']) }},"item":"{{ $b['url'] }}"}{{ $i < count($breadcrumbList) - 1 ? ',' : ''}}
        @endforeach
    ]}
    </script>
    {{-- Full Product JSON-LD: name, image, offer, availability, brand, optional aggregateRating --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": {{ json_encode(__($product->name)) }},
        "url": "{{ $productUrl }}",
        "image": @json($productImages),
        "description": @json(strLimit(strip_tags($product->summary ?? $product->description ?? ''), 500)),
        @if($product->product_sku)
        "sku": "{{ $product->product_sku }}",
        @endif
        @if($product->brand)
        "brand": {"@type":"Brand","name":{{ json_encode(__($product->brand->name)) }}},
        @endif
        "offers": {
            "@type": "Offer",
            "url": "{{ $productUrl }}",
            "priceCurrency": "{{ $general->cur_text ?? 'BDT' }}",
            "price": "{{ number_format($detailPrice, 2, '.', '') }}",
            "availability": "https://schema.org/{{ ($product->quantity ?? 0) > 0 ? 'InStock' : 'OutOfStock' }}",
            "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}"
        }
        @if($reviewsTotalSeo > 0 && $avgRateSeo > 0)
        ,"aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ number_format($avgRateSeo, 1) }}",
            "reviewCount": "{{ $reviewsTotalSeo }}",
            "bestRating": "5",
            "worstRating": "1"
        }
        @endif
    }
    </script>
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_detail_above'])

    @php $productDetailTimers = get_offer_timers_for_display('product_detail', 'product_detail', $product->id, $product->category_id ?? null); @endphp
    @if($productDetailTimers->isNotEmpty())
        <div class="container mb-3">
            @foreach($productDetailTimers as $pt)
                @include('partials.offer_timer_bar', ['timer' => $pt])
            @endforeach
        </div>
    @endif

    @php
        $features = $product->features ?? [];
        $detailSave = $product->price - $detailPrice;
        $detailPercent = $product->price > 0 ? round(($detailSave / $product->price) * 100) : 0;
        $hasDiscount = $product->price > 0 && $detailPrice < $product->price;
        $galleryImages = array_merge([$product->image], $product->gallery ?? []);
        $totalGallery = count($product->gallery ?? []) + 1;
    @endphp

    <section class="products-single-section pt-80 pb-80 pro-detail-page">
        <div class="container">
            <div class="row g-4">
                {{-- Left: Vertical thumbnails + Large main image (hover zoom below + click lightbox) --}}
                <div class="col-lg-5 pro-detail-gallery-col">
                    <div class="pro-detail-gallery-and-size">
                    <div class="pro-detail-gallery-row">
                        <div class="pro-detail-thumbs-col">
                            <button type="button" class="pro-detail-thumb-nav pro-detail-thumb-prev" aria-label="@lang('Previous')">@include($activeTemplate . 'partials.icon', ['name' => 'angle-up'])</button>
                            <div class="pro-detail-thumbs-vertical" id="proDetailThumbs">
                                <div class="thumb-item active" data-index="0">
                                    <img src="{{ $product->imageShowWebP() }}" alt="{{ __($product->name) }}" width="500" height="500" fetchpriority="high" decoding="async">
                                </div>
                                @foreach ($product->gallery ?? [] as $idx => $gallery)
                                    <div class="thumb-item" data-index="{{ $idx + 1 }}">
                                        <img src="{{ getImageWebP(getFilePath('productGallery') . '/' . $gallery, getFileSize('productGallery')) }}" alt="{{ __($product->name) }}" width="500" height="500" loading="lazy" decoding="async">
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="pro-detail-thumb-nav pro-detail-thumb-next" aria-label="@lang('Next')">@include($activeTemplate . 'partials.icon', ['name' => 'angle-down'])</button>
                        </div>
                        <div class="pro-detail-main-image-wrap">
                            <div class="pro-detail-zoom-area" id="proDetailZoomArea">
                                <div class="main-img-inner" id="proDetailMainInner">
                                    <img id="proDetailMainImg" src="{{ $product->imageShowWebP() }}" alt="{{ __($product->name) }}" data-zoom-src="{{ $product->imageShowWebP() }}" fetchpriority="high" decoding="async" width="500" height="500">
                                </div>
                                <div class="pro-detail-zoom-lens" id="proDetailZoomLens"></div>
                            </div>
                        </div>
                    </div>
                    @php
                        $standardSizes = ['NO', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '4XL', '5XL'];
                        $sizeVariants = $product->has_variants && $product->activeVariants->isNotEmpty()
                            ? $product->activeVariants->keyBy(function($v) { return is_array($v->attributes) ? ($v->attributes['size'] ?? $v->id) : $v->id; })
                            : collect();
                    @endphp
                    <div class="pro-detail-size-wrap">
                        <span class="pro-detail-size-label">@lang('Size') <span class="text-muted small">(@lang('optional'))</span>:</span>
                        <div class="pro-detail-size-btns" role="group" aria-label="@lang('Select size')">
                            @if($sizeVariants->isNotEmpty())
                                @foreach($standardSizes as $sizeLabel)
                                    @php $v = $sizeVariants->get($sizeLabel); @endphp
                                    @if($v)
                                        <button type="button" class="pro-detail-size-btn {{ $sizeLabel == 'NO' ? 'pro-detail-size-btn--custom' : '' }}" data-variant-id="{{ $v->id }}" data-size="{{ $sizeLabel }}" data-qty="{{ $v->quantity }}" title="{{ $v->quantity < 1 ? __('Out of stock') : ($sizeLabel == 'NO' ? __('Custom Size') : $sizeLabel) }}" {{ $v->quantity < 1 ? 'disabled' : '' }}>
                                            <span class="size-text">{{ $sizeLabel == 'NO' ? __('Custom Size') : $sizeLabel }}</span>
                                            @include($activeTemplate . 'partials.icon', ['name' => 'check', 'class' => 'size-tick'])
                                        </button>
                                    @else
                                        <button type="button" class="pro-detail-size-btn pro-detail-size-btn--na {{ $sizeLabel == 'NO' ? 'pro-detail-size-btn--custom' : '' }}" disabled title="@lang('Not available')"><span class="size-text">{{ $sizeLabel == 'NO' ? __('Custom Size') : $sizeLabel }}</span>@include($activeTemplate . 'partials.icon', ['name' => 'check', 'class' => 'size-tick'])</button>
                                    @endif
                                @endforeach
                            @else
                                @foreach($standardSizes as $sizeLabel)
                                    <button type="button" class="pro-detail-size-btn pro-detail-size-btn--no-variant {{ $sizeLabel == 'NO' ? 'pro-detail-size-btn--custom' : '' }}" data-size="{{ $sizeLabel }}">
                                        <span class="size-text">{{ $sizeLabel == 'NO' ? __('Custom Size') : $sizeLabel }}</span>
                                        @include($activeTemplate . 'partials.icon', ['name' => 'check', 'class' => 'size-tick'])
                                    </button>
                                @endforeach
                            @endif
                        </div>
                        <div class="pro-detail-custom-size-wrap mt-2" id="customSizeWrap" style="display: none;">
                            <label for="customSizeInput" class="form-label small mb-1">@lang('Custom Size') <span class="text-danger">*</span></label>
                            <input type="text" id="customSizeInput" class="form-control form-control-sm" name="custom_size" placeholder="@lang('e.g. 38 inch, 42')" maxlength="100" aria-label="@lang('Enter your custom size')">
                            <small class="text-muted">@lang('Enter the size you need (only for Custom Size option).')</small>
                        </div>
                        <input type="hidden" name="selected_size" id="selectedSizeInput" value="">
                    </div>
                    </div>
                </div>

                {{-- Center: Product info & purchase - zoom preview appears here on hover --}}
                <div class="col-lg-4 pro-detail-info-col">
                    <div class="pro-detail-zoom-preview-wrap" id="proDetailZoomPreviewWrap">
                        <div class="pro-detail-zoom-preview" id="proDetailZoomPreview" aria-hidden="true"></div>
                    </div>
                        <div class="pro-detail-info-card">
                        <h1 class="pro-detail-title" itemprop="name">{{ __($product->name) }}</h1>
                        <div class="pro-detail-meta">
                            <div class="pro-detail-meta-row pro-detail-meta-row--reviews">
                                <div class="ratings">
                                    @php echo showProductRatings($product->avg_rate); @endphp
                                </div>
                                <span class="review-count">({{ $product->reviews->count() }} @lang('reviews'))</span>
                            </div>
                            <div class="pro-detail-meta-row pro-detail-meta-row--product-id">
                                <span class="pro-detail-product-id">@lang('Product ID'): {{ $product->product_sku ?: $product->id }}</span>
                                @if($product->brand)
                                    <span class="pro-detail-product-id ms-2">@lang('Brand'): <a href="{{ route('brand.products', [slug($product->brand->name), $product->brand->id]) }}" class="link-secondary text-decoration-none">{{ __($product->brand->name) }}</a></span>
                                @endif
                                @if($product->category)
                                    <span class="pro-detail-product-id ms-2">@lang('Category'): <a href="{{ route('category.products', [slug($product->category->name), $product->category->id]) }}" class="link-secondary text-decoration-none">{{ __($product->category->name) }}</a></span>
                                @endif
                            </div>
                        </div>

                        <div class="pro-detail-price-block pro-detail-price-one-line">
                            <span class="pro-detail-special-price product-price">{{ $general->cur_sym }}{{ showAmount($detailPrice) }}</span>
                            @if ($hasDiscount)
                                <span class="pro-detail-regular-price">{{ $general->cur_sym }}{{ showAmount($product->price) }}</span>
                                <span class="badge bg-success pro-detail-discount-badge">{{ $detailPercent }}% @lang('OFF')</span>
                                <span class="pro-detail-save-extra">{{ $general->cur_sym }}{{ showAmount($detailSave) }} @lang('saved')</span>
                            @endif
                        </div>

                        <div class="pro-detail-availability">
                            @if (isset($general->display_stock) && $general->display_stock == \App\Constants\Status::ENABLE)
                                @php
                                    $detailQty = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->sum('quantity') : (int) $product->quantity;
                                    $inStockMin = config('product_upload.in_stock_min', 20);
                                    $lowStockMax = config('product_upload.low_stock_max', 20);
                                    $detailStockStatus = $detailQty > $lowStockMax ? 'in' : ($detailQty >= 1 ? 'low' : 'out');
                                    $detailStockLabel = $detailQty > $lowStockMax ? __('In Stock') : ($detailQty >= 1 ? __('Low Stock') : __('Out Of Stock'));
                                @endphp
                                <span class="pro-detail-availability-text pro-detail-availability-text--{{ $detailStockStatus }}">
                                    @if(!$detailQty)
                                        @lang('Out Of Stock')
                                    @else
                                        {{ $detailStockLabel }} (<span class="amount" id="productStockDisplay">{{ $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->first()->quantity : $product->quantity }}</span> @lang('available'))
                                        @if($detailStockStatus === 'low' && $detailQty > 0)
                                            <span class="text-danger ms-1 fw-semibold">{{ __('Only :count left!', ['count' => $detailQty]) }}</span>
                                        @endif
                                    @endif
                                </span>
                            @else
                                <span class="pro-detail-availability-text pro-detail-availability-text--na">@lang('Check Availability')</span>
                            @endif
                        </div>

                        {{-- Social proof: viewed in 24h (when admin enabled) / sold count --}}
                        @php $showViewCount = isset($general->display_view_count) && $general->display_view_count == \App\Constants\Status::ENABLE; @endphp
                        @if(($showViewCount && ($productViews24h ?? 0) > 0) || ($product->sale_count ?? 0) > 0)
                        <div class="pro-detail-social-proof mb-2">
                            @if($showViewCount && ($productViews24h ?? 0) > 0)
                                <span class="pro-detail-social-proof-item">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'eye', 'class' => 'text-muted small'])
                                    {{ __('🔥 :count people are viewing this product', ['count' => $productViews24h]) }}
                                </span>
                            @endif
                            @if(($product->sale_count ?? 0) > 0)
                                <span class="pro-detail-social-proof-item">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag', 'class' => 'text-muted small'])
                                    {{ __('⚡ :count sold in the last 24 hours', ['count' => $product->sale_count]) }}
                                </span>
                            @endif
                        </div>
                        @endif

                        {{-- Estimated delivery --}}
                        @if(!empty($product->delivery_time) || isset($product->delivery_type))
                        <div class="pro-detail-estimated-delivery mb-2 d-flex">
                            @include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast', 'class' => 'text--base me-2'])
                            <div class="d-flex flex-column small">
                                <span>
                                    @if(!empty($product->delivery_time))
                                        {{ __('🚚 Delivery: :time (Dhaka 2–3 days)', ['time' => __($product->delivery_time)]) }}
                                    @else
                                        {{ __('🚚 Delivery: Usually 3–7 business days (depends on your location).') }}
                                    @endif
                                    @if(isset($product->delivery_type) && ($product->delivery_type ?? 'free') === 'paid' && ($product->delivery_charge ?? 0) > 0)
                                        <span class="text-muted">({{ $general->cur_sym }}{{ showAmount($product->delivery_charge) }} @lang('shipping'))</span>
                                    @endif
                                </span>
                                <span>{{ __('💰 Cash on Delivery Available') }}</span>
                                <span>{{ __('🔁 7 Days Easy Return') }}</span>
                            </div>
                        </div>
                        @endif

                        @if ($features && count($features) > 0)
                            <div class="pro-detail-quick-overview">
                                <h6>@lang('Quick Overview')</h6>
                                <ul>
                                    @foreach ($features as $feature)
                                        <li>
                                            <strong>{{ __($feature['title'] ?? '') }}</strong>
                                            <span>{{ __($feature['description'] ?? '') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            @if ($product->summary)
                                <div class="pro-detail-quick-overview">
                                    <h6>@lang('Summary')</h6>
                                    <p class="mb-0 small">{{ __($product->summary) }}</p>
                                </div>
                            @endif
                        @endif

                        {{-- লাল মার্ক জায়গা: Key Features – অ্যাডমিনে যা লেখা হবে তা এখানে দেখাবে --}}
                        @if(!empty($product->key_features))
                            <div class="pro-detail-quick-overview pro-detail-key-features">
                                <h6>@lang('Key Features')</h6>
                                <div class="pro-detail-key-features-content">{!! nl2br(e(__($product->key_features))) !!}</div>
                            </div>
                        @endif

                        @if($product->has_variants && $product->activeVariants->isNotEmpty())
                            <div class="mb-3 pro-detail-variant-select-wrap">
                                <label class="form-label">@lang('Size') / @lang('Variant')</label>
                                <select class="form-select product-variant-select" name="variant_id" id="productVariantId">
                                    <option value="">@lang('Select Size')</option>
                                    @foreach($product->activeVariants as $v)
                                        @php $dispSize = is_array($v->attributes) ? ($v->attributes['size'] ?? $v->id) : $v->id; @endphp
                                        <option value="{{ $v->id }}" data-qty="{{ $v->quantity }}" data-price="{{ $v->final_price }}" data-size="{{ $dispSize }}" {{ $v->quantity < 1 ? 'disabled' : '' }}>
                                            {{ ($dispSize === 'NO' ? __('Custom Size') : $dispSize) }} — {{ $general->cur_sym }}{{ showAmount($v->final_price) }}
                                            @if($v->quantity < 1) (@lang('Out of stock')) @else ({{ $v->quantity }} @lang('pcs')) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- লাল মার্ক জায়গা: একই কার্ডের ভেতরে, নিচের দিকে – সমস্ত ফিচার এখানে --}}
                        @php $detailMaxQty = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->pluck('quantity')->max() : (int)$product->quantity; @endphp
                        <div class="pro-detail-actions-block pro-detail-actions-block--in-card">
                            {{-- Quantity + all action buttons in a single row (cart-action-row + action-buttons) --}}
                            {{-- PDP action row: isolated layout — qty + flex action-buttons (no main.css single-add-cart-area bleed) --}}
                            {{-- qty + action-buttons: এক স্ক্রল স্ট্রিপে ৪ বাটন (মোবাইলে সব দৃশ্যমান) --}}
                            <div class="cart-action-row single-add-cart-area pdp-cart-action-row">
                                <div class="qty-box pro-detail-qty-wrap cart-plus-minus">
                                    <button type="button" class="qty-btn cart-decrease dec" aria-label="@lang('Decrease quantity')">@include($activeTemplate . 'partials.icon', ['name' => 'minus'])</button>
                                    <input type="number" class="productQuantity" name="quantity" value="1" min="1" inputmode="numeric">
                                    <button type="button" class="qty-btn cart-increase inc" aria-label="@lang('Increase quantity')">@include($activeTemplate . 'partials.icon', ['name' => 'plus'])</button>
                                </div>
                                <div class="action-buttons pdp-actions-scroll" aria-label="{{ __('Product actions') }}">
                                    <button type="button"
                                            class="btn cart-btn add-to-cart"
                                            data-product_id="{{ $product->id }}"
                                            id="addToCartBtn"
                                            title="{{ __('Add to cart — click again when red to remove') }}">
                                        @include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart'])
                                        <span>@lang('Add To Cart')</span>
                                    </button>
                                    <button type="button"
                                            class="btn compare-btn btn-compare add-to-compare"
                                            data-product_id="{{ $product->id }}"
                                            title="{{ __('In compare list — click again to remove') }}">
                                        @include($activeTemplate . 'partials.icon', ['name' => 'sync-alt'])
                                        <span>@lang('Compare')</span>
                                    </button>
                                    <a href="javascript:void(0)"
                                       class="btn wishlist-btn add-wishlist {{ in_array($product->id, $wishListProductIds ?? []) ? 'added' : '' }}"
                                       data-product_id="{{ $product->id }}"
                                       role="button"
                                       title="{{ __('Wishlist — click again when red to remove') }}">
                                        @include($activeTemplate . 'partials.icon', ['name' => 'heart'])
                                        <span>@lang('Wishlist')</span>
                                    </a>
                                    <button type="button"
                                            class="btn chat-btn pdp-chat-toggle"
                                            id="pdpChatToggleBtn"
                                            data-product_id="{{ $product->id }}"
                                            title="{{ __('Open chat — click again to close') }}">
                                        @include($activeTemplate . 'partials.icon', ['name' => 'comments'])
                                        <span>@lang('Chat')</span>
                                    </button>
                                </div>
                            </div>
                            <div class="pro-detail-buy-now-wrap mb-2">
                                @if($detailMaxQty > 0)
                                <a class="cmn--btn buy-now w-100 justify-content-center" href="#0" data-product_id="{{ $product->id }}">@include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1']) @lang('Buy Now')</a>
                                @else
                                <span class="cmn--btn w-100 justify-content-center disabled" style="cursor:not-allowed; opacity:0.7;">@include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1']) @lang('Out Of Stock')</span>
                                @endif
                            </div>
                            @guest
                            <div class="pro-detail-quick-order-wrap mb-2">
                                <a href="#0"
                                   class="cmn--btn w-100 justify-content-center btn-outline--base buy-now"
                                   id="openGuestCheckoutFromProduct"
                                   data-product_id="{{ $product->id }}">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1']) @lang('Quick Order')
                                </a>
                                <p class="mb-0 mt-1 text-muted small text-center">{{ __('Quick Order – no login required. We will confirm by phone.') }}</p>
                            </div>
                            @endguest
                            @php
                                $footerData = getCachedFooterData();
                                $policyPages = $footerData['policy_pages'] ?? collect();
                                $policyPayment = $policyPages->first(fn($p) => stripos((string)($p->data_values->title ?? ''), 'payment') !== false);
                                $policyShipping = $policyPages->first(fn($p) => stripos((string)($p->data_values->title ?? ''), 'shipping') !== false || stripos((string)($p->data_values->title ?? ''), 'charge') !== false);
                                $policyOrder = $policyPages->first(fn($p) => stripos((string)($p->data_values->title ?? ''), 'order') !== false || stripos((string)($p->data_values->title ?? ''), 'procedure') !== false);
                                if (!$policyPayment && $policyPages->isNotEmpty()) $policyPayment = $policyPages->get(0);
                                if (!$policyShipping && $policyPages->count() > 1) $policyShipping = $policyPages->get(1);
                                if (!$policyOrder && $policyPages->count() > 2) $policyOrder = $policyPages->get(2);
                            @endphp
                            <div class="pro-detail-service-links">
                                @if($policyPayment)
                                    <a href="{{ route('policy.pages.short', $policyPayment->id) }}">@include($activeTemplate . 'partials.icon', ['name' => 'credit-card']) @lang('Payment Method')</a>
                                @else
                                    <a href="{{ route('policy.pages.short', 1) }}">@include($activeTemplate . 'partials.icon', ['name' => 'credit-card']) @lang('Payment Method')</a>
                                @endif
                                @if($policyShipping)
                                    <a href="{{ route('policy.pages.short', $policyShipping->id) }}">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast']) @lang('Shipping & Charge')</a>
                                @else
                                    <a href="{{ route('policy.pages.short', 2) }}">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast']) @lang('Shipping & Charge')</a>
                                @endif
                                @if($policyOrder)
                                    <a href="{{ route('policy.pages.short', $policyOrder->id) }}">@include($activeTemplate . 'partials.icon', ['name' => 'list-alt']) @lang('Order Procedure')</a>
                                @else
                                    <a href="{{ route('policy.pages.short', 3) }}">@include($activeTemplate . 'partials.icon', ['name' => 'list-alt']) @lang('Order Procedure')</a>
                                @endif
                            </div>
                            <div class="mt-2 small text-muted">
                                <ul class="list-unstyled mb-0 pro-detail-trust-list">
                                    <li>✔ @lang('100% Authentic Product')</li>
                                    <li>✔ @lang('Secure Payment')</li>
                                    <li>✔ @lang('Trusted by 10,000+ customers')</li>
                                </ul>
                            </div>
                            <div class="pro-detail-share">
                                <span>@lang('Share'):</span>
                                <ul class="social-icons">
                                    <li><a href="https://wa.me/?text={{ urlencode(__($product->name) . ' ' . $shareUrl) }}" target="_blank" rel="noopener" style="background:#25D366" title="WhatsApp">@include($activeTemplate . 'partials.icon', ['name' => 'whatsapp'])</a></li>
                                    <li><a href="mailto:?subject={{ urlencode(__($product->name)) }}&body={{ urlencode($shareUrl) }}" style="background:#ea4335" title="@lang('Email')">@include($activeTemplate . 'partials.icon', ['name' => 'envelope'])</a></li>
                                    <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" style="background:#1877f2" title="@lang('Facebook')">@include($activeTemplate . 'partials.icon', ['name' => 'facebook-f'])</a></li>
                                    <li><a href="https://twitter.com/intent/tweet?text={{ urlencode(__($product->name)) }}&url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" style="background:#000" title="@lang('Twitter')">@include($activeTemplate . 'partials.icon', ['name' => 'twitter'])</a></li>
                                    <li><a href="javascript:window.print()" style="background:#6c757d" title="@lang('Print')">@include($activeTemplate . 'partials.icon', ['name' => 'print'])</a></li>
                                    <li><a href="javascript:void(0)" class="copy-link-btn" data-url="{{ $shareUrl }}" style="background:#0d6efd" title="@lang('Copy Link')">@include($activeTemplate . 'partials.icon', ['name' => 'link'])</a></li>
                                </ul>
                            </div>
                            <div class="d-grid gap-2 mt-2">
                                <a href="{{ route('contact.live') }}?open_contact=1" class="btn btn-outline-secondary js-contact-panel-open" role="button" data-contact-live-url="{{ route('contact.live') }}?open_contact=1">@include($activeTemplate . 'partials.icon', ['name' => 'comments', 'class' => 'me-1']) @lang('Chat with us')</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Similar products (reference: clean white cards, image + name + price + actions) --}}
                <div class="col-lg-3 order-lg-3">
                    @if ($relatedProduct->count() > 0)
                        <div class="pro-detail-similar-wrap">
                            <h5>@lang('Similar Products')</h5>
                            <div class="pro-detail-similar-list">
                                @foreach ($relatedProduct->take(4) as $singleProduct)
                                    @php
                                        $spPrice = productPrice($singleProduct);
                                        $spQty = (int) ($singleProduct->quantity ?? 0);
                                    @endphp
                                    <div class="pro-detail-similar-card">
                                        <a href="{{ product_detail_url($singleProduct) }}" class="pro-detail-similar-card-link">
                                            <span class="pro-detail-similar-card-img">
                                                <img src="{{ getImageWebP(getFilePath('product') . '/' . $singleProduct->image, getFileSize('product')) }}" alt="{{ __($singleProduct->name) }}" loading="lazy" decoding="async" width="200" height="200">
                                            </span>
                                            <span class="pro-detail-similar-card-body">
                                                <span class="pro-detail-similar-card-title">{{ __($singleProduct->name) }}</span>
                                                <span class="pro-detail-similar-card-price">{{ $general->cur_sym }}{{ showAmount($spPrice) }}</span>
                                            </span>
                                        </a>
                                        <div class="pro-detail-similar-card-actions">
                                            @if($spQty > 0)
                                            <button type="button" class="pro-detail-similar-btn pro-detail-similar-btn--cart add-to-cart" data-product_id="{{ $singleProduct->id }}">@lang('Add to Cart')</button>
                                            <a href="#0" class="pro-detail-similar-btn pro-detail-similar-btn--buy buy-now" data-product_id="{{ $singleProduct->id }}">@lang('Buy Now')</a>
                                            @else
                                            <span class="pro-detail-similar-btn disabled">@lang('Out Of Stock')</span>
                                            @endif
                                            <button type="button" class="pro-detail-similar-icon add-to-compare" data-product_id="{{ $singleProduct->id }}" title="@lang('Compare')">@include($activeTemplate . 'partials.icon', ['name' => 'exchange-alt'])</button>
                                            <button type="button" class="pro-detail-similar-icon quickView" data-product_id="{{ $singleProduct->id }}" title="@lang('Quick view')">@include($activeTemplate . 'partials.icon', ['name' => 'eye'])</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bottom: Tabs (Specifications, Details, Video, Q&A, Review) --}}
            <div class="pro-detail-tabs-wrap description-wrapper bg--section" id="product-detail-tabs">
                <div class="description__header">
                    <ul class="nav nav-tabs van-tabs nav--tabs" role="tablist">
                        <li><a href="#tab-details" data-bs-toggle="tab" class="active">@lang('Details')</a></li>
                        @if ($features && count($features) > 0)
                            <li><a href="#tab-specs" data-bs-toggle="tab">@lang('Specifications')</a></li>
                        @endif
                        @if(!empty($product->video))
                            <li><a href="#tab-video" data-bs-toggle="tab">@lang('Video')</a></li>
                        @endif
                        <li><a href="#tab-shipping" data-bs-toggle="tab">@lang('Shipping Info')</a></li>
                        <li><a href="#tab-review" data-bs-toggle="tab" class="review-tab-link">@lang('Review')</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-details">
                        <div class="description__body">
                            <p>@php echo $product->description; @endphp</p>
                        </div>
                    </div>
                    @if ($features && count($features) > 0)
                        <div class="tab-pane fade" id="tab-specs">
                            <div class="description__body border-0 p-0">
                                <table class="table feature-table">
                                    <tbody>
                                        @foreach ($features as $feature)
                                            <tr>
                                                <th>{{ __($feature['title'] ?? '') }}</th>
                                                <td>{{ __($feature['description'] ?? '') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if(!empty($product->video))
                        <div class="tab-pane fade" id="tab-video">
                            <div class="description__body pro-detail-video-inner">
                                <video controls class="w-100 rounded" preload="none" loading="lazy">
                                    <source src="{{ asset(getFilePath('productVideo') . '/' . $product->video) }}" type="video/{{ pathinfo($product->video, PATHINFO_EXTENSION) === 'webm' ? 'webm' : 'mp4' }}">
                                    @lang('Your browser does not support the video tag.')
                                </video>
                            </div>
                        </div>
                    @endif
                    <div class="tab-pane fade" id="tab-shipping">
                        <div class="description__body">
                            <ul class="list-unstyled mb-0 small">
                                <li>🚚 @lang('Standard delivery within 3–7 business days (Dhaka often 2–3 days).')</li>
                                <li>💰 @lang('Cash on Delivery available on most locations.')</li>
                                <li>🔁 @lang('7 Days easy return on eligible items (see policy pages above).')</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-review">
                        <div class="description__body review-tab-body">
                            {{-- Rating summary: compact --}}
                            @if(($reviewsTotal ?? 0) > 0)
                                <div class="review-summary review-summary--clean">
                                    <span class="review-summary__avg">{{ number_format($product->avg_rate ?? 0, 1) }}</span>
                                    <div class="review-summary__stars">@php echo showProductRatings($product->avg_rate ?? 0); @endphp</div>
                                    <span class="review-summary__based">@lang('Based on') {{ $reviewsTotal }} @lang('Reviews')</span>
                                </div>
                            @endif

                            {{-- Write a Review --}}
                            <div class="review-write-section">
                                <h6 class="review-form-title">@lang('Your Review')</h6>
                                @guest
                                    <a href="{{ route('user.login') }}?redirect={{ urlencode(request()->url()) }}" class="cmn--btn review-write-section__login-btn">@lang('Login to write a review')</a>
                                @else
                                    @if(isset($reviewBlockedReason) && $reviewBlockedReason === 'profile_incomplete')
                                        <p class="review-form-blocked-note text-warning mb-2">@include($activeTemplate . 'partials.icon', ['name' => 'exclamation-circle']) @lang('Please complete your profile to write a review.')</p>
                                        <a href="{{ route('user.data') }}" class="cmn--btn review-write-section__login-btn">@lang('Complete profile')</a>
                                    @elseif(!isset($userReview) || !$userReview)
                                        <div class="review-form-wrap" id="reviewFormWrap">
                                            @if(!empty($hasPurchased))<p class="review-form-verified-note">@include($activeTemplate . 'partials.icon', ['name' => 'check-circle']) @lang('Verified Purchase')</p>@endif
                                            <form id="productReviewForm" class="review-form-advanced" enctype="multipart/form-data" action="{{ route('user.review.store', $product->id) }}" method="post">
                                                @csrf
                                                <div class="review-form-row review-form-row--stars">
                                                    <span class="review-form-label">@lang('Rating')</span>
                                                    <div class="rating-wrapper">
                                                        <div class="star-rating" id="starRating" role="group" aria-label="@lang('Rate 1 to 5 stars')">
                                                            <span class="star" data-value="1">&#9733;</span>
                                                            <span class="star" data-value="2">&#9733;</span>
                                                            <span class="star" data-value="3">&#9733;</span>
                                                            <span class="star" data-value="4">&#9733;</span>
                                                            <span class="star" data-value="5">&#9733;</span>
                                                        </div>
                                                        <input type="hidden" name="rating" id="selectedRating" value="" required>
                                                    </div>
                                                </div>
                                                <div class="review-form-row">
                                                    <label class="review-form-label" for="reviewTitle">@lang('Review Title')</label>
                                                    <input type="text" class="form-control" id="reviewTitle" name="title" maxlength="255" placeholder="@lang('Sum up your experience')" autocomplete="off" aria-label="@lang('Review Title')">
                                                </div>
                                                <div class="review-form-row">
                                                    <label class="review-form-label" for="reviewComment">@lang('Your Review')</label>
                                                    <textarea class="form-control" id="reviewComment" name="review_comment" rows="4" placeholder="@lang('Write your review...')" required aria-label="@lang('Your Review')"></textarea>
                                                </div>
                                                <div class="review-form-row">
                                                    <label class="review-form-label" for="review_image">@lang('Photo') <span class="text-muted">(@lang('optional'))</span></label>
                                                    <input type="file" class="form-control" id="review_image" name="review_image" accept=".jpg,.jpeg,.png,.webp" aria-label="@lang('Review photo')">
                                                </div>
                                                <button type="submit" class="cmn--btn btn-submit-review" id="reviewSubmitBtn">@lang('Submit Review')</button>
                                            </form>
                                        </div>
                                    @else
                                        <p class="review-form-edited-note">@lang('You have already reviewed this product.') <button type="button" class="btn btn-sm btn-outline-primary" id="btnEditReview">@lang('Edit')</button></p>
                                    @endif
                                @endguest
                            </div>

                            {{-- Customer Reviews list --}}
                            <div class="review-list-header">
                                <h6 class="review-list-title">@lang('Customer Reviews')</h6>
                                @if(($reviewsTotal ?? 0) > 0)
                                    <select class="form-select form-select-sm review-list-sort__select" id="reviewSortSelect" aria-label="@lang('Sort by')">
                                        <option value="recent">@lang('Most Recent')</option>
                                        <option value="highest">@lang('Highest Rating')</option>
                                        <option value="lowest">@lang('Lowest Rating')</option>
                                        <option value="helpful">@lang('Most Helpful')</option>
                                    </select>
                                @endif
                            </div>
                            <div class="review-area load-reviews" id="reviewListContainer">
                                @forelse ($reviews as $review)
                                    @include($activeTemplate.'products.basic_review')
                                @empty
                                    <p class="review-empty-msg">{{ __($emptyMessage ?? __('No reviews yet. Be the first to review!')) }}</p>
                                @endforelse
                            </div>
                            @if(($reviewsTotal ?? 0) > 10)
                                <div class="review-load-more-wrap">
                                    <button type="button" class="cmn--btn btn-outline-secondary loadMoreReviews" id="loadMoreReviewsBtn">@lang('Load More')</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Multiple product sections: Related, Same Brand, You May Also Like (e-commerce standard) --}}
            @if ($relatedProduct->count() > 0 || ($sameBrandProducts ?? collect())->count() > 0 || ($youMayAlsoLike ?? collect())->count() > 0)
                <div class="pro-detail-more-sections mt-5">
                    @if ($relatedProduct->count() > 0)
                        <div class="pro-detail-related-section">
                            @include($activeTemplate . 'partials.product_carousel_section', [
                                'products' => $relatedProduct,
                                'sectionTitle' => view($activeTemplate . 'partials.icon', ['name' => 'th-large', 'class' => 'me-1'])->render() . __('Related Products'),
                                'sectionLink' => $product->category ? route('category.products', [slug($product->category->name), $product->category->id]) : null,
                                'sectionLinkText' => __('View all'),
                                'sectionClass' => 'pro-section pro-section--tight',
                                'sectionId' => 'related-products-section',
                                'priorityFirst' => false,
                            ])
                        </div>
                    @endif
                    @if(($sameBrandProducts ?? collect())->count() > 0)
                        <div class="pro-detail-related-section mt-4">
                            @include($activeTemplate . 'partials.product_carousel_section', [
                                'products' => $sameBrandProducts,
                                'sectionTitle' => view($activeTemplate . 'partials.icon', ['name' => 'tag', 'class' => 'me-1'])->render() . __('From same brand'),
                                'sectionLink' => null,
                                'sectionClass' => 'pro-section pro-section--tight',
                                'sectionId' => 'same-brand-section',
                                'priorityFirst' => false,
                            ])
                        </div>
                    @endif
                    @if(($youMayAlsoLike ?? collect())->count() > 0)
                        <div class="pro-detail-related-section mt-4">
                            @include($activeTemplate . 'partials.product_carousel_section', [
                                'products' => $youMayAlsoLike,
                                'sectionTitle' => view($activeTemplate . 'partials.icon', ['name' => 'heart', 'class' => 'me-1'])->render() . __('You may also like'),
                                'sectionLink' => route('products'),
                                'sectionLinkText' => __('Browse all'),
                                'sectionClass' => 'pro-section pro-section--tight',
                                'sectionId' => 'you-may-also-like-section',
                                'priorityFirst' => false,
                            ])
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Sticky Add to Cart bar (mobile only) --}}
        @php $detailMaxQtySticky = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->pluck('quantity')->max() : (int)$product->quantity; @endphp
        {{-- Sticky Buy Now / Add to Cart bar (mobile only, improves conversion & UX) --}}
        <div class="pro-detail-sticky-cart d-block d-md-none" id="proDetailStickyCart" role="region" aria-label="{{ __('Sticky purchase bar') }}">
            <div class="container d-flex align-items-center justify-content-between gap-2 py-2">
                <div class="pro-detail-sticky-cart-price">
                    <span class="cur_sym">{{ $general->cur_sym }}</span><span class="sticky-price-amount">{{ showAmount($detailPrice) }}</span>
                </div>
                @if($detailMaxQtySticky > 0)
                <button type="button" class="btn btn--base flex-grow-1 add-to-cart" data-product_id="{{ $product->id }}" id="addToCartStickyBtn">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart', 'class' => 'me-1']) @lang('Add To Cart')</button>
                <a href="#0" class="btn btn-outline--base buy-now" data-product_id="{{ $product->id }}">@include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1']) @lang('Buy Now')</a>
                @else
                <span class="btn btn-secondary disabled flex-grow-1">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart', 'class' => 'me-1']) @lang('Out Of Stock')</span>
                @endif
            </div>
        </div>
    </section>

    {{-- Product image lightbox: below lg = fullscreen (mobile/tablet) so only prev/next/close visible; desktop = centered --}}
    <div class="modal fade modal-fullscreen-lg-down" id="productImageLightbox" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered modal-xl product-lightbox-dialog">
            <div class="modal-content border-0 bg-transparent product-lightbox-content">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3 product-lightbox-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                <button type="button" class="pro-lightbox-prev position-absolute start-0 top-50 translate-middle-y m-2 rounded-circle border-0 bg-dark bg-opacity-50 text-white p-2 product-lightbox-btn" aria-label="@lang('Previous')">@include($activeTemplate . 'partials.icon', ['name' => 'angle-left', 'class' => 'fs-4'])</button>
                <div class="modal-body p-0 text-center product-lightbox-body">
                    <img id="productLightboxImg" src="" alt="{{ __($product->name) }}" class="img-fluid pro-lightbox-img">
                    {{-- Bottom close button: visible near price area on mobile/tablet --}}
                    <button type="button" class="product-lightbox-close-bottom rounded-circle border-0 bg-dark bg-opacity-70 text-white product-lightbox-btn" data-bs-dismiss="modal" aria-label="@lang('Close')">
                        @include($activeTemplate . 'partials.icon', ['name' => 'times', 'class' => 'fs-5'])
                    </button>
                </div>
                <button type="button" class="pro-lightbox-next position-absolute end-0 top-50 translate-middle-y m-2 rounded-circle border-0 bg-dark bg-opacity-50 text-white p-2 product-lightbox-btn" aria-label="@lang('Next')">@include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'fs-4'])</button>
            </div>
        </div>
    </div>

    @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_detail_below'])
@endsection

@push('script')
    {{-- Professional clickable star rating: default black, hover/click green, value to backend --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var starRating = document.getElementById("starRating");
            var ratingInput = document.getElementById("selectedRating");
            if (!starRating || !ratingInput) return;
            var stars = starRating.querySelectorAll(".star");
            function highlightStars(value) {
                var v = parseInt(value, 10);
                stars.forEach(function (star) {
                    if (parseInt(star.getAttribute("data-value"), 10) <= v) {
                        star.classList.add("selected");
                    }
                });
            }
            function resetStars() {
                stars.forEach(function (star) {
                    star.classList.remove("hovered", "selected");
                });
            }
            stars.forEach(function (star) {
                star.addEventListener("mouseover", function () {
                    resetStars();
                    highlightStars(this.getAttribute("data-value"));
                });
                star.addEventListener("mouseout", function () {
                    resetStars();
                    if (ratingInput.value) {
                        highlightStars(ratingInput.value);
                    }
                });
                star.addEventListener("click", function () {
                    ratingInput.value = this.getAttribute("data-value");
                    highlightStars(ratingInput.value);
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var chatBtn = document.getElementById('pdpChatToggleBtn');
            if (!chatBtn) return;
            function panelOpen() {
                var p = document.getElementById('contactPanelGlass');
                return p && p.classList.contains('is-open');
            }
            chatBtn.addEventListener('click', function (e) {
                e.preventDefault();
                if (chatBtn.classList.contains('pdp-chat-active') && panelOpen()) {
                    if (typeof window.closeContactPanel === 'function') window.closeContactPanel();
                    chatBtn.classList.remove('pdp-chat-active');
                    return;
                }
                var floatBtn = document.getElementById('contactFloatBtn');
                if (floatBtn) floatBtn.click();
                else if (typeof window.openContactPanel === 'function') window.openContactPanel();
                chatBtn.classList.add('pdp-chat-active');
            });
            document.addEventListener('click', function (ev) {
                if (ev.target && ev.target.id === 'contactPanelBackdrop') chatBtn.classList.remove('pdp-chat-active');
                if (ev.target && ev.target.closest && ev.target.closest('#contactPanelClose')) chatBtn.classList.remove('pdp-chat-active');
            });
            document.addEventListener('keydown', function (ev) {
                if (ev.key === 'Escape' && panelOpen()) chatBtn.classList.remove('pdp-chat-active');
            });
        });
    </script>
    <script>
        (function initProductDetails() {
            if (typeof jQuery === 'undefined') {
                setTimeout(initProductDetails, 25);
                return;
            }
            (function($) {
                "use strict";

                // Move product lightbox to body so it escapes main's stacking context (main has z-index:0 on mobile/tablet in glass-header.css).
                // Otherwise the modal stays under the backdrop and no buttons work on touch devices.
                var productLightboxEl = document.getElementById('productImageLightbox');
                if (productLightboxEl && productLightboxEl.parentNode && productLightboxEl.parentNode !== document.body) {
                    document.body.appendChild(productLightboxEl);
                }

                var showReviews = 10;
                var reviewSort = 'recent';

                // Review tab: prevent default (stop scroll-to-top); scroll to Review section first, then show tab
                $(document).on('click', 'a.review-tab-link, a[href="#tab-review"]', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var linkEl = this;
                    var tabsWrap = document.getElementById('product-detail-tabs');
                    var pane = document.getElementById('tab-review');
                    if (!tabsWrap && !pane) return;
                    var scrollTarget = tabsWrap || pane;
                    scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        var tab = bootstrap.Tab.getOrCreateInstance(linkEl);
                        tab.show();
                    } else {
                        $(linkEl).tab('show');
                    }
                });
                $(document).on('shown.bs.tab', 'a[href="#tab-review"]', function() {
                    var el = document.getElementById('product-detail-tabs') || document.getElementById('tab-review');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                $('#reviewSortSelect').on('change', function() {
                    reviewSort = $(this).val();
                    showReviews = 0;
                    $.get('{{ route('fetch.reviews', $product->id) }}', { skip: 0, sort: reviewSort }, function(r) {
                        if (r.success) {
                            $('#reviewListContainer').html(r.html || '<p class="review-empty-msg">{{ __("No reviews yet.") }}</p>');
                            showReviews = 10;
                            $('#loadMoreReviewsBtn').toggle(!!r.has_more);
                        }
                    });
                });

                $(document).on('click', '.loadMoreReviews', function(e) {
                    e.preventDefault();
                    var $btn = $(this);
                    $btn.prop('disabled', true).addClass('btn-disabled');
                    $.get('{{ route('fetch.reviews', $product->id) }}', { skip: showReviews, sort: reviewSort }, function(r) {
                        if (r.success && r.html) {
                            $('#reviewListContainer').append(r.html);
                            showReviews += 10;
                            $btn.toggle(!!r.has_more).prop('disabled', false).removeClass('btn-disabled');
                        } else {
                            $btn.prop('disabled', false).removeClass('btn-disabled');
                        }
                    });
                });

                $('#productReviewForm').on('submit', function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var $btn = $('#reviewSubmitBtn');
                    var ratingVal = $('#selectedRating').val();
                    if (!ratingVal || parseInt(ratingVal, 10) < 1 || parseInt(ratingVal, 10) > 5) {
                        if (typeof notify === 'function') notify('error', '{{ __("Please select a rating (1-5 stars).") }}');
                        return;
                    }
                    $btn.prop('disabled', true);
                    var fd = new FormData($form[0]);
                    var submitUrl = $form.attr('action') || '{{ route('user.review.store', $product->id) }}';
                    $.ajax({
                        url: submitUrl,
                        type: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function(r) {
                            if (r && r.success === true) {
                                if (typeof notify === 'function') notify('success', r.message);
                                $('#reviewFormWrap').html('<p class="review-submit-success">' + (r.message || '{{ __("Review submitted successfully. Thank you!") }}') + '</p>');
                                setTimeout(function() { location.reload(); }, 1500);
                            } else {
                                var errMsg = (r && r.message) ? r.message : '{{ __("Please complete your profile to write a review, or try again later.") }}';
                                if (typeof notify === 'function') notify('error', errMsg);
                                $btn.prop('disabled', false);
                            }
                        },
                        error: function(xhr) {
                            var msg = '{{ __("Something went wrong. Try again.") }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errs = xhr.responseJSON.errors;
                                msg = typeof errs === 'object' ? (Object.values(errs).flat().join(' ') || msg) : msg;
                            }
                            if (xhr.status === 419) {
                                msg = '{{ __("Session expired. Please refresh the page and try again.") }}';
                                if (typeof notify === 'function') notify('error', msg);
                                setTimeout(function() { location.reload(); }, 2000);
                                return;
                            }
                            if (xhr.status === 401) {
                                msg = '{{ __("Please login again to submit your review.") }}';
                                window.location.href = '{{ route('user.login') }}?redirect=' + encodeURIComponent(window.location.href);
                                return;
                            }
                            if (typeof notify === 'function') notify('error', msg);
                            $btn.prop('disabled', false);
                        },
                        complete: function() { $btn.prop('disabled', false); }
                    });
                });

                $(document).on('click', '.btn-helpful-review', function() {
                    var $btn = $(this);
                    var id = $btn.data('review-id');
                    if (!$btn.hasClass('disabled')) {
                        var helpfulUrl = '{{ route("review.helpful", ["id" => 0]) }}'.replace('0', id);
                        $.post(helpfulUrl, { _token: '{{ csrf_token() }}' }, function(r) {
                            if (r.success) {
                                $btn.find('.review-item__helpful-count').text(r.helpful_count);
                                $btn.addClass('disabled');
                            }
                        });
                    }
                });

                var maxQty = {{ $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->pluck('quantity')->max() : (int)$product->quantity }};
                function getMaxQty() {
                    var sel = document.getElementById('productVariantId');
                    if (sel && sel.value) {
                        var opt = $(sel).find('option:selected');
                        return opt.data('qty') !== undefined ? parseInt(opt.data('qty'), 10) : maxQty;
                    }
                    return maxQty;
                }

                $('.single-add-cart-area .cart-decrease').on('click', function(e) {
                    e.preventDefault();
                    var inp = $('.single-add-cart-area .productQuantity');
                    var q = parseInt(inp.val(), 10) || 1;
                    if (q > 1) { inp.val(q - 1); } else { if (typeof notify === 'function') notify('error', '{{ __("You have to order a minimum amount of one.") }}'); }
                });
                $('.single-add-cart-area .cart-increase').on('click', function(e) {
                    e.preventDefault();
                    var inp = $('.single-add-cart-area .productQuantity');
                    var limit = getMaxQty();
                    var q = parseInt(inp.val(), 10) || 0;
                    if (q < limit) { inp.val(q + 1); } else { if (typeof notify === 'function') notify('error', '{{ __("You can not order more than the available stock.") }}'); }
                });
                $('.single-add-cart-area input[name="quantity"]').on('change focusout', function() {
                    var quantity = parseInt($(this).val(), 10);
                    var limit = getMaxQty();
                    if (quantity <= 0 || quantity > limit) {
                        $(this).val(Math.min(Math.max(1, quantity || 1), limit));
                        if ($('#productStockDisplay').length) $('#productStockDisplay').text(limit);
                    }
                });
                var basePrice = {{ $detailPrice }};
                var curSym = '{{ $general->cur_sym ?? "৳" }}';
                function updateDisplayedPrice(price) {
                    var p = price !== undefined && price !== '' ? parseFloat(price) : basePrice;
                    var formatted = (typeof showAmount === 'function') ? showAmount(p) : p.toFixed(2);
                    $('.pro-detail-special-price.product-price').html(curSym + formatted);
                    $('.sticky-price-amount').text(formatted);
                }
                $('#productVariantId').on('change', function() {
                    var opt = $(this).find('option:selected');
                    var qty = opt.data('qty');
                    var price = opt.data('price');
                    if ($('#productStockDisplay').length && qty !== undefined) $('#productStockDisplay').text(qty);
                    if (price !== undefined && price !== '') updateDisplayedPrice(price);
                    else updateDisplayedPrice(basePrice);
                    var vid = $(this).val();
                    $('.pro-detail-size-btn[data-variant-id]').removeClass('selected');
                    if (vid) $('.pro-detail-size-btn[data-variant-id="' + vid + '"]').addClass('selected');
                });

                function toggleCustomSizeWrap(isCustom) {
                    var wrap = document.getElementById('customSizeWrap');
                    var inp = document.getElementById('customSizeInput');
                    if (!wrap || !inp) return;
                    if (isCustom) {
                        wrap.style.display = 'block';
                        inp.removeAttribute('disabled');
                        inp.setAttribute('required', 'required');
                    } else {
                        wrap.style.display = 'none';
                        inp.value = '';
                        inp.removeAttribute('required');
                        inp.setAttribute('disabled', 'disabled');
                    }
                }
                // Size buttons: sync with variant select and show tick; show Custom Size input when NO selected
                $(document).on('click', '.pro-detail-size-btn[data-variant-id]:not(:disabled)', function() {
                    var vId = $(this).data('variant-id');
                    var sizeKey = $(this).data('size');
                    var qty = $(this).data('qty');
                    $('.pro-detail-size-btn[data-variant-id]').removeClass('selected');
                    $(this).addClass('selected');
                    var sel = document.getElementById('productVariantId');
                    if (sel) { sel.value = vId; $(sel).trigger('change'); }
                    if ($('#productStockDisplay').length && qty !== undefined) $('#productStockDisplay').text(qty);
                    toggleCustomSizeWrap(sizeKey === 'NO');
                });
                $(document).on('click', '.pro-detail-size-btn[data-size]', function() {
                    var size = $(this).data('size');
                    $('.pro-detail-size-btn[data-size]').removeClass('selected');
                    $(this).addClass('selected');
                    $('#selectedSizeInput').val(size);
                    toggleCustomSizeWrap(size === 'NO');
                });
                $('#productVariantId').on('change', function() {
                    var opt = $(this).find('option:selected');
                    var sizeKey = opt.data('size');
                    toggleCustomSizeWrap(sizeKey === 'NO');
                });

                var initialVariant = $('#productVariantId').val();
                if (initialVariant) {
                    $('.pro-detail-size-btn[data-variant-id="' + initialVariant + '"]').addClass('selected');
                    var opt = $('#productVariantId').find('option:selected');
                    if (opt.length && opt.data('size') === 'NO') toggleCustomSizeWrap(true);
                }

                // Thumbnail gallery: click (always) + hover only on non-touch devices to set main image (WebP when available for faster load)
                var mainImg = document.getElementById('proDetailMainImg');
                var thumbSrcs = [
                    '{{ $product->imageShowWebP() }}'
                    @foreach ($product->gallery ?? [] as $gallery)
                        , '{{ getImageWebP(getFilePath('productGallery') . '/' . $gallery, getFileSize('productGallery')) }}'
                    @endforeach
                ];
                var zoomPreviewRef = null;
                var canHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;
                function setMainImage(idx) {
                    if (thumbSrcs[idx] === undefined) return;
                    $('#proDetailThumbs .thumb-item').removeClass('active');
                    $('#proDetailThumbs .thumb-item[data-index="' + idx + '"]').addClass('active');
                    if (mainImg) {
                        mainImg.src = thumbSrcs[idx];
                        mainImg.setAttribute('data-zoom-src', thumbSrcs[idx]);
                    }
                    if (zoomPreviewRef && zoomPreviewRef.classList.contains('visible'))
                        zoomPreviewRef.style.backgroundImage = 'url(' + thumbSrcs[idx] + ')';
                }
                $('#proDetailThumbs .thumb-item').on('click', function() { setMainImage($(this).data('index')); });
                if (canHover) {
                    $('#proDetailThumbs .thumb-item').on('mouseenter', function() { setMainImage($(this).data('index')); });
                }

                // Thumb prev/next: change main image to previous/next in gallery (not scroll only)
                var thumbsEl = document.getElementById('proDetailThumbs');
                var totalThumbs = thumbSrcs.length;
                if (totalThumbs <= 1) {
                    $('.pro-detail-thumb-prev, .pro-detail-thumb-next').attr('disabled', true).addClass('d-none');
                    $('#productImageLightbox .pro-lightbox-prev, #productImageLightbox .pro-lightbox-next').addClass('d-none');
                }
                function getCurrentIndex() {
                    var active = $('#proDetailThumbs .thumb-item.active');
                    return active.length ? (parseInt(active.attr('data-index'), 10) || 0) : 0;
                }
                function scrollThumbIntoView(idx) {
                    if (!thumbsEl || thumbSrcs[idx] === undefined) return;
                    var item = thumbsEl.querySelector('.thumb-item[data-index="' + idx + '"]');
                    if (item) item.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
                }
                $('.pro-detail-thumb-prev').on('click', function(e) {
                    e.preventDefault();
                    if (totalThumbs <= 1) return;
                    var idx = (getCurrentIndex() - 1 + totalThumbs) % totalThumbs;
                    setMainImage(idx);
                    scrollThumbIntoView(idx);
                });
                $('.pro-detail-thumb-next').on('click', function(e) {
                    e.preventDefault();
                    if (totalThumbs <= 1) return;
                    var idx = (getCurrentIndex() + 1) % totalThumbs;
                    setMainImage(idx);
                    scrollThumbIntoView(idx);
                });

                // Hover zoom: optimized with rAF (desktop/laptop only – skipped on touch devices for performance)
                var zoomArea = document.getElementById('proDetailZoomArea');
                var zoomLens = document.getElementById('proDetailZoomLens');
                var zoomPreview = document.getElementById('proDetailZoomPreview');
                var zoomWrap = document.getElementById('proDetailZoomPreviewWrap');
                if (canHover && zoomArea && mainImg && zoomLens && zoomPreview) {
                    zoomPreviewRef = zoomPreview;
                    var zoomLevel = 2.2;
                    var zoomRect = null;
                    var zoomImgUrl = '';
                    var zoomRaf = null;
                    function updateZoomPreview(e) {
                        if (!zoomRect) return;
                        var x = e.clientX - zoomRect.left;
                        var y = e.clientY - zoomRect.top;
                        var w = zoomRect.width;
                        var h = zoomRect.height;
                        var lensSize = Math.min(w, h) * 0.28;
                        var l = Math.max(0, Math.min(x - lensSize/2, w - lensSize));
                        var t = Math.max(0, Math.min(y - lensSize/2, h - lensSize));
                        zoomLens.style.width = lensSize + 'px';
                        zoomLens.style.height = lensSize + 'px';
                        zoomLens.style.left = l + 'px';
                        zoomLens.style.top = t + 'px';
                        var pw = zoomPreview.offsetWidth || (zoomWrap ? zoomWrap.offsetWidth : 0) || w;
                        var ph = zoomPreview.offsetHeight || (zoomWrap ? zoomWrap.offsetHeight : 0) || h;
                        var bgW = w * zoomLevel;
                        var bgH = h * zoomLevel;
                        var posX = -(x * zoomLevel - pw / 2);
                        var posY = -(y * zoomLevel - ph / 2);
                        zoomPreview.style.backgroundSize = bgW + 'px ' + bgH + 'px';
                        zoomPreview.style.backgroundPosition = posX + 'px ' + posY + 'px';
                    }
                    zoomArea.addEventListener('mouseenter', function() {
                        zoomRect = zoomArea.getBoundingClientRect();
                        zoomImgUrl = mainImg.getAttribute('data-zoom-src') || mainImg.src || '';
                        if (zoomImgUrl) zoomPreview.style.backgroundImage = 'url(' + zoomImgUrl + ')';
                        zoomLens.classList.add('visible');
                        zoomPreview.classList.add('visible');
                    });
                    zoomArea.addEventListener('mouseleave', function() {
                        zoomRect = null;
                        if (zoomRaf) cancelAnimationFrame(zoomRaf);
                        zoomLens.classList.remove('visible');
                        zoomPreview.classList.remove('visible');
                    });
                    zoomArea.addEventListener('mousemove', function(e) {
                        if (!zoomRect) return;
                        if (zoomRaf) cancelAnimationFrame(zoomRaf);
                        zoomRaf = requestAnimationFrame(function() {
                            zoomRaf = null;
                            updateZoomPreview(e);
                        });
                    });
                }

                // Click/tap main image to open lightbox (works on desktop + mobile/tablet)
                var currentLightboxIdx = 0;
                var lightboxJustOpenedByTouch = false;
                var lightboxHistoryPushed = false;
                function openProductLightbox() {
                    currentLightboxIdx = $('#proDetailThumbs .thumb-item.active').length ? ($('#proDetailThumbs .thumb-item.active').data('index') || 0) : 0;
                    var src = thumbSrcs[currentLightboxIdx];
                    if (src && $('#productImageLightbox').length) {
                        $('#productLightboxImg').attr('src', src);
                        var m = document.getElementById('productImageLightbox');
                        if (m && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            if (!lightboxHistoryPushed && window.history && window.history.pushState) {
                                try {
                                    window.history.pushState({ productImageLightbox: true }, '', window.location.href);
                                    lightboxHistoryPushed = true;
                                } catch (e) {}
                            }
                            bootstrap.Modal.getOrCreateInstance(m).show();
                        }
                    }
                }
                $('#proDetailZoomArea, #proDetailMainImg').on('click', function(e) {
                    e.preventDefault();
                    if (lightboxJustOpenedByTouch) { lightboxJustOpenedByTouch = false; return; }
                    openProductLightbox();
                });
                // Touch: open lightbox on touchend so mobile/tablet get immediate full screen without 300ms delay
                $('#proDetailZoomArea, #proDetailMainImg').on('touchend', function(e) {
                    e.preventDefault();
                    lightboxJustOpenedByTouch = true;
                    openProductLightbox();
                });
                // When lightbox opens/closes, toggle a class so CSS can lock background scroll on touch devices
                productLightboxEl = document.getElementById('productImageLightbox');
                if (productLightboxEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    productLightboxEl.addEventListener('shown.bs.modal', function () {
                        document.documentElement.classList.add('pro-lightbox-open');
                        document.body.classList.add('pro-lightbox-open');
                    });
                    productLightboxEl.addEventListener('hidden.bs.modal', function () {
                        document.documentElement.classList.remove('pro-lightbox-open');
                        document.body.classList.remove('pro-lightbox-open');
                        lightboxHistoryPushed = false;
                    });
                }
                function goLightboxPrev() {
                    currentLightboxIdx = (currentLightboxIdx - 1 + thumbSrcs.length) % thumbSrcs.length;
                    $('#productLightboxImg').attr('src', thumbSrcs[currentLightboxIdx]);
                    setMainImage(currentLightboxIdx);
                }
                function goLightboxNext() {
                    currentLightboxIdx = (currentLightboxIdx + 1) % thumbSrcs.length;
                    $('#productLightboxImg').attr('src', thumbSrcs[currentLightboxIdx]);
                    setMainImage(currentLightboxIdx);
                }
                // Delegated click + touch so prev/next work on desktop and mobile/tablet (including small UI)
                function handleLightboxPrev(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goLightboxPrev();
                }
                function handleLightboxNext(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goLightboxNext();
                }
                $(document).on('click', '#productImageLightbox .pro-lightbox-prev', handleLightboxPrev);
                $(document).on('click', '#productImageLightbox .pro-lightbox-next', handleLightboxNext);
                $(document).on('touchend', '#productImageLightbox .pro-lightbox-prev', handleLightboxPrev);
                $(document).on('touchend', '#productImageLightbox .pro-lightbox-next', handleLightboxNext);
                // touchstart so button gets the event before any parent (avoids body capturing on some devices)
                $(document).on('touchstart', '#productImageLightbox .pro-lightbox-prev', function(e) { e.stopPropagation(); });
                $(document).on('touchstart', '#productImageLightbox .pro-lightbox-next', function(e) { e.stopPropagation(); });

                // Handle mobile back button: first press closes lightbox instead of leaving the page
                window.addEventListener('popstate', function() {
                    var m = document.getElementById('productImageLightbox');
                    if (!m || typeof bootstrap === 'undefined' || !bootstrap.Modal) return;
                    var modalInstance = bootstrap.Modal.getInstance(m);
                    var isVisible = m.classList.contains('show');
                    if (isVisible) {
                        lightboxHistoryPushed = false;
                        modalInstance.hide();
                    }
                });

                // Copy link
                $('.copy-link-btn, .pdp-copy-short').on('click', function() {
                    var url = $(this).data('url');
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(url).then(function() { if (typeof notify === 'function') notify('success', '{{ __("Link copied!") }}'); });
                    } else {
                        var ta = document.createElement('textarea'); ta.value = url; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
                        if (typeof notify === 'function') notify('success', '{{ __("Link copied!") }}');
                    }
                });

                // Compare add (AJAX)
                $('.btn-compare').on('click', function(e) {
                    var href = $(this).attr('href');
                    if (href && href.indexOf('compare') !== -1 && !$(this).hasClass('no-ajax')) {
                        e.preventDefault();
                        $.post('{{ route('compare.add') }}', { product_id: {{ $product->id }}, _token: '{{ csrf_token() }}' }, function(r) {
                            if (r.success) {
                                if (typeof notify === 'function') notify('success', r.message);
                                window.location.href = '{{ route('user.compare') }}';
                            } else {
                                if (r.message && r.message.toLowerCase().indexOf('already') !== -1) {
                                    window.location.href = '{{ route('user.compare') }}';
                                } else {
                                    if (typeof notify === 'function') notify('error', r.message);
                                }
                            }
                        }).fail(function() { window.location.href = '{{ route('user.compare') }}'; });
                    }
                });

                (function(){
                    var productId = {{ $product->id }};
                    var key = 'recently_viewed_ids';
                    var max = 20, days = 7;
                    try {
                        var raw = document.cookie.split(';').filter(function(c){ return c.trim().indexOf(key + '=') === 0; })[0];
                        var arr = raw ? JSON.parse(decodeURIComponent((raw.split('=')[1] || '[]').trim())) : [];
                        arr = arr.filter(function(id){ return id !== productId; });
                        arr.unshift(productId);
                        arr = arr.slice(0, max);
                        document.cookie = key + '=' + encodeURIComponent(JSON.stringify(arr)) + ';path=/;max-age=' + (days * 86400) + ';SameSite=Lax';
                    } catch (e) {}
                })();
            })(jQuery);
        })();
    </script>
@endpush
