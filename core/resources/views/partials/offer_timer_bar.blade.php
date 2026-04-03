@php
    $timer = $timer ?? null;
@endphp
@if($timer && $timer->end_at->isFuture())
@php
    $endTs = $timer->end_at->timestamp * 1000;
    $styleClass = 'offer-timer-bar offer-timer-bar--' . $timer->style;
    $linkUrl = $timer->getLinkUrl();
    $sizeStyle = '';
    if (!empty($timer->bar_width)) $sizeStyle .= 'width:' . e($timer->bar_width) . ';';
    if (!empty($timer->bar_height)) $sizeStyle .= 'min-height:' . e($timer->bar_height) . ';';
    if ($linkUrl !== '#') $sizeStyle .= ' cursor:pointer;';
@endphp
<div class="{{ $styleClass }} rounded-3 p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3" data-end-ts="{{ $endTs }}"@if($sizeStyle !== '') style="{{ $sizeStyle }}"@endif @if($linkUrl !== '#') role="button" tabindex="0" onclick="window.location.href={{ json_encode($linkUrl) }}"@endif>
    <div class="d-flex align-items-center gap-3">
        <div class="offer-timer-bar__icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="las la-bolt text-white fs-4"></i>
        </div>
        <div>
            <strong class="d-block">{{ __($timer->title) }}</strong>
            @if($timer->subtitle)
                <p class="mb-0 small text-muted">{{ __($timer->subtitle) }}</p>
            @endif
        </div>
    </div>
    <div class="offer-timer-bar__countdown d-flex gap-2" data-timer-id="{{ $timer->id }}">
        <span class="countdown-box rounded px-3 py-2 text-center"><span class="countdown-hours fw-bold text--base d-block fs-5">00</span><small class="text-muted">@lang('Hrs')</small></span>
        <span class="countdown-box rounded px-3 py-2 text-center"><span class="countdown-mins fw-bold text--base d-block fs-5">00</span><small class="text-muted">@lang('Min')</small></span>
        <span class="countdown-box rounded px-3 py-2 text-center"><span class="countdown-secs fw-bold text--base d-block fs-5">00</span><small class="text-muted">@lang('Sec')</small></span>
    </div>
</div>
@endif
