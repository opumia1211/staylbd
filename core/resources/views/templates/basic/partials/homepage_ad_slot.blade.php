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
<style>
  .hp-ad-slot { padding: 0; background: transparent; }
  .hp-ad { width: 100%; border: none; box-shadow: none; background: transparent; }
  .hp-ad--none .hp-ad__img,
  .hp-ad--thin .hp-ad__img,
  .hp-ad--card .hp-ad__img,
  .hp-ad--minimal .hp-ad__img {
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
  }
  .hp-ad--card { padding: 0; border-radius: 0; box-shadow: none; }
  .hp-ad--bordered {
    border: 2px solid rgba(15, 23, 42, 0.2);
    padding: 4px;
    border-radius: 8px;
    box-sizing: border-box;
  }
  .hp-ad--bordered .hp-ad__img { border-radius: 4px !important; }
  .hp-ad__link { display: block; text-decoration: none; }
  .hp-ad__img {
    width: 100%;
    height: auto;
    display: block;
    max-width: 100%;
    border: 0;
    border-radius: 0;
    box-shadow: none;
  }
  .hp-ad__iframe { width: 100%; min-height: 120px; border: 0; display: block; }
</style>
@endpush
@endif

