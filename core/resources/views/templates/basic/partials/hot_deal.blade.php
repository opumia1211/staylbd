@php
    $products = $homeData['hotDealProducts'] ?? collect();
    $sectionLabel = $sectionLabel ?? __('Hot Deals');
@endphp
@include($activeTemplate . 'partials.product_carousel_section', [
    'products' => $products,
    'sectionKey' => 'hot_deal',
    'sectionTitle' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #f97316;" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2s1 3-1.5 5.5S8 10 8 13a4 4 0 1 0 8 0c0-2-1.1-3.7-1.1-3.7S17 11 17 14a6 6 0 1 1-12 0c0-4.6 3.2-6.7 5.3-8.8C12.2 3.4 13 2 13 2z"/></svg>' . e($sectionLabel),
    'sectionLink' => route('product.hot.deal'),
    'sectionLinkText' => __('Show All'),
    'sectionClass' => 'pro-section pro-section--tight hot-deal-section wow fadeInUp',
    'sectionId' => 'hot-deal-section',
    'carouselIntervalSec' => $carouselIntervalSec ?? null,
    'carouselSpeedMs' => $carouselSpeedMs ?? null,
])
