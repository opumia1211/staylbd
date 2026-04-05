@extends($activeTemplate . 'layouts.frontend')
@php
    $disableLegacyBootstrapBundle = true;
    $disableLegacyJquery = true;
    $disableLegacyJqueryUi = true;
@endphp

{{-- পেজ-স্কোপড: টেইলউইন্ড বান্ডল ক্যাশে পুরনো হলেও ডেস্কটপে মোবাইল হাব লুকাবে, রেল সারি নয় --}}
@push('style')
<style>
@media (min-width: 992px) {
    #catMobileHub { display: none !important; }
    .all-categories-section .all-categories-desktop-only { display: block !important; }
}
@media (max-width: 991.98px) {
    .all-categories-section .all-categories-desktop-only { display: none !important; }
}
#catMobileHub .cat-mobile-hub__rail {
    display: flex !important;
    flex-direction: column !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
}
#catMobileHub .cat-mobile-hub__cat {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
</style>
@endpush

@section('content')
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'category_above'])

    @php
        $categoriesNav = $categoriesNav ?? collect();
    @endphp

    <section class="all-categories-section products-section products-section--compact pb-120" aria-label="@lang('All Categories')">
        {{-- Phone / tablet: left rail = categories, right = subcategories (app-style) --}}
        <div class="cat-mobile-hub px-2 pt-1" id="catMobileHub">
            @if($categoriesNav->isEmpty())
                <div class="cat-mobile-hub__empty rounded-3 border bg-white p-4">
                    <p class="mb-2">{{ $emptyMessage }}</p>
                    <a href="{{ route('home') }}" class="cat-mobile-hub__cta">@lang('Home')</a>
                </div>
            @else
                <div class="cat-mobile-hub__body">
                    <nav class="cat-mobile-hub__rail" aria-label="@lang('Categories')">
                        @foreach ($categoriesNav as $cat)
                            <button type="button"
                                    class="cat-mobile-hub__cat @if($loop->first) is-active @endif"
                                    data-cat-hub-select="{{ $cat->id }}"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                                <span class="all-category-card__ring cat-mobile-hub__cat-ring" aria-hidden="true">
                                    <img src="{{ $cat->imageShow() }}" alt="" class="all-category-card__photo" width="96" height="96" loading="lazy" decoding="async">
                                </span>
                                <span class="cat-mobile-hub__cat-name">{{ __($cat->name) }}</span>
                            </button>
                        @endforeach
                    </nav>
                    <div class="cat-mobile-hub__content">
                        @foreach ($categoriesNav as $cat)
                            <div class="cat-mobile-hub__panel @if($loop->first) is-active @endif"
                                 data-cat-hub-panel="{{ $cat->id }}"
                                 @if(!$loop->first) hidden @endif>
                                <div class="cat-mobile-hub__section-title">{{ __($cat->name) }}</div>
                                @if($cat->subcategories->isEmpty())
                                    <p class="small text-muted mb-2">@lang('No subcategories yet.')</p>
                                    <a href="{{ route('category.products', [slug($cat->name), $cat->id]) }}" class="cat-mobile-hub__cta">@lang('View products')</a>
                                @else
                                    <div class="cat-mobile-hub__subgrid">
                                        @foreach ($cat->subcategories as $sub)
                                            <a href="{{ route('subcategory.products', [slug($sub->name), $sub->id]) }}" class="cat-mobile-hub__sub">
                                                <span class="all-category-card__ring cat-mobile-hub__sub-ring" aria-hidden="true">
                                                    <img src="{{ $cat->imageShow() }}" alt="" class="all-category-card__photo" width="96" height="96" loading="lazy" decoding="async">
                                                </span>
                                                <span class="cat-mobile-hub__sub-label">{{ __($sub->name) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                    <a href="{{ route('category.products', [slug($cat->name), $cat->id]) }}" class="cat-mobile-hub__cta d-inline-flex mt-3">@lang('View all in category')</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="container-fluid px-3 px-lg-4 all-categories-desktop-only">
            <div class="row all-categories-page-row g-0">
                {{-- SIDEBAR: Search & Filter (aligned with products page filter) --}}
                <div class="col-12 col-lg-3 order-2 order-lg-1 all-categories-sidebar-col">
                    <aside class="all-categories-sidebar filter--sidebar" id="allCategoriesSidebar">
                        <div class="close--sidebar d-lg-none" id="allCategoriesSidebarClose" aria-label="@lang('Close')">
                            @include($activeTemplate . 'partials.icon', ['name' => 'times'])
                        </div>
                        @if($categoriesNav->isNotEmpty())
                            <div class="all-categories-sidebar-cats d-lg-none">
                                <div class="all-categories-sidebar-cats__title">@lang('Browse categories')</div>
                                <div class="all-categories-sidebar-cats__scroll" role="list">
                                    @foreach ($categoriesNav as $sc)
                                        <a href="{{ route('category.products', [slug($sc->name), $sc->id]) }}" class="all-categories-sidebar-cats__item" role="listitem">
                                            <span class="all-category-card__ring all-categories-sidebar-cats__ring" aria-hidden="true">
                                                <img src="{{ $sc->imageShow() }}" alt="" class="all-category-card__photo" width="96" height="96" loading="lazy" decoding="async">
                                            </span>
                                            <span class="all-categories-sidebar-cats__label">{{ __($sc->name) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="filter--sidebar-inner cat-filter-panel">
                            <form method="get" action="{{ route('category.all') }}" id="allCategoryToolbar" class="cat-filter-form">
                                <div class="cat-filter-header">
                                    <span class="cat-filter-header__icon">@include($activeTemplate . 'partials.icon', ['name' => 'sliders-h'])</span>
                                    <span class="cat-filter-header__text">@lang('Search & Filter')</span>
                                </div>

                                {{-- Quick filters (advanced) --}}
                                <div class="cat-filter-block">
                                    <div class="cat-filter__heading">@lang('Quick filters')</div>
                                    <div class="cat-filter__body">
                                        <label class="cat-filter-check">
                                            <input type="checkbox" name="featured" value="1" @checked(!empty($featuredOnly)) class="cat-filter-check__input">
                                            <span class="cat-filter-check__box"></span>
                                            <span class="cat-filter-check__label">@include($activeTemplate . 'partials.icon', ['name' => 'star', 'class' => 'text-warning me-1'])@lang('Featured only')</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Search --}}
                                <div class="cat-filter-block">
                                    <div class="cat-filter__heading">@lang('Search')</div>
                                    <div class="cat-filter__body">
                                        <div class="cat-filter-search">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'search', 'class' => 'cat-filter-search__icon'])
                                            <input type="search" name="q" id="categorySearch" value="{{ $search }}"
                                                   class="cat-filter-search__input" placeholder="@lang('Type to search...')" aria-label="@lang('Search categories')" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                {{-- Sort --}}
                                <div class="cat-filter-block">
                                    <div class="cat-filter__heading">@lang('Sort by')</div>
                                    <div class="cat-filter__body">
                                        <select name="sort" id="categorySort" class="cat-filter-select" aria-label="@lang('Sort by')">
                                            <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>@lang('Name A–Z')</option>
                                            <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>@lang('Name Z–A')</option>
                                            <option value="products_desc" {{ $sort === 'products_desc' ? 'selected' : '' }}>@lang('Most products')</option>
                                            <option value="featured_first" {{ $sort === 'featured_first' ? 'selected' : '' }}>@lang('Featured first')</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Display --}}
                                <div class="cat-filter-block">
                                    <div class="cat-filter__heading">@lang('Display')</div>
                                    <div class="cat-filter__body">
                                        <label class="cat-filter-label">@lang('Per page')</label>
                                        <select name="per_page" id="categoryPerPage" class="cat-filter-select" aria-label="@lang('Items per page')">
                                            @foreach([12, 24, 48, 96] as $n)
                                                <option value="{{ $n }}" {{ (int)$perPage === $n ? 'selected' : '' }}>{{ $n }}</option>
                                            @endforeach
                                        </select>
                                        <label class="cat-filter-label cat-filter-label--mt">@lang('View')</label>
                                        <div class="cat-filter-view">
                                            <input type="radio" class="cat-filter-view__input" name="viewMode" id="viewGrid" value="grid" autocomplete="off" checked>
                                            <label class="cat-filter-view__btn" for="viewGrid" title="@lang('Grid')">@include($activeTemplate . 'partials.icon', ['name' => 'th-large'])</label>
                                            <input type="radio" class="cat-filter-view__input" name="viewMode" id="viewList" value="list" autocomplete="off">
                                            <label class="cat-filter-view__btn" for="viewList" title="@lang('List')">@include($activeTemplate . 'partials.icon', ['name' => 'list'])</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="cat-filter-actions">
                                    <button type="submit" class="cat-filter-btn cat-filter-btn--primary">
                                        @include($activeTemplate . 'partials.icon', ['name' => 'check', 'class' => 'me-1'])@lang('Apply')
                                    </button>
                                    @if($search || request()->has('sort') || request()->has('per_page') || ($featuredOnly ?? false))
                                        <a href="{{ route('category.all') }}" class="cat-filter-btn cat-filter-btn--secondary">@lang('Clear')</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </aside>
                </div>

                {{-- MAIN: toolbar + breadcrumb + title + grid + pagination --}}
                <div class="col-12 col-lg-9 order-1 order-lg-2 all-categories-main-col">
                    <div class="all-categories-main-inner">
                        {{-- একটি মাত্র বার: ব্রেডক্রাম্ব + টাইটেল + কাউন্ট + সর্ট + ভিউ – হেডারের ঠিক নিচে --}}
                        <div class="cat-page-header">
                            <div class="all-categories-toolbar cat-toolbar cat-toolbar--single">
                                <h1 class="visually-hidden">@lang('All Categories')</h1>
                                <nav class="all-categories-breadcrumb cat-toolbar__crumb" aria-label="Breadcrumb">
                                    <ol class="breadcrumb breadcrumb--minimal mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('Home')</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">@lang('All Categories')</li>
                                    </ol>
                                </nav>
                                <span class="cat-toolbar__sep" aria-hidden="true"></span>
                                @if($categories->isNotEmpty() && $categories->total() > 0)
                                    <span class="cat-toolbar__count">{{ $categories->total() }} @lang('categories')</span>
                                    <span class="cat-toolbar__range d-none d-md-inline">{{ $categories->firstItem() }}–{{ $categories->lastItem() }} @lang('of') {{ $categories->total() }}</span>
                                @else
                                    <span class="cat-toolbar__range">{{ $categories->isEmpty() ? __('Browse by category') : __('No categories') }}</span>
                                @endif
                                <div class="cat-toolbar__spacer"></div>
                                <label class="cat-toolbar__field">
                                    <span class="cat-toolbar__label">@lang('Sort')</span>
                                    <select class="cat-toolbar__select all-categories-toolbar-sort" id="toolbarSort" aria-label="@lang('Sort by')">
                                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>@lang('Name A–Z')</option>
                                        <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>@lang('Name Z–A')</option>
                                        <option value="products_desc" {{ $sort === 'products_desc' ? 'selected' : '' }}>@lang('Most products')</option>
                                        <option value="featured_first" {{ $sort === 'featured_first' ? 'selected' : '' }}>@lang('Featured first')</option>
                                    </select>
                                </label>
                                <label class="cat-toolbar__field">
                                    <span class="cat-toolbar__label">@lang('Per page')</span>
                                    <select class="cat-toolbar__select all-categories-toolbar-perpage" id="toolbarPerPage" aria-label="@lang('Per page')">
                                        @foreach([12, 24, 48, 96] as $n)
                                            <option value="{{ $n }}" {{ (int)$perPage === $n ? 'selected' : '' }}>{{ $n }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <div class="cat-toolbar__divider" aria-hidden="true"></div>
                                <div class="cat-toolbar__view" role="group" aria-label="@lang('View')">
                                    <button type="button" class="cat-toolbar__view-btn all-categories-view-btn active" data-view="grid" aria-label="Grid" title="@lang('Grid')">@include($activeTemplate . 'partials.icon', ['name' => 'th-large'])</button>
                                    <button type="button" class="cat-toolbar__view-btn all-categories-view-btn" data-view="list" aria-label="List" title="@lang('List')">@include($activeTemplate . 'partials.icon', ['name' => 'list'])</button>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm d-lg-none all-categories-filter-btn" id="allCategoriesFilterBtn" aria-label="@lang('Filter')">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'filter']) @lang('Filter & Sort')
                                </button>
                            </div>

                            {{-- Active filters (advanced): chips + Clear all --}}
                            @if($search || !empty($featuredOnly))
                                @php
                                    $queryWithoutQ = request()->except('q'); $urlNoQ = url()->current() . (empty($queryWithoutQ) ? '' : '?' . http_build_query($queryWithoutQ));
                                    $queryWithoutFeat = request()->except('featured'); $urlNoFeat = url()->current() . (empty($queryWithoutFeat) ? '' : '?' . http_build_query($queryWithoutFeat));
                                @endphp
                                <div class="cat-toolbar-chips">
                                    @if($search)
                                        <span class="cat-toolbar-chip">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'search', 'class' => 'me-1']) {{ Str::limit($search, 20) }}
                                            <a href="{{ $urlNoQ }}" class="cat-toolbar-chip__remove" aria-label="@lang('Remove')">&times;</a>
                                        </span>
                                    @endif
                                    @if(!empty($featuredOnly))
                                        <span class="cat-toolbar-chip">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'star', 'class' => 'me-1']) @lang('Featured only')
                                            <a href="{{ $urlNoFeat }}" class="cat-toolbar-chip__remove" aria-label="@lang('Remove')">&times;</a>
                                        </span>
                                    @endif
                                    <a href="{{ route('category.all') }}" class="cat-toolbar-chip-clear">@lang('Clear all')</a>
                                </div>
                            @endif
                        </div>

                        @if($categories->isEmpty())
                            <div class="all-categories-empty py-5 text-center rounded-3">
                                <span class="text-muted mb-3 d-inline-flex" style="font-size: 3rem;">@include($activeTemplate . 'partials.icon', ['name' => 'folder-open'])</span>
                                <p class="text-muted mb-2">{{ $emptyMessage }}</p>
                                <a href="{{ route('category.all') }}" class="btn btn--base btn-sm">@lang('View all categories')</a>
                            </div>
                        @else
                            <div class="row g-3 all-categories-grid" id="allCategoriesGrid" data-view="grid">
                                @foreach ($categories as $category)
                                    <div class="col-sm-12 col-md-6 col-lg-4 all-category-card-col">
                                        <article class="all-category-card product-card" aria-label="{{ __($category->name) }}">
                                            <a class="text-decoration-none text-body all-category-card__link" href="{{ route('category.products', [slug($category->name), $category->id]) }}">
                                                <span class="all-category-card__ring-wrap position-relative d-inline-flex">
                                                    <span class="all-category-card__ring" aria-hidden="true">
                                                        <img src="{{ $category->imageShow() }}" alt="{{ __($category->name) }}" loading="lazy" decoding="async"
                                                             class="all-category-card__photo" width="256" height="256" sizes="(max-width: 576px) 40vw, 180px">
                                                    </span>
                                                    @if($category->featured ?? 0)
                                                        <span class="badge all-category-card__badge all-category-card__badge--featured">@lang('Featured')</span>
                                                    @endif
                                                </span>
                                                <span class="all-category-card__label">{{ __($category->name) }}</span>
                                            </a>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                            <div class="all-categories-pagination mt-4">
                                {{ paginateLinks($categories) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include($activeTemplate . 'partials.scrollbar', ['position' => 'category_below'])
@endsection

@push('script')
    <script>
        (function() {
            var gridEl = document.getElementById('allCategoriesGrid');
            var viewGrid = document.getElementById('viewGrid');
            var viewList = document.getElementById('viewList');
            var sidebar = document.getElementById('allCategoriesSidebar');
            var sidebarClose = document.getElementById('allCategoriesSidebarClose');
            var filterBtn = document.getElementById('allCategoriesFilterBtn');
            var STORAGE_KEY = 'staylbd_category_view';

            function setView(mode) {
                if (!gridEl) return;
                gridEl.setAttribute('data-view', mode);
                try { localStorage.setItem(STORAGE_KEY, mode); } catch (e) {}
                if (viewGrid) viewGrid.checked = (mode === 'grid');
                if (viewList) viewList.checked = (mode === 'list');
                document.querySelectorAll('.all-categories-view-btn').forEach(function(btn) {
                    btn.classList.toggle('active', btn.getAttribute('data-view') === mode);
                });
            }

            function init() {
                var hub = document.getElementById('catMobileHub');
                if (hub) {
                    hub.addEventListener('click', function(e) {
                        var btn = e.target.closest('[data-cat-hub-select]');
                        if (!btn || !hub.contains(btn)) return;
                        var id = btn.getAttribute('data-cat-hub-select');
                        hub.querySelectorAll('[data-cat-hub-panel]').forEach(function(p) {
                            var on = p.getAttribute('data-cat-hub-panel') === id;
                            p.hidden = !on;
                            p.classList.toggle('is-active', on);
                        });
                        hub.querySelectorAll('[data-cat-hub-select]').forEach(function(b) {
                            var on = b.getAttribute('data-cat-hub-select') === id;
                            b.classList.toggle('is-active', on);
                            b.setAttribute('aria-pressed', on ? 'true' : 'false');
                        });
                    });
                }
                try {
                    var saved = localStorage.getItem(STORAGE_KEY);
                    if (saved === 'list' || saved === 'grid') setView(saved);
                } catch (e) {}
                if (viewGrid) viewGrid.addEventListener('change', function() { setView('grid'); });
                if (viewList) viewList.addEventListener('change', function() { setView('list'); });
                document.querySelectorAll('.all-categories-view-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        setView(btn.getAttribute('data-view'));
                    });
                });
                var toolbarSort = document.getElementById('toolbarSort');
                var toolbarPerPage = document.getElementById('toolbarPerPage');
                var formEl = document.getElementById('allCategoryToolbar');
                if (toolbarSort) {
                    toolbarSort.addEventListener('change', function() {
                        var sid = document.getElementById('categorySort');
                        if (sid) sid.value = this.value;
                        if (formEl) formEl.submit();
                    });
                }
                if (toolbarPerPage) {
                    toolbarPerPage.addEventListener('change', function() {
                        var sid = document.getElementById('categoryPerPage');
                        if (sid) sid.value = this.value;
                        if (formEl) formEl.submit();
                    });
                }
                if (document.getElementById('categorySort')) {
                    document.getElementById('categorySort').addEventListener('change', function() {
                        if (toolbarSort) toolbarSort.value = this.value;
                    });
                }
                if (document.getElementById('categoryPerPage')) {
                    document.getElementById('categoryPerPage').addEventListener('change', function() {
                        if (toolbarPerPage) toolbarPerPage.value = this.value;
                    });
                }
                function openSidebar() {
                    if (sidebar) sidebar.classList.add('active');
                    var ov = document.querySelector('.all-categories-filter-overlay');
                    if (!ov) {
                        ov = document.createElement('div');
                        ov.className = 'filter-overlay all-categories-filter-overlay';
                        ov.setAttribute('aria-hidden', 'true');
                        document.body.appendChild(ov);
                    }
                    ov.style.display = 'block';
                    ov.onclick = closeSidebar;
                    document.body.classList.add('filter-open');
                    document.body.style.overflow = 'hidden';
                }
                function closeSidebar() {
                    if (sidebar) sidebar.classList.remove('active');
                    var ov = document.querySelector('.all-categories-filter-overlay');
                    if (ov) ov.style.display = 'none';
                    document.body.classList.remove('filter-open');
                    document.body.style.overflow = '';
                }
                if (filterBtn && sidebar) filterBtn.addEventListener('click', openSidebar);
                if (sidebarClose && sidebar) sidebarClose.addEventListener('click', closeSidebar);
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar && sidebar.classList.contains('active')) closeSidebar();
                });
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
            else init();
        })();
    </script>
@endpush
