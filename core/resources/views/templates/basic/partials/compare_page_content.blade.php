@php
    $count = $products->count();
    $compareMax = \App\Models\ProductComparison::COMPARE_MAX;
    $prices = $products->map(fn($item) => $item->product ? productPrice($item->product) : null)->filter(fn($p) => $p !== null);
    $minPrice = $prices->isNotEmpty() ? $prices->min() : null;
    $general = $general ?? gs();
@endphp
<div class="compare-page compare-container pt-1 pb-3 dashboard-list-page" data-view-mode="compact">
    <div class="container compare-page__container">
        @if($products->isNotEmpty())
        <div class="compare-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-light text-dark px-3 py-2">{{ $count }} / {{ $compareMax }}</span>
                @if($count < $compareMax)
                    <a href="{{ route('products') }}" class="btn btn-sm btn-outline-primary">@include($activeTemplate . 'partials.icon', ['name' => 'plus', 'class' => 'me-1'])@lang('Add more')</a>
                @else
                    <span class="small text-muted">@lang('Maximum :max products can be compared. Remove one to add another.', ['max' => $compareMax])</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-secondary btn-print" title="@lang('Print')">@include($activeTemplate . 'partials.icon', ['name' => 'print', 'class' => 'me-1'])@lang('Print')</button>
                <button type="button" class="btn btn-sm btn-outline-danger clear-compare-btn" title="@lang('Clear all')">@include($activeTemplate . 'partials.icon', ['name' => 'broom', 'class' => 'me-1'])@lang('Clear all')</button>
            </div>
        </div>

        {{-- Laptop/Desktop only: table (hidden on mobile, tablet & slightly larger – cards for better UX) --}}
        <div class="card border-0 shadow-sm rounded-3 compare-table-card d-none d-xl-block">
            <div class="table-responsive compare-table-wrap">
                <table class="table table-hover align-middle mb-0 dashboard-compact-table compare-compact-table list-page-table">
                    <thead class="table-success">
                        <tr>
                            <th class="py-2 ps-2" style="width: 5%;">@lang('Image')</th>
                            <th class="py-2" style="width: 18%;">@lang('Product')</th>
                            <th class="py-2" style="width: 7%;">@lang('SKU')</th>
                            <th class="py-2" style="width: 8%;">@lang('Category')</th>
                            <th class="py-2" style="width: 8%;">@lang('Brand')</th>
                            <th class="py-2" style="width: 7%;">@lang('Stock')</th>
                            <th class="py-2 text-end" style="width: 8%;">@lang('Price')</th>
                            <th class="py-2 text-center" style="width: 8%;">@lang('Rating')</th>
                            <th class="py-2 pe-2 text-end" style="width: 31%;">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $item)
                            @php
                                $product = $item->product;
                                if (!$product) continue;
                                $price = productPrice($product);
                                $isBest = ($minPrice !== null && $count > 1 && $price == $minPrice);
                                $qty = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->sum('quantity') : ($product->quantity ?? 0);
                                $lowStockMax = (int) config('product_upload.low_stock_max', 20);
                                $stockTier = $qty > $lowStockMax ? 'in' : ($qty >= 1 ? 'low' : 'out');
                                $stockLabel = $stockTier === 'in' ? __('In Stock') : ($stockTier === 'low' ? __('Low Stock') : __('Out of Stock'));
                                $canPurchase = $qty > 0;
                            @endphp
                            <tr class="dashboard-compact-row product-list-row" data-product-id="{{ $product->id }}">
                                <td class="py-2 ps-2 align-middle">
                                    <a href="{{ product_detail_url($product) }}" class="compare-table-img-link d-block rounded overflow-hidden bg-light up-product-img" data-no-ajax>
                                        <img src="{{ $product->imageShow() }}" alt="{{ __($product->name) }}" class="w-100 h-100 object-fit-cover" loading="lazy" decoding="async" width="72" height="72">
                                    </a>
                                </td>
                                <td class="py-2 align-middle dashboard-compact-name">
                                    <a href="{{ product_detail_url($product) }}" class="text-decoration-none fw-semibold d-block dashboard-list-name-link" style="font-size:0.8rem;line-height:1.3;color:#0d6efd;" data-no-ajax>{{ __($product->name) }}</a>
                                </td>
                                <td class="py-2 align-middle small text-muted">{{ $product->product_sku ?? '—' }}</td>
                                <td class="py-2 align-middle small text-muted">{{ __($product->category->name ?? '—') }}</td>
                                <td class="py-2 align-middle small text-muted">{{ __($product->brand->name ?? '—') }}</td>
                                <td class="py-2 align-middle">
                                    <span class="badge staylbd-rt-stock {{ $stockTier === 'in' ? 'bg-success' : ($stockTier === 'low' ? 'bg-warning text-dark' : 'bg-danger') }}" style="font-size:0.65rem;" data-stock-tier="{{ $stockTier }}">{{ $stockLabel }}</span>
                                </td>
                                <td class="py-2 text-end align-middle text--base fw-semibold compare-price-cell" style="font-size:0.8rem;">
                                    <span class="staylbd-rt-price">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
                                    @if($isBest)<span class="badge bg-success ms-1" style="font-size:0.6rem;">@lang('Best value')</span>@endif
                                </td>
                                <td class="py-2 text-center align-middle">
                                    <span class="ratings d-inline-block">{!! showProductRatings($product->avg_rate ?? 0) !!}</span>
                                    <span class="small text-muted">({{ $product->reviews->count() ?? 0 }})</span>
                                </td>
                                <td class="py-2 pe-2 align-middle">
                                    <div class="action-buttons product-list-row__action-btns d-flex flex-wrap gap-2 justify-content-end">
                                        <a href="{{ product_detail_url($product) }}" class="btn btn-primary list-page-action-btn compare-desktop-view-btn" title="@lang('View')" data-no-ajax>@lang('View')</a>
                                        <button type="button" class="btn btn-success list-page-action-btn add-to-cart compare-btn--cart staylbd-rt-atc" data-product_id="{{ $product->id }}" title="@lang('Add to Cart')" @if(!$canPurchase) disabled aria-disabled="true" @endif>@lang('Add to Cart')</button>
                                        <button type="button" class="btn btn-warning list-page-action-btn add-wishlist" data-product_id="{{ $product->id }}" title="@lang('Add to Wishlist')">@lang('Add to Wishlist')</button>
                                        <button type="button" class="btn btn-danger list-page-action-btn remove-compare-btn" data-product_id="{{ $product->id }}" title="@lang('Remove')">@lang('Remove')</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile, tablet & slightly larger: stacked cards – compact, professional --}}
        <div class="compare-mobile-cards d-xl-none">
            @foreach($products as $item)
                @php
                    $product = $item->product;
                    if (!$product) continue;
                    $price = productPrice($product);
                    $isBest = ($minPrice !== null && $count > 1 && $price == $minPrice);
                    $qty = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->sum('quantity') : ($product->quantity ?? 0);
                    $lowStockMax = (int) config('product_upload.low_stock_max', 20);
                    $stockTier = $qty > $lowStockMax ? 'in' : ($qty >= 1 ? 'low' : 'out');
                    $stockLabel = $stockTier === 'in' ? __('In Stock') : ($stockTier === 'low' ? __('Low Stock') : __('Out of Stock'));
                    $canPurchase = $qty > 0;
                @endphp
                <div class="compare-mobile-card card border-0 shadow-sm rounded-3 overflow-hidden" data-product-id="{{ $product->id }}">
                    <div class="card-body compare-mobile-card__body">
                        <div class="compare-mobile-card__inner d-flex">
                            <a href="{{ product_detail_url($product) }}" class="compare-mobile-card__img flex-shrink-0 rounded overflow-hidden bg-light d-block" data-no-ajax>
                                <img src="{{ $product->imageShow() }}" alt="{{ __($product->name) }}" class="w-100 h-100 object-fit-cover" loading="lazy" decoding="async" width="72" height="72">
                            </a>
                            <div class="compare-mobile-card__content flex-grow-1 min-w-0">
                                <a href="{{ product_detail_url($product) }}" class="compare-mobile-card__name fw-semibold text-dark text-decoration-none d-block text-break" data-no-ajax>{{ __($product->name) }}</a>
                                <div class="compare-mobile-card__meta small text-muted">
                                    @if($product->product_sku)<span>@lang('SKU'): {{ $product->product_sku }}</span>@endif
                                    @if($product->category)<span>@lang('Category'): {{ __($product->category->name ?? '') }}</span>@endif
                                    @if($product->brand)<span>@lang('Brand'): {{ __($product->brand->name ?? '') }}</span>@endif
                                    <span class="badge staylbd-rt-stock {{ $stockTier === 'in' ? 'bg-success' : ($stockTier === 'low' ? 'bg-warning text-dark' : 'bg-danger') }}" data-stock-tier="{{ $stockTier }}">{{ $stockLabel }}</span>
                                    @if($isBest)<span class="badge bg-success compare-mobile-card__best-badge">@lang('Best value')</span>@endif
                                </div>
                                <div class="compare-mobile-card__price-row d-flex align-items-center flex-wrap gap-2">
                                    <span class="compare-mobile-card__price staylbd-rt-price fw-bold text-success">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
                                    <span class="ratings d-inline-block">{!! showProductRatings($product->avg_rate ?? 0) !!}</span>
                                    <span class="small text-muted">({{ $product->reviews->count() ?? 0 }})</span>
                                </div>
                                <div class="compare-mobile-card__actions action-buttons d-flex flex-wrap gap-2">
                                    <a href="{{ product_detail_url($product) }}" class="btn btn-primary list-page-action-btn" data-no-ajax>@lang('View')</a>
                                    <button type="button" class="btn btn-success list-page-action-btn add-to-cart compare-btn--cart staylbd-rt-atc" data-product_id="{{ $product->id }}" @if(!$canPurchase) disabled aria-disabled="true" @endif>@lang('Add to Cart')</button>
                                    <button type="button" class="btn btn-warning list-page-action-btn add-wishlist" data-product_id="{{ $product->id }}">@lang('Wishlist')</button>
                                    <button type="button" class="btn btn-danger list-page-action-btn remove-compare-btn" data-product_id="{{ $product->id }}">@lang('Remove')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            <a href="{{ route('products') }}" class="btn btn--base">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag', 'class' => 'me-1'])@lang('Continue Shopping')</a>
        </div>
        @else
        <div class="card border-0 shadow-sm rounded-3 list-page-empty">
            <div class="card-body text-center py-5 px-4">
                @include($activeTemplate . 'partials.icon', ['name' => 'balance-scale', 'class' => 'list-page-empty__icon text-muted'])
                <h5 class="list-page-empty__title text-muted">@lang('No products to compare')</h5>
                <p class="list-page-empty__text text-muted mb-4">@lang('Add products from the product page using the compare button to see them here.')</p>
                <a href="{{ route('products') }}" class="btn btn--base">@lang('Browse Products')</a>
            </div>
        </div>
        @endif
    </div>
</div>
