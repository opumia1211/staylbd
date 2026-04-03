{{-- Lazy-load fragment: append more product cards to a section (no wrapper) --}}
@foreach($products as $product)
<div class="product-card-col product-card-col--home product-carousel__item">
    @php $fp = $loop->iteration <= 8 ? 'high' : 'low'; @endphp
    @include($activeTemplate . 'products.partials.card', ['product' => $product, 'general' => $general, 'fetchpriority' => $fp])
</div>
@endforeach
