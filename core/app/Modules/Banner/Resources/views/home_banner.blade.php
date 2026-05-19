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
    $settings = $settings ?? ['slide_interval_seconds' => 5, 'autoplay' => 1, 'banner_width' => 2560, 'banner_height' => 800];
    $slideIntervalSeconds = (int)($settings['slide_interval_seconds'] ?? 5);
    $bannerAutoplay = (int)($settings['autoplay'] ?? 1);
    $bannerWidth = (int)($settings['banner_width'] ?? 2560);
    $bannerHeight = (int)($settings['banner_height'] ?? 400);
    if ($bannerWidth < 100) {
        $bannerWidth = 2560;
    }
    if ($bannerHeight < 50) {
        $bannerHeight = 400;
    }
    $bannerSizesAttr = '(max-width: 640px) 100vw, (max-width: 1024px) 100vw, min(1920px, 100vw)';
    $bannerImgOnError = "this.onerror=null;var l=this.closest('.banner-slide-link');if(l){l.classList.add('is-banner-media-error');}this.style.visibility='hidden';";
@endphp

<style>
    /* STANDARD SAFE RESPONSIVE OPTIMIZATION */
    /* Keeps banner safely inside .main-container maximum bounds */
    html body #home-banner-section {
        display: block !important;
        width: 100% !important;
        background: transparent !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    html body #home-banner-section .banner-fullscreen-inner {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        position: relative !important;
    }

    html body #home-banner-section .banner-slider {
        display: block !important;
        position: relative !important;
        width: 100% !important;
        height: auto !important;
        aspect-ratio: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow: hidden !important;
        border-radius: 0 !important;
    }

    /* All slides: transparent layer, soft cross-fade transitions */
    html body #home-banner-section .banner-slider .banner-slide-inner {
        width: 100% !important;
        height: 100% !important;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out;
        z-index: 1;
        pointer-events: none !important;
        position: absolute !important;
        inset: 0 !important;
        overflow: hidden !important;
    }
    
    /* Active Slide */
    html body #home-banner-section .banner-slider .banner-slide-inner.banner-slide-active {
        opacity: 1;
        visibility: visible;
        z-index: 2;
        pointer-events: auto !important;
        transform: translateZ(0);
        will-change: opacity;
    }

    /* The first slide dictates the container height */
    html body #home-banner-section .banner-slider .banner-slide-inner:first-child {
        position: relative !important;
        height: auto !important;
    }

    html body #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        position: absolute !important;
        inset: 0 !important;
        background: transparent !important;
        overflow: hidden !important;
    }

    html body #home-banner-section .banner-slider .banner-slide-inner:first-child .banner-slide-link {
        position: relative !important;
        height: auto !important;
    }

    /* Standard responsive images & videos - completely static layout (no zoom) */
    html body #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link img,
    html body #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link picture,
    html body #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link picture img,
    html body #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link video {
        width: 100% !important;
        height: 100% !important;
        display: block !important;
        object-fit: fill !important; 
        object-position: center !important;
        max-height: none !important;
        background-color: transparent !important;
        position: absolute !important;
        inset: 0 !important;
        transform: none !important;
    }

    /* First slide's media pushes physical height naturally */
    html body #home-banner-section .banner-slider .banner-slide-inner:first-child .banner-slide-link img,
    html body #home-banner-section .banner-slider .banner-slide-inner:first-child .banner-slide-link picture,
    html body #home-banner-section .banner-slider .banner-slide-inner:first-child .banner-slide-link picture img,
    html body #home-banner-section .banner-slider .banner-slide-inner:first-child .banner-slide-link video {
        position: relative !important;
        height: auto !important;
        object-fit: contain !important; /* Safety for natural flow */
    }

    /* High-fidelity dots navigation styling */
    .banner-slider-dots {
        position: absolute !important;
        bottom: 20px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        display: flex !important;
        gap: 8px !important;
        z-index: 10 !important;
    }
    
    .banner-slider-dots .banner-slider-dot {
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.4) !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        padding: 0 !important;
        outline: none !important;
    }
    
    .banner-slider-dots .banner-slider-dot.is-active {
        width: 24px !important;
        border-radius: 4px !important;
        background: #ffffff !important;
    }

    /* Clean Static Text Overlays - no complex sliding delay */
    .banner-overlay .banner-badge,
    .banner-overlay .banner-title,
    .banner-overlay .banner-subtitle,
    .banner-overlay .banner-desc,
    .banner-overlay .banner-cta {
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    .banner-slide-active .banner-overlay .banner-badge,
    .banner-slide-active .banner-overlay .banner-title,
    .banner-slide-active .banner-overlay .banner-subtitle,
    .banner-slide-active .banner-overlay .banner-desc,
    .banner-slide-active .banner-overlay .banner-cta {
        opacity: 1 !important;
    }
</style>
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
                    
                    // Determine if the media is a video file
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4', 'webm', 'ogv']);
                @endphp
                <div class="banner-slide-inner banner-layout-{{ $layoutType }}">
                    <a href="{{ !empty($dv['url']) ? $dv['url'] : '#' }}" class="banner-slide-link" data-banner-id="{{ $banner->id }}">
                        @if($isVideo)
                        <video src="{{ $imgUrl }}" autoplay muted loop playsinline class="banner-img-fullscreen banner-slide-media" style="width: 100% !important; height: 100% !important; object-fit: fill !important;"></video>
                        @else
                            @if($useMobileArt)
                            <picture class="banner-slide-media">
                                <source media="(max-width: 1024px)" srcset="{{ $mobileUrl }}">
                                <img src="{{ $imgUrl }}" alt="{{ $bc['title'] ?? 'banner' }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" {{ $index === 0 ? 'fetchpriority="high"' : '' }} decoding="async" width="{{ $bannerWidth }}" height="{{ $bannerHeight }}" class="banner-img-fullscreen" sizes="{{ $bannerSizesAttr }}" onerror="{{ $bannerImgOnError }}">
                            </picture>
                            @else
                            <img src="{{ $imgUrl }}" alt="{{ $bc['title'] ?? 'banner' }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" {{ $index === 0 ? 'fetchpriority="high"' : '' }} decoding="async" width="{{ $bannerWidth }}" height="{{ $bannerHeight }}" class="banner-img-fullscreen banner-slide-media" sizes="{{ $bannerSizesAttr }}" onerror="{{ $bannerImgOnError }}">
                            @endif
                        @endif
                        {{-- Dark Obscuring Gradient Removed as requested --}}
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
            {{-- Extra Buttons Removed to ensure full banner visibility as requested --}}
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
