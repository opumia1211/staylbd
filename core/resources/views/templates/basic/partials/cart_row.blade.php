@php
    if ($cart === null) return;
    $user     = auth()->user() ?? null;
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
    $reviewCount = $product ? ($product->reviews_count ?? ($product->reviews->count() ?? 0)) : 0;
    $avgRate = $product ? ($product->avg_rate ?? 0) : 0;
    $purl = $product ? product_detail_url($product) : product_detail_url_for_id($productId, $name);
@endphp
@php
    $simpleCart = $simpleCart ?? false;
@endphp
@php $variantDetailsRaw = isset($cart->variant_details) ? (is_string($cart->variant_details) ? $cart->variant_details : json_encode($cart->variant_details)) : ''; @endphp
<tr class="cart-row product-list-row" data-cart-id="{{ $cartId }}" data-row-subtotal="{{ getAmount($subTotal) }}" data-variant-details="{{ e($variantDetailsRaw) }}">
    <td class="cart-page__td cart-page__td--check align-middle">
        <label class="cart-page__check-label mb-0"><input type="checkbox" class="form-check-input cart-select-item" @if($cartId) name="cart_ids[]" value="{{ $cartId }}" @endif data-row-subtotal="{{ getAmount($subTotal) }}" @checked(true) aria-label="@lang('Include in order')"></label>
    </td>
    <td class="cart-page__td cart-page__td--img align-middle">
        <a href="{{ $purl }}" class="product-list-row__img-link d-block" data-no-ajax>
            <img src="{{ getImage(getFilePath('product') . '/' . $image, getFileSize('product')) }}" alt="{{ __($name) }}" loading="lazy" width="28" height="28">
        </a>
    </td>
    <td class="cart-page__td cart-page__td--product align-middle">
        <div class="cart-product-cell">
            <a href="{{ $purl }}" class="productName product-list-row__name text-decoration-none" data-product_id="{{ $productId }}" data-variant_id="{{ $cart->variant_id ?? '' }}" title="{{ __($name ?: 'Product') }}">{{ __($name ?: 'Product') }}</a>
            <div class="product-list-row__meta mt-1">
                @if($variantLabel)
                    <span class="me-2">@include($activeTemplate . 'partials.icon', ['name' => 'tag', 'class' => 'me-1']){{ $variantLabel }}</span>
                @endif
                @if($product && (!empty($product->product_sku) || !empty(optional($product->brand)->name) || $product->product_type || $product->shipping_weight))
                    <span class="product-list-row__meta-pill" title="@lang('Details')">
                        @if(!empty($product->product_sku))
                            <span class="me-1">@include($activeTemplate . 'partials.icon', ['name' => 'barcode', 'class' => 'me-1']){{ $product->product_sku }}</span>
                        @endif
                        @if(!empty(optional($product->brand)->name))
                            <span>@include($activeTemplate . 'partials.icon', ['name' => 'store', 'class' => 'me-1']){{ __($product->brand->name) }}</span>
                        @endif
                        @if(in_array($product->product_type, ['digital', 'service']))
                            <span class="ms-1 text-primary"><i class="las la-download me-1"></i>{{ ucfirst($product->product_type) }}</span>
                        @endif
                        @if($product->shipping_weight > 0)
                            <span class="ms-1 text-muted"><i class="las la-weight me-1"></i>{{ $product->shipping_weight }} kg</span>
                        @endif
                    </span>
                @endif
                @if($product && $reviewCount > 0)
                    <span class="product-list-row__meta-rating ms-2" title="@lang('Rating')">
                        {!! showProductRatings($avgRate) !!}<span class="small text-muted ms-1">({{ $reviewCount }})</span>
                    </span>
                @endif
            </div>
        </div>
    </td>
    <td class="cart-page__td cart-page__td--price text-end align-middle">
        <span class="price product-list-row__price-val">{{ $general->cur_sym }}{{ getAmount($price) }}</span>
    </td>
    <td class="cart-page__td cart-page__td--qty text-end align-middle">
        <div class="cart-qty-control">
            <button type="button" class="qty-btn cart-decrease" aria-label="@lang('Decrease')">@include($activeTemplate . 'partials.icon', ['name' => 'minus'])</button>
            <input type="number" class="cart-quantity-input" name="quantity" value="{{ $qty }}" min="1" aria-label="@lang('Quantity')">
            <button type="button" class="qty-btn cart-increase" aria-label="@lang('Increase')">@include($activeTemplate . 'partials.icon', ['name' => 'plus'])</button>
        </div>
    </td>
    <td class="cart-page__td cart-page__td--subtotal text-end align-middle"><span class="subtotal fw-bold">{{ $general->cur_sym }}{{ getAmount($subTotal) }}</span></td>
    <td class="cart-page__td cart-page__td--actions text-end align-middle">
        <div class="action-buttons product-list-row__action-btns d-flex flex-wrap gap-2 justify-content-end">
            <a href="{{ $purl }}" class="btn btn-primary list-page-action-btn" data-no-ajax>@lang('View')</a>
            <button type="button" class="btn btn-danger list-page-action-btn remove-btn" title="@lang('Remove')">@lang('Remove')</button>
        </div>
    </td>
</tr>
