{{-- Banner Module: homepage hero. Styles: resources/css/home-banner-slider.css (--hb-w / --hb-h on section). --}}
@php
    $flashSaleEndsAt = $flashSaleEndsAt ?? config('stayl.flash_sale_ends_at');
    $flashEnd = null;
    if (!empty($flashSaleEndsAt)) {
        try {
            $flashEnd = \Carbon\Carbon::parse($flashSaleEndsAt);
        } catch (\Throwable $e) {
            $flashEnd = null;
        }
    }
    $banners = $banners ?? collect();
    $settings = $settings ?? ['slide_interval_seconds' => 5, 'autoplay' => 1, 'banner_width' => 2560, 'banner_height' => 600];
    $slideIntervalSeconds = (int)($settings['slide_interval_seconds'] ?? 5);
    $bannerAutoplay = (int)($settings['autoplay'] ?? 1);
    $bannerWidth = (int)($settings['banner_width'] ?? 2560);
    $bannerHeight = (int)($settings['banner_height'] ?? 600);
    if ($bannerWidth < 100) {
        $bannerWidth = 2560;
    }
    if ($bannerHeight < 50) {
        $bannerHeight = 600;
    }
    $bannerSizesAttr = '(max-width: 640px) 100vw, (max-width: 1024px) 100vw, min(1920px, 100vw)';
    $bannerImgOnError = "this.onerror=null;var l=this.closest('.banner-slide-link');if(l){l.classList.add('is-banner-media-error');}this.style.visibility='hidden';";
