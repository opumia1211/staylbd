@extends('admin.layouts.app')
@section('panel')
    {{-- Section Header: Scoped Title (Featured, Hot Deal, etc.) --}}
    @if(isset($productScope) && $productScope)
        @php
            $scopeConfig = [
                'featured' => ['icon' => 'bx bxs-star', 'title' => __('Featured Products'), 'desc' => __('Products highlighted on homepage and category sections.'), 'addHint' => __('Add or remove from All Products using Quick actions (cog) or Bulk Edit.')],
                'hotDeal' => ['icon' => 'bx bxs-hot', 'title' => __('Hot Deal Products'), 'desc' => __('Deal products shown in Hot Deal section.'), 'addHint' => __('Toggle Hot Deal from All Products or use Bulk Edit.')],
                'todayDeal' => ['icon' => 'bx bx-time-five', 'title' => __('Today Deal Products'), 'desc' => __('Shown in homepage Quick Deals row and Power Zone.'), 'addHint' => __('Toggle Today Deal from All Products, Bulk Edit, or when adding a product.')],
                'bestSelling' => ['icon' => 'bx bx-line-chart', 'title' => __('Best Selling Products'), 'desc' => __('Sorted by sale count. No manual toggle.'), 'addHint' => __('Ordered by number of sales. Manage stock & pricing in All Products.')],
                'trendingNow' => ['icon' => 'bx bx-trending-up', 'title' => __('Trending Now Products'), 'desc' => __('Products shown in homepage Trending Now section.'), 'addHint' => __('Mark products as Trending from All Products (Quick actions) or here.')],
            ];
            $config = $scopeConfig[$productScope] ?? ['icon' => 'bx bx-package', 'title' => $pageTitle ?? '', 'desc' => '', 'addHint' => ''];
        @endphp

    @endif

    {{-- Advanced Filters Card --}}
    @if(isset($brands) && isset($categories))
    <div class="card mb-6">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">@lang('Product Filter')</h5>
            <form method="get" action="{{ request()->url() }}" id="adminProductFilterForm" class="mt-4">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('per_page'))<input type="hidden" name="per_page" value="{{ request('per_page') }}">@endif
                @if(request('low_stock'))<input type="hidden" name="low_stock" value="1">@endif
                
                <div class="row g-6">
                    <div class="col-md-3">
                        <label class="form-label">@lang('Category')</label>
                        <select name="category_id" class="form-select select2">
                            <option value="">@lang('All Categories')</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ __($name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">@lang('Brand')</label>
                        <select name="brand_id" class="form-select select2">
                            <option value="">@lang('All Brands')</option>
                            @foreach($brands as $id => $name)
                                <option value="{{ $id }}" {{ request('brand_id') == $id ? 'selected' : '' }}>{{ __($name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">@lang('Status')</label>
                        <select name="status" class="form-select">
                            <option value="">@lang('All')</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>@lang('Active')</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>@lang('Inactive')</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">@lang('Stock Availability')</label>
                        <select name="stock" class="form-select">
                            <option value="">@lang('All')</option>
                            <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>@lang('Out of stock')</option>
                            <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>@lang('Low (1-5)')</option>
                            <option value="ok" {{ request('stock') === 'ok' ? 'selected' : '' }}>@lang('In stock')</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> @lang('Filter')</button>
                        <a href="{{ request()->url() }}" class="btn btn-label-secondary"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Main Product Display Card --}}
    <div class="card mb-6">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-4">
            <div class="d-flex align-items-center gap-4">
                @if($products->total() > 0)
                <div class="form-check ms-1">
                    <input type="checkbox" id="selectAllProducts" class="form-check-input">
                    <label for="selectAllProducts" class="form-check-label fw-medium text-heading">@lang('Select All')</label>
                </div>

                <div id="adminProductBulkBar" class="d-none">
                    <div class="d-flex align-items-center gap-2 border-start ps-3 ms-1">
                        <span class="badge bg-label-info" id="bulkSelectedCount">0 selected</span>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn"><i class="bx bx-trash me-1"></i>@lang('Delete')</button>
                        <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#bulkEditModal"><i class="bx bx-edit me-1"></i>@lang('Bulk Edit')</button>
                        <button type="button" class="btn btn-sm btn-text-secondary" id="bulkDeselectAll">@lang('Clear')</button>
                    </div>
                </div>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="mb-0 text-muted small">@lang('Show')</label>
                    <select id="adminProductPerPage" class="form-select form-select-sm w-auto">
                        @foreach([10, 20, 50, 100] as $n)
                            <option value="{{ $n }}" {{ request('per_page', getPaginate()) == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="btn-group btn-group-sm" id="adminProductViewToggle">
                    <button type="button" class="btn btn-label-primary admin-view-btn active" data-view="grid"><i class="bx bx-grid-alt lh-1"></i></button>
                    <button type="button" class="btn btn-outline-primary admin-view-btn text-muted" data-view="list"><i class="bx bx-list-ul lh-1"></i></button>
                </div>

                <div class="text-muted small border-start ps-3">
                   Total: <span class="fw-medium text-heading">{{ $products->total() }}</span>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            {{-- Grid View (following app-academy-course pattern) --}}
            <div id="adminProductGridView" class="row gy-6 mb-6">
                @forelse($products as $product)
                    <div class="col-sm-6 col-lg-4 col-xxl-3">
                        <div class="card p-2 h-100 shadow-none border">
                            <div class="position-relative rounded-2 text-center mb-0">
                                <div class="admin-product-card__check-wrap position-absolute top-0 start-0 m-2 z-3">
                                    <input type="checkbox" class="form-check-input product-select-check" value="{{ $product->id }}">
                                </div>
                                <a href="{{ route('admin.product.edit', $product->id) }}" class="d-block">
                                    <img src="{{ $product->imageShow() }}" alt="{{ __($product->name) }}" class="img-fluid rounded-2 w-100" style="aspect-ratio: 4/3; object-fit: cover;">
                                </a>
                                <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-1">
                                    @if($product->status) <span class="badge bg-success small py-1">@lang('Active')</span> @else <span class="badge bg-danger small py-1">@lang('Inactive')</span> @endif
                                    @if($product->featured_product)<span class="badge bg-info small py-1" title="@lang('Featured')">F</span>@endif
                                    @if($product->hot_deals)<span class="badge bg-warning text-dark small py-1" title="@lang('Hot Deal')">H</span>@endif
                                    @if($product->today_deals)<span class="badge bg-danger small py-1" title="@lang('Today Deal')">T</span>@endif
                                    @if(isset($product->trending_now) && $product->trending_now)<span class="badge bg-success small py-1" title="@lang('Trending Now')">Tr</span>@endif
                                </div>
                            </div>
                            <div class="card-body p-4 pt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-label-primary">{{ $product->category ? \Illuminate\Support\Str::limit(__($product->category->name), 12) : '-' }}</span>
                                    <p class="d-flex align-items-center fw-medium gap-1 mb-0 small">
                                        <span class="text-warning"><i class="bx bxs-star me-1 mb-1"></i></span> {{ $product->quantity }} <small class="text-muted fw-normal">(@lang('pcs'))</small>
                                    </p>
                                </div>
                                <a href="{{ route('admin.product.edit', $product->id) }}" class="h6 d-block mb-2 text-truncate" title="{{ __($product->name) }}">{{ __($product->name) }}</a>
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <span class="text-muted small">#{{ $product->product_sku }}</span>
                                    <span class="fw-bold text-primary ms-auto">{{ $general->cur_sym }}{{ showAmount(productPrice($product)) }}</span>
                                </div>
                                
                                <div class="d-flex align-items-center gap-2 mt-auto">
                                    <a class="btn btn-label-primary flex-grow-1 d-flex align-items-center justify-content-center" href="{{ route('admin.product.edit', $product->id) }}">
                                        <i class="bx bx-edit-alt me-1"></i> @lang('Edit')
                                    </a>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-label-secondary dropdown-toggle hide-arrow px-2" data-bs-toggle="dropdown" aria-expanded="false" style="padding-top: 0.4375rem; padding-bottom: 0.4375rem;">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li><a class="dropdown-item py-2" href="{{ product_detail_url($product) }}" target="_blank"><i class="bx bx-link-external me-2 text-primary"></i> @lang('View on site')</a></li>
                                            <li><a class="dropdown-item py-2" href="{{ route('admin.product.reviews', $product->id) }}"><i class="bx bx-message-square-dots me-2 text-info"></i> @lang('Reviews')</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if(!$product->status)
                                                <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Enable this product?')"><i class="bx bx-show me-2 text-success"></i> @lang('Enable')</button></li>
                                            @else
                                                <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Disable this product?')"><i class="bx bx-hide me-2 text-warning"></i> @lang('Disable')</button></li>
                                            @endif
                                            <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.featured', $product->id) }}" data-question="@lang('Toggle featured status?')"><i class="bx bxs-star me-2 text-warning"></i> @lang($product->featured_product ? 'Unfeatured' : 'Featured')</button></li>
                                            <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.hot.deal', $product->id) }}" data-question="@lang('Toggle hot deal?')"><i class="bx bxs-hot me-2 text-danger"></i> @lang($product->hot_deals ? 'Hot Deal Disable' : 'Hot Deal Enable')</button></li>
                                            <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.today.deal', $product->id) }}" data-question="@lang('Toggle today deal?')"><i class="bx bx-time-five me-2 text-info"></i> @lang($product->today_deals ? 'Today Deal Disable' : 'Today Deal Enable')</button></li>
                                            @if(isset($product->trending_now))
                                                <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.trending.deal', $product->id) }}" data-question="@lang('Toggle trending now?')"><i class="bx bx-trending-up me-2 text-success"></i> @lang($product->trending_now ? 'Trending Disable' : 'Trending Enable')</button></li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.delete', $product->id) }}" data-question="@lang('Delete this product? This cannot be undone.')"><i class="bx bx-trash me-2 text-danger"></i> @lang('Delete')</button></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-12">
                        <i class="bx bx-package fs-1 text-muted mb-2"></i>
                        <h6 class="text-muted">{{ __($emptyMessage) }}</h6>
                    </div>
                @endforelse
            </div>

            {{-- List View (Table within integrated card) --}}
            <div id="adminProductListView" class="d-none">
                <div class="table-responsive text-nowrap border rounded">
                    <table class="table table-hover table-border-bottom-0">
                        <thead class="bg-label-secondary">
                            <tr>
                                <th width="40" class="ps-4"><input type="checkbox" id="selectAllProductsList" class="form-check-input"></th>
                                <th>@lang('Product')</th>
                                <th>@lang('SKU')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Stock')</th>
                                <th>@lang('Status')</th>
                                <th class="text-center">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td class="ps-4"><input type="checkbox" class="form-check-input product-select-check" value="{{ $product->id }}"></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3"><img src="{{ $product->imageShow() }}" alt="Product" class="rounded"></div>
                                            <div class="d-flex flex-column"><span class="text-heading fw-bold text-truncate" style="max-width: 180px;">{{ __($product->name) }}</span><small class="text-muted">ID: #{{ $product->id }}</small></div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted small">{{ $product->product_sku }}</span></td>
                                    <td><span class="badge bg-label-secondary">{{ $product->category ? __($product->category->name) : '-' }}</span></td>
                                    <td><span class="fw-bold text-primary">{{ $general->cur_sym }}{{ showAmount(productPrice($product)) }}</span></td>
                                    <td><span class="fw-medium {{ $product->quantity <= 0 ? 'text-danger' : ($product->quantity <= 5 ? 'text-warning' : 'text-success') }}">{{ $product->quantity }}</span></td>
                                    <td>@if($product->status) <span class="badge bg-label-success">@lang('Active')</span> @else <span class="badge bg-label-danger">@lang('Inactive')</span> @endif</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-icon btn-label-primary btn-sm" title="@lang('Edit')"><i class="bx bx-edit-alt"></i></a>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-icon btn-label-secondary btn-sm hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li><a class="dropdown-item py-2" href="{{ product_detail_url($product) }}" target="_blank"><i class="bx bx-link-external me-2 text-primary"></i> @lang('View on site')</a></li>
                                                    <li><a class="dropdown-item py-2" href="{{ route('admin.product.reviews', $product->id) }}"><i class="bx bx-message-square-dots me-2 text-info"></i> @lang('Reviews')</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    @if(!$product->status)
                                                        <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Enable this product?')"><i class="bx bx-show me-2 text-success"></i> @lang('Enable')</button></li>
                                                    @else
                                                        <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Disable this product?')"><i class="bx bx-hide me-2 text-warning"></i> @lang('Disable')</button></li>
                                                    @endif
                                                    <li><button type="button" class="dropdown-item py-2 confirmationBtn" data-action="{{ route('admin.product.delete', $product->id) }}" data-question="@lang('Delete this product?')"><i class="bx bx-trash me-2 text-danger"></i> @lang('Delete')</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="text-center text-muted py-8" colspan="8">{{ __($emptyMessage) }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($products->hasPages())
            <div class="card-footer border-top py-4 d-flex justify-content-center">
                {{ paginateLinks($products) }}
            </div>
        @endif
    </div>

    {{-- Bulk Edit Modal (Styled following theme modals) --}}
    <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <form id="bulkEditForm" method="post" action="{{ route('admin.product.bulk.edit') }}">
                    @csrf
                    <div class="modal-header border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-primary"><i class="bx bx-edit-alt"></i></span></div>
                            <h5 class="modal-title mb-0">@lang('Bulk Edit Products')</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">@lang('Select Action to Apply')</label>
                            <select name="action" class="form-select" required size="10">
                                <optgroup label="@lang('Status Management')">
                                    <option value="enable">@lang('Enable (Set Active)')</option>
                                    <option value="disable">@lang('Disable (Set Inactive)')</option>
                                </optgroup>
                                <optgroup label="@lang('Marketing Toggles')">
                                    <option value="featured_on">@lang('Mark as Featured')</option>
                                    <option value="featured_off">@lang('Remove Featured')</option>
                                    <option value="hot_deal_on">@lang('Mark as Hot Deal')</option>
                                    <option value="hot_deal_off">@lang('Remove Hot Deal')</option>
                                    <option value="today_deal_on">@lang('Mark as Today Deal')</option>
                                    <option value="today_deal_off">@lang('Remove Today Deal')</option>
                                    <option value="trending_on">@lang('Mark as Trending Now')</option>
                                    <option value="trending_off">@lang('Remove Trending Now')</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="alert alert-warning small border-0 mb-0"><i class="bx bx-error-circle me-1"></i> @lang('This action will be applied to all currently selected products.')</div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary px-6 shadow">@lang('Apply changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex align-items-center gap-2">
        @if(isset($productScope) && $productScope)
            <a href="{{ route('admin.product.index') }}" class="btn btn-label-secondary"><i class="bx bx-arrow-back me-1"></i> @lang('Back')</a>
        @endif
        <x-search-form placeholder="@lang('Search product...')" />
        <div class="btn-group">
            <a href="{{ route('admin.product.create') }}" class="btn btn-label-primary shadow-sm border-0"><i class="bx bx-closet me-1"></i> @lang('Add Clothing')</a>
            <a href="{{ route('admin.product.create2') }}" class="btn btn-primary shadow-sm"><i class="bx bx-package me-1"></i> @lang('Add General')</a>
        </div>
    </div>
@endpush

@push('style')
<style>
    .extra-small { font-size: 0.65rem; }
</style>
@endpush

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        (function () {
            var STORAGE_KEY = 'admin_product_view_mode';
            var perPageEl = document.getElementById('adminProductPerPage');
            var gridView = document.getElementById('adminProductGridView');
            var listView = document.getElementById('adminProductListView');
            var viewToggle = document.getElementById('adminProductViewToggle');
            var selectAllProducts = document.getElementById('selectAllProducts');
            var selectAllProductsList = document.getElementById('selectAllProductsList');
            var bulkBar = document.getElementById('adminProductBulkBar');
            var bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            function getSelectedIds() {
                var checkboxes = document.querySelectorAll('.product-select-check:checked');
                var ids = []; checkboxes.forEach(function (cb) { ids.push(cb.value); });
                return ids;
            }

            function updateBulkBar() {
                var count = getSelectedIds().length;
                if (bulkBar) bulkBar.classList.toggle('d-none', count === 0);
                var countEl = document.getElementById('bulkSelectedCount');
                if (countEl) countEl.textContent = count + ' @lang("selected")';
                
                var allChecked = document.querySelectorAll('.product-select-check:checked').length;
                var totalOnPage = document.querySelectorAll('.product-select-check').length;
                if (selectAllProducts) selectAllProducts.checked = totalOnPage > 0 && allChecked === totalOnPage;
                if (selectAllProductsList) selectAllProductsList.checked = totalOnPage > 0 && allChecked === totalOnPage;
            }

            function toggleSelectAll(checked) {
                document.querySelectorAll('.product-select-check').forEach(function (cb) { cb.checked = checked; });
                updateBulkBar();
            }

            if (selectAllProducts) selectAllProducts.addEventListener('change', function () { toggleSelectAll(this.checked); });
            if (selectAllProductsList) selectAllProductsList.addEventListener('change', function () { toggleSelectAll(this.checked); });
            
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select-check')) updateBulkBar();
            });

            if (document.getElementById('bulkDeselectAll')) {
                document.getElementById('bulkDeselectAll').addEventListener('click', function() { toggleSelectAll(false); });
            }

            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function (e) {
                    var ids = getSelectedIds();
                    if (ids.length === 0) return alert('@lang("Please select products")');
                    if (confirm('@lang("Are you sure to delete selected products? This action cannot be undone.")')) {
                        var form = document.createElement('form'); form.method = 'POST'; form.action = '{{ route("admin.product.bulk.delete") }}';
                        var csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}'; form.appendChild(csrf);
                        ids.forEach(function (id) { var inp = document.createElement('input'); inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id; form.appendChild(inp); });
                        document.body.appendChild(form); form.submit();
                    }
                });
            }

            function applyViewMode(mode) {
                if (!gridView || !listView) return;
                var btns = viewToggle ? viewToggle.querySelectorAll('.admin-view-btn') : [];
                if (mode === 'list') {
                    gridView.classList.add('d-none'); listView.classList.remove('d-none');
                    btns.forEach(function (b) { b.classList.toggle('active', b.getAttribute('data-view') === 'list');
                        if (b.getAttribute('data-view') === 'list') { b.classList.add('btn-label-primary'); b.classList.remove('btn-outline-primary', 'text-muted'); }
                        else { b.classList.remove('btn-label-primary', 'active'); b.classList.add('btn-outline-primary', 'text-muted'); }
                    });
                } else {
                    gridView.classList.remove('d-none'); listView.classList.add('d-none');
                    btns.forEach(function (b) { b.classList.toggle('active', b.getAttribute('data-view') === 'grid');
                        if (b.getAttribute('data-view') === 'grid') { b.classList.add('btn-label-primary'); b.classList.remove('btn-outline-primary', 'text-muted'); }
                        else { b.classList.remove('btn-label-primary', 'active'); b.classList.add('btn-outline-primary', 'text-muted'); }
                    });
                }
                try { localStorage.setItem(STORAGE_KEY, mode); } catch (e) { }
            }

            if (viewToggle) {
                viewToggle.querySelectorAll('.admin-view-btn').forEach(function (btn) {
                    btn.addEventListener('click', function (e) { applyViewMode(this.getAttribute('data-view')); });
                });
            }
            if (perPageEl) {
                perPageEl.addEventListener('change', function () {
                    var url = new URL(window.location.href); url.searchParams.set('per_page', this.value); window.location.href = url.toString();
                });
            }
            try { var saved = localStorage.getItem(STORAGE_KEY); if (saved) applyViewMode(saved); } catch (e) { }
        })();
    });
</script>
@endpush