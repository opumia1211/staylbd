@php
    if ($cart === null) return;
    $user     = auth()->user();
    $product  = $cart->product ?? null;
    $productId = (int) ($cart->product_id ?? 0);
    if ($productId <= 0 || !$product) return;
    $image    = $product->image ?? '';
    $name     = $product->name ?? '';
    $variant  = null;
    if (($cart->variant_id ?? 0)) {
        $variant = \App\Models\ProductVariant::find($cart->variant_id);
        $price  = $variant ? showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1) : productPrice($product);
    } else {
        $price  = productPrice($product);
    }
    $qty = (int)($cart->quantity ?? 1);
    $subTotal = $price * $qty;
    $cartId = $cart->id ?? null;
    if ($cartId !== null && is_numeric($cartId)) $cartId = (int) $cartId;
    $stockQty = $product->has_variants && $product->activeVariants->isNotEmpty() ? (int) $product->activeVariants->sum('quantity') : (int)($product->quantity ?? 0);
    if ($cart->variant_id && $variant) {
        $availableForOrder = (int) ($variant->quantity ?? 0);
        $inStock = $availableForOrder >= $qty;
    } else {
        $availableForOrder = (int) ($product->quantity ?? 0);
        $inStock = $availableForOrder >= $qty;
    }
    $reviewCount = $product->reviews_count ?? $product->reviews->count() ?? 0;
    $avgRate = $product->avg_rate ?? 0;
    $sku = $product->product_sku ?? '—';
    $categoryName = $product->category && $product->category->name ? $product->category->name : '—';
    $brandName = $product->brand && $product->brand->name ? $product->brand->name : '—';
    $discountLabel = null;
    if (($cart->variant_id ?? 0) && isset($variant)) {
        $d = $variant->discount ?? 0;
        if ($d > 0) $discountLabel = ($variant->discount_type ?? 1) == 1 ? (int)$d . '%' : $general->cur_sym . getAmount($d);
    } elseif (($product->discount ?? 0) > 0) {
        $d = $product->discount;
        $discountLabel = ($product->discount_type ?? 1) == 1 ? (int)$d . '%' : $general->cur_sym . getAmount($d);
    }