@endphp
<section id="home-banner-section" class="banner-module banner-section" style="--hb-w: {{ $bannerWidth }}; --hb-h: {{ $bannerHeight }};" aria-label="@lang('Banner')">
    <div class="banner-fullscreen-inner">
        @if($flashEnd && $flashEnd->isFuture())
            <div
                id="hero-flash-sale-bar"
                class="hero-flash-sale-bar mb-2 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-amber-400/40 bg-gradient-to-r from-amber-500/95 via-orange-500/95 to-rose-600/95 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-amber-900/20 sm:px-4"
                role="status"
                aria-live="polite"
                data-end="{{ $flashEnd->toIso8601String() }}"
            >
                <span class="flex items-center gap-2">
                    <span class="inline-flex h-2 w-2 animate-pulse rounded-full bg-white/90" aria-hidden="true"></span>
                    @lang('Flash sale — limited time')
                </span>
                <span class="tabular-nums tracking-tight">
                    <span class="opacity-90">@lang('Ends in')</span>
                    <span id="hero-flash-countdown" class="ml-1 font-mono text-[13px] font-bold sm:text-sm">—</span>
                </span>
            </div>
        @endif
        <div class="banner-slider js-banner-slider" data-slide-interval="{{ $slideIntervalSeconds }}" data-slide-interval-ms="{{ $slideIntervalSeconds * 1000 }}" data-autoplay="{{ $bannerAutoplay }}">
            @foreach ($banners as $index => $banner)
                @php
                    $filename = app(\App\Modules\Banner\BannerModuleService::class)->getImageFilename($banner);
                    if ($filename === null) {
                        continue;
                    }
                    $imgUrl = \App\Services\BannerService::bannerImageUrl($filename);
                    $dv = is_array($banner->data_values ?? null) ? $banner->data_values : (array)($banner->data_values ?? []);
                    $mobileUrl = \App\Services\BannerService::mobileImageUrl($dv['mobile_image'] ?? null, $filename);
                    $bc = $dv['banner_content'] ?? [];
                    if (is_object($bc)) {
                        $bc = (array)$bc;
                    }
                    $layoutType = $dv['layout_type'] ?? 'hero_full_width';
                    $overlayStyle = 'background:'.($bc['overlay_color'] ?? 'rgba(0,0,0,0.3)').'; color:'.($bc['text_color'] ?? '#fff').'; text-align:'.($bc['title_align'] ?? 'center').';';
                    $useMobileArt = $mobileUrl !== $imgUrl;
                @endphp
                <div class="banner-slide-inner banner-layout-{{ $layoutType }}">
                    <a href="{{ !empty($dv['url']) ? $dv['url'] : '#' }}" class="banner-slide-link" data-banner-id="{{ $banner->id }}">
                        @if($useMobileArt)
                        <picture class="banner-slide-media">
                            <source media="(max-width: 1024px)" srcset="{{ $mobileUrl }}">
                            <img src="{{ $imgUrl }}" alt="{{ $bc['title'] ?? 'banner' }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" {{ $index === 0 ? 'fetchpriority="high"' : '' }} decoding="async" width="{{ $bannerWidth }}" height="{{ $bannerHeight }}" class="banner-img-fullscreen" sizes="{{ $bannerSizesAttr }}" onerror="{{ $bannerImgOnError }}">
                        </picture>
                        @else
                        <img src="{{ $imgUrl }}" alt="{{ $bc['title'] ?? 'banner' }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" {{ $index === 0 ? 'fetchpriority="high"' : '' }} decoding="async" width="{{ $bannerWidth }}" height="{{ $bannerHeight }}" class="banner-img-fullscreen banner-slide-media" sizes="{{ $bannerSizesAttr }}" onerror="{{ $bannerImgOnError }}">
                        @endif
                        <div class="pointer-events-none absolute inset-0 z-[2] bg-gradient-to-t from-slate-950/90 via-slate-900/35 to-slate-900/10" aria-hidden="true"></div>
                        @if(!empty($bc['title']) || !empty($bc['subtitle']) || !empty($bc['button_text']))
                        <div class="banner-overlay hero-overlay-motion pointer-events-none absolute inset-0 z-[3] flex flex-col justify-center gap-2 p-4 sm:p-6 md:p-8" style="{{ $overlayStyle }}">
                            @if(!empty($bc['badge']))<span class="banner-badge">{{ $bc['badge'] }}</span>@endif
                            @if(!empty($bc['title']))<h2 class="banner-title">{{ $bc['title'] }}</h2>@endif
                            @if(!empty($bc['subtitle']))<p class="banner-subtitle">{{ $bc['subtitle'] }}</p>@endif
                            @if(!empty($bc['description']))<p class="banner-desc">{{ $bc['description'] }}</p>@endif
                            @if(!empty($bc['button_text']))<span class="banner-cta">{{ $bc['button_text'] }}</span>@endif
                        </div>
                        @endif
                    </a>
                </div>
            @endforeach
            @if($banners->isNotEmpty())
            <div class="pointer-events-none absolute inset-0 z-[18] flex flex-col justify-end pb-10 pl-4 pr-4 pt-16 sm:items-start sm:justify-center sm:pb-0 sm:pl-8 sm:pr-8 md:pl-12 lg:pl-14">
                <div class="pointer-events-auto flex max-w-lg flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <a href="{{ route('products') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg transition duration-200 hover:from-emerald-500 hover:to-teal-400 hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900/50">
                        @lang('Shop Now')
                    </a>
                    <a href="{{ route('product.hot.deal') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border-2 border-white/90 bg-white/10 px-6 py-2.5 text-sm font-bold text-white backdrop-blur-sm transition duration-200 hover:bg-white/20 hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900/50">
                        @lang('View Deals')
                    </a>
                </div>
            </div>
            @endif
            @if($banners->isEmpty())
            <div class="banner-slide-inner banner-slide-active flex min-h-[200px] items-center justify-center bg-gradient-to-br from-slate-400 via-slate-500 to-slate-700">
                <div class="px-4 py-4 text-center">
                    <p class="mb-2 text-base font-semibold text-white">@lang('Banner section')</p>
                    <p class="mb-0 text-sm text-white/90">@lang('Add banners from') <a href="{{ route('admin.frontend.sections.banner') }}" class="font-semibold text-white underline decoration-white/80 underline-offset-2 hover:text-white">@lang('Admin Panel') → Banner</a></p>
                </div>
            </div>
            @endif
        </div>
        @if($banners->isNotEmpty() && $banners->count() > 1)
        <div class="banner-slider-dots" id="banner-slider-dots" aria-hidden="true"></div>
        @endif
    </div>
</section>
@if($flashEnd && $flashEnd->isFuture())
@push('script')
<script>
(function () {
    var bar = document.getElementById('hero-flash-sale-bar');
    var el = document.getElementById('hero-flash-countdown');
    if (!bar || !el || !bar.getAttribute('data-end')) return;
    var end = Date.parse(bar.getAttribute('data-end'));
    if (Number.isNaN(end)) return;
    function pad(n) { return n < 10 ? '0' + n : String(n); }
    var tid = null;
    function tick() {
        var ms = end - Date.now();
        if (ms <= 0) {
            el.textContent = '00:00:00';
            bar.classList.add('hero-flash-sale-bar--ended');
            if (tid) clearInterval(tid);
            return;
        }
        var s = Math.floor(ms / 1000);
        var h = Math.floor(s / 3600);
        var m = Math.floor((s % 3600) / 60);
        var sec = s % 60;
        el.textContent = (h > 0 ? pad(h) + ':' : '') + pad(m) + ':' + pad(sec);
    }
    tid = setInterval(tick, 1000);
    tick();
})();
</script>
@endpush
@endif
