@php
    $products = $homeData['trendingBest'] ?? collect();
    $sectionLabel = $sectionLabel ?? __('Trending Now');
@endphp
@include($activeTemplate . 'partials.product_carousel_section', [
    'products' => $products,
    'sectionKey' => 'trending',
    'sectionTitle' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #3b82f6;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>' . e($sectionLabel),
    'sectionLink' => route('products') . '?sort=popular',
    'sectionLinkText' => __('Show All'),
    'sectionClass' => 'pro-section pro-section--tight trending-now-section wow fadeInUp',
    'sectionId' => 'trending-now-section',
    'carouselIntervalSec' => $carouselIntervalSec ?? null,
    'carouselSpeedMs' => $carouselSpeedMs ?? null,
])
