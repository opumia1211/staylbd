@forelse($products as $product)
    <div class="product-card-col product-card-col--home">
        @php $fp = $loop->iteration <= 8 ? 'high' : 'low'; @endphp
        @include($activeTemplate . 'products.partials.card', ['product' => $product, 'general' => $general, 'fetchpriority' => $fp])
    </div>
@empty
    <div class="product-card-col product-card-col--empty text-center col-span-full">
        <strong class="text--danger">{{ __($emptyMessage) }}</strong>
    </div>
@endforelse