@endphp
@php $variantDetailsRaw = isset($cart->variant_details) ? (is_string($cart->variant_details) ? $cart->variant_details : json_encode($cart->variant_details)) : ''; @endphp
<tr class="cart-row cart-row--user align-middle" data-cart-id="{{ $cartId }}" data-row-subtotal="{{ getAmount($subTotal) }}" data-variant-details="{{ e($variantDetailsRaw) }}" data-max-qty="{{ $availableForOrder }}">
    <td class="cart-row-user__check align-middle">
        <label class="mb-0"><input type="checkbox" class="form-check-input cart-select-item" name="cart_ids[]" value="{{ $cartId }}" data-row-subtotal="{{ getAmount($subTotal) }}" checked aria-label="@lang('Include in order')"></label>
    </td>
    <td class="cart-row-user__img align-middle">
        <a href="{{ product_detail_url($product) }}" class="cart-row-user__img-link d-block bg-light rounded overflow-hidden" data-no-ajax>
            <img src="{{ getImage(getFilePath('product') . '/' . $image, getFileSize('product')) }}" alt="{{ __($name) }}" loading="lazy" width="42" height="42" class="cart-row-user__thumb" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <span class="cart-row-user__img-fallback" style="display:none;width:42px;height:42px;align-items:center;justify-content:center;font-size:10px;color:#94a3b8;background:#f1f5f9;">—</span>
        </a>
    </td>
    <td class="cart-row-user__name align-middle">
        <a href="{{ product_detail_url($product) }}" class="productName text-primary text-decoration-none fw-semibold cart-row-user__name-link" data-product_id="{{ $productId }}" data-variant_id="{{ $cart->variant_id ?? '' }}">{{ __($name) }}</a>
        @if(!empty($cart->variant_details))
        @php
            $vd = is_string($cart->variant_details) ? json_decode($cart->variant_details, true) : $cart->variant_details;
            $vdParts = is_array($vd) ? array_filter(array_map(function($v) { return is_string($v) ? $v : (is_numeric($v) ? (string)$v : null); }, array_values($vd))) : [];
        @endphp
        @if(!empty($vdParts))
        <span class="d-block small text-muted mt-0 cart-row-user__variant" style="font-size:0.65rem;">{{ implode(' · ', $vdParts) }}</span>
        @endif
        @endif
    </td>
    <td class="cart-row-user__sku align-middle small text-muted">{{ $sku }}</td>
    <td class="cart-row-user__category align-middle small">{{ __($categoryName) }}</td>
    <td class="cart-row-user__brand align-middle small">{{ __($brandName) }}</td>
    <td class="cart-row-user__stock align-middle">
        @if($inStock)
        <span class="badge bg-success">@lang('In Stock')</span><span class="small text-muted"> ({{ $stockQty }})</span>
        @else
        <span class="badge bg-secondary">@lang('Out of Stock')</span>
        @endif
    </td>
    <td class="cart-row-user__discount align-middle">
        @if($discountLabel)
        <span class="badge bg-warning text-dark">{{ $discountLabel }} @lang('Off')</span>
        @else
        <span class="text-muted">—</span>
        @endif
    </td>
    <td class="cart-row-user__price align-middle">
        <span class="price fw-medium">{{ $general->cur_sym }}{{ getAmount($price) }}</span>
    </td>
    <td class="cart-row-user__rating align-middle text-center">
        <span class="ratings d-inline-block">{!! showProductRatings($avgRate) !!}</span>
        <span class="small text-muted">({{ $reviewCount }})</span>
    </td>
    <td class="cart-row-user__qty align-middle">
        <div class="cart-qty-control d-inline-flex align-items-center border rounded">
            <button type="button" class="qty-btn cart-decrease border-0 bg-light px-1" aria-label="@lang('Decrease')"><span style="font-size:10px;">@include($activeTemplate . 'partials.icon', ['name' => 'minus'])</span></button>
            <input type="number" class="cart-quantity-input border-0 text-center" name="quantity" value="{{ $qty }}" min="1" aria-label="@lang('Quantity')">
            <button type="button" class="qty-btn cart-increase border-0 bg-light px-1" aria-label="@lang('Increase')"><span style="font-size:10px;">@include($activeTemplate . 'partials.icon', ['name' => 'plus'])</span></button>
        </div>
    </td>
    <td class="cart-row-user__subtotal align-middle fw-bold">
        <span class="subtotal">{{ $general->cur_sym }}{{ getAmount($subTotal) }}</span>
    </td>
    <td class="cart-row-user__action align-middle">
        <div class="action-buttons cart-row-user__action-btns d-flex flex-nowrap gap-2 justify-content-end">
            <a href="{{ product_detail_url($product) }}" class="btn btn-primary list-page-action-btn cart-action-btn" title="@lang('View')" data-no-ajax>@lang('View')</a>
            @if($inStock)
            <a href="{{ route('cart.list.buy.now', $productId) }}" class="btn btn-success list-page-action-btn cart-action-btn" title="@lang('Buy Now')" data-no-ajax>@lang('Buy Now')</a>
            @else
            <span class="btn btn-secondary list-page-action-btn disabled" title="@lang('Stock Out')">@lang('Stock Out')</span>
            @endif
            <button type="button" class="btn btn-warning list-page-action-btn cart-move-wishlist" data-product_id="{{ $productId }}" title="@lang('Add to Wishlist')">@lang('Add to Wishlist')</button>
            <button type="button" class="btn btn-danger list-page-action-btn remove-btn cart-remove-btn-user" title="@lang('Remove')" data-product_id="{{ $productId }}" data-variant_id="{{ $cart->variant_id ?? '' }}" data-variant_details="{{ e($variantDetailsRaw) }}">@lang('Remove')</button>
        </div>
    </td>
</tr>
