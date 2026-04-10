@php
    $products = $products ?? collect();
    $sectionTitle = $sectionTitle ?? '';
    $sectionLink = $sectionLink ?? null;
    $sectionLinkText = $sectionLinkText ?? __('View All');
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
    $hasProducts = $products->isNotEmpty();
    $autoScrollAttr = $hasProducts ? '1' : '0';
@endphp
<section class="{{ $sectionClass }} product-carousel-section mb-8 sm:mb-10" id="{{ $sectionId }}" data-section-id="{{ $sectionId }}" @if($sectionKey) data-section-key="{{ $sectionKey }}" data-load-more-offset="{{ \App\Services\HomepageDataService::INITIAL_PAGE_SIZE }}" @endif>
    <div class="w-full">
        @if($sectionTitle)
            <div class="mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="flex min-w-0 items-center gap-2 text-lg font-bold tracking-tight text-slate-900 sm:text-xl md:text-2xl">
                    {!! $sectionTitle !!}
                </h2>
                @if($sectionLink)
                    <a href="{{ $sectionLink }}" class="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-emerald-600 transition hover:text-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 rounded-lg px-1 py-0.5">
                        {{ $sectionLinkText }}
                        @include($activeTemplate . 'partials.icon', ['name' => 'chevron-right', 'class' => 'h-4 w-4'])
                    </a>
                @endif
            </div>
        @endif

        @if($hasProducts)
            <div class="product-carousel-wrap">
                <div class="product-carousel-track">
                    <div class="product-line-flex-row product-line-flex-row--snap-mand home-product-strip scroll-smooth" data-auto-scroll="{{ $autoScrollAttr }}" data-interval-sec="{{ $intervalSec }}">
                        @foreach($products as $product)
                            @php $fp = ($priorityFirst && $loop->iteration <= 4) ? 'high' : 'low'; @endphp
                            <div class="product-card-col product-card-col--home product-carousel__item">
                                @include($activeTemplate . 'products.partials.home_product_card', ['product' => $product, 'general' => $general, 'activeTemplate' => $activeTemplate, 'fetchpriority' => $fp])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="product-carousel-wrap">
                <div class="product-carousel-track">
                    <div class="product-line-flex-row product-line-flex-row--snap-mand home-product-strip scroll-smooth" data-auto-scroll="0" data-interval-sec="{{ $intervalSec }}">
                        @include($activeTemplate . 'partials.home_product_skeleton_row', ['skeletonCount' => 6])
                    </div>
                </div>
            </div>
            <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('products') }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:from-emerald-500 hover:to-teal-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    @lang('Browse Products')
                </a>
            </div>
        @endif
    </div>
</section>
