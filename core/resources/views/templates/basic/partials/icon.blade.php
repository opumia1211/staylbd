@php
    $class = $class ?? '';
    $rawName = (string) $name;
    $mappedName = lucide_icon_kebab($rawName);
    $sizePx = isset($sizePx) ? (int) $sizePx : 20;
@endphp
<span
    class="stayl-lucide-wrap stayl-lucide-wrap--flat inline-flex shrink-0 items-center justify-center {{ $class }}"
    style="--stayl-lucide-size: {{ $sizePx }}px;"
>
    <i data-lucide="{{ $mappedName }}" class="stayl-lucide-glyph" aria-hidden="true"></i>
</span>
