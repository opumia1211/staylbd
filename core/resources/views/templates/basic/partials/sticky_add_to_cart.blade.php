@php
    $homeData = getCachedHomeSectionData();
    if (!($homeData['settings']->sticky_cart_enabled ?? 1)) return;
    if (!isset($product)) return;
    $price = productPrice($product);
@endphp
<div id="stickyAddToCartBar" class="sticky-add-to-cart-bar" aria-hidden="true">
    <div class="container">
        <div class="sticky-add-to-cart-bar__inner d-flex flex-wrap align-items-center justify-content-between gap-3 py-2">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <img src="{{ $product->imageShow() }}" alt="" class="sticky-add-to-cart-bar__img rounded" width="50" height="50" loading="lazy" decoding="async">
                <div class="min-w-0">
                    <span class="sticky-add-to-cart-bar__name d-block text-truncate">{{ \Illuminate\Support\Str::limit(__($product->name), 40) }}</span>
                    <span class="sticky-add-to-cart-bar__price text--base fw-bold">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="cart-plus-minus cart-plus-minus--sticky">
                    <div class="cart-decrease qtybutton dec">@include($activeTemplate . 'partials.icon', ['name' => 'minus'])</div>
                    <input type="number" class="form-control productQuantity" name="quantity" value="1" min="1" max="{{ $product->has_variants ? 999 : $product->quantity }}">
                    <div class="cart-increase qtybutton inc">@include($activeTemplate . 'partials.icon', ['name' => 'plus'])</div>
                </div>
                <a href="#0" class="btn btn--base add-to-cart sticky-add-to-cart-btn" data-product_id="{{ $product->id }}">@lang('Add To Cart')</a>
            </div>
        </div>
    </div>
</div>

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush

@push('script')
<script>
(function(){
    var bar = document.getElementById('stickyAddToCartBar');
    if (!bar) return;
    var mainCart = document.querySelector('.single-add-cart-area');
    if (!mainCart) return;
    var sentinel = mainCart.getBoundingClientRect().top + window.pageYOffset;
    function update() {
        if (window.pageYOffset + window.innerHeight > sentinel + 150) {
            bar.classList.add('is-visible');
            bar.setAttribute('aria-hidden', 'false');
        } else {
            bar.classList.remove('is-visible');
            bar.setAttribute('aria-hidden', 'true');
        }
    }
    window.addEventListener('scroll', function(){ requestAnimationFrame(update); }, { passive: true });
    update();
    var qtyInput = bar.querySelector('.productQuantity');
    var dec = bar.querySelector('.cart-decrease');
    var inc = bar.querySelector('.cart-increase');
    if (dec) dec.addEventListener('click', function(){ var v = parseInt(qtyInput.value, 10) || 1; if (v > 1) qtyInput.value = v - 1; });
    if (inc) inc.addEventListener('click', function(){ var v = parseInt(qtyInput.value, 10) || 1; var max = parseInt(qtyInput.getAttribute('max'), 10) || 999; if (v < max) qtyInput.value = v + 1; });
    bar.querySelector('.sticky-add-to-cart-btn').addEventListener('click', function(e){
        e.preventDefault();
        var mainQty = document.querySelector('.single-add-cart-area .productQuantity');
        if (mainQty) mainQty.value = qtyInput.value;
        var mainBtn = document.getElementById('addToCartBtn');
        if (mainBtn) mainBtn.click();
    });
})();
</script>
@endpush
