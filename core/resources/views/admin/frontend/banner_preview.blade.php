{{-- Live Banner Preview: single banner as it appears on frontend. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@lang('Banner Preview')</title>
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/main.css') }}">
    <style>
        body { margin: 0; padding: 0; background: #f0f0f0; }
        .banner-preview-wrap { max-width: 100%; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="banner-preview-wrap">
        <section class="banner-section banner-section--fullscreen">
            <div class="banner-fullscreen-inner">
                <div class="banner-slider owl-theme owl-carousel" data-slide-interval="{{ $slideIntervalSeconds }}" data-autoplay="0">
                    @php
                        $banner = $bannerElement;
                        $imgUrl = \App\Services\BannerService::bannerImageUrl($banner->data_values->image);
                        $mobileUrl = \App\Services\BannerService::mobileImageUrl(@$banner->data_values->mobile_image, $banner->data_values->image);
                        $bc = $banner->data_values->banner_content ?? (object)[];
                        $layoutType = $banner->data_values->layout_type ?? 'hero_full_width';
                        $overlayStyle = 'background:'.($bc->overlay_color ?? 'rgba(0,0,0,0.3)').'; color:'.($bc->text_color ?? '#fff').'; text-align:'.($bc->title_align ?? 'center').';';
                        if (!empty($bc->title_font_size)) { $overlayStyle .= ' --banner-title-size: '.$bc->title_font_size.';'; }
                        if (!empty($bc->title_font_weight)) { $overlayStyle .= ' --banner-title-weight: '.$bc->title_font_weight.';'; }
                    @endphp
                    <div class="banner-slide-inner banner-layout-{{ $layoutType }}" style="aspect-ratio: {{ $bannerWidth }}/{{ $bannerHeight }};">
                        <a href="{{ @$banner->data_values->url ?: '#' }}" class="banner-slide-link">
                            @if($mobileUrl !== $imgUrl)
                            <picture>
                                <source media="(max-width: 768px)" srcset="{{ $mobileUrl }}">
                                <img src="{{ $imgUrl }}" alt="{{ $bc->title ?? 'banner' }}" loading="eager" decoding="async" width="{{ $bannerWidth }}" height="{{ $bannerHeight }}" class="banner-img-fullscreen">
                            </picture>
                            @else
                            <img src="{{ $imgUrl }}" alt="{{ $bc->title ?? 'banner' }}" loading="eager" decoding="async" width="{{ $bannerWidth }}" height="{{ $bannerHeight }}" class="banner-img-fullscreen">
                            @endif
                            @if(!empty($bc->title) || !empty($bc->subtitle) || !empty($bc->button_text))
                            <div class="banner-overlay" style="{{ $overlayStyle }}">
                                @if(!empty($bc->badge))<span class="banner-badge">{{ $bc->badge }}</span>@endif
                                @if(!empty($bc->title))<h2 class="banner-title">{{ $bc->title }}</h2>@endif
                                @if(!empty($bc->subtitle))<p class="banner-subtitle">{{ $bc->subtitle }}</p>@endif
                                @if(!empty($bc->description))<p class="banner-desc">{{ $bc->description }}</p>@endif
                                @if(!empty($bc->button_text))<span class="banner-cta">{{ $bc->button_text }}</span>@endif
                            </div>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset(activeTemplate(true) . 'js/owl.min.js') }}"></script>
    <script>
        (function() {
            if (typeof jQuery !== 'undefined' && jQuery.fn.owlCarousel) {
                jQuery('.banner-slider').owlCarousel({ loop: false, nav: false, dots: false, items: 1 });
            }
        })();
    </script>
</body>
</html>
