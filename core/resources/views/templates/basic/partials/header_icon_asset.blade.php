@php
    $iconKey = $iconKey ?? 'cart_icon';
    $fallback = $fallback ?? 'circle';
    $w = (int) ($width ?? 22);
    $h = (int) ($height ?? 22);
    $imgClass = trim('ui-icon ' . ($class ?? ''));
    $svgClass = $svgClass ?? $class ?? '';
    $uploaded = header_icon_uploaded($iconKey);
    $alt = $alt ?? '';
    $loading = $loading ?? null;
    $decoding = $decoding ?? 'async';
    $inlineSvg = header_icon_inline_svg_html($iconKey, $imgClass, $w, $h, (string) $alt);
@endphp
@if($inlineSvg)
    {!! $inlineSvg !!}
@elseif($uploaded)
    <img src="{{ header_icon_uploaded_asset_url($uploaded) }}" alt="{{ $alt }}" class="{{ $imgClass }}" width="{{ $w }}" height="{{ $h }}" decoding="{{ $decoding }}" @if($loading) loading="{{ $loading }}" @endif>
@else
    @include($activeTemplate . 'partials.icon', ['name' => header_icon_svg($iconKey, $fallback), 'class' => $svgClass])
@endif
