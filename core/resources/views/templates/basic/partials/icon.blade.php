@php
    $class = $class ?? '';
    $rawName = (string) $name;
    $mappedName = lucide_icon_kebab($rawName);
    $sizePx = isset($sizePx) ? (int) $sizePx : 20;
    $isCompareIcon = $mappedName === 'arrow-left-right'
        || in_array(strtolower($rawName), ['compare', 'exchange-alt', 'exchange', 'compare-alt'], true);
@endphp
@if($isCompareIcon)
    @include($activeTemplate . 'partials.icons.compare', ['size' => $sizePx, 'class' => trim('inline-flex shrink-0 '.$class)])
@else
<span
    class="stayl-lucide-wrap stayl-lucide-wrap--flat inline-flex shrink-0 items-center justify-center {{ $class }}"
    style="--stayl-lucide-size: {{ $sizePx }}px;"
>
    <i data-lucide="{{ $mappedName }}" class="stayl-lucide-glyph" aria-hidden="true"></i>
</span>
@endif
