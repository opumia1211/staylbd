@php
    /** @var \App\Models\HomepageAdSlot $ad */
    $url = trim((string) ($ad->link_url ?? ''));
    $target = $ad->open_new_tab ? '_blank' : '_self';
    $rel = $ad->open_new_tab ? 'noopener noreferrer' : '';
    $sourceType = (string) ($ad->source_type ?? 'upload');
    $img = $sourceType === 'upload' ? $ad->imageUrl() : trim((string) ($ad->external_url ?? ''));
    $frameStyle = (string) ($ad->frame_style ?? 'none');
    if (!in_array($frameStyle, ['none', 'thin', 'card', 'minimal', 'bordered'], true)) {
        $frameStyle = 'none';
    }
    $maxHeight = (int) ($ad->max_height_px ?? 0);
    $isEmbed = $sourceType === 'embed_url';
@endphp

@if($img || $isEmbed)
<section class="hp-ad-slot">
    <div class="container-fluid px-0">
        <div class="hp-ad hp-ad--{{ e($frameStyle) }}">
            @if($url)
                <a href="{{ $url }}" target="{{ $target }}" rel="{{ $rel }}" class="hp-ad__link" aria-label="{{ $ad->admin_title }}">
                    @if($isEmbed)
                        <iframe src="{{ $ad->external_url }}" class="hp-ad__iframe" title="{{ $ad->admin_title }}" loading="lazy"></iframe>
                    @else
                        <img src="{{ $img }}" alt="{{ $ad->admin_title }}" class="hp-ad__img" loading="lazy" decoding="async" @if($maxHeight > 0) style="max-height: {{ $maxHeight }}px;" @endif>
                    @endif
                </a>
            @else
                <div class="hp-ad__link" aria-label="{{ $ad->admin_title }}">
                    @if($isEmbed)
                        <iframe src="{{ $ad->external_url }}" class="hp-ad__iframe" title="{{ $ad->admin_title }}" loading="lazy"></iframe>
                    @else
                        <img src="{{ $img }}" alt="{{ $ad->admin_title }}" class="hp-ad__img" loading="lazy" decoding="async" @if($maxHeight > 0) style="max-height: {{ $maxHeight }}px;" @endif>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush
@endif

