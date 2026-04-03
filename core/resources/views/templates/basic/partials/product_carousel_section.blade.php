@php
    $products = $products ?? collect();
    $sectionTitle = $sectionTitle ?? '';
    $sectionLink = $sectionLink ?? null;
    $sectionLinkText = $sectionLinkText ?? __('Show All');
    $sectionId = $sectionId ?? 'product-carousel-' . uniqid();
    $sectionClass = $sectionClass ?? 'pro-section pro-section--tight';
    $sectionKey = $sectionKey ?? null;
    $priorityFirst = $priorityFirst ?? true;
    
    $_intervalOverride = isset($carouselIntervalSec) && $carouselIntervalSec !== null && $carouselIntervalSec !== ''
        ? max(2, min(30, (int) $carouselIntervalSec))
        : null;
    $intervalSec = $_intervalOverride !== null
        ? $_intervalOverride
        : (function_exists('getSectionScrollIntervalSeconds') ? getSectionScrollIntervalSeconds($sectionKey ?? null) : 4);
@endphp
<section class="{{ $sectionClass }} product-carousel-section" id="{{ $sectionId }}" data-section-id="{{ $sectionId }}" @if($sectionKey) data-section-key="{{ $sectionKey }}" data-load-more-offset="{{ \App\Services\HomepageDataService::INITIAL_PAGE_SIZE }}" @endif>
    <div class="w-full">
        @if($sectionTitle)
            <div class="pro-section__head">
                <h2 class="pro-section__title">{!! $sectionTitle !!}</h2>
                @if($sectionLink)
                    <a href="{{ $sectionLink }}" class="pro-section__link">{{ $sectionLinkText }}</a>
                @endif
            </div>
        @endif

        @if($products->isEmpty())
            <div class="product-carousel-section__empty text-center w-full py-10" style="display: flex; justify-content: center; align-items: center; min-height: 150px;">
                <div class="pro-section__empty-inner flex flex-col items-center justify-center opacity-70 text-gray-500">
                    <svg class="w-12 h-12 mb-3 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p class="mb-0 text-gray-500 font-medium tracking-wide">@lang('No products yet.')</p>
                </div>
            </div>
        @else
            <div class="product-carousel-wrap">
                <div class="product-carousel-track">
                    <div class="product-line-flex-row" data-auto-scroll="1" data-interval-sec="{{ $intervalSec }}">
                        @foreach($products as $product)
                            @php $fp = ($priorityFirst && $loop->iteration <= 4) ? 'high' : 'low'; @endphp
                            <div class="product-card-col product-card-col--home product-carousel__item">
                                @include($activeTemplate . 'products.partials.card', ['product' => $product, 'general' => $general, 'fetchpriority' => $fp])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
