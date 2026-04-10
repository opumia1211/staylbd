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

{{-- inline style moved to critical-storefront.css --}}

@endpush
@endif
