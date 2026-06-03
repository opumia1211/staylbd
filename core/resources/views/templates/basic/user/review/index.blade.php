@extends($activeTemplate . 'layouts.master')
@section('dashboard_page_title')
    @include($activeTemplate . 'partials.dashboard_page_header', ['title' => __('Review Products'), 'subtitle' => __('Rate and review products you have purchased')])
@endsection
@section('content')
    <div class="review-page review-view-compact dashboard-list-page pt-0" data-view-mode="compact">
        {{-- ভিউ টগল: চিকন লাইন | হালকা বড় --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="dashboard-view-toggle btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary dashboard-view-btn active" data-view="compact">@include($activeTemplate . 'partials.icon', ['name' => 'list']) <span class="d-none d-sm-inline">@lang('Compact')</span></button>
                <button type="button" class="btn btn-outline-secondary dashboard-view-btn" data-view="comfortable">@include($activeTemplate . 'partials.icon', ['name' => 'th-large']) <span class="d-none d-sm-inline">@lang('Comfortable')</span></button>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0 review-table dashboard-compact-table review-compact-table" style="min-width: 900px;">
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
                        @forelse ($products as $product)
                            @php
                                $qty = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->sum('quantity') : ($product->quantity ?? 0);
                            @endphp
                            <tr id="tr_{{ $product->id }}" class="dashboard-compact-row">
                                <td class="py-2 ps-2 align-middle">
                                    <a href="{{ product_detail_url($product) }}" class="d-block rounded overflow-hidden bg-light up-product-img" style="width:48px;height:48px;min-width:48px;min-height:48px;">
                                        <img src="{{ $product->imageShow() }}" alt="@lang('image')" class="w-100 h-100 object-fit-contain" loading="lazy" decoding="async" width="48" height="48">
                                    </a>
                                </td>
                                <td class="py-2 align-middle">
                                    <a href="{{ product_detail_url($product) }}" class="text-decoration-none fw-semibold dashboard-compact-name review-compact-name d-block" style="font-size:0.8rem;line-height:1.3;color:#0d6efd;">{{ __($product->name) }}</a>
                                </td>
                                <td class="py-2 align-middle small text-muted">{{ $product->product_sku ?? '—' }}</td>
                                <td class="py-2 align-middle small text-muted">{{ $product->category->name ?? '—' }}</td>
                                <td class="py-2 align-middle small text-muted">{{ $product->brand->name ?? '—' }}</td>
                                <td class="py-2 align-middle">
                                    <span class="badge {{ $qty > 0 ? 'bg-success' : 'bg-danger' }}" style="font-size:0.65rem;">{{ $qty > 0 ? __('In Stock') : __('Out of Stock') }}</span>
                                </td>
                                <td class="py-2 text-end align-middle text--base fw-semibold" style="font-size:0.8rem;">{{ $general->cur_sym }}{{ showAmount(productPrice($product)) }}</td>
                                <td class="py-2 text-center align-middle">
                                    <span class="ratings d-inline-block">@php echo showProductRatings($product->avg_rate); @endphp</span>
                                    <span class="small text-muted">({{ $product->reviews->count() ?? 0 }})</span>
                                </td>
                                <td class="py-2 pe-2 align-middle">
                                    <div class="product-list-row__action-btns d-flex flex-wrap gap-1 justify-content-end">
                                        <a href="{{ product_detail_url($product) }}" class="product-list-row__btn product-list-row__btn--view btn btn-sm btn-review-action" title="@lang('View')" data-no-ajax>@include($activeTemplate . 'partials.icon', ['name' => 'external-link-alt'])</a>
                                        <a href="{{ storefront_route('cart.list.buy.now', ['id' => $product->id]) }}" class="product-list-row__btn product-list-row__btn--buy btn btn-sm btn-review-action" title="@lang('Buy Now')" data-no-ajax>@include($activeTemplate . 'partials.icon', ['name' => 'bolt'])</a>
                                        <button type="button" class="product-list-row__btn product-list-row__btn--wishlist add-wishlist btn btn-sm btn-review-action" data-product_id="{{ $product->id }}" title="@lang('Wishlist')">@include($activeTemplate . 'partials.icon', ['name' => 'heart'])</button>
                                        <a href="{{ route('user.review.create', [slug($product->name), $product->id]) }}" class="product-list-row__btn product-list-row__btn--review btn btn-sm btn--base btn-review-action @if ($product->reviews->count()) disabled @endif" title="@lang('Add Review')" data-bs-toggle="tooltip" data-bs-position="top">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'star-of-david'])
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text--danger py-4">{{ __($emptyMessage ?? 'No products to review.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ paginateLinks($products) }}
    </div>
@endsection

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush
@push('script')
<script>
(function() {
    var KEY = 'review_view';
    var page = document.querySelector('.review-page.dashboard-list-page');
    function setView(mode) {
        if (!page) return;
        page.setAttribute('data-view-mode', mode);
        page.classList.remove('review-view-compact', 'review-view-comfortable');
        page.classList.add('review-view-' + mode);
        try { localStorage.setItem(KEY, mode); } catch (e) {}
        page.querySelectorAll('.dashboard-view-btn').forEach(function(btn) {
            btn.classList.toggle('active', btn.getAttribute('data-view') === mode);
        });
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.review-page .dashboard-view-btn')) return;
        var btn = e.target.closest('.dashboard-view-btn');
        var view = btn && btn.getAttribute('data-view');
        if (view) setView(view);
    });
    (function init() {
        try {
            var saved = localStorage.getItem(KEY);
            if (saved === 'compact' || saved === 'comfortable') setView(saved);
            else setView('compact');
        } catch (e) { setView('compact'); }
    })();
})();
</script>
@endpush
