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
<style>
/* Power Zone – এক লাইনে সব ফিচার, একই সাইজের কার্ড (ব্যানার নিচের কার্ডের মতো) */
.power-zone-section--compact { padding: 12px 0 16px; background: linear-gradient(180deg, #fafbfc 0%, #fff 100%); border-bottom: 1px solid rgba(0,0,0,.06); }
.power-zone-unified-row { display: flex; flex-wrap: wrap; gap: 14px 18px; align-items: stretch; }
@media (min-width: 576px) { .power-zone-unified-row { gap: 16px 18px; } }
@media (min-width: 768px) { .power-zone-unified-row { gap: 18px; } }
/* কার্ডের বাহ্যিক সাইজ আগের মতো – ভিতরের ছবি 120x140px (কার্ড একই রাখতে ছবি কন্টেইনার 120x140, কার্ডে overflow) */
.power-zone-unified-card { display: flex; flex-direction: column; align-items: center; justify-content: flex-start; min-width: 110px; min-height: 120px; padding: 8px 6px; background: #fff; border-radius: 14px; border: 1px solid rgba(0,0,0,.08); box-shadow: 0 2px 8px rgba(0,0,0,.06); text-decoration: none; color: #1f2937; text-align: center; transition: transform .2s, box-shadow .2s, border-color .2s; flex: 0 0 auto; overflow: hidden; }
@media (min-width: 576px) { .power-zone-unified-card { min-width: 120px; min-height: 130px; padding: 8px 6px; } }
@media (min-width: 768px) { .power-zone-unified-card { min-width: 130px; min-height: 140px; padding: 8px 6px; } }
.power-zone-unified-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,.1); border-color: rgba(0,0,0,.12); color: var(--base, #6366f1); }
.power-zone-unified-card__media { width: 120px; height: 140px; border-radius: 12px; background: rgba(0,0,0,.04); display: flex; align-items: center; justify-content: center; margin-bottom: 8px; font-size: 1.8rem; color: #374151; flex-shrink: 0; }
.power-zone-unified-card__media--img { padding: 0; overflow: hidden; width: 120px; height: 140px; }
.power-zone-unified-card__media--img img { width: 100%; height: 100%; object-fit: cover; display: block; }
.power-zone-unified-card:hover .power-zone-unified-card__media { color: var(--base, #6366f1); background: rgba(99,102,241,.08); }
.power-zone-unified-card:hover .power-zone-unified-card__media--img { background: rgba(99,102,241,.08); }
.power-zone-unified-card__label { line-height: 1.3; font-weight: 600; font-size: 0.85rem; letter-spacing: 0.01em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.power-zone-unified-card__meta { font-size: 0.8rem; font-weight: 700; color: var(--base, #6366f1); margin-top: 4px; }
</style>
@endpush
