@extends($activeTemplate . 'layouts.frontend')
@php
    $disableLegacyBootstrapBundle = true;
    $disableLegacyJquery = true;
@endphp
@php
    if (!isset($categoryScrollIntervalSec)) {
        $categoryScrollIntervalSec = function_exists('getSectionScrollIntervalSeconds')
            ? getSectionScrollIntervalSeconds(null)
            : 4;
    }
@endphp
@php
    $bannerModuleDataForPreload = $bannerModuleData ?? ['banners' => collect(), 'settings' => []];
    $bannersPreload = collect($bannerModuleDataForPreload['banners'] ?? []);
    $homeBannerPreloadUrl = '';
    $homeBannerPreloadMobileUrl = '';
    if ($bannersPreload->isNotEmpty()) {
        $firstBanner = $bannersPreload->first();
        $firstFilename = app(\App\Modules\Banner\BannerModuleService::class)->getImageFilename($firstBanner);
        if ($firstFilename !== null) {
            $homeBannerPreloadUrl = \App\Services\BannerService::bannerImageUrl($firstFilename);
            $dvPre = is_array($firstBanner->data_values ?? null) ? $firstBanner->data_values : (array)($firstBanner->data_values ?? []);
            $homeBannerPreloadMobileUrl = \App\Services\BannerService::mobileImageUrl($dvPre['mobile_image'] ?? null, $firstFilename);
        }
    }
@endphp
@if($homeBannerPreloadUrl !== '' && $homeBannerPreloadMobileUrl !== '' && $homeBannerPreloadMobileUrl !== $homeBannerPreloadUrl)
@push('head-meta')
<link rel="preload" as="image" href="{{ $homeBannerPreloadMobileUrl }}" fetchpriority="high" media="(max-width: 1024px)">
<link rel="preload" as="image" href="{{ $homeBannerPreloadUrl }}" fetchpriority="high" media="(min-width: 1025px)">
@endpush
@elseif($homeBannerPreloadUrl !== '')
@push('head-meta')
<link rel="preload" as="image" href="{{ $homeBannerPreloadUrl }}" fetchpriority="high">
@endpush
@endif
@push('style')
<style>
/* Below-fold — NOT .pro-section--tight (breaks product row scrollWidth / auto-scroll) */
.latest-products-section, .new-arrivals-section, .trending-now-section, .power-zone-section {
    content-visibility: auto;
    contain-intrinsic-size: auto 280px;
}

