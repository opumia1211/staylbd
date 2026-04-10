{{-- Async non-critical CSS chunk (cart / account / compare). Matches core deferred link pattern in layouts/app.blade.php. --}}
@php
    $bundle = $bundle ?? 'tailwind-storefront-deferred-cart';
    $href = storefront_compiled_stylesheet_url($bundle);
@endphp
<link rel="preload" href="{{ $href }}" as="style">
<link rel="stylesheet" href="{{ $href }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ $href }}" crossorigin="anonymous"></noscript>
