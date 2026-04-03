@php
    $general = $general ?? gs();
    $count = $products->count();
    $wishlistMax = $wishlistMax ?? \App\Models\Wishlist::WISHLIST_MAX;
    $prices = $products->isNotEmpty() ? $products->map(fn($p) => productPrice($p))->filter(fn($p) => $p !== null) : collect();
    $minPrice = $prices->isNotEmpty() ? $prices->min() : null;
@endphp
<div class="wishlist-page wishlist-container pt-0 pb-3 dashboard-list-page" data-cur-sym="{{ $general->cur_sym ?? '' }}" data-wishlist-max="{{ $wishlistMax }}">
    <div class="container wishlist-page__container">
        @if($products->isNotEmpty())
        {{-- Toolbar: count badge, Add more, Print, Clear all --}}
        <div class="wishlist-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-light text-dark px-3 py-2 wishlist-count-badge">{{ $count }} / {{ $wishlistMax }}</span>
                @if($count < $wishlistMax)
                    <a href="{{ route('products') }}" class="btn btn-sm btn-outline-primary">@include($activeTemplate . 'partials.icon', ['name' => 'plus', 'class' => 'me-1'])@lang('Add more')</a>
                @else
                    <span class="small text-muted">@lang('Maximum :max products in wishlist. Remove one to add another.', ['max' => $wishlistMax])</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-secondary wishlist-btn-print" title="@lang('Print')">@include($activeTemplate . 'partials.icon', ['name' => 'print', 'class' => 'me-1'])@lang('Print')</button>
                <button type="button" class="btn btn-sm btn-outline-danger clear-wishlist-btn" title="@lang('Clear all')">@include($activeTemplate . 'partials.icon', ['name' => 'broom', 'class' => 'me-1'])@lang('Clear all')</button>
            </div>
        </div>

        {{-- Desktop (≥1200px): Table – Image, Product, SKU, Category, Brand, Stock, Price, Rating, Action --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden d-none d-xl-block wishlist-card-table">
            <div class="table-responsive wishlist-table-wrap">
                <table class="table table-hover align-middle mb-0 dashboard-compact-table wishlist-compact-table list-page-table">
                    <thead class="table-success">
                        <tr>
                            <th class="py-2 ps-2" style="width: 5%;">@lang('Image')</th>
                            <th class="py-2" style="width: 16%;">@lang('Product')</th>
                            <th class="py-2" style="width: 6%;">@lang('SKU')</th>
                            <th class="py-2" style="width: 7%;">@lang('Category')</th>
                            <th class="py-2" style="width: 7%;">@lang('Brand')</th>
                            <th class="py-2" style="width: 6%;">@lang('Stock')</th>
                            <th class="py-2 text-end" style="width: 8%;">@lang('Price')</th>
                            <th class="py-2 text-center" style="width: 7%;">@lang('Rating')</th>
                            <th class="py-2 pe-2 text-end wishlist-th-action" style="width: 38%; min-width: 200px;">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            @php
                                $price = productPrice($product);
                                $stockQty = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->sum('quantity') : ($product->quantity ?? 0);
                                $isBest = $minPrice !== null && $count > 1 && abs($price - $minPrice) < 0.001;
                            @endphp
                            <tr class="dashboard-compact-row product-list-row wishlist-row" data-product-id="{{ $product->id }}">
                                <td class="py-2 ps-2 align-middle">
                                    <a href="{{ product_detail_url($product) }}" class="d-block rounded overflow-hidden bg-light wishlist-table-img-link" data-no-ajax>
                                        <img src="{{ $product->imageShow() }}" alt="{{ __($product->name) }}" class="w-100 h-100 object-fit-cover" loading="lazy" decoding="async" width="72" height="72">
                                    </a>
                                </td>
                                <td class="py-2 align-middle">
                                    <a href="{{ product_detail_url($product) }}" class="text-decoration-none fw-semibold dashboard-compact-name d-block dashboard-list-name-link" data-no-ajax>{{ __($product->name) }}</a>
                                </td>
                                <td class="py-2 align-middle small text-muted">{{ $product->product_sku ?? '—' }}</td>
                                <td class="py-2 align-middle small text-muted">{{ __($product->category->name ?? '—') }}</td>
                                <td class="py-2 align-middle small text-muted">{{ __($product->brand->name ?? '—') }}</td>
                                <td class="py-2 align-middle">
                                    <span class="badge {{ $stockQty > 0 ? 'bg-success' : 'bg-danger' }} wishlist-stock-badge">{{ $stockQty > 0 ? __('In Stock') : __('Out of Stock') }}</span>
                                </td>
                                <td class="py-2 text-end align-middle text--base fw-semibold wishlist-price-cell">
                                    {{ $general->cur_sym }}{{ showAmount($price) }}
                                    @if($isBest)<span class="badge bg-success ms-1 wishlist-best-badge">@lang('Best value')</span>@endif
                                </td>
                                <td class="py-2 text-center align-middle">
                                    <span class="ratings d-inline-block">{!! showProductRatings($product->avg_rate ?? 0) !!}</span>
                                    <span class="small text-muted">({{ $product->reviews_count ?? ($product->reviews->count() ?? 0) }})</span>
                                </td>
                                <td class="py-2 pe-2 align-middle product-list-td-action">
                                    <div class="action-buttons product-list-row__action-btns d-flex flex-wrap gap-2 justify-content-end">
                                        <a href="{{ product_detail_url($product) }}" class="btn btn-primary list-page-action-btn wishlist-desktop-view-btn" title="@lang('View')" data-no-ajax>@lang('View')</a>
                                        <button type="button" class="btn btn-success list-page-action-btn add-to-cart" data-product_id="{{ $product->id }}" title="@lang('Add to Cart')">@lang('Add to Cart')</button>
                                        <span class="btn btn-warning list-page-action-btn disabled" aria-label="@lang('In Wishlist')">@lang('In Wishlist')</span>
                                        <button type="button" class="btn btn-danger list-page-action-btn wishlist-delete-btn" data-product_id="{{ $product->id }}" title="@lang('Remove')">@lang('Remove')</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile / Tablet (<1200px): Card layout with CSS variables per breakpoint --}}
        <div class="wishlist-mobile-cards d-xl-none">
            @foreach($products as $product)
                @php
                    $price = productPrice($product);
                    $stockQty = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->sum('quantity') : ($product->quantity ?? 0);
                    $isBest = $minPrice !== null && $count > 1 && abs($price - $minPrice) < 0.001;
                @endphp
                <div class="wishlist-mobile-card card border-0 shadow-sm rounded-3 overflow-hidden wishlist-row" data-product-id="{{ $product->id }}">
                    <div class="card-body wishlist-mobile-card__body">
                        <div class="wishlist-mobile-card__inner d-flex">
                            <a href="{{ product_detail_url($product) }}" class="wishlist-mobile-card__img flex-shrink-0 rounded overflow-hidden bg-light d-block" data-no-ajax>
                                <img src="{{ $product->imageShow() }}" alt="{{ __($product->name) }}" class="w-100 h-100 object-fit-cover" loading="lazy" decoding="async" width="72" height="72">
                            </a>
                            <div class="wishlist-mobile-card__content flex-grow-1 min-w-0">
                                <a href="{{ product_detail_url($product) }}" class="wishlist-mobile-card__name fw-semibold text-dark text-decoration-none d-block text-break" data-no-ajax>{{ __($product->name) }}</a>
                                <div class="wishlist-mobile-card__meta small text-muted">
                                    @if(!empty($product->product_sku))<span>@lang('SKU'): {{ $product->product_sku }}</span>@endif
                                    @if(!empty($product->category))<span>@lang('Category'): {{ __($product->category->name ?? '') }}</span>@endif
                                    @if(!empty($product->brand))<span>@lang('Brand'): {{ __($product->brand->name ?? '') }}</span>@endif
                                    <span class="badge {{ $stockQty > 0 ? 'bg-success' : 'bg-danger' }}">{{ $stockQty > 0 ? __('In Stock') : __('Out of Stock') }}</span>
                                    @if($isBest)<span class="badge bg-success wishlist-mobile-card__best-badge">@lang('Best value')</span>@endif
                                </div>
                                <div class="wishlist-mobile-card__price-row d-flex align-items-center flex-wrap gap-2">
                                    <span class="wishlist-mobile-card__price fw-bold text-success">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
                                    <span class="ratings d-inline-block">{!! showProductRatings($product->avg_rate ?? 0) !!}</span>
                                    <span class="small text-muted">({{ $product->reviews_count ?? ($product->reviews->count() ?? 0) }})</span>
                                </div>
                                <div class="wishlist-mobile-card__actions action-buttons d-flex flex-wrap gap-2">
                                    <a href="{{ product_detail_url($product) }}" class="btn btn-primary list-page-action-btn" data-no-ajax>@lang('View')</a>
                                    <button type="button" class="btn btn-success list-page-action-btn add-to-cart" data-product_id="{{ $product->id }}">@lang('Add to Cart')</button>
                                    <span class="btn btn-warning list-page-action-btn disabled">@lang('In Wishlist')</span>
                                    <button type="button" class="btn btn-danger list-page-action-btn wishlist-delete-btn" data-product_id="{{ $product->id }}">@lang('Remove')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer: Continue Shopping --}}
        <div class="mt-3 wishlist-footer">
            <a href="{{ route('products') }}" class="btn btn--base">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag', 'class' => 'me-1'])@lang('Continue Shopping')</a>
        </div>
        @else
        <div class="card border-0 shadow-sm rounded-3 list-page-empty">
            <div class="card-body text-center py-5 px-4">
                @include($activeTemplate . 'partials.icon', ['name' => 'heart', 'class' => 'list-page-empty__icon text-muted'])
                <h5 class="list-page-empty__title text-muted">@lang('No products in wishlist')</h5>
                <p class="list-page-empty__text text-muted mb-4">{{ __($emptyMessage ?? 'Your wishlist is empty. Start adding products now!') }}</p>
                <a href="{{ route('products') }}" class="btn btn--base">@lang('Browse Products')</a>
            </div>
        </div>
        @endif
    </div>
</div>