/* Home Category – কার্ডের মাঝে gap সর্বোচ্চ 1mm */
.home-category-section { margin-top: 0 !important; padding: 0 0 10px; background: #fff; border-bottom: 1px solid rgba(0,0,0,.06); min-width: 0; width: 100%; box-sizing: border-box; }
/* টাইটেল প্রোডাক্ট সেকশন হেডার লাইনের মতো — কার্ড সারি একই কন্টেন্ট উইথ (কোনো অতিরিক্ত পাশের বর্ডার/গাটার নয়) */
.home-category-section__title { font-size: 1.05rem; font-weight: 600; letter-spacing: 0.01em; color: #374151; margin: 0 0 8px; padding: 0; width: 100%; box-sizing: border-box; }
/* Hard clip: nothing draws past padded content area (fixes mobile right-edge bleed) */
.home-category-section__viewport {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow-x: hidden;
    overflow-y: visible;
    position: relative;
    box-sizing: border-box;
}
.home-category-section__grid {
    display: flex;
    gap: 1mm;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
    padding-bottom: 4px;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-x: contain;
    touch-action: pan-x pan-y;
    scroll-behavior: smooth;
    scroll-snap-type: x proximity;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.home-category-section__grid::-webkit-scrollbar { width: 0; height: 0; display: none; }
.home-category-section__grid + .home-category-section__grid { margin-top: 10px; }
/* ফ্রেম = ছোট স্কয়ার কার্ড, স্ক্রলযোগ্য রোতে অনেকগুলো ক্যাটাগরি দেখানোর জন্য */
.home-category-section__card { flex: 0 0 auto; width: 148px; max-width: 148px; aspect-ratio: 1; display: block; padding: 0; background: #f5f5f5; border: 1px solid rgba(0,0,0,.12); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06); text-decoration: none; color: #1f2937; transition: transform .2s, box-shadow .2s; box-sizing: border-box; overflow: hidden; position: relative; scroll-snap-align: start; }
.home-category-section__card:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,.1); }
/* ফ্রেমের ভেতরে ছবি পুরো জায়গা – সম্পূর্ণ বক্স ছবি */
.home-category-section__card-media { position: absolute; inset: 0; width: 100%; height: 100%; border-radius: inherit; overflow: hidden; line-height: 0; font-size: 0; }
.home-category-section__card-media img { position: absolute; left: 0; top: 0; width: 100%; height: 100%; min-width: 100%; min-height: 100%; object-fit: cover; object-position: center; display: block; }
.home-category-section__card-icon { position: absolute; inset: 0; font-size: 1.75rem; color: #6b7280; display: flex; align-items: center; justify-content: center; }
.home-category-section__card:hover .home-category-section__card-icon { color: var(--base, #6366f1); }
/* ক্যাটাগরি নাম ফ্রেমের নিচে ওভারলে – ছবির ওপর */
.home-category-section__card-label { position: absolute; bottom: 0; left: 0; right: 0; padding: 6px 6px 6px; background: linear-gradient(to top, rgba(0,0,0,.7), transparent); color: #fff; font-size: 0.7rem; font-weight: 600; line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-shadow: 0 0 2px rgba(0,0,0,.8); z-index: 1; }

/* Inner shell: keeps scroll track width = viewport shell (no horizontal page grow) */
.home-category-section .pass-scroll {
    position: relative;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow-x: hidden;
    box-sizing: border-box;
}
.pass-scroll__track { max-width: 100%; min-width: 0; box-sizing: border-box; }

/* Product line flex row — hidden scrollbar, smooth programmatic scroll */
.product-line-flex-row {
    display: flex;
    flex-wrap: nowrap;
    gap: 1mm;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-x: contain;
    touch-action: pan-x pan-y;
    scroll-snap-type: x proximity;
    padding-bottom: 6px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.product-line-flex-row::-webkit-scrollbar { width: 0; height: 0; display: none; }
.product-line-flex-row > .product-card-col {
    flex: 0 0 auto;
    scroll-snap-align: start;
    width: calc((100% - 1mm) / 2);
}
@media (min-width: 768px) { .product-line-flex-row > div.product-card-col { width: calc((100% - 3mm) / 4); } }
@media (min-width: 1024px) { .product-line-flex-row > div.product-card-col { width: calc((100% - 4mm) / 5); } }
@media (min-width: 1280px) { .product-line-flex-row > div.product-card-col { width: calc((100% - 5mm) / 6); } }
</style>
@endpush
@section('content')
    @php
        $bannerModuleData = $bannerModuleData ?? ['banners' => collect(), 'settings' => []];
        $bannerSettings = $bannerModuleData['settings'] ?? [];
        $slideIntervalSeconds = (int)($bannerSettings['slide_interval_seconds'] ?? 5);
        $bannerAutoplay = (int)($bannerSettings['autoplay'] ?? 1);
        // Category + Quick deals: একই ইন্টারভালে অটো হরিজন্টাল স্ক্রল
        $categoryScrollIntervalSec = getSectionScrollIntervalSeconds(null);
        $homeRowScrollIntervalSec = max(2, min(30, (int) $categoryScrollIntervalSec));
    @endphp

    {{-- ব্যানার সেকশন (Banner মডিউল) – সবসময় হেডারের নিচে --}}
    @include('modules.Banner::home_banner', $bannerModuleData)
    {{-- Banner নিচে ticker/scrollbar restore --}}
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'banner_above', 'options' => ['page' => 'home']])
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'banner_below', 'options' => ['page' => 'home']])
    {{-- Keep banner and category tightly synchronized; no extra banner-adjacent rows --}}

    @php
        $customRowsById = $customRowsById ?? [];
        $adSlotsById = $adSlotsById ?? [];
        $hpLayout = \App\Services\HomepageLayoutService::getOrderedSections();
        $renderedHomeSlots = [];
    @endphp
    @foreach($hpLayout as $hpSlot)
        @continue(empty($hpSlot['enabled']))
        @php $hpId = $hpSlot['id']; @endphp
        @php
            // Prevent accidental duplicate sections in homepage layout config.
            if (in_array($hpId, $renderedHomeSlots, true)) {
                continue;
            }
            $renderedHomeSlots[] = $hpId;
        @endphp
        @php
            $hpLabel = \App\Services\HomepageLayoutService::displayLabel($hpId, is_array($hpSlot) ? $hpSlot : []);
            $hpInterval = isset($hpSlot['interval_seconds']) && $hpSlot['interval_seconds'] !== '' ? (int) $hpSlot['interval_seconds'] : null;
            $hpSpeedMs = isset($hpSlot['speed_ms']) && $hpSlot['speed_ms'] !== '' ? (int) $hpSlot['speed_ms'] : null;
        @endphp

        @if($hpId === 'scrollbar')
            {{-- Kept for backward-compatible layout slot; banner scrollbars are rendered above unconditionally --}}
        @elseif($hpId === 'home_category')
            @include($activeTemplate . 'partials.home_category_section', [
                'categoryScrollIntervalSec' => $hpInterval ?? $categoryScrollIntervalSec,
                'sectionTitle' => $hpLabel,
            ])
        @elseif($hpId === 'quick_deals')
            @if(isset($todayDealProducts) && $todayDealProducts->isNotEmpty())
                @include($activeTemplate . 'partials.product_carousel_section', [
                    'products' => $todayDealProducts,
                    'general' => $general,
                    'sectionTitle' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #eab308;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>' . e($hpLabel),
                    'sectionId' => 'home-quick-deals',
                    'sectionClass' => 'pro-section pro-section--tight',
                    'sectionKey' => 'today_deals',
                    'carouselIntervalSec' => $hpInterval,
                    'carouselSpeedMs' => $hpSpeedMs,
                    'priorityFirst' => true,
                    'forceScrollbars' => false,
                ])
            @endif
        @elseif($hpId === 'power_zone')
            @if(($homeSectionData['settings']->power_zone_enabled ?? 1))
                @include($activeTemplate . 'partials.power_zone', ['todayDealProducts' => $todayDealProducts ?? collect()])
            @else
                <section class="banner-below-section">
                    <div class="w-full max-w-storefront mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="banner-below-row">
                            <div class="banner-below-card banner-below-card--categories">
                                <a href="{{ route('category.all') }}" class="banner-below-card__link">
                                    <div class="banner-below-card__icon-wrap">@include($activeTemplate . 'partials.icon', ['name' => 'th-large'])</div>
                                    <h6 class="banner-below-card__title">@lang('Categories')</h6>
                                </a>
                                <div class="banner-below-card__dropdown d-none d-lg-block">@include($activeTemplate . 'partials.navbar')</div>
                            </div>
                            <div class="banner-below-card banner-below-card--deals">
                                <div class="banner-below-card__header">
                                    <div class="banner-below-card__icon-wrap">@include($activeTemplate . 'partials.icon', ['name' => 'bolt'])</div>
                                    <h6 class="banner-below-card__title">@lang('Today\'s Deal')</h6>
                                </div>
                                <div class="banner-below-card__body">
                                    <div class="product-max-xl-slider">
                                        @forelse ($todayDealProducts ?? [] as $product)
                                            @php $price = productPrice($product); @endphp
                                            <a href="{{ product_detail_url($product) }}" class="deal__item">
                                                <div class="deal__item-img">
                                                    <img src="{{ getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product')) }}" loading="lazy" decoding="async" alt="{{ __($product->name) }}" width="150" height="150">
                                                </div>
                                                <div class="deal__item-cont">
                                                    <h6 class="price text--base">{{ $general->cur_sym }}{{ showAmount($price) }}</h6>
                                                    <del class="old-price">{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="deal__item"><div class="deal__item-cont"><h6 class="price text--base">@lang('No deal found yet')</h6></div></div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @elseif($hpId === 'hot_deal')
            @include($activeTemplate . 'partials.hot_deal', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif($hpId === 'featured')
            @include($activeTemplate . 'partials.featured_product', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif($hpId === 'new_arrivals')
            @include($activeTemplate . 'partials.new_arrivals', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif($hpId === 'trending')
            @include($activeTemplate . 'partials.trending_now', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif($hpId === 'best_selling')
            @include($activeTemplate . 'partials.best_selling', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif(\Illuminate\Support\Str::startsWith($hpId, 'custom_row_'))
            @php $crid = (int) substr($hpId, 11); @endphp
            @if(!empty($customRowsById[$crid]))
                @include($activeTemplate . 'partials.custom_home_product_row', [
                    'rowModel' => $customRowsById[$crid]['row'],
                    'products' => $customRowsById[$crid]['products'] ?? collect(),
                    'general' => $general,
                    'activeTemplate' => $activeTemplate,
                    'sectionLabel' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #14b8a6;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>' . e($hpLabel),
                    'carouselIntervalSec' => $hpInterval,
                    'carouselSpeedMs' => $hpSpeedMs,
                ])
            @endif
        @elseif(\Illuminate\Support\Str::startsWith($hpId, 'ad_slot_'))
            @php $adid = (int) substr($hpId, 8); @endphp
            @if(!empty($adSlotsById[$adid]))
                @include($activeTemplate . 'partials.homepage_ad_slot', [
                    'ad' => $adSlotsById[$adid],
                ])
            @endif
        @elseif($hpId === 'social_proof')
            @include($activeTemplate . 'partials.social_proof')
        @elseif($hpId === 'recommendations')
            @include($activeTemplate . 'partials.recommendations', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @endif
    @endforeach
@endsection

@push('script')
<script>
(function() {
  try {
  var slideIntervalMs = {{ $slideIntervalSeconds }} * 1000;
  if (slideIntervalMs < 1000) slideIntervalMs = 5000;
  if (slideIntervalMs > 60000) slideIntervalMs = 60000;
  var autoplay = {{ $bannerAutoplay }} ? 1 : 0;

  function runNativeBannerSlider() {
    var section = document.getElementById("home-banner-section");
    if (!section) return;
    var slider = section.querySelector(".js-banner-slider");
    if (!slider) return;
    var slides = slider.querySelectorAll(".banner-slide-inner");
    if (slides.length === 0) return;
    var intervalMs = parseInt(slider.getAttribute("data-slide-interval-ms"), 10) || slideIntervalMs;
    var autoplayVal = slider.getAttribute("data-autoplay");
    if (autoplayVal !== null && autoplayVal !== "") autoplay = (autoplayVal === "1" || autoplayVal === "true") ? 1 : 0;

    slides.forEach(function(s, i) { s.classList.remove("banner-slide-active"); s.setAttribute("data-slide-index", i); });
    slides[0].classList.add("banner-slide-active");

    var dotsWrap = section.querySelector(".banner-slider-dots");
    if (dotsWrap && slides.length > 1) {
      dotsWrap.innerHTML = "";
      for (var i = 0; i < slides.length; i++) {
        var dot = document.createElement("span");
        dot.className = "dot" + (i === 0 ? " active" : "");
        dot.setAttribute("data-index", i);
        dot.setAttribute("aria-label", "Slide " + (i + 1));
        (function(idx) { dot.addEventListener("click", function() { goTo(idx); }); })(i);
        dotsWrap.appendChild(dot);
      }
    }

    var currentIndex = 0;
    var timerId = null;

    function goTo(index) {
      if (index < 0) index = slides.length - 1;
      if (index >= slides.length) index = 0;
      currentIndex = index;
      slides.forEach(function(s, i) {
        s.classList.toggle("banner-slide-active", i === currentIndex);
      });
      if (dotsWrap) {
        var dots = dotsWrap.querySelectorAll(".dot");
        dots.forEach(function(d, i) { d.classList.toggle("active", i === currentIndex); });
      }
    }

    function next() {
      goTo(currentIndex + 1);
    }

    if (autoplay === 1 && slides.length > 1) {
      timerId = setInterval(next, intervalMs);
    }

    window.__bannerGoTo = goTo;
    window.__bannerNext = next;
  }

  function init() {
    runNativeBannerSlider();
  }

  if (document.readyState === "complete") {
    init();
  } else {
    window.addEventListener("load", init);
  }

  // Lazy load more products (button click or scroll into view via Intersection Observer)
  function loadMoreForSection(sectionId) {
    var section = document.getElementById(sectionId);
    if (!section || !section.dataset.sectionKey) return;
    var btn = section.querySelector(".home-load-more-btn");
    var wrap = section.querySelector(".product-carousel-track-inner");
    var sentinel = section.querySelector("[data-load-more-sentinel]");
    if (!btn || !wrap || !sentinel || btn.disabled) return;
    var sectionKey = section.dataset.sectionKey;
    var offset = parseInt(section.dataset.loadMoreOffset, 10) || 8;
    btn.disabled = true;
    btn.textContent = "…";
    fetch("{{ route('home.section.products') }}?section=" + encodeURIComponent(sectionKey) + "&offset=" + offset + "&limit=8", {
      headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
    })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.html && data.html.length > 0) {
          wrap.insertAdjacentHTML("beforeend", data.html);
          section.dataset.loadMoreOffset = offset + (data.count || 0);
        }
        if (!data.count || data.count < 8) {
          sentinel.style.display = "none";
        }
      })
      .catch(function() { sentinel.style.display = "none"; })
      .finally(function() {
        btn.disabled = false;
        btn.textContent = "{{ __('Load more') }}";
      });
  }
  document.addEventListener("click", function(e) {
    var btn = e.target.closest(".home-load-more-btn");
    if (btn) loadMoreForSection(btn.getAttribute("data-section-id"));
  });
  if ("IntersectionObserver" in window) {
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (!entry.isIntersecting) return;
        var sentinel = entry.target;
        var section = sentinel.closest("[data-section-key]");
        if (section) {
          io.unobserve(sentinel);
          loadMoreForSection(section.id);
        }
      });
    }, { rootMargin: "100px", threshold: 0.1 });
    window.addEventListener("load", function() {
      document.querySelectorAll("[data-load-more-sentinel]").forEach(function(el) {
        if (el.querySelector(".home-load-more-btn")) io.observe(el);
      });
    });
  }

  // Category + product rows: every data-interval-sec, smooth-scroll one card width (right→left); at end smooth back to start; no visible scrollbar
  function initHomeHorizontalAutoScroll() {
    var rows = [
      { sel: ".home-category-section__grid[data-auto-scroll=\"1\"]", card: ".home-category-section__card" },
      { sel: ".product-line-flex-row[data-auto-scroll=\"1\"]", card: ".product-card-col" }
    ];

    function gapPx(el) {
      var st = getComputedStyle(el);
      var g = st.gap || st.columnGap;
      if (!g || g === "normal") return 0;
      var n = parseFloat(g, 10);
      return isNaN(n) ? 0 : n;
    }

    function stepForGrid(grid, cardSel) {
      var first = grid.querySelector(cardSel);
      if (!first) return 0;
      return first.getBoundingClientRect().width + gapPx(grid);
    }

    rows.forEach(function(cfg) {
      document.querySelectorAll(cfg.sel).forEach(function(grid) {
        if (grid.dataset.staylAutoBound === "1") {
          if (grid._staylScrollTimer) clearInterval(grid._staylScrollTimer);
          grid._staylScrollTimer = null;
        }

        var cards = grid.querySelectorAll(cfg.card);
        if (cards.length < 2) return;

        var sec = parseFloat(grid.getAttribute("data-interval-sec"), 10);
        if (!sec || sec < 2) sec = 4;
        if (sec > 30) sec = 30;
        var intervalMs = Math.round(sec * 1000);

        var paused = false;

        function maxScroll() {
          return grid.scrollWidth - grid.clientWidth;
        }

        function tick() {
          if (paused) return;
          var mx = maxScroll();
          if (mx <= 4) return;
          var step = stepForGrid(grid, cfg.card);
          if (step < 48) step = Math.min(200, mx);
          if (grid.scrollLeft >= mx - 6) {
            grid.scrollTo({ left: 0, behavior: "smooth" });
          } else {
            var next = Math.min(grid.scrollLeft + step, mx);
            grid.scrollTo({ left: next, behavior: "smooth" });
          }
        }

        function start() {
          paused = false;
        }

        function stop() {
          paused = true;
        }

        grid.addEventListener("mouseenter", stop);
        grid.addEventListener("mouseleave", start);
        grid.addEventListener("touchstart", stop, { passive: true });
        grid.addEventListener("touchend", function() {
          window.setTimeout(start, 1800);
        }, { passive: true });

        grid._staylScrollTimer = setInterval(tick, intervalMs);
        grid.dataset.staylAutoBound = "1";
      });
    });
  }

  function runHomeAutoScrollWhenReady() {
    if (document.readyState === "complete") {
      requestAnimationFrame(function() {
        requestAnimationFrame(initHomeHorizontalAutoScroll);
      });
    } else {
      window.addEventListener("load", function() {
        requestAnimationFrame(function() {
          requestAnimationFrame(initHomeHorizontalAutoScroll);
        });
      });
    }
  }
  runHomeAutoScrollWhenReady();

  var _staylResizeT;
  window.addEventListener("resize", function() {
    window.clearTimeout(_staylResizeT);
    _staylResizeT = window.setTimeout(function() {
      document.querySelectorAll(".home-category-section__grid[data-auto-scroll=\"1\"], .product-line-flex-row[data-auto-scroll=\"1\"]").forEach(function(el) {
        if (el._staylScrollTimer) {
          clearInterval(el._staylScrollTimer);
          el._staylScrollTimer = null;
        }
        delete el.dataset.staylAutoBound;
      });
      initHomeHorizontalAutoScroll();
    }, 350);
  });
  } catch (e) {
    console.error('home storefront script failed', e);
  }
})();
</script>
@endpush
