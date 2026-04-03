{{-- Banner Module: হোমপেজে হেডারের নিচে। UI শেল = প্রকল্পের max 1920 (--stayl-content-max / main-container); ব্যানার তার ভেতরে width:100%, স্লাইড বক্সে মিডিয়া object-fit:cover — সব ডিভাইসে বক্স পূর্ণ। --}}
@php
    $banners = $banners ?? collect();
    $settings = $settings ?? ['slide_interval_seconds' => 5, 'autoplay' => 1, 'banner_width' => 2560, 'banner_height' => 900];
    $slideIntervalSeconds = (int)($settings['slide_interval_seconds'] ?? 5);
    $bannerAutoplay = (int)($settings['autoplay'] ?? 1);
    $bannerWidth = (int)($settings['banner_width'] ?? 2560);
    $bannerHeight = (int)($settings['banner_height'] ?? 900);
    if ($bannerWidth < 100) {
        $bannerWidth = 2560;
    }
    if ($bannerHeight < 50) {
        $bannerHeight = 900;
    }
    $bannerSizesAttr = '(max-width: 640px) 100vw, (max-width: 1024px) 100vw, min(1920px, 100vw)';
@endphp
<section class="banner-module banner-section" id="home-banner-section" aria-label="@lang('Banner')" style="min-height: 0; background: transparent;">
    <style>
        /* Slider box = অ্যাডমিন banner_width/banner_height অনুপাত; কনটেন্ট শেল সর্বোচ্চ 1920px (main-container) */
        #home-banner-section {
            min-height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 1mm !important;
            width: 100%;
            max-width: 100%;
            margin-left: 0;
            margin-right: 0;
            position: relative;
            box-sizing: border-box;
        }
        #home-banner-section .banner-fullscreen-inner {
            width: 100%;
            max-width: var(--stayl-content-max, min(1920px, 100%));
            margin-left: auto;
            margin-right: auto;
            position: relative;
            box-sizing: border-box;
        }
        #home-banner-section .banner-slider {
            display: block !important;
            width: 100%;
            max-width: 100%;
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
            height: auto !important;
            min-height: 0 !important;
            aspect-ratio: {{ $bannerWidth }} / {{ $bannerHeight }};
        }
        #home-banner-section .banner-slider svg,
        #home-banner-section .banner-slider i,
        #home-banner-section .banner-slider .ui-icon {
            max-width: 44px !important;
            max-height: 44px !important;
            object-fit: contain !important;
        }
        #home-banner-section .banner-slider .banner-slide-inner {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.55s ease;
            pointer-events: none;
        }
        #home-banner-section .banner-slider .banner-slide-inner.banner-slide-active {
            opacity: 1;
            z-index: 1;
            pointer-events: auto;
        }
        #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link,
        #home-banner-section .banner-slider .banner-slide-inner img,
        #home-banner-section .banner-slider .banner-slide-inner picture {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link {
            position: absolute;
            inset: 0;
            overflow: hidden;
            background: #e8eaed;
        }
        #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link picture.banner-slide-media {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            max-width: 100%;
            display: block;
            z-index: 1;
        }
        #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link picture.banner-slide-media img {
            position: absolute;
            inset: 0;
            width: 100% !important;
            height: 100% !important;
            max-width: none;
            max-height: none !important;
            display: block;
            object-fit: cover !important;
            object-position: center center !important;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        #home-banner-section .banner-slider .banner-slide-inner .banner-slide-link img.banner-slide-media {
            position: absolute;
            inset: 0;
            width: 100% !important;
            height: 100% !important;
            max-height: none !important;
            display: block;
            object-fit: cover !important;
            object-position: center center !important;
            z-index: 1;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        /* main.css .banner-img-fullscreen { max-height:280px !important } — হোম স্লাইডারে বাতিল */
        #home-banner-section .banner-slider .banner-slide-inner img.banner-img-fullscreen {
            max-height: none !important;
            min-height: 0;
        }
        #home-banner-section .banner-slider .owl-carousel { display: block !important; height: 100% !important; min-height: 0 !important; }
        #home-banner-section .banner-slider .owl-stage-outer,
        #home-banner-section .banner-slider .owl-item {
            min-height: 0 !important;
            height: 100% !important;
        }
        #home-banner-section .banner-slider-dots {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 6px;
            text-align: center;
            z-index: 12;
            display: block !important;
            pointer-events: auto;
        }
        #home-banner-section .banner-slider-dots .dot { display: inline-block; width: 10px; height: 10px; margin: 0 4px; border-radius: 50%; background: rgba(255,255,255,0.5); cursor: pointer; transition: background 0.25s ease, transform 0.2s ease; }
        #home-banner-section .banner-slider-dots .dot:hover { background: rgba(255,255,255,0.8); transform: scale(1.15); }
        #home-banner-section .banner-slider-dots .dot.active { background: #fff; box-shadow: 0 0 0 2px rgba(0,0,0,0.2); }
        #home-banner-section .banner-slider .slick-arrow,
        #home-banner-section .banner-slider .owl-nav,
        #home-banner-section .banner-slider .owl-prev,
        #home-banner-section .banner-slider .owl-next {
            display: none !important;
        }
        .banner-section .owl-nav button,
        .banner-section .slick-arrow {
            width: 40px !important;
            height: 40px !important;
            font-size: 0 !important;
        }
        .banner-section svg {
            width: 20px !important;
            height: 20px !important;
        }
    </style>
    <div class="banner-fullscreen-inner">
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
                            <img src="{{ $imgUrl }}" alt="{{ $bc['title'] ?? 'banner' }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" {{ $index === 0 ? 'fetchpriority="high"' : '' }} decoding="async" width="{{ $bannerWidth }}" height="{{ $bannerHeight }}" class="banner-img-fullscreen" sizes="{{ $bannerSizesAttr }}">
                        </picture>
                        @else
                        <img src="{{ $imgUrl }}" alt="{{ $bc['title'] ?? 'banner' }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" {{ $index === 0 ? 'fetchpriority="high"' : '' }} decoding="async" width="{{ $bannerWidth }}" height="{{ $bannerHeight }}" class="banner-img-fullscreen banner-slide-media" sizes="{{ $bannerSizesAttr }}">
                        @endif
                        @if(!empty($bc['title']) || !empty($bc['subtitle']) || !empty($bc['button_text']))
                        <div class="banner-overlay" style="{{ $overlayStyle }}">
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
            @if($banners->isEmpty())
            <div class="banner-slide-inner d-flex align-items-center justify-content-center" style="min-height: 200px; background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);">
                <div class="text-center px-4 py-4">
                    <p class="mb-2 fw-600 text-white">@lang('Banner section')</p>
                    <p class="mb-0 small text-white">@lang('Add banners from') <a href="{{ route('admin.frontend.sections.banner') }}" class="text-white fw-600 text-decoration-underline">@lang('Admin Panel') → Banner</a></p>
                </div>
            </div>
            @endif
        </div>
        @if($banners->isNotEmpty() && $banners->count() > 1)
        <div class="banner-slider-dots" id="banner-slider-dots" aria-hidden="true"></div>
        @endif
    </div>
</section>
