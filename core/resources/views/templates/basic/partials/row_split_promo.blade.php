@php
    $cfg = $rowModel->split_banner_json ?? [];
    $slides = isset($cfg['large']) && is_array($cfg['large']) ? $cfg['large'] : [];
    $small = isset($cfg['small']) && is_array($cfg['small']) ? $cfg['small'] : null;
    $hasLarge = count($slides) > 0;
    $hasSmall = $small && !empty($small['image']);
    $intervalMs = max(2000, min(30000, (int) ($cfg['interval'] ?? 5) * 1000));
    $rowId = (int) $rowModel->id;
    $cdRaw = $cfg['countdown_ends_at'] ?? null;
    $cdTitle = !empty($cfg['countdown_title']) ? $cfg['countdown_title'] : __('Offer ends in');
    $cdEnd = null;
    if ($cdRaw) {
        try {
            $cdEnd = \Carbon\Carbon::parse($cdRaw);
        } catch (\Throwable $e) {
            $cdEnd = null;
        }
    }
@endphp
<section class="row-split-promo mb-6 font-sans" id="row-split-promo-{{ $rowId }}" aria-label="@lang('Promotions')">
    <div class="w-full max-w-storefront mx-auto px-4 sm:px-6 lg:px-8">
        @if($cdEnd && $cdEnd->isFuture())
            <div class="offer-timer-bar offer-timer-bar--bar_small mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-200/90 bg-gradient-to-r from-amber-50 to-orange-50 px-4 py-3 shadow-sm" data-end-ts="{{ $cdEnd->timestamp * 1000 }}" role="status" aria-live="polite">
                <span class="text-sm font-semibold text-slate-800">{{ $cdTitle }}</span>
                <div class="offer-timer-bar__countdown flex gap-2" aria-label="@lang('Countdown')">
                    <span class="countdown-box rounded-lg border border-amber-100 bg-white px-3 py-1.5 text-center shadow-sm"><span class="countdown-hours font-mono text-base font-bold text-amber-600">00</span><span class="mt-0.5 block text-[10px] font-medium text-slate-500">@lang('Hrs')</span></span>
                    <span class="countdown-box rounded-lg border border-amber-100 bg-white px-3 py-1.5 text-center shadow-sm"><span class="countdown-mins font-mono text-base font-bold text-amber-600">00</span><span class="mt-0.5 block text-[10px] font-medium text-slate-500">@lang('Min')</span></span>
                    <span class="countdown-box rounded-lg border border-amber-100 bg-white px-3 py-1.5 text-center shadow-sm"><span class="countdown-secs font-mono text-base font-bold text-amber-600">00</span><span class="mt-0.5 block text-[10px] font-medium text-slate-500">@lang('Sec')</span></span>
                </div>
            </div>
        @endif
        <div class="grid grid-cols-1 {{ ($hasSmall && $hasLarge) ? 'lg:grid-cols-12' : '' }} gap-4 lg:gap-5 items-stretch">
            @if($hasLarge)
            <div class="{{ ($hasSmall && $hasLarge) ? 'lg:col-span-8' : 'lg:col-span-12' }} min-w-0">
                <div class="js-row-split-slider relative overflow-hidden rounded-2xl border border-slate-100 bg-gradient-to-br from-slate-50 to-slate-100/80 shadow-sm ring-1 ring-black/5"
                     data-interval-ms="{{ $intervalMs }}">
                    @foreach ($slides as $idx => $slide)
                        @php
                            $img = $slide['image'] ?? '';
                            $src = $img ? \App\Services\BannerService::rowSplitImageUrl($img) : '';
                            $href = trim((string) ($slide['url'] ?? ''));
                        @endphp
                        <div class="js-row-split-slide {{ $idx === 0 ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 md:p-8 lg:p-10 items-center">
                                <div class="relative z-10 order-2 md:order-1 text-center md:text-left">
                                    @if (!empty($slide['kicker']))
                                        <p class="text-sm font-medium text-amber-600 mb-2 tracking-wide">{{ $slide['kicker'] }}</p>
                                    @endif
                                    @if (!empty($slide['heading']))
                                        <h3 class="text-2xl sm:text-3xl font-bold text-slate-800 leading-tight mb-4">{{ $slide['heading'] }}</h3>
                                    @endif
                                    @if (!empty($slide['btn']))
                                        @if ($href !== '')
                                            <a href="{{ $href }}" class="inline-flex items-center gap-2 rounded-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2.5 shadow-sm transition-colors">
                                                {{ $slide['btn'] }}
                                                <svg class="w-4 h-4 -rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full bg-amber-500 text-white text-sm font-semibold px-6 py-2.5 shadow-sm">
                                                {{ $slide['btn'] }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <div class="relative order-1 md:order-2 flex justify-center">
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none" aria-hidden="true">
                                        <div class="w-48 h-48 sm:w-64 sm:h-64 rounded-full bg-rose-100/60 blur-2xl"></div>
                                    </div>
                                    @if ($src)
                                        <img src="{{ $src }}" alt="" class="relative z-[1] max-h-56 sm:max-h-64 object-contain drop-shadow-md" loading="{{ $idx === 0 ? 'eager' : 'lazy' }}" decoding="async" width="400" height="360">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if (count($slides) > 1)
                        <div class="js-row-split-dots flex justify-center gap-2 pb-4 pt-0"></div>
                    @endif
                </div>
            </div>
            @endif
            @if ($hasSmall)
                @php
                    $sImg = $small['image'] ?? '';
                    $sSrc = $sImg ? \App\Services\BannerService::rowSplitImageUrl($sImg) : '';
                    $sHref = trim((string) ($small['url'] ?? ''));
                    $smallCol = ($hasLarge && $hasSmall) ? 'lg:col-span-4' : 'lg:col-span-12';
                @endphp
                <div class="{{ $smallCol }} min-w-0">
                    <div class="h-full flex flex-col justify-between rounded-2xl border border-amber-100/80 bg-gradient-to-b from-amber-50/90 to-stone-50 p-6 lg:p-7 shadow-sm ring-1 ring-black/5">
                        <div>
                            @if (!empty($small['badge']))
                                <span class="inline-block rounded-full bg-white px-3 py-1 text-xs font-semibold text-amber-600 shadow-sm border border-amber-100/80 mb-3">{{ $small['badge'] }}</span>
                            @endif
                            @if (!empty($small['heading']))
                                <h3 class="text-xl sm:text-2xl font-bold text-slate-800 leading-snug mb-4">{{ $small['heading'] }}</h3>
                            @endif
                        </div>
                        @if ($sSrc)
                            <div class="flex justify-center my-4">
                                <img src="{{ $sSrc }}" alt="" class="max-h-44 object-contain drop-shadow" loading="lazy" decoding="async" width="280" height="320">
                            </div>
                        @endif
                        @if (!empty($small['btn']))
                            <div class="mt-auto pt-2">
                                @if ($sHref !== '')
                                    <a href="{{ $sHref }}" class="inline-flex items-center gap-2 rounded-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-6 py-2.5 shadow-sm transition-colors w-full sm:w-auto justify-center">
                                        {{ $small['btn'] }}
                                        <svg class="w-4 h-4 -rotate-45 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-500 text-white text-sm font-semibold px-6 py-2.5 shadow-sm w-full sm:w-auto justify-center">
                                        {{ $small['btn'] }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

@once
@push('script')
<script>
(function () {
  function initRowSplitSliders() {
    document.querySelectorAll('.js-row-split-slider').forEach(function (root) {
      if (root.getAttribute('data-rss-inited') === '1') return;
      var slides = root.querySelectorAll('.js-row-split-slide');
      if (!slides.length) return;
      root.setAttribute('data-rss-inited', '1');
      var ms = parseInt(root.getAttribute('data-interval-ms'), 10);
      if (!ms || ms < 2000) ms = 5000;
      var i = 0;
      var dotsWrap = root.querySelector('.js-row-split-dots');
      var dots = [];
      if (dotsWrap && slides.length > 1) {
        dotsWrap.innerHTML = '';
        for (var j = 0; j < slides.length; j++) {
          var d = document.createElement('button');
          d.type = 'button';
          d.className = 'h-2.5 rounded-full transition-all ' + (j === 0 ? 'w-6 bg-amber-500' : 'w-2.5 bg-slate-300');
          d.setAttribute('aria-label', 'Slide ' + (j + 1));
          (function (idx) {
            d.addEventListener('click', function () { go(idx); });
          })(j);
          dotsWrap.appendChild(d);
          dots.push(d);
        }
      }
      function go(idx) {
        i = idx;
        slides.forEach(function (s, k) {
          if (k === idx) { s.classList.remove('hidden'); }
          else { s.classList.add('hidden'); }
        });
        dots.forEach(function (d, k) {
          d.className = 'h-2.5 rounded-full transition-all ' + (k === idx ? 'w-6 bg-amber-500' : 'w-2.5 bg-slate-300');
        });
      }
      function next() {
        if (slides.length < 2) return;
        go((i + 1) % slides.length);
      }
      var t = slides.length > 1 ? setInterval(next, ms) : null;
      root.addEventListener('mouseenter', function () { if (t) { clearInterval(t); t = null; } });
      root.addEventListener('mouseleave', function () {
        if (slides.length > 1 && !t) t = setInterval(next, ms);
      });
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRowSplitSliders);
  } else {
    initRowSplitSliders();
  }
})();
</script>
@endpush
@endonce
