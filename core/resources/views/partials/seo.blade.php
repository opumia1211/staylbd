
@php
    if (isset($seoContents) && count($seoContents)) {
        $seoContents = (object) $seoContents;
        $socialImageSize = explode('x', $seoContents->image_size ?? '1200x630');
        if (empty($seoContents->image) && isset($seo) && !empty($seo->image)) {
            $seoContents->image = getImage(getFilePath('seo') . '/' . $seo->image, getFileSize('seo'));
            $socialImageSize = explode('x', getFileSize('seo'));
        }
    } elseif ($seo) {
        $seoContents = $seo;
        $socialImageSize = explode('x', getFileSize('seo'));
        $seoContents->image = getImage(getFilePath('seo') . '/' . ($seo->image ?? ''), getFileSize('seo'));
    } else {
        $seoContents = null;
    }
    $globalSeo = isset($seo) ? $seo : null;
    $canonicalBase = $seoContents ? ($seoContents->canonical_base ?? ($globalSeo->canonical_base ?? null)) : null;
    $canonicalUrl = !empty($canonicalBase) ? rtrim($canonicalBase, '/') . parse_url(url()->current(), PHP_URL_PATH) : url()->current();
    if ($seoContents && !empty($seoContents->canonical_url)) {
        $canonicalUrl = $seoContents->canonical_url;
    }
    $pageTitle = $pageTitle ?? 'Home';
@endphp

<meta name="title" content="{{ $general->sitename(__($pageTitle)) }}">

@if ($seoContents)
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="description" content="{{ $seoContents->meta_description ?? $seoContents->description }}">
    <meta name="keywords" content="{{ implode(',', $seoContents->keywords ?? []) }}">
    @if(!empty($seoContents->robots ?? null))
    <meta name="robots" content="{{ $seoContents->robots }}">
    @endif
    @if(!empty($seoContents->google_site_verification ?? null))
    <meta name="google-site-verification" content="{{ $seoContents->google_site_verification }}">
    @endif
    @if(!empty($globalSeo->bing_site_verification ?? null))
    <meta name="msvalidate.01" content="{{ $globalSeo->bing_site_verification }}">
    @endif
    @php $favicon = getLogo('favicon'); $mainLogo = getLogo('logo'); @endphp
    @if($favicon)
    <link rel="icon" sizes="32x32" href="{{ $favicon }}">
    <link rel="icon" sizes="64x64" href="{{ $favicon }}">
    <link rel="icon" sizes="180x180" href="{{ $favicon }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $favicon }}">
    <link rel="shortcut icon" href="{{ $favicon }}">
    @elseif($mainLogo)
    <link rel="icon" sizes="32x32" href="{{ $mainLogo }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $mainLogo }}">
    <link rel="shortcut icon" href="{{ $mainLogo }}">
    @endif
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ $general->sitename(__($pageTitle)) }}">
    {{--
    <!-- Google / Search Engine Tags --> --}}
    <meta itemprop="name" content="{{ $general->sitename(__($pageTitle)) }}">
    <meta itemprop="description" content="{{ $seoContents->description }}">
    <meta itemprop="image" content="{{ $seoContents->image }}">
    {{--
    <!-- Facebook Meta Tags --> --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seoContents->social_title }}">
    <meta property="og:description" content="{{ $seoContents->social_description }}">
    <meta property="og:image" content="{{ $seoContents->image }}" />
    @php
        $imgExt = pathinfo($seoContents->image ?? '', PATHINFO_EXTENSION);
        $ogImageType = $imgExt ? ('image/' . (strtolower($imgExt) === 'jpg' ? 'jpeg' : strtolower($imgExt))) : 'image/jpeg';
    @endphp
    <meta property="og:image:type" content="{{ $ogImageType }}" />
    <meta property="og:image:width" content="{{ $socialImageSize[0] ?? '' }}" />
    <meta property="og:image:height" content="{{ $socialImageSize[1] ?? '' }}" />
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if(!empty($globalSeo->og_site_name ?? null))
    <meta property="og:site_name" content="{{ $globalSeo->og_site_name }}">
    @endif
    <meta property="og:locale" content="{{ $globalSeo->og_locale ?? 'en_GB' }}">
    {{--
    <!-- Twitter Meta Tags --> --}}
    <meta name="twitter:card" content="summary_large_image">
    @if(!empty($globalSeo->twitter_handle ?? null))
    <meta name="twitter:site" content="@{{ $globalSeo->twitter_handle }}">
    <meta name="twitter:creator" content="@{{ $globalSeo->twitter_handle }}">
    @endif
    <meta name="twitter:title" content="{{ $seoContents->social_title ?? $seoContents->description ?? '' }}">
    <meta name="twitter:description" content="{{ $seoContents->social_description ?? $seoContents->description ?? '' }}">
    @if(!empty($seoContents->image))
    <meta name="twitter:image" content="{{ $seoContents->image }}">
    @endif
    @if((int)($globalSeo->jsonld_organization ?? 1) === 1)
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"Organization","name":{{ json_encode($general->sitename ?? '') }},"url":{{ json_encode(url('/')) }}}
    </script>
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"WebSite","name":{{ json_encode($general->sitename ?? '') }},"url":{{ json_encode(url('/')) }},"potentialAction":{"@type":"SearchAction","target":{"@type":"EntryPoint","urlTemplate":{{ json_encode(route('products') . '?search={search_term_string}') }}},"query-input":"required name=search_term_string"}}
    </script>
    @endif
@endif
