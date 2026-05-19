@extends($activeTemplate . 'layouts.frontend')
@push('body_attrs') data-page="home" @endpush
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
            $extPre = strtolower(pathinfo($firstFilename, PATHINFO_EXTENSION));
            if (!in_array($extPre, ['mp4', 'webm', 'ogv'])) {
                $homeBannerPreloadUrl = \App\Services\BannerService::bannerImageUrl($firstFilename);
                $dvPre = is_array($firstBanner->data_values ?? null) ? $firstBanner->data_values : (array)($firstBanner->data_values ?? []);
                $homeBannerPreloadMobileUrl = \App\Services\BannerService::mobileImageUrl($dvPre['mobile_image'] ?? null, $firstFilename);
            }
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
{{-- Premium Features CSS injected via partial --}}
{{-- inline style moved to critical-storefront.css --}}

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

    {{-- Banner ওপরে ticker/scrollbar --}}
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'banner_above', 'options' => ['page' => 'home']])

    @include('modules.Banner::home_banner', array_merge($bannerModuleData, [
        'flashSaleEndsAt' => config('stayl.flash_sale_ends_at'),
    ]))
    <div class="storefront-banner-separation" aria-hidden="true"></div>
    {{-- Banner নিচে ticker/scrollbar restore --}}
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'banner_below', 'options' => ['page' => 'home']])

    {{-- Premium Services/Promo Features section --}}
    @include($activeTemplate . 'partials.home_features')

    <div class="storefront-section-separation" aria-hidden="true"></div>
    {{-- Keep banner and category tightly synchronized; no extra banner-adjacent rows --}}

    @php
        $customRowsById = $customRowsById ?? [];
        $adSlotsById = $adSlotsById ?? [];
        $hpLayout = \App\Services\HomepageLayoutService::getOrderedSections();
        $renderedHomeSlots = [];
        $midBanners = \App\Models\Frontend::where('data_keys', 'middle_banner.element')->get();
        $bottomBanners = \App\Models\Frontend::where('data_keys', 'bottom_banner.element')->get();
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
        @elseif($hpId === 'middle_banner')
            @if($midBanners->isNotEmpty())
            <div class="main-container py-4">
                <div class="row">
                    @foreach($midBanners as $mb)
                        <div class="col-12 mb-4 px-2">
                            <a href="{{ $mb->data_values->url ?? '#' }}" class="block overflow-hidden rounded-2xl shadow-sm border border-slate-100 hover:scale-[1.005] transition-transform duration-300">
                                <img src="{{ getImage('assets/images/frontend/middle_banner/' . $mb->data_values->image, '1440x300') }}" alt="Banner" class="w-full h-auto" loading="lazy">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @elseif($hpId === 'bottom_banner')
            @if($bottomBanners->isNotEmpty())
            <div class="main-container py-4">
                <div class="row">
                    @foreach($bottomBanners as $bb)
                        <div class="col-12 mb-4 px-2">
                            <a href="{{ $bb->data_values->url ?? '#' }}" class="block overflow-hidden rounded-2xl shadow-sm border border-slate-100 hover:scale-[1.005] transition-transform duration-300">
                                <img src="{{ getImage('assets/images/frontend/bottom_banner/' . $bb->data_values->image, '1440x250') }}" alt="Banner" class="w-full h-auto" loading="lazy">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @elseif($hpId === 'home_category')
            @include($activeTemplate . 'partials.home_category_section', [
                'categoryScrollIntervalSec' => $hpInterval ?? $categoryScrollIntervalSec,
                'sectionTitle' => $hpLabel,
            ])
        @elseif($hpId === 'quick_deals')
            @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_line'])
            @include($activeTemplate . 'partials.product_carousel_section', [
                'products' => $todayDealProducts ?? collect(),
                'general' => $general,
                'activeTemplate' => $activeTemplate,
                'sectionTitle' => '<svg class="inline-block h-6 w-6 shrink-0 text-amber-500 sm:h-7 sm:w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg><span class="ml-2">' . e(__($hpLabel)) . '</span>',
                'sectionLink' => route('product.hot.deal'),
                'sectionLinkText' => __('View All'),
                'sectionId' => 'home-quick-deals',
                'sectionClass' => 'pro-section pro-section--tight',
                'sectionKey' => 'today_deals',
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
                'priorityFirst' => true,
                'forceScrollbars' => false,
            ])
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
                                            @php $dealP = productDisplayPricing($product); $price = $dealP['effective']; @endphp
                                            <a href="{{ product_detail_url($product) }}" class="deal__item">
                                                <div class="deal__item-img">
                                                    <img src="{{ getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product')) }}" loading="lazy" decoding="async" alt="{{ __($product->name) }}" width="150" height="150">
                                                </div>
                                                <div class="deal__item-cont">
                                                    <h6 class="price text--base">{{ $general->cur_sym }}{{ showAmount($price) }}</h6>
                                                    @if($dealP['show_strike'] && $dealP['compare_at'] !== null)
                                                        <del class="old-price">{{ $general->cur_sym }}{{ showAmount($dealP['compare_at']) }}</del>
                                                    @endif
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
            @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_line'])
            @include($activeTemplate . 'partials.hot_deal', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif($hpId === 'featured')
            @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_line'])
            @include($activeTemplate . 'partials.featured_product', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif($hpId === 'new_arrivals')
            @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_line'])
            @include($activeTemplate . 'partials.new_arrivals', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif($hpId === 'trending')
            @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_line'])
            @include($activeTemplate . 'partials.trending_now', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif($hpId === 'best_selling')
            @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_line'])
            @include($activeTemplate . 'partials.best_selling', [
                'sectionLabel' => $hpLabel,
                'carouselIntervalSec' => $hpInterval,
                'carouselSpeedMs' => $hpSpeedMs,
            ])
        @elseif(\Illuminate\Support\Str::startsWith($hpId, 'custom_row_'))
            @php $crid = (int) substr($hpId, 11); @endphp
            @if(!empty($customRowsById[$crid]))
                @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_line'])
                @include($activeTemplate . 'partials.custom_home_product_row', [
                    'rowModel' => $customRowsById[$crid]['row'],
                    'products' => $customRowsById[$crid]['products'] ?? collect(),
                    'general' => $general,
                    'activeTemplate' => $activeTemplate,
                    'sectionLabel' => '<svg style="width: 1.15em; height: 1.15em; vertical-align: -0.15em; margin-right: 0.35em; color: #14b8a6;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>' . e(__($hpLabel)),
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

  // Lazy load more products (button click or scroll into view via Intersection Observer)
  function loadMoreForSection(sectionId) {
    var section = document.getElementById(sectionId);
    if (!section || !section.dataset.sectionKey) return;
    var btn = section.querySelector(".home-load-more-btn");
    var wrap = section.querySelector(".product-line-flex-row");
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
          if (typeof window.refreshStaylLucide === "function") window.refreshStaylLucide(wrap);
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
      { sel: ".product-line-flex-row[data-auto-scroll=\"1\"]", card: ".product-card-col" },
      { sel: ".home-category-section__grid[data-auto-scroll=\"1\"]", card: ".home-category-section__card" }
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
          
          // Current position
          var cur = grid.scrollLeft;
          
          if (cur >= mx - 10) {
            // Reached the end: scroll back to start smoothly
            grid.scrollTo({ left: 0, behavior: "smooth" });
          } else {
            // Scroll to next step
            var target = Math.min(cur + step, mx);
            grid.scrollTo({ left: target, behavior: "smooth" });
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
          window.setTimeout(start, 2500);
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
      document.querySelectorAll(".product-line-flex-row[data-auto-scroll=\"1\"]").forEach(function(el) {
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
