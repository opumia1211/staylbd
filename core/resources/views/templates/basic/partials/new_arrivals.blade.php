@php
    $products = $homeData['newArrivals'] ?? collect();
    $sectionLabel = $sectionLabel ?? __('New Arrivals');
@endphp
@include($activeTemplate . 'partials.product_carousel_section', [
    'products' => $products,
    'sectionKey' => 'new_arrivals',
    'sectionTitle' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #a855f7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.912 5.813a2 2 0 001.275 1.275L21 12l-5.813 1.912a2 2 0 00-1.275 1.275L12 21l-1.912-5.813a2 2 0 00-1.275-1.275L3 12l5.813-1.912a2 2 0 001.275-1.275L12 3z"></path></svg>' . e($sectionLabel),
    'sectionLink' => route('products') . '?sort=newest',
    'sectionLinkText' => __('Show All'),
    'sectionClass' => 'pro-section pro-section--tight new-arrivals-section wow fadeInUp',
    'sectionId' => 'new-arrivals-section',
    'carouselIntervalSec' => $carouselIntervalSec ?? null,
    'carouselSpeedMs' => $carouselSpeedMs ?? null,
])
