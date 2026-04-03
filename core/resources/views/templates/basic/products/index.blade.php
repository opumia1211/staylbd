@extends($activeTemplate . 'layouts.frontend')
@php
    $disableLegacyBootstrapBundle = true;
    $disableLegacyJquery = true;
    $disableLegacyJqueryUi = true;
@endphp
@section('content')
    <section class="products-section products-section--compact products-page-with-pro-filter pb-60 wow fadeInUp" data-wow-duration="0.35s" data-wow-delay="0.05s">
        @php
            $catId = $currentCategoryId ?? $currentSubcategoryId ?? null;
            $categoryTimers = $catId ? get_offer_timers_for_display('category', 'category_top', null, $catId) : get_offer_timers_for_display('category', 'category_top');
        @endphp
        @if($categoryTimers->isNotEmpty())
            <div class="container mb-3">
                @foreach($categoryTimers as $ct)
                    @include('partials.offer_timer_bar', ['timer' => $ct])
                @endforeach
            </div>
        @endif
        {{-- Scrollbar slots for product/category listing pages --}}
        @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_listing_above'])
        @include($activeTemplate . 'partials.scrollbar', ['position' => 'category_above'])
        @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_listing'])
        @include($activeTemplate . 'partials.scrollbar', ['position' => 'category_page'])
        {{-- Full-width row: sidebar at absolute left, then main content --}}
        <div class="row products-page-row g-0">
            {{-- Left sidebar: একই প্রোফেশনাল ফিল্টার সাইডবার সব প্রোডাক্ট পেজে (category, brand, all/products, search, featured, hot deal ইত্যাদি) --}}
            @php
                $subcategoryList = $data['subcategoryList'] ?? collect();
                $sizeList = $data['sizeList'] ?? collect();
                $colorList = $data['colorList'] ?? collect();
                $curSym = $general->cur_sym ?? '৳';
                $minP = (int) $data['minPrice'];
                $maxP = (int) $data['maxPrice'];
                $rangeStep = $maxP > $minP ? max(1, (int) floor(($maxP - $minP) / 4)) : 1;
                $priceRanges = [];
                if ($maxP > $minP) {
                    $priceRanges = [
                        ['label' => __('Under') . ' ' . $curSym . number_format($minP + $rangeStep), 'min' => $minP, 'max' => $minP + $rangeStep],
                        ['label' => $curSym . number_format($minP + $rangeStep) . ' - ' . $curSym . number_format($minP + 2 * $rangeStep), 'min' => $minP + $rangeStep, 'max' => $minP + 2 * $rangeStep],
                        ['label' => $curSym . number_format($minP + 2 * $rangeStep) . ' - ' . $curSym . number_format($minP + 3 * $rangeStep), 'min' => $minP + 2 * $rangeStep, 'max' => $minP + 3 * $rangeStep],
                        ['label' => __('Above') . ' ' . $curSym . number_format($minP + 3 * $rangeStep), 'min' => $minP + 3 * $rangeStep, 'max' => $maxP],
                    ];
                } else {
                    $priceRanges = [['label' => __('All'), 'min' => $minP, 'max' => $maxP]];
                }
            @endphp
            <div class="col-12 col-lg-3 order-2 order-lg-1 products-sidebar-col">
                    @include($activeTemplate . 'products.partials.filter_sidebar')
            </div>

            <div class="col-12 col-lg-9 order-1 order-lg-2 products-main-col">
                <div class="products-main-inner container">
                    <div class="products-wrapper">
                        @if (request()->search)
                            <div class="products-search-info mb-2">
                                <strong>@lang('Search results for') <span class="text--base">{{ __(request()->search) }}</span> — {{ $products->total() }} @lang('found')</strong>
                            </div>
                        @endif
                        <div class="products-toolbar">
                            <div class="products-toolbar__left">
                                <h1 class="products-toolbar__title">{{ $pageTitle ?? __('All Products') }}</h1>
                                <span class="products-toolbar__sub d-none d-md-inline">{{ isset($currentCategoryId) || isset($currentSubcategoryId) ? __('Products in this category') : __('Browse catalog') }}</span>
                            </div>
                            <div class="products-toolbar__right">
                                <label class="products-toolbar__select-wrap">
                                    <span class="d-none d-sm-inline">@lang('Sort')</span>
                                    <select class="products-toolbar__select sortProduct">
                                        <option value="" selected disabled>@lang('Sort By')</option>
                                        <option value="id_desc">@lang('Latest')</option>
                                        <option value="price_asc">@lang('Low to High')</option>
                                        <option value="price_desc">@lang('High to Low')</option>
                                    </select>
                                </label>
                                <label class="products-toolbar__select-wrap">
                                    <span class="d-none d-sm-inline">@lang('Per page')</span>
                                    <select class="products-toolbar__select productPaginate">
                                        <option value="12">12</option>
                                        <option value="20" selected>20</option>
                                        <option value="40">40</option>
                                        <option value="60">60</option>
                                        <option value="80">80</option>
                                        <option value="100">100</option>
                                    </select>
                                </label>
                                <div class="filter--bar d-lg-none products-toolbar__filter-btn">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'filter']) @lang('Filter')
                                </div>
                            </div>
                        </div>
                        <div class="loader-wrapper d-none">
                            <div class="loader"></div>
                        </div>
                        <div class="product-grid-container products-page-rail-container">
                        <div id="products">
                            @include($activeTemplate . 'products.show_products')
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include($activeTemplate . 'partials.scrollbar', ['position' => 'product_listing_below'])
        @include($activeTemplate . 'partials.scrollbar', ['position' => 'category_below'])
    </section>
