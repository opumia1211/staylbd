@php
    $iconKey = $iconKey ?? 'cart_icon';
    $fallback = $fallback ?? 'circle';
    $w = (int) ($width ?? 22);
    $h = (int) ($height ?? $w);
    $lucide = header_icon_lucide_name($iconKey, $fallback);
    $icon3d = filter_var($icon3d ?? false, FILTER_VALIDATE_BOOLEAN);
    $icon3dSm = filter_var($icon3dSm ?? false, FILTER_VALIDATE_BOOLEAN);
    $variant = in_array(($variant ?? 'primary'), ['primary', 'accent', 'danger', 'neutral', 'light'], true)
        ? ($variant ?? 'primary')
        : 'primary';
    $wrapClass = trim(($class ?? '').' '.($svgClass ?? ''));
    $alt = $alt ?? '';
@endphp
<span
    class="stayl-lucide-wrap {{ $icon3d ? 'icon-3d icon-3d--'.$variant.($icon3dSm ? ' icon-3d--sm' : '') : 'stayl-lucide-wrap--flat' }}{{ $wrapClass !== '' ? ' '.$wrapClass : '' }}"
    style="--stayl-lucide-size: {{ $w }}px;{{ $h !== $w ? ' --stayl-lucide-size-y: '.$h.'px;' : '' }}"
    @if($alt !== '') title="{{ $alt }}" @endif
>
    <i data-lucide="{{ $lucide }}" class="stayl-lucide-glyph" aria-hidden="true"></i>
</span>
