@php
    $settings = ($homeSectionData['settings'] ?? null) ?: (object)[];
    if (!($settings->social_proof_enabled ?? 1)) return;
    $reviews = $homeData['reviews'] ?? collect();
    $topRatedProducts = $homeData['topRatedProducts'] ?? collect();
@endphp
<section class="pro-section pro-section--tight social-proof-section wow fadeInUp" data-wow-duration="0.4s" data-wow-delay="0.25s">
    <div class="container-fluid px-3 px-lg-4">
        @if(($settings->reviews_slider_enabled ?? 1) && $reviews->isNotEmpty())
        <div class="pro-section__head">
            <h2 class="pro-section__title">@include($activeTemplate . 'partials.icon', ['name' => 'quote-right']) @lang('Customer Reviews')</h2>
        </div>
        <div class="social-proof-reviews-slider owl-carousel owl-theme">
            @foreach($reviews as $review)
                <div class="social-proof-review-item card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            {!! showProductRatings($review->rating ?? 0) !!}
                            <span class="small text-muted">({{ $review->created_at ? $review->created_at->diffForHumans() : '' }})</span>
                        </div>
                        <p class="mb-2 small">{{ \Illuminate\Support\Str::limit($review->review ?? '', 120) }}</p>
                        @if($review->product)
                            <a href="{{ product_detail_url($review->product) }}" class="small text--base">{{ \Illuminate\Support\Str::limit($review->product->name, 30) }}</a>
                        @endif
                        @if($review->user)
                            <div class="mt-2 small text-muted">— {{ $review->user->username ?? __('Customer') }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        @if(($settings->top_rated_enabled ?? 1) && $topRatedProducts->isNotEmpty())
            @include($activeTemplate . 'partials.product_carousel_section', [
                'products' => $topRatedProducts,
                'sectionTitle' => view($activeTemplate . 'partials.icon', ['name' => 'star'])->render() . ' ' . __('Top Rated Products'),
                'sectionLink' => route('home') . '#top-rated',
                'sectionLinkText' => __('View All'),
                'sectionClass' => 'pro-section pro-section--tight social-proof-section wow fadeInUp pro-section__head-gap',
                'sectionId' => 'top-rated',
            ])
        @endif
    </div>
</section>

@push('script')
@if(($settings->reviews_slider_enabled ?? 1) && $reviews->isNotEmpty())
<script>
(function(){
    if (typeof jQuery !== 'undefined' && jQuery.fn.owlCarousel && document.querySelector('.social-proof-reviews-slider')) {
        jQuery('.social-proof-reviews-slider').owlCarousel({
            loop: true,
            margin: 16,
            nav: true,
            dots: true,
            items: 1,
            responsive: { 576: { items: 2 }, 992: { items: 3 } }
        });
    }
})();
</script>
@endif
@endpush
