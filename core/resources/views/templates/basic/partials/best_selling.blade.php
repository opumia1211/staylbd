@php
    $products = $homeData['bestSellingProducts'] ?? collect();
    $sectionLabel = $sectionLabel ?? __('Best Selling Products');
@endphp
@include($activeTemplate . 'partials.product_carousel_section', [
    'products' => $products,
    'sectionKey' => 'best_selling',
    'sectionTitle' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #ef4444;" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>' . e($sectionLabel),
    'sectionLink' => route('products.best.selling'),
    'sectionLinkText' => __('View All'),
    'sectionClass' => 'pro-section pro-section--tight best-selling-section wow fadeInUp',
    'sectionId' => 'best-selling-section',
    'carouselIntervalSec' => $carouselIntervalSec ?? null,
    'carouselSpeedMs' => $carouselSpeedMs ?? null,
])
