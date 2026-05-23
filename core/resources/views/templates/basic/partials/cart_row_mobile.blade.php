@php
    if ($cart === null) return;
    $user     = auth()->user() ?? null;
    // Ensure product data is always used when available (same as desktop row)
    $product  = $cart->product ?? null;
    $productId = (int) ($cart->product_id ?? 0);
    if ($productId <= 0) return;
    if ($user && $product === null) return;
    $image    = $product ? ($product->image ?? '') : ($cart->image ?? '');
    $name     = $product ? ($product->name ?? '') : ($cart->name ?? '');
    if ($user && ($cart->variant_id ?? 0)) {
        $variant = \App\Models\ProductVariant::find($cart->variant_id);
        $price  = $variant ? showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1) : productPrice($product);
    } else {
        $price  = $user ? productPrice($product) : showDiscountPrice($cart->price ?? 0, $cart->discount ?? 0, $cart->discount_type ?? 1);
    }
    $qty = (int)($cart->quantity ?? 1);
    $subTotal = $price * $qty;
    $variantLabel = null;
    if (!empty($cart->variant_details)) {
        $vd = is_string($cart->variant_details) ? json_decode($cart->variant_details, true) : $cart->variant_details;
        if (is_array($vd)) {
            if (!empty($vd['custom_size'])) {
                $variantLabel = __('Custom Size') . ': ' . $vd['custom_size'];
            } elseif (!empty($vd['size'])) {
                $variantLabel = ($vd['size'] === 'NO_SIZE') ? __('No size') : $vd['size'];
            } else {
                $variantLabel = implode(', ', array_map(fn($k, $v) => $k . ': ' . $v, array_keys($vd), $vd));
            }
        }
    }
    $cartId = $user && isset($cart->id) ? (int) $cart->id : null;
    $stockQty = $product ? ($product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->sum('quantity') : ($product->quantity ?? 0)) : 0;
    $hasProduct = (bool) $product;
    $sku = $hasProduct ? (optional($product)->product_sku ?? '—') : '—';
    $categoryName = $hasProduct && $product->category ? __($product->category->name ?? '—') : '—';
    $brandName = $hasProduct && $product->brand ? __($product->brand->name ?? '—') : '—';
    $discountLabel = null;
    if (($cart->variant_id ?? 0) && isset($variant)) {
        $d = $variant->discount ?? 0;
        if ($d > 0) {
            $discountLabel = ($variant->discount_type ?? 1) == 1
                ? (int) $d . '%'
                : $general->cur_sym . getAmount($d);
        }
    } elseif ($hasProduct && ($product->discount ?? 0) > 0) {
        $d = $product->discount;
        $discountLabel = ($product->discount_type ?? 1) == 1
            ? (int) $d . '%'
            : $general->cur_sym . getAmount($d);
    }
    $inStock = true;
    if ($hasProduct) {
        if (($cart->variant_id ?? 0) && isset($variant)) {
            $inStock = (int)($variant->quantity ?? 0) >= $qty;
        } else {
            $inStock = $stockQty >= $qty;
        }
    }
    $reviewCount = $hasProduct ? ($product->reviews_count ?? $product->reviews->count() ?? 0) : 0;
    $avgRate = $hasProduct ? ($product->avg_rate ?? 0) : 0;
    $purl = $product ? product_detail_url($product) : product_detail_url_for_id($productId, $name);
@endphp
@php $variantDetailsRaw = isset($cart->variant_details) ? (is_string($cart->variant_details) ? $cart->variant_details : json_encode($cart->variant_details)) : ''; @endphp
@php
    $maxQty = $hasProduct
        ? (($cart->variant_id ?? 0) && isset($variant) ? (int) ($variant->quantity ?? 0) : (int) $stockQty)
        : 0;
@endphp
<div class="cart-row-mobile product-list-row-mobile border rounded bg-white" data-cart-id="{{ $cartId }}" data-row-subtotal="{{ getAmount($subTotal) }}" data-variant-details="{{ e($variantDetailsRaw) }}" @if($maxQty > 0) data-max-qty="{{ $maxQty }}" @endif>
    {{-- প্রতিটি প্রোডাক্টে আলাদা টিক চিহ্ন – মোবাইল/ট্যাব/ছোট ডিভাইসে সবসময় দেখা যাবে --}}
    <div class="cart-row-mobile__mark cart-row-mobile__mark--per-item d-flex align-items-center gap-2">
        <label class="cart-row-mobile__mark-label d-flex align-items-center gap-2 mb-0 cursor-pointer flex-grow-1">
            <input type="checkbox"
                   class="form-check-input cart-select-item cart-row-mobile__mark-input"
                   name="cart_ids[]"
                   value="{{ $cartId ?? ('item-'.$productId.'-'.($cart->variant_id ?? 0)) }}"
                   data-row-subtotal="{{ getAmount($subTotal) }}"
                   @checked(true)
                   aria-label="@lang('Select this product')">
            <span class="cart-row-mobile__mark-text small fw-medium text-dark">@lang('Select this product')</span>
        </label>
    </div>
    <div class="d-flex gap-2 cart-row-mobile__body">
        <div class="cart-row-mobile__media position-relative flex-shrink-0">
            <a href="{{ $purl }}" class="product-list-row__img-link cart-row-mobile__img rounded overflow-hidden bg-light d-block" data-no-ajax>
                <img src="{{ getImage(getFilePath('product') . '/' . $image, getFileSize('product')) }}" alt="{{ __($name) }}" class="w-100 h-100 object-fit-cover" loading="lazy" width="100" height="100">
            </a>
        </div>
        <div class="flex-grow-1 min-w-0 overflow-hidden">
            <a href="{{ $purl }}" class="productName product-list-row__name cart-row-mobile__name text-decoration-none d-block fw-semibold text-dark" data-product_id="{{ $productId }}" data-variant_id="{{ $cart->variant_id ?? '' }}">{{ __($name ?: 'Product') }}</a>
            @if($hasProduct)
            <div class="product-list-row__meta cart-row-mobile__meta mt-1">
                <span>@lang('SKU'): {{ $sku }}</span>
                <span>@lang('Category'): {{ $categoryName }}</span>
                <span>@lang('Brand'): {{ $brandName }}</span>
                @if(in_array($product->product_type, ['digital', 'service']))
                    <span class="text-primary"><i class="las la-download"></i> {{ ucfirst($product->product_type) }}</span>
                @endif
                @if($product->shipping_weight > 0)
                    <span><i class="las la-weight"></i> {{ $product->shipping_weight }} kg</span>
                @endif
            </div>
            <div class="cart-row-mobile__stock-rating mt-1 d-flex align-items-center flex-wrap gap-2">
                <span class="badge {{ $inStock ? 'bg-success' : 'bg-secondary' }}">{{ $inStock ? __('In Stock') : __('Out of Stock') }}</span>
                @if($discountLabel)
                    <span class="badge bg-warning text-dark">@lang('Best Value') • {{ $discountLabel }}</span>
                @endif
                <span class="small text-muted cart-row-mobile__stock-qty">{{ $stockQty }} @lang('in stock')</span>
                <span class="cart-row-mobile__price fw-bold text-success">{{ $general->cur_sym }}{{ getAmount($price) }}</span>
                <span class="ratings d-inline-block">{!! showProductRatings($avgRate) !!}</span>
                <span class="small text-muted">({{ $reviewCount }})</span>
            </div>
            @else
            <div class="product-list-row__meta cart-row-mobile__meta mt-1">
                @if($variantLabel)
                    <span>@lang('Size'): {{ $variantLabel }}</span>
                @endif
            </div>
            <div class="cart-row-mobile__prices mt-1 d-flex align-items-center justify-content-between flex-wrap gap-1">
                <span class="price cart-row-mobile__unit-price">{{ $general->cur_sym }}{{ getAmount($price) }} <span class="cart-row-mobile__unit-label small text-muted">@lang('each')</span></span>
                <span class="subtotal cart-row-mobile__total fw-bold text--base">{{ $general->cur_sym }}{{ getAmount($subTotal) }}</span>
            </div>
            @endif
        </div>
    </div>
    <div class="cart-row-mobile__qty-subtotal mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted">@lang('Qty'):</span>
            <div class="cart-qty-control">
                <button type="button" class="qty-btn cart-decrease" aria-label="@lang('Decrease')">@include($activeTemplate . 'partials.icon', ['name' => 'minus'])</button>
                <input type="number" class="cart-quantity-input" name="quantity" value="{{ $qty }}" min="1" aria-label="@lang('Quantity')">
                <button type="button" class="qty-btn cart-increase" aria-label="@lang('Increase')">@include($activeTemplate . 'partials.icon', ['name' => 'plus'])</button>
            </div>
        </div>
        <div class="cart-row-mobile__subtotal-row">
            <span class="small text-muted">@lang('Subtotal'):</span>
            <span class="subtotal fw-bold ms-1">{{ $general->cur_sym }}{{ getAmount($subTotal) }}</span>
        </div>
    </div>
    <div class="cart-row-mobile__actions mt-2 pt-2 border-top d-flex flex-wrap align-items-center gap-2">
        <a href="{{ $purl }}"
           class="btn btn-primary btn-sm list-page-action-btn product-list-row__btn product-list-row__btn--view"
           data-no-ajax>@lang('View')</a>
        <a href="{{ route('cart.list.buy.now', $productId) }}"
           class="btn btn-success btn-sm list-page-action-btn product-list-row__btn product-list-row__btn--buy"
           data-no-ajax>
            @if($hasProduct && $inStock)
                @lang('Buy Now')
            @else
                @lang('Buy Now')
            @endif
        </a>
        <button type="button"
                class="btn btn-warning btn-sm list-page-action-btn product-list-row__btn product-list-row__btn--wishlist cart-move-wishlist"
                data-product_id="{{ $productId }}"
                title="@lang('Add to Wishlist')">@lang('Wishlist')</button>
        <button type="button"
                class="btn btn-danger btn-sm list-page-action-btn product-list-row__btn product-list-row__btn--remove remove-btn cart-remove-btn-user"
                title="@lang('Remove')"
                data-product_id="{{ $productId }}"
                data-variant_id="{{ $cart->variant_id ?? '' }}"
                data-variant_details="{{ e($variantDetailsRaw) }}">@lang('Remove')</button>
    </div>
</div>
