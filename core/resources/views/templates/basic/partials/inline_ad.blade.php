@php
    $inlineAds = $inlineAds ?? collect();
    $placement = $placement ?? 'sidebar_right';
@endphp
@if($inlineAds->isNotEmpty())
<div class="inline-ad-wrap inline-ad-wrap--{{ $placement }}" aria-label="{{ __("Advertisement") }}">
    @foreach($inlineAds as $ad)
    @php
        $boxWidth = $ad->getWidth();
        $boxHeight = $ad->getHeight();
        $heightIsAuto = ($boxHeight === 'auto');
    @endphp
    <div class="inline-ad-box {{ $heightIsAuto ? 'inline-ad-box--auto-height' : '' }}" style="--inline-w: {{ $boxWidth }}; --inline-h: {{ $boxHeight }};">
        <a href="{{ $ad->getLinkUrl() }}" class="inline-ad-link" target="_blank" rel="noopener" @if(!$ad->link_url) onclick="return false;" style="pointer-events: none;" @endif>
            @if($ad->image)
            <img src="{{ getImage(getFilePath('popupAd') . '/' . $ad->image) }}" alt="{{ __($ad->name) }}" class="inline-ad-img" loading="lazy">
            @else
            <span class="inline-ad-placeholder">{{ __($ad->name) }}</span>
            @endif
        </a>
    </div>
    @endforeach
</div>
@push('style')
<style>
.inline-ad-wrap { margin: 0; }
.inline-ad-wrap--sidebar_right { margin-top: 1rem; }
.inline-ad-wrap--sidebar_left { margin-top: 1rem; }
.inline-ad-wrap--content_top, .inline-ad-wrap--content_bottom { margin: 0.75rem 0; }
.inline-ad-box { width: 100%; max-width: min(var(--inline-w), 100%); height: var(--inline-h); max-height: min(var(--inline-h), 400px); background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; display: flex; align-items: center; justify-content: center; box-sizing: border-box; margin-bottom: 0.75rem; }
.inline-ad-box:last-child { margin-bottom: 0; }
.inline-ad-box--auto-height { height: auto; max-height: 400px; }
.inline-ad-link { display: block; width: 100%; height: 100%; min-height: 0; }
.inline-ad-img { max-width: 100% !important; max-height: 100% !important; width: auto !important; height: auto !important; object-fit: contain !important; display: block; }
.inline-ad-placeholder { padding: 1rem; text-align: center; color: #666; font-size: 0.9rem; }
@media (max-width: 991.98px) {
    .inline-ad-wrap--sidebar_right .inline-ad-box { max-height: min(var(--inline-h), 280px); }
}
</style>
@endpush
@endif
