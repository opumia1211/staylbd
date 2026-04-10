@php
    $homeSectionData = $homeSectionData ?? getCachedHomeSectionData();
    $settings = $homeSectionData['settings'] ?? (object)[];
    if (!($settings->power_zone_enabled ?? 1)) return;
    $topFeaturesFromDb = $homeSectionData['top_features'] ?? getPowerZoneTopFeatures();
    $limit = (int)($settings->flash_deals_limit ?? 8);
    $flashProducts = isset($todayDealProducts) ? $todayDealProducts->take($limit) : ($homeData['recommendedProducts'] ?? collect())->take($limit);
    $trendingBest = $homeData['trendingBest'] ?? collect();
    $newArrivals = $homeData['newArrivals'] ?? collect();
    $categories = $homeData['categories'] ?? collect();
    $topCategories = $homeData['topCategories'] ?? collect();
    $topBrands = $homeData['topBrands'] ?? collect();
    $quickServices = $homeSectionData['quick_service_elements'];
    $promoBlocks = $homeSectionData['promo_banner_elements'];
    $flashEnd = $settings->flash_sale_end_date ?? null;
    $flashEndTs = $flashEnd ? \Carbon\Carbon::parse($flashEnd)->timestamp * 1000 : null;
    $quickCategoryElements = $homeSectionData['quick_category_elements'] ?? collect();
@endphp
@if($topFeaturesFromDb->isNotEmpty())
{{-- শুধুমাত্র অ্যাডমিন প্যানেল থেকে যুক্ত করা অ্যাক্টিভ ফিচার/কার্ড ইউজার পেজে দেখাবে; ডিলিট বা Hidden করলে দেখাবে না --}}
<section class="power-zone-section power-zone-section--compact wow fadeInUp" data-wow-duration="0.35s" data-wow-delay="0.05s">
    <div class="container-fluid px-2 px-md-3 px-lg-4">
        <div class="power-zone-unified-row">
            @foreach($topFeaturesFromDb as $f)
                @php $url = $f->getRedirectUrl(); $offerActive = $f->isOfferActive(); @endphp
                <a href="{{ $url }}" class="power-zone-unified-card" title="{{ __($f->title) }}">
                    @if($f->icon_image)
                        <span class="power-zone-unified-card__media power-zone-unified-card__media--img"><img src="{{ $f->imageShow() }}" alt="" loading="lazy" width="120" height="140"></span>
                    @else
                        <span class="power-zone-unified-card__media">@include($activeTemplate . 'partials.icon', ['name' => 'th-large'])</span>
                    @endif
                    <span class="power-zone-unified-card__label">{{ \Illuminate\Support\Str::limit(__($f->title), 20) }}</span>
                    @if($f->offer_price !== null)
                        <span class="power-zone-unified-card__meta">{{ $general->cur_sym }}{{ showAmount($f->offer_price) }}</span>
                    @endif
                    @if($offerActive && $f->offer_end)
                        <span class="power-zone-unified-card__countdown small text-muted" data-end-ts="{{ $f->offer_end->timestamp * 1000 }}"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@push('script')
<script>
(function(){
    document.querySelectorAll('.power-zone-unified-card__countdown[data-end-ts]').forEach(function(el) {
        var endTs = parseInt(el.getAttribute('data-end-ts'), 10);
        function pad(n){ return (n < 10 ? '0' : '') + n; }
        function up() {
            var now = Date.now();
            if (now >= endTs) { el.textContent = ''; return; }
            var d = Math.floor((endTs - now) / 86400000);
            var h = Math.floor(((endTs - now) % 86400000) / 3600000);
            var m = Math.floor(((endTs - now) % 3600000) / 60000);
            var s = Math.floor(((endTs - now) % 60000) / 1000);
            el.textContent = pad(d) + 'd ' + pad(h) + 'h ' + pad(m) + 'm ' + pad(s) + 's';
        }
        up(); setInterval(up, 1000);
    });
})();
</script>
@endpush
@if($flashEndTs)
@push('script')
<script>
(function(){
    var el = document.querySelector('.power-zone-countdown[data-end-ts]');
    if (!el) return;
    var endTs = parseInt(el.getAttribute('data-end-ts'), 10);
    function pad(n){ return (n < 10 ? '0' : '') + n; }
    function up(){
        var now = Date.now();
        if (now >= endTs) return;
        var d = Math.floor((endTs - now) / 86400000);
        var h = Math.floor(((endTs - now) % 86400000) / 3600000);
        var m = Math.floor(((endTs - now) % 3600000) / 60000);
        var s = Math.floor(((endTs - now) % 60000) / 1000);
        var de = el.querySelector('[data-days]'); if(de) de.textContent = pad(d);
        var he = el.querySelector('[data-hours]'); if(he) he.textContent = pad(h);
        var me = el.querySelector('[data-mins]'); if(me) me.textContent = pad(m);
        var se = el.querySelector('[data-secs]'); if(se) se.textContent = pad(s);
    }
    up(); setInterval(up, 1000);
})();
</script>
@endpush
@endif

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush
