@php
    $popupAds = $popupAds ?? collect();
    $ad = $popupAds->first();
    $boxWidth = $ad ? $ad->getWidth() : '400px';
    $boxHeight = $ad ? $ad->getHeight() : '300px';
    $heightIsAuto = ($boxHeight === 'auto');
    $position = $ad ? $ad->getPosition() : 'center';
    $positionClass = 'popup-ad-overlay--' . str_replace(' ', '-', $position);
@endphp
@if($ad)
<div id="popupAdOverlay" class="popup-ad-overlay {{ $positionClass }}" role="dialog" aria-modal="true" aria-label="{{ __("Promotional offer") }}" style="display: none;">
    <div class="popup-ad-backdrop" data-close="1" aria-hidden="true"></div>
    <div class="popup-ad-box {{ $heightIsAuto ? 'popup-ad-box--auto-height' : '' }}" style="--popup-w: {{ $boxWidth }}; --popup-h: {{ $boxHeight }};">
        <button type="button" class="popup-ad-close" id="popupAdClose" aria-label="@lang('Close')" title="@lang('Close')">
            @include($activeTemplate . 'partials.icon', ['name' => 'times'])
            <span class="visually-hidden">@lang('Close')</span>
        </button>
        <a href="{{ $ad->getLinkUrl() }}" class="popup-ad-link" target="_blank" rel="noopener" @if($ad->link_url) @else onclick="return false;" style="pointer-events: none;" @endif>
            @if($ad->image)
            <img src="{{ getImage(getFilePath('popupAd') . '/' . $ad->image) }}" alt="{{ __($ad->name) }}" class="popup-ad-img" loading="lazy">
            @else
            <span class="popup-ad-placeholder">{{ __($ad->name) }}</span>
            @endif
        </a>
    </div>
</div>
@push('style')
<style>
/* Light, soft popup – flexible, no break. Overlay: full screen, flex; position class controls where the box appears */
#popupAdOverlay.popup-ad-overlay { position: fixed; inset: 0; z-index: 99998; overflow: auto; padding: 12px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease; }
#popupAdOverlay.popup-ad-overlay.popup-ad-overlay--visible { opacity: 1; }
#popupAdOverlay.popup-ad-overlay .popup-ad-box { opacity: 0; transform: scale(0.97); transition: opacity 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease; }
#popupAdOverlay.popup-ad-overlay.popup-ad-overlay--visible .popup-ad-box { opacity: 1; transform: scale(1); }
#popupAdOverlay.popup-ad-overlay--center { align-items: center; justify-content: center; }
#popupAdOverlay.popup-ad-overlay--top-left { align-items: flex-start; justify-content: flex-start; }
#popupAdOverlay.popup-ad-overlay--top-right { align-items: flex-start; justify-content: flex-end; }
#popupAdOverlay.popup-ad-overlay--bottom-left { align-items: flex-end; justify-content: flex-start; }
#popupAdOverlay.popup-ad-overlay--bottom-right { align-items: flex-end; justify-content: flex-end; }
.popup-ad-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.4); cursor: pointer; transition: opacity 0.25s ease; }
/* Box: light shadow, soft radius; admin size; flexible. Laptop/desktop: smaller frame cap */
#popupAdOverlay .popup-ad-box { position: relative; flex-shrink: 0; min-width: 0; min-height: 0; width: var(--popup-w); max-width: min(var(--popup-w), 55vw, 520px); height: var(--popup-h); max-height: min(var(--popup-h), 65vh, 480px); background: #fff; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); overflow: hidden; display: flex; align-items: center; justify-content: center; box-sizing: border-box; }
#popupAdOverlay .popup-ad-box:hover { box-shadow: 0 12px 40px rgba(0,0,0,0.14); }
@if($heightIsAuto)
#popupAdOverlay .popup-ad-box.popup-ad-box--auto-height { height: auto; max-height: min(65vh, 480px); }
@endif
.popup-ad-close { position: absolute; top: 8px; right: 8px; z-index: 10; width: 36px; height: 36px; border: 1px solid rgba(0,0,0,0.08); border-radius: 50%; background: rgba(255,255,255,0.95); color: #333; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: background 0.2s ease, color 0.2s ease, transform 0.15s ease; -webkit-tap-highlight-color: transparent; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
.popup-ad-close:hover, .popup-ad-close:focus { background: #fff; color: #000; outline: none; transform: scale(1.05); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.popup-ad-close .visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
.popup-ad-link { display: block; width: 100%; height: 100%; min-width: 0; min-height: 0; }
.popup-ad-img { max-width: 100% !important; max-height: 100% !important; width: auto !important; height: auto !important; object-fit: contain !important; object-position: center; display: block; }
.popup-ad-placeholder { padding: 2rem; text-align: center; color: #666; }
body.popup-ad-open { overflow: hidden; }

/* Tablet: smaller frame so it doesn't get cut off */
@media (max-width: 991px) {
    #popupAdOverlay.popup-ad-overlay { padding: 10px; }
    #popupAdOverlay .popup-ad-box {
        max-width: min(400px, 88vw) !important;
        max-height: min(420px, 78vh) !important;
        width: min(var(--popup-w), 400px) !important;
        height: min(var(--popup-h), 420px) !important;
        border-radius: 10px;
    }
    #popupAdOverlay .popup-ad-box.popup-ad-box--auto-height { max-height: min(420px, 78vh) !important; }
    .popup-ad-close { width: 32px; height: 32px; font-size: 1rem; top: 6px; right: 6px; }
}

/* Mobile: even smaller frame – fits screen, no cut-off */
@media (max-width: 576px) {
    #popupAdOverlay.popup-ad-overlay { padding: 8px; }
    #popupAdOverlay .popup-ad-box {
        max-width: min(320px, 92vw) !important;
        max-height: min(360px, 70vh) !important;
        width: min(var(--popup-w), 320px) !important;
        height: min(var(--popup-h), 360px) !important;
        border-radius: 8px;
    }
    #popupAdOverlay .popup-ad-box.popup-ad-box--auto-height { max-height: min(360px, 70vh) !important; }
    .popup-ad-close { width: 28px; height: 28px; font-size: 0.9rem; top: 5px; right: 5px; }
}
</style>
@endpush
@push('script')
<script>
(function() {
    var overlay = document.getElementById('popupAdOverlay');
    if (!overlay) return;
    var adId = {{ (int) $ad->id }};
    var storageKey = 'popup_ad_closed_' + adId;
    try {
        if (sessionStorage.getItem(storageKey) === '1') return;
    } catch (e) {}
    var delay = {{ (int) $ad->delay_seconds * 1000 }};
    var shown = false;
    function show() {
        if (shown) return;
        shown = true;
        overlay.style.display = 'flex';
        document.body.classList.add('popup-ad-open');
        requestAnimationFrame(function() { overlay.classList.add('popup-ad-overlay--visible'); });
        var closeBtn = document.getElementById('popupAdClose');
        if (closeBtn) { closeBtn.focus({ preventScroll: true }); }
    }
    function hide() {
        overlay.classList.remove('popup-ad-overlay--visible');
        overlay.style.display = 'none';
        document.body.classList.remove('popup-ad-open');
        try { sessionStorage.setItem(storageKey, '1'); } catch (e) {}
    }
    setTimeout(show, delay);
    var closeBtn = document.getElementById('popupAdClose');
    if (closeBtn) closeBtn.addEventListener('click', hide);
    var backdrop = overlay.querySelector('.popup-ad-backdrop');
    if (backdrop) backdrop.addEventListener('click', hide);
    overlay.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hide();
    });
})();
</script>
@endpush
@endif
