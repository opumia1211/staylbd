<div class="products-grid grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-0" role="list">
@forelse($products as $product)
    <div class="product-card-col product-card-col--home" role="listitem">
        @php $fp = $loop->iteration <= 8 ? 'high' : 'low'; @endphp
        @include($activeTemplate . 'products.partials.card', ['product' => $product, 'general' => $general, 'fetchpriority' => $fp])
    </div>
@empty
    <div class="product-card-col product-card-col--empty col-span-full" role="status">
        <div class="text-center py-5 px-3">
            <p class="text-muted mb-0">{{ __($emptyMessage) }}</p>
        </div>
    </div>
@endforelse
</div>
<div class="products-page-rail__pagination mt-4">
    {{ paginateLinks($products) }}
</div>
