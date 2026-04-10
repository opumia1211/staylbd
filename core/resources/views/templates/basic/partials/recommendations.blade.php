@php
    $settings = ($homeSectionData['settings'] ?? null) ?: (object)[];
    if (!($settings->recommendation_enabled ?? 1)) return;
    $recentIds = [];
    if ($settings->recently_viewed_enabled ?? 1) {
        $recentIds = array_slice(array_filter((array) json_decode(request()->cookie('recently_viewed_ids', '[]'), true)), 0, 8);
    }
    $recentlyViewed = collect();
    if ($recentIds) {
        $recentlyViewed = \App\Models\Product::available()->whereIn('id', $recentIds)->with(['category:id,name'])->get()->sortBy(function ($p) use ($recentIds) {
            $pos = array_search($p->id, $recentIds);
            return $pos === false ? 999 : $pos;
        })->take(8)->values();
    }
    $recommended = $homeData['recommendedProducts'] ?? collect();
    $sectionLabel = $sectionLabel ?? __('Recommended For You');
@endphp
@if(($settings->recently_viewed_enabled ?? 1) && $recentlyViewed->isNotEmpty())
    @include($activeTemplate . 'partials.product_carousel_section', [
        'products' => $recentlyViewed,
        'sectionTitle' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #60a5fa;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>' . __('Recently Viewed'),
        'sectionLink' => null,
        'sectionClass' => 'pro-section pro-section--tight recommendations-section wow fadeInUp',
        'sectionId' => 'recently-viewed-section',
    ])
@endif

@if($recommended->isNotEmpty())
    @include($activeTemplate . 'partials.product_carousel_section', [
        'products' => $recommended,
        'sectionKey' => 'recommended',
        'sectionTitle' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #ec4899;" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>' . e($sectionLabel),
        'sectionLink' => route('products.best.selling'),
        'sectionLinkText' => __('View All'),
        'sectionClass' => 'pro-section pro-section--tight recommendations-section pro-section__head-gap wow fadeInUp',
        'sectionId' => 'recommended-section',
        'carouselIntervalSec' => $carouselIntervalSec ?? null,
        'carouselSpeedMs' => $carouselSpeedMs ?? null,
    ])
@endif
