@extends('admin.layouts.app')
@section('panel')
    {{-- Section header for Featured / Hot Deal / Today Deal / Best Selling (not shown on main All Products) --}}
    @if(isset($productScope) && $productScope)
    @php
        $scopeConfig = [
            'featured' => [
                'icon' => 'las la-star',
                'title' => __('Featured Products'),
                'desc' => __('Products highlighted on homepage and category sections.'),
                'addHint' => __('Add or remove from All Products using Quick actions (cog) or Bulk Edit.'),
            ],
            'hotDeal' => [
                'icon' => 'las la-fire',
                'title' => __('Hot Deal Products'),
                'desc' => __('Deal products shown in Hot Deal section.'),
                'addHint' => __('Toggle Hot Deal from All Products or use Bulk Edit.'),
            ],
            'todayDeal' => [
                'icon' => 'las la-clock',
                'title' => __('Today Deal Products'),
                'desc' => __('Shown in homepage Quick Deals row (under categories) and Power Zone when enabled.'),
                'addHint' => __('Toggle Today Deal from All Products, Bulk Edit, or when adding a product.'),
            ],
            'bestSelling' => [
                'icon' => 'las la-chart-line',
                'title' => __('Best Selling Products'),
                'desc' => __('Sorted by sale count. No manual toggle – based on orders.'),
                'addHint' => __('Ordered by number of sales. Manage stock & pricing in All Products.'),
            ],
            'trendingNow' => [
                'icon' => 'las la-fire-alt',
                'title' => __('Trending Now Products'),
                'desc' => __('Products shown in homepage Trending Now section. Toggle per product or use Bulk Edit.'),
                'addHint' => __('Mark products as Trending from All Products (Quick actions) or here.'),
            ],
        ];
        $config = $scopeConfig[$productScope] ?? ['icon' => 'las la-box', 'title' => $pageTitle ?? '', 'desc' => '', 'addHint' => ''];
    @endphp
    <div class="product-section-header card b-radius--10 mb-4 border-0 shadow-sm">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="product-section-header__icon rounded-3 d-flex align-items-center justify-content-center">
                        <i class="{{ $config['icon'] }} fs-4"></i>
                    </div>
                </div>
                <div class="col">
                    <h5 class="mb-1">{{ $config['title'] }}</h5>
                    <p class="text-muted small mb-0">{{ $config['desc'] }}</p>
                    @if(!empty($config['addHint']))
                        <p class="text-muted small mb-0 mt-1"><i class="las la-info-circle"></i> {{ $config['addHint'] }}</p>
                    @endif
                </div>
                <div class="col-auto text-end">
                    <span class="product-section-header__count badge bg--primary rounded-pill px-3 py-2">{{ $products->total() }} @lang('products')</span>
                    <a href="{{ route('admin.product.index') }}" class="btn btn-sm btn--outline-primary ms-2">
                        <i class="las la-th-large"></i> @lang('All Products')
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Advanced Filters --}}
    @if(isset($brands) && isset($categories))
    <div class="row mb-3">
        <div class="col-12">
            <form method="get" action="{{ request()->url() }}" class="card b-radius--10 border-light" id="adminProductFilterForm">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('per_page'))<input type="hidden" name="per_page" value="{{ request('per_page') }}">@endif
                @if(request('low_stock'))<input type="hidden" name="low_stock" value="1">@endif
                <div class="card-body py-2 px-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label class="form-label small mb-0">@lang('Category')</label>
                            <select name="category_id" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                                <option value="">@lang('All')</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ __($name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-0">@lang('Brand')</label>
                            <select name="brand_id" class="form-select form-select-sm" style="width: auto; min-width: 120px;">
                                <option value="">@lang('All')</option>
                                @foreach($brands as $id => $name)
                                    <option value="{{ $id }}" {{ request('brand_id') == $id ? 'selected' : '' }}>{{ __($name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-0">@lang('Status')</label>
                            <select name="status" class="form-select form-select-sm" style="width: auto;">
                                <option value="">@lang('All')</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>@lang('Active')</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>@lang('Inactive')</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label small mb-0">@lang('Stock')</label>
                            <select name="stock" class="form-select form-select-sm" style="width: auto;">
                                <option value="">@lang('All')</option>
                                <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>@lang('Out of stock')</option>
                                <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>@lang('Low (1-5)')</option>
                                <option value="ok" {{ request('stock') === 'ok' ? 'selected' : '' }}>@lang('In stock')</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn--primary"><i class="las la-filter"></i> @lang('Filter')</button>
                            <a href="{{ request()->url() }}" class="btn btn-sm btn-outline--dark">@lang('Reset')</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Bulk Actions Bar --}}
    @if($products->total() > 0)
    <div id="adminProductBulkBar" class="row mb-3 d-none">
        <div class="col-12">
            <div class="card b-radius--10 border-primary">
                <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="text-muted small" id="bulkSelectedCount">0 @lang('selected')</span>
                        <button type="button" class="btn btn-sm btn-outline--dark" id="bulkDeselectAll">@lang('Deselect All')</button>
                        <button type="button" class="btn btn-sm btn-outline--danger" id="bulkDeleteBtn">
                            <i class="las la-trash"></i> @lang('Delete Selected')
                        </button>
                        <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
                            <i class="las la-edit"></i> @lang('Bulk Edit')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Toolbar: per page + view mode --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <label class="mb-0 text-muted small">@lang('Per page')</label>
                    <select id="adminProductPerPage" class="form-select form-select-sm" style="width: auto;">
                        @php $currentPerPage = (int) request('per_page', getPaginate()); @endphp
                        @foreach([10, 20, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $currentPerPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span class="text-muted small">@lang('View')</span>
                    <div class="btn-group btn-group-sm" role="group" id="adminProductViewToggle">
                        <button type="button" class="btn btn-outline--primary admin-view-btn active" data-view="grid" title="@lang('Grid')" aria-pressed="true"><i class="las la-th-large"></i></button>
                        <button type="button" class="btn btn-outline--primary admin-view-btn" data-view="list" title="@lang('List')" aria-pressed="false"><i class="las la-list"></i></button>
                    </div>
                    @if($products->total() > 0)
                    <span class="ms-2 text-muted small">
                        <input type="checkbox" id="selectAllProducts" class="form-check-input" title="@lang('Select All')">
                        <label for="selectAllProducts" class="form-check-label ms-1">@lang('Select All')</label>
                    </span>
                    @endif
                </div>
                <div class="text-muted small">
                    @lang('Total'): <strong>{{ $products->total() }}</strong> @lang('products')
                </div>
            </div>
        </div>
    </div>

    {{-- Grid View: 8 per row at 100% zoom, 2mm gap, flexible wrap on zoom --}}
    <div id="adminProductGridView" class="admin-product-grid-wrapper">
        <div class="admin-product-grid">
            @forelse($products as $product)
                <div class="admin-product-grid__item">
                    <div class="admin-product-card card b-radius--10 h-100">
                        <div class="admin-product-card__img-wrap">
                            <div class="admin-product-card__check-wrap">
                                <input type="checkbox" class="form-check-input product-select-check" value="{{ $product->id }}" id="prod_{{ $product->id }}">
                            </div>
                            <a href="{{ route('admin.product.edit', $product->id) }}" class="admin-product-card__img-link">
                                <img src="{{ $product->imageShow() }}" alt="{{ __($product->name) }}" class="admin-product-card__img" loading="lazy">
                            </a>
                            <div class="admin-product-card__badges">
                                @if($product->status)
                                    <span class="badge bg-success">@lang('Active')</span>
                                @else
                                    <span class="badge bg-danger">@lang('Inactive')</span>
                                @endif
                                @if($product->featured_product)<span class="badge bg-info" title="@lang('Featured')">F</span>@endif
                                @if($product->hot_deals)<span class="badge bg-warning text-dark" title="@lang('Hot Deal')">H</span>@endif
                                @if($product->today_deals)<span class="badge bg-danger" title="@lang('Today Deal')">T</span>@endif
                                @if(isset($product->trending_now) && $product->trending_now)<span class="badge bg-success" title="@lang('Trending Now')">Tr</span>@endif
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <h6 class="admin-product-card__title" title="{{ __($product->name) }}">{{ \Illuminate\Support\Str::limit(__($product->name), 22) }}</h6>
                            <div class="admin-product-card__meta small text-muted">
                                <span class="d-block text-truncate" title="{{ $product->product_sku }}">{{ $product->product_sku }}</span>
                            </div>
                            <div class="admin-product-card__price">
                                <strong class="text--base">{{ $general->cur_sym }}{{ showAmount(productPrice($product)) }}</strong>
                            </div>
                            <div class="admin-product-card__stock small">
                                <span class="{{ $product->quantity <= 0 ? 'text-danger' : ($product->quantity <= 5 ? 'text-warning' : 'text-success') }}">{{ $product->quantity }}</span> @lang('pcs')
                            </div>
                            <div class="admin-product-card__actions d-flex flex-wrap gap-1">
                                <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-sm btn-outline--primary px-1" title="@lang('Edit')"><i class="las la-pen"></i></a>
                                <a href="{{ route('admin.product.reviews', $product->id) }}" class="btn btn-sm btn-outline--info px-1" title="@lang('Reviews')"><i class="las la-comment-dots"></i></a>
                                <a href="{{ product_detail_url($product) }}" target="_blank" class="btn btn-sm btn-outline--success px-1" title="@lang('View on site')"><i class="las la-external-link-alt"></i></a>
                                <div class="dropdown d-inline">
                                    <button class="btn btn-sm btn-outline--dark dropdown-toggle px-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="@lang('Quick actions')"><i class="las la-cog"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><button type="button" class="dropdown-item text-danger confirmationBtn" data-action="{{ route('admin.product.delete', $product->id) }}" data-question="@lang('Are you sure to delete this product? This cannot be undone.')"><i class="las la-trash me-1"></i> @lang('Delete')</button></li>
                                        <li><hr class="dropdown-divider"></li>
                                        @if(!$product->status)
                                            <li><button type="button" class="dropdown-item text-success confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Are you sure to enable this product?')"><i class="las la-eye me-1"></i> @lang('Enable')</button></li>
                                        @else
                                            <li><button type="button" class="dropdown-item text-danger confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Are you sure to disable this product?')"><i class="las la-eye-slash me-1"></i> @lang('Disable')</button></li>
                                        @endif
                                        @if(!$product->featured_product)
                                            <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.featured', $product->id) }}" data-question="@lang('Are you sure to featured this product?')"><i class="las la-check-circle me-1"></i> @lang('Featured')</button></li>
                                        @else
                                            <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.featured', $product->id) }}" data-question="@lang('Remove from featured?')"><i class="las la-times-circle me-1"></i> @lang('Unfeatured')</button></li>
                                        @endif
                                        @if(!$product->hot_deals)
                                            <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.hot.deal', $product->id) }}" data-question="@lang('Enable hot deal?')"><i class="las la-fire me-1"></i> @lang('Hot Deal Enable')</button></li>
                                        @else
                                            <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.hot.deal', $product->id) }}" data-question="@lang('Disable hot deal?')"><i class="las la-ban me-1"></i> @lang('Hot Deal Disable')</button></li>
                                        @endif
                                        @if(!$product->today_deals)
                                            <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.today.deal', $product->id) }}" data-question="@lang('Enable today deal?')"><i class="las la-clock me-1"></i> @lang('Today Deal Enable')</button></li>
                                        @else
                                            <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.today.deal', $product->id) }}" data-question="@lang('Disable today deal?')"><i class="las la-clock me-1"></i> @lang('Today Deal Disable')</button></li>
                                        @endif
                                        @if(isset($product->trending_now))
                                            @if(!$product->trending_now)
                                                <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.trending.deal', $product->id) }}" data-question="@lang('Show in Trending Now?')"><i class="las la-fire-alt me-1"></i> @lang('Trending Now Enable')</button></li>
                                            @else
                                                <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.trending.deal', $product->id) }}" data-question="@lang('Remove from Trending Now?')"><i class="las la-fire-alt me-1"></i> @lang('Trending Now Disable')</button></li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="admin-product-grid__empty">
                    <div class="card b-radius--10">
                        <div class="card-body text-center py-5 text-muted">
                            {{ __($emptyMessage) }}
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- List View (table) --}}
    <div id="adminProductListView" class="d-none">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--sm">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="selectAllProductsList" class="form-check-input" title="@lang('Select All')"></th>
                                <th>@lang('Image')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('SKU')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Stock')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td><input type="checkbox" class="form-check-input product-select-check" value="{{ $product->id }}"></td>
                                    <td>
                                        <div class="admin-product-list-thumb">
                                            <img src="{{ $product->imageShow() }}" alt="">
                                        </div>
                                    </td>
                                    <td><span class="name">{{ __($product->name) }}</span></td>
                                    <td>{{ $product->product_sku }}</td>
                                    <td>{{ $product->category ? __($product->category->name) : '-' }}</td>
                                    <td>{{ $general->cur_sym }}{{ showAmount(productPrice($product)) }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{!! $product->statusBadge ?? '' !!}</td>
                                    <td>
                                        <div class="button--group d-flex flex-wrap gap-1">
                                            <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-pen"></i></a>
                                            <a href="{{ route('admin.product.reviews', $product->id) }}" class="btn btn-sm btn-outline--info"><i class="las la-comment-dots"></i></a>
                                            <a href="{{ product_detail_url($product) }}" target="_blank" class="btn btn-sm btn-outline--success"><i class="las la-external-link-alt"></i></a>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline--primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="las la-ellipsis-v"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><button type="button" class="dropdown-item text-danger confirmationBtn" data-action="{{ route('admin.product.delete', $product->id) }}" data-question="@lang('Are you sure to delete this product? This cannot be undone.')"><i class="las la-trash me-1"></i> @lang('Delete')</button></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    @if(!$product->status)
                                                        <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Enable this product?')">@lang('Enable')</button></li>
                                                    @else
                                                        <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Disable this product?')">@lang('Disable')</button></li>
                                                    @endif
                                                    <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.featured', $product->id) }}" data-question="@lang('Toggle featured?')">@lang($product->featured_product ? 'Unfeatured' : 'Featured')</button></li>
                                                    <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.hot.deal', $product->id) }}" data-question="@lang('Toggle hot deal?')">@lang($product->hot_deals ? 'Hot Deal Disable' : 'Hot Deal Enable')</button></li>
                                                    <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.today.deal', $product->id) }}" data-question="@lang('Toggle today deal?')">@lang($product->today_deals ? 'Today Deal Disable' : 'Today Deal Enable')</button></li>
                                                    @if(isset($product->trending_now))
                                                    <li><button type="button" class="dropdown-item confirmationBtn" data-action="{{ route('admin.product.trending.deal', $product->id) }}" data-question="@lang('Toggle Trending Now?')">@lang(($product->trending_now ?? 0) ? 'Trending Now Disable' : 'Trending Now Enable')</button></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="9">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($products->hasPages() && $products->total() > 0)
        <div class="mt-4">
            {{ paginateLinks($products) }}
        </div>
    @endif

    {{-- Bulk Edit Modal --}}
    <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="bulkEditForm" method="post" action="{{ route('admin.product.bulk.edit') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkEditModalLabel">@lang('Bulk Edit Selected Products')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">@lang('Choose an action to apply to all selected products.')</p>
                        <div class="mb-0">
                            <label class="form-label">@lang('Action')</label>
                            <select name="action" class="form-select" required>
                                <option value="">@lang('Select action')...</option>
                                <optgroup label="@lang('Status')">
                                    <option value="enable">@lang('Enable (Active)')</option>
                                    <option value="disable">@lang('Disable (Inactive)')</option>
                                </optgroup>
                                <optgroup label="@lang('Featured')">
                                    <option value="featured_on">@lang('Set as Featured')</option>
                                    <option value="featured_off">@lang('Remove from Featured')</option>
                                </optgroup>
                                <optgroup label="@lang('Hot Deal')">
                                    <option value="hot_deal_on">@lang('Enable Hot Deal')</option>
                                    <option value="hot_deal_off">@lang('Disable Hot Deal')</option>
                                </optgroup>
                                <optgroup label="@lang('Today Deal')">
                                    <option value="today_deal_on">@lang('Enable Today Deal')</option>
                                    <option value="today_deal_off">@lang('Disable Today Deal')</option>
                                </optgroup>
                                <optgroup label="@lang('Trending Now')">
                                    <option value="trending_on">@lang('Enable Trending Now')</option>
                                    <option value="trending_off">@lang('Disable Trending Now')</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--primary">@lang('Apply')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="@lang('Search product...')" />
    <div class="btn-group">
        <a href="{{ route('admin.product.create') }}" class="btn btn-outline--primary h-45">
            <i class="las la-tshirt"></i> @lang('Add Clothing')
        </a>
        <a href="{{ route('admin.product.create2') }}" class="btn btn--primary h-45">
            <i class="las la-box"></i> @lang('Add General Product')
        </a>
    </div>
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
(function() {
    var STORAGE_KEY = 'admin_product_view_mode';
    var perPageEl = document.getElementById('adminProductPerPage');
    var gridView = document.getElementById('adminProductGridView');
    var listView = document.getElementById('adminProductListView');
    var viewToggle = document.getElementById('adminProductViewToggle');
    var selectAllProducts = document.getElementById('selectAllProducts');
    var selectAllProductsList = document.getElementById('selectAllProductsList');
    var bulkBar = document.getElementById('adminProductBulkBar');
    var bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    var bulkEditForm = document.getElementById('bulkEditForm');

    function getSelectedIds() {
        var checkboxes = document.querySelectorAll('.product-select-check:checked');
        var ids = [];
        checkboxes.forEach(function(cb) { ids.push(cb.value); });
        return ids;
    }

    function updateBulkBar() {
        var ids = getSelectedIds();
        var count = ids.length;
        if (bulkBar) {
            bulkBar.classList.toggle('d-none', count === 0);
        }
        var countEl = document.getElementById('bulkSelectedCount');
        if (countEl) countEl.textContent = count + ' @lang("selected")';

        if (selectAllProducts) {
            var all = document.querySelectorAll('.product-select-check');
            selectAllProducts.checked = all.length > 0 && all.length === document.querySelectorAll('.product-select-check:checked').length;
        }
        if (selectAllProductsList) {
            var all2 = document.querySelectorAll('.product-select-check');
            selectAllProductsList.checked = all2.length > 0 && all2.length === document.querySelectorAll('.product-select-check:checked').length;
        }
    }

    function toggleSelectAll(checked) {
        document.querySelectorAll('.product-select-check').forEach(function(cb) { cb.checked = checked; });
        updateBulkBar();
    }

    if (selectAllProducts) {
        selectAllProducts.addEventListener('change', function() { toggleSelectAll(this.checked); });
    }
    if (selectAllProductsList) {
        selectAllProductsList.addEventListener('change', function() { toggleSelectAll(this.checked); });
    }
    document.querySelectorAll('.product-select-check').forEach(function(cb) {
        cb.addEventListener('change', updateBulkBar);
    });

    if (document.getElementById('bulkDeselectAll')) {
        document.getElementById('bulkDeselectAll').addEventListener('click', function() {
            toggleSelectAll(false);
        });
    }

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function(e) {
            var ids = getSelectedIds();
            if (ids.length === 0) {
                e.preventDefault();
                alert('@lang("Please select at least one product.")');
                return;
            }
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.product.bulk.delete") }}';
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            ids.forEach(function(id) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                form.appendChild(inp);
            });
            document.body.appendChild(form);
            if (confirm('@lang("Are you sure to delete selected products? This cannot be undone.")')) {
                form.submit();
            }
            document.body.removeChild(form);
        });
    }

    if (bulkEditForm) {
        bulkEditForm.addEventListener('submit', function(e) {
            var ids = getSelectedIds();
            if (ids.length === 0) {
                e.preventDefault();
                alert('@lang("Please select at least one product.")');
                return false;
            }
            bulkEditForm.querySelectorAll('input[name="ids[]"]').forEach(function(inp) { inp.remove(); });
            ids.forEach(function(id) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'ids[]';
                inp.value = id;
                bulkEditForm.appendChild(inp);
            });
        });
    }

    function applyViewMode(mode) {
        if (!gridView || !listView) return;
        var btns = viewToggle ? viewToggle.querySelectorAll('.admin-view-btn') : [];
        if (mode === 'list') {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            btns.forEach(function(b) {
                b.classList.toggle('active', b.getAttribute('data-view') === 'list');
                b.setAttribute('aria-pressed', b.getAttribute('data-view') === 'list' ? 'true' : 'false');
            });
        } else {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            btns.forEach(function(b) {
                b.classList.toggle('active', b.getAttribute('data-view') === 'grid');
                b.setAttribute('aria-pressed', b.getAttribute('data-view') === 'grid' ? 'true' : 'false');
            });
        }
        try { localStorage.setItem(STORAGE_KEY, mode); } catch (e) {}
    }

    if (viewToggle) {
        viewToggle.querySelectorAll('.admin-view-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var mode = this.getAttribute('data-view');
                if (mode === 'grid' || mode === 'list') applyViewMode(mode);
            });
        });
    }

    if (perPageEl) {
        perPageEl.addEventListener('change', function() {
            var url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });
    }

    try {
        var saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'list' || saved === 'grid') applyViewMode(saved);
    } catch (e) {}
})();
});
</script>
@endpush