@endsection
@push('script')
    <script>
        (function() {
            "use strict";

            var page = null;
            var activeFetchController = null;
            var dataMinPrice = {{ $data['minPrice'] }};
            var dataMaxPrice = {{ $data['maxPrice'] }};
            var currentCategoryId = {{ isset($currentCategoryId) ? (int)$currentCategoryId : 'null' }};
            var currentSubcategoryId = {{ isset($currentSubcategoryId) ? (int)$currentSubcategoryId : 'null' }};
            var productScope = @if(isset($productScope) && in_array($productScope, ['featured','hotDeal','bestSelling','todayDeal'], true))"{{ $productScope }}"@else null @endif;
            var productsEl = null;

            function setAutoCols() {
                productsEl = productsEl || document.getElementById('products');
                if (!productsEl) return;
                if (productsEl.getAttribute('data-auto-cols') === '0') return;
                var w = window.innerWidth;
                var cols = (w >= 2560) ? '7' : (w >= 1536) ? '6' : (w >= 1200) ? '5' : (w >= 992) ? '4' : (w >= 768) ? '3' : (w >= 480) ? '2' : '1';
                productsEl.setAttribute('data-cols', cols);
                var activeBtn = document.querySelector('.products-toolbar__grid-btn[data-cols="' + cols + '"]');
                if (activeBtn) {
                    document.querySelectorAll('.products-toolbar__grid-btn').forEach(function(b) { b.classList.remove('active'); });
                    activeBtn.classList.add('active');
                }
            }
            setAutoCols();
            window.addEventListener('resize', function() {
                clearTimeout(window._filterResizeTimer);
                window._filterResizeTimer = setTimeout(setAutoCols, 120);
            });

            document.addEventListener('click', function(e) {
                var btn = e.target.closest('.products-toolbar__grid-btn');
                if (!btn) return;
                productsEl = productsEl || document.getElementById('products');
                var cols = btn.getAttribute('data-cols');
                if (productsEl && cols) {
                    productsEl.setAttribute('data-cols', cols);
                    productsEl.setAttribute('data-auto-cols', '0');
                    document.querySelectorAll('.products-toolbar__grid-btn').forEach(function(b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                }
            });
            function qsa(selector, root) { return Array.prototype.slice.call((root || document).querySelectorAll(selector)); }
            function qs(selector, root) { return (root || document).querySelector(selector); }
            function goToPageOne() { page = null; }
            function selectedValues(name) {
                return qsa('[name="' + name + '"]:checked').map(function(el) { return (el.value || '').trim(); }).filter(Boolean);
            }

            function updateFilterActiveCount() {
                var count = 0;
                ['category', 'brand', 'subcategory', 'discount_offer', 'size', 'color'].forEach(function(name) {
                    count += selectedValues(name).length;
                });
                var minPrice = parseInt((qs('input[name="min_price"]') || {}).value, 10);
                var maxPrice = parseInt((qs('input[name="max_price"]') || {}).value, 10);
                if (!isNaN(minPrice) && !isNaN(maxPrice) && (minPrice !== dataMinPrice || maxPrice !== dataMaxPrice)) count++;
                var searchInput = qs('#filterProductSearch');
                if (searchInput && searchInput.value.trim().length >= 2) count++;
                var badge = qs('#filterActiveCount');
                if (!badge) return;
                if (count > 0) { badge.textContent = String(count); badge.classList.add('visible'); }
                else { badge.textContent = ''; badge.classList.remove('visible'); }
            }

            function syncPriceFromInputs(triggerFetch) {
                var minInput = qs('#priceMinInput');
                var maxInput = qs('#priceMaxInput');
                var minHidden = qs('input[name="min_price"]');
                var maxHidden = qs('input[name="max_price"]');
                if (!minInput || !maxInput || !minHidden || !maxHidden) return;
                var minVal = parseInt(String(minInput.value || '').trim(), 10);
                var maxVal = parseInt(String(maxInput.value || '').trim(), 10);
                if (isNaN(minVal)) minVal = dataMinPrice;
                if (isNaN(maxVal)) maxVal = dataMaxPrice;
                minVal = Math.max(dataMinPrice, Math.min(dataMaxPrice, minVal));
                maxVal = Math.max(dataMinPrice, Math.min(dataMaxPrice, maxVal));
                if (minVal > maxVal) { var t = minVal; minVal = maxVal; maxVal = t; }
                minInput.value = String(minVal);
                maxInput.value = String(maxVal);
                minHidden.value = String(minVal);
                maxHidden.value = String(maxVal);
                updateFilterActiveCount();
                if (triggerFetch) { goToPageOne(); fetchProduct(); }
            }

            function openFilterPanel() {
                qsa('.filter--sidebar, .overlay').forEach(function(el) { el.classList.add('active'); });
                document.body.classList.add('filter-open');
                document.body.style.overflow = 'hidden';
                var panelOverlay = qs('.filter-overlay');
                if (!panelOverlay) {
                    panelOverlay = document.createElement('div');
                    panelOverlay.className = 'filter-overlay';
                    document.body.appendChild(panelOverlay);
                }
                panelOverlay.classList.add('active');
            }

            function closeFilterPanel() {
                qsa('.filter--sidebar, .overlay').forEach(function(el) { el.classList.remove('active'); });
                document.body.classList.remove('filter-open');
                document.body.style.overflow = '';
                var panelOverlay = qs('.filter-overlay');
                if (panelOverlay) panelOverlay.remove();
            }

            function fetchProduct() {
                productsEl = productsEl || document.getElementById('products');
                if (!productsEl) return;
                if (activeFetchController) activeFetchController.abort();
                activeFetchController = new AbortController();
                productsEl.classList.add('updating');

                var payload = new URLSearchParams();
                var minEl = qs('input[name="min_price"]');
                var maxEl = qs('input[name="max_price"]');
                var sortEl = qs('.sortProduct');
                var paginateEl = qs('.productPaginate');
                var searchEl = qs('#filterProductSearch');
                payload.append('min', minEl ? (minEl.value || '') : '');
                payload.append('max', maxEl ? (maxEl.value || '') : '');
                payload.append('sort', sortEl ? (sortEl.value || '') : '');
                payload.append('paginate', paginateEl ? (paginateEl.value || '') : '');
                payload.append('search', searchEl ? searchEl.value.trim() : '');
                if (productScope !== null) payload.append('product_scope', productScope);
                if (currentCategoryId !== null) payload.append('categoryId', String(currentCategoryId));
                if (currentSubcategoryId !== null) payload.append('subcategoryId', String(currentSubcategoryId));
                selectedValues('category').forEach(function(v){ payload.append('categories[]', v); });
                selectedValues('brand').forEach(function(v){ payload.append('brands[]', v); });
                selectedValues('subcategory').forEach(function(v){ payload.append('subcategories[]', v); });
                selectedValues('discount_offer').forEach(function(v){ payload.append('discount_offers[]', v); });
                selectedValues('size').forEach(function(v){ payload.append('sizes[]', v); });
                selectedValues('color').forEach(function(v){ payload.append('colors[]', v); });

                var url = "{{ route('all.products.filter') }}";
                if (page) url += (url.indexOf('?') >= 0 ? '&' : '?') + 'page=' + encodeURIComponent(page);
                url += (url.indexOf('?') >= 0 ? '&' : '?') + payload.toString();

                fetch(url, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    signal: activeFetchController.signal
                }).then(function(res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.text();
                }).then(function(html) {
                    productsEl.innerHTML = html;
                    productsEl.classList.remove('updating');
                    if (typeof window.getWishlistCount === 'function') window.getWishlistCount();
                    if (typeof window.initProductCardGalleryCycle === 'function') window.initProductCardGalleryCycle();
                    initProductsRailAutoScroll();
                }).catch(function(err) {
                    if (err && err.name === 'AbortError') return;
                    productsEl.classList.remove('updating');
                    if (window.console && console.error) console.error('Filter error:', err);
                });
            }

            function initProductsRailAutoScroll() { return; }

            function getWishlistCountVanilla() {
                fetch("{{ route('wish.list.count') }}", {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                }).then(function(r){ return r.json(); }).then(function(response){
                    var values = Object.values(response || {});
                    qsa('.add-wishlist').forEach(function(btn){ btn.classList.remove('active'); });
                    values.forEach(function(val){
                        if (!val || !val.product_id) return;
                        qsa('[data-product_id="' + val.product_id + '"]').forEach(function(el){
                            var w = el.closest('.add-wishlist');
                            if (w) w.classList.add('active');
                        });
                    });
                    qsa('.show-wishlist-count').forEach(function(el){ el.textContent = String(values.length); });
                }).catch(function(){});
            }
            window.getWishlistCount = getWishlistCountVanilla;

            document.addEventListener('change', function(e) {
                var t = e.target;
                if (!t) return;
                if (t.matches('.sortCategory, .sortBrand')) {
                    if (t.id === 'cate-0' && t.checked) qsa("input[name='category']").forEach(function(el){ if (el.id !== 'cate-0') el.checked = false; });
                    if (t.id !== 'cate-0' && t.checked) { var c0 = qs('#cate-0'); if (c0) c0.checked = false; }
                    if (t.id === 'brand0' && t.checked) qsa("input[name='brand']").forEach(function(el){ if (el.id !== 'brand0') el.checked = false; });
                    if (t.id !== 'brand0' && t.checked) { var b0 = qs('#brand0'); if (b0) b0.checked = false; }
                    updateFilterActiveCount(); goToPageOne(); fetchProduct(); return;
                }
                if (t.matches('.sortSubcategory, .filterDiscountOffer, .filterSize, .filterColor, .sortProduct, .productPaginate')) {
                    updateFilterActiveCount(); goToPageOne(); fetchProduct(); return;
                }
            });

            document.addEventListener('input', function(e) {
                var t = e.target;
                if (!t) return;
                if (t.matches('#filterProductSearch')) {
                    var q = (t.value || '').toLowerCase().trim();
                    qsa('.product-filter-category-item').forEach(function(el){ var n = (el.dataset.name || ''); el.style.display = (!q || n.indexOf(q) !== -1) ? '' : 'none'; });
                    qsa('.product-filter-brand-item').forEach(function(el){ var n = (el.dataset.name || ''); el.style.display = (!q || n.indexOf(q) !== -1) ? '' : 'none'; });
                }
            });

            document.addEventListener('blur', function(e){
                if (e.target && (e.target.matches('#priceMinInput') || e.target.matches('#priceMaxInput'))) {
                    setTimeout(function(){ syncPriceFromInputs(false); }, 0);
                }
            }, true);

            document.addEventListener('keydown', function(e) {
                if (e.target && (e.target.matches('#priceMinInput') || e.target.matches('#priceMaxInput') || e.target.matches('#filterProductSearch')) && e.key === 'Enter') {
                    e.preventDefault();
                    syncPriceFromInputs(false);
                    goToPageOne();
                    fetchProduct();
                }
                if (e.key === 'Escape' && qs('.filter--sidebar.active')) closeFilterPanel();
            });

            document.addEventListener('click', function(e) {
                var target = e.target;
                if (!target) return;
                if (target.closest('.filter--bar')) { openFilterPanel(); return; }
                if (target.closest('.close--sidebar') || target.closest('.filter-overlay')) { closeFilterPanel(); return; }

                var pagination = target.closest('.pagination a');
                if (pagination) {
                    e.preventDefault();
                    var href = pagination.getAttribute('href') || '';
                    var match = href.match(/page=(\d+)/);
                    page = match ? match[1] : null;
                    fetchProduct();
                    return;
                }

                var priceRadio = target.closest('.filter-price-radio');
                if (priceRadio) {
                    e.preventDefault();
                    var min = parseInt(priceRadio.getAttribute('data-min'), 10);
                    var max = parseInt(priceRadio.getAttribute('data-max'), 10);
                    if (isNaN(min)) min = dataMinPrice;
                    if (isNaN(max)) max = dataMaxPrice;
                    var minInput = qs('#priceMinInput'); var maxInput = qs('#priceMaxInput');
                    var minHidden = qs('input[name="min_price"]'); var maxHidden = qs('input[name="max_price"]');
                    if (minInput) minInput.value = String(min);
                    if (maxInput) maxInput.value = String(max);
                    if (minHidden) minHidden.value = String(min);
                    if (maxHidden) maxHidden.value = String(max);
                    qsa('.filter-price-radio').forEach(function(el){ el.classList.remove('active'); });
                    priceRadio.classList.add('active');
                    updateFilterActiveCount(); goToPageOne(); fetchProduct();
                    return;
                }

                if (target.closest('#filterPriceReset')) {
                    e.preventDefault();
                    var mI = qs('#priceMinInput'); var xI = qs('#priceMaxInput');
                    var mH = qs('input[name="min_price"]'); var xH = qs('input[name="max_price"]');
                    if (mI) mI.value = String(dataMinPrice); if (xI) xI.value = String(dataMaxPrice);
                    if (mH) mH.value = String(dataMinPrice); if (xH) xH.value = String(dataMaxPrice);
                    qsa('.filter-price-radio').forEach(function(el){ el.classList.remove('active'); });
                    var first = qs('.filter-price-radio'); if (first) first.classList.add('active');
                    updateFilterActiveCount(); goToPageOne(); fetchProduct();
                    return;
                }

                var clearBtn = target.closest('[data-clear]');
                if (clearBtn) {
                    e.preventDefault();
                    var section = clearBtn.getAttribute('data-clear');
                    if (section === 'brand') { qsa('input[name=brand]').forEach(function(el){ el.checked = false; }); var b0 = qs('#brand0'); if (b0) b0.checked = true; }
                    else if (section === 'subcategory') { qsa('input[name=subcategory]').forEach(function(el){ el.checked = false; }); }
                    else if (section === 'discount') { qsa('input[name=discount_offer]').forEach(function(el){ el.checked = false; }); }
                    else if (section === 'size') { qsa('input[name=size]').forEach(function(el){ el.checked = false; }); }
                    else if (section === 'color') { qsa('input[name=color]').forEach(function(el){ el.checked = false; }); }
                    updateFilterActiveCount(); goToPageOne(); fetchProduct();
                    return;
                }

                if (target.closest('#productFilterClear')) {
                    qsa('.sortCategory').forEach(function(el){ el.checked = false; }); var c0 = qs('#cate-0'); if (c0) c0.checked = true;
                    qsa('.sortBrand').forEach(function(el){ el.checked = false; }); var b00 = qs('#brand0'); if (b00) b00.checked = true;
                    qsa('input[name=subcategory], input[name=discount_offer], input[name=size], input[name=color]').forEach(function(el){ el.checked = false; });
                    var minIn = qs('#priceMinInput'); var maxIn = qs('#priceMaxInput');
                    var minHid = qs('input[name=min_price]'); var maxHid = qs('input[name=max_price]');
                    if (minIn) minIn.value = String(dataMinPrice); if (maxIn) maxIn.value = String(dataMaxPrice);
                    if (minHid) minHid.value = String(dataMinPrice); if (maxHid) maxHid.value = String(dataMaxPrice);
                    var s = qs('#filterProductSearch'); if (s) s.value = '';
                    qsa('.product-filter-category-item, .product-filter-brand-item').forEach(function(el){ el.style.display = ''; });
                    qsa('.filter-price-radio').forEach(function(el){ el.classList.remove('active'); });
                    var firstR = qs('.filter-price-radio'); if (firstR) firstR.classList.add('active');
                    updateFilterActiveCount(); goToPageOne(); fetchProduct();
                    return;
                }

                if (target.closest('.product-filter-apply-btn')) {
                    syncPriceFromInputs(false); goToPageOne(); fetchProduct();
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                qsa('.loader-wrapper').forEach(function(el){ el.style.display = 'none'; });
                updateFilterActiveCount();
                getWishlistCountVanilla();
                initProductsRailAutoScroll();
            });
        })();
    </script>
@endpush

@push('style')
    <style>
        /* প্রোডাক্ট লিস্টিং পেজে শুধু নতুন প্রফেশনাল ফিল্টার – পুরনো সাইডবার লুকিয়ে রাখা */
        .products-page-with-pro-filter .site-filter-sidebar,
        .products-page-with-pro-filter .site-filter-toggle,
        .products-page-with-pro-filter .site-filter-overlay {
            display: none !important;
        }
        /* পুরনো গ্লাস স্টাইল ওভাররাইড: সাদা কার্ড + লাইট গ্রে ব্যাকগ্রাউন্ড (স্ক্রিনশটের মতো) */
        .products-page-with-pro-filter .product-filter-panel.filter--sidebar,
        .products-page-with-pro-filter .product-filter-panel--pro.filter--sidebar {
            background: #fff !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border: 1px solid rgba(0,0,0,0.06) !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
            border-radius: 10px !important;
        }
        .products-page-header { padding-bottom: 0.5rem; }
        .products-page-title { font-size: 1.5rem; font-weight: 700; }
        .products-main-inner { padding-top: 0; }
        .filter--sidebar-inner { padding: 0; }

        /* প্রোডাক্ট ফিল্টার সাইডবার: স্ক্রিনশটের মতো লাইট গ্রে ব্যাকগ্রাউন্ড, সাদা কার্ড */
        .products-sidebar-col {
            background: #f0f2f5;
            padding: 0.75rem 0.6rem;
            border-radius: 0 8px 8px 0;
        }
        .product-filter-panel--pro .filter--sidebar-inner {
            padding: 0.4rem 0.35rem 0.75rem;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        .product-filter-header--pro {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            padding: 0.4rem 0.5rem;
            background: #fff;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .product-filter-header--pro .product-filter-badge {
            margin-left: auto;
            font-size: 0.7rem;
            min-width: 1.25rem;
            padding: 0.15rem 0.4rem;
            border-radius: 999px;
            background: var(--base, #14b8a6);
            color: #fff;
        }
        .product-filter-header--pro .product-filter-badge.visible { display: inline-block; }
        .filter-widget--pro {
            margin-bottom: 0.5rem;
            padding: 0.5rem 0.6rem;
            background: #fff;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .filter-widget__title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 0.35rem 0;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .filter-widget__title i { color: var(--base, #14b8a6); font-size: 0.85rem; }
        .filter-widget__title--row { flex-wrap: wrap; justify-content: space-between; align-items: center; }
        .filter-widget__reset, .filter-widget__clear { font-size: 0.7rem; font-weight: 600; color: #0d9488; text-decoration: none; }
        .filter-widget__reset:hover, .filter-widget__clear:hover { color: #0f766e; text-decoration: underline; }
        .filter-widget__clear--red { color: #dc2626; }
        .filter-widget__clear--red:hover { color: #b91c1c; }
        .filter-widget__body { font-size: 0.75rem; }
        .filter-widget__body .input-group { min-height: 32px; border-radius: 6px; overflow: hidden; }
        .filter-widget__body .input-group-text { padding: 0 8px; font-size: 0.75rem; background: #f8fafc; border-color: #e2e8f0; }
        .filter-widget__body .form-control { font-size: 0.75rem; padding: 0.35rem 0.5rem; border-color: #e2e8f0; }
        .filter-widget__body .form-control::placeholder { color: #94a3b8; }
        .filter-price-radios { display: flex; flex-direction: column; gap: 0.25rem; }
        .form-check-inline-filter { margin: 0; min-height: auto; }
        .form-check-inline-filter .form-check-input { margin-top: 0.15rem; width: 0.9em; height: 0.9em; accent-color: var(--base, #14b8a6); }
        .form-check-inline-filter .form-check-label { margin-left: 0.35rem; font-size: 0.75rem; }
        .filter-price-radios .form-check-input:checked + .form-check-label { font-weight: 600; color: var(--base, #14b8a6); }
        .filter-price-inputs.mt-2 { margin-top: 0.4rem !important; }
        .product-filter-price-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.35rem 0.5rem;
            width: 100%;
            min-width: 0;
            margin-top: 0.35rem;
        }
        .product-filter-price-group {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex: 1 1 0;
            min-width: 0;
            max-width: 100%;
        }
        .product-filter-price-group .product-filter-price-label {
            flex-shrink: 0;
            font-size: 0.7rem;
            font-weight: 600;
            margin: 0;
            color: #64748b;
            white-space: nowrap;
        }
        .product-filter-price-group .product-filter-price-input {
            flex: 1 1 0;
            min-width: 0;
            max-width: 100%;
            font-size: 0.75rem;
            padding: 0.3rem 0.4rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .product-filter-price-input--no-spin { -moz-appearance: textfield; }
        .product-filter-price-input--no-spin::-webkit-outer-spin-button,
        .product-filter-price-input--no-spin::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        /* Apply ও Clear all বাটন – স্ক্রিনশটের মতো প্রফেশনাল */
        .product-filter-actions--pro {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid rgba(0,0,0,0.08);
        }
        .product-filter-actions--pro .product-filter-apply-btn {
            flex: 1;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            color: #fff;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
        }
        .product-filter-actions--pro .product-filter-apply-btn:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #fff;
            box-shadow: 0 4px 8px rgba(99, 102, 241, 0.35);
        }
        .product-filter-actions--pro .product-filter-clear {
            flex: 1;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #cbd5e1;
            color: #64748b;
        }
        .product-filter-actions--pro .product-filter-clear:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #475569;
        }
        .form--check-compact { margin-bottom: 0.3rem; min-height: auto; }
        .form--check-compact .form-check-input { width: 0.9em; height: 0.9em; margin-top: 0.12rem; accent-color: var(--base, #14b8a6); }
        .form--check-compact .form-check-label { font-size: 0.75rem; }
        .filter-widget__body--scroll { max-height: 8rem; overflow-y: auto; overflow-x: hidden; }
        .filter-widget__body--scroll::-webkit-scrollbar { width: 5px; }
        .filter-widget__body--scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 3px; }
    </style>
@endpush
