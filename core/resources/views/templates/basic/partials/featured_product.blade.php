@php
    $products = $homeData['featuredProducts'] ?? collect();
    $sectionLabel = $sectionLabel ?? __('Featured Products');
@endphp
@include($activeTemplate . 'partials.product_carousel_section', [
    'products' => $products,
    'sectionKey' => 'featured',
    'sectionTitle' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #eab308;" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' . e($sectionLabel),
    'sectionLink' => route('products.featured'),
    'sectionLinkText' => __('Show All'),
    'sectionClass' => 'pro-section pro-section--tight latest-products-section wow fadeInUp',
    'sectionId' => 'featured-products-section',
    'carouselIntervalSec' => $carouselIntervalSec ?? null,
    'carouselSpeedMs' => $carouselSpeedMs ?? null,
])

