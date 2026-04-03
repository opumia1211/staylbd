{{--
    Single product row – screenshot style: Image | Product (name, SKU, Category, Brand, In Stock) | Price | Rating | View, Buy Now, Cart, Remove
    Use: @include(..., ['product' => $product, 'price' => $price, 'showQuantity' => false, 'showRemove' => true, 'removeRoute' => '...', 'removeBtnClass' => '...'])
--}}
@php
    $product = $product ?? null;
    if (!$product) return;
    $price = $price ?? productPrice($product);
    $showQuantity = $showQuantity ?? false;
    $showSubtotal = $showSubtotal ?? false;
    $qty = $product->has_variants && $product->activeVariants->isNotEmpty() ? $product->activeVariants->sum('quantity') : ($product->quantity ?? 0);
    $inWishlist = in_array($product->id, $wishListProductIds ?? []);
@endphp
<tr class="product-list-row" data-product-id="{{ $product->id }}">
    <td class="product-list-row__img align-middle">
        <a href="{{ product_detail_url($product) }}" class="product-list-row__img-link" data-no-ajax>
            <img src="{{ $product->imageShow() }}" alt="{{ __($product->name) }}" loading="lazy" width="80" height="80">
        </a>
    </td>
    <td class="product-list-row__info align-middle">
        <a href="{{ product_detail_url($product) }}" class="product-list-row__name" data-no-ajax>{{ __($product->name) }}</a>
        <div class="product-list-row__meta">
            @if(!empty($product->product_sku))<span>@lang('SKU'): {{ $product->product_sku }}</span>@endif
            @if(!empty($product->category))<span>@lang('Category'): {{ __($product->category->name ?? '') }}</span>@endif
            @if(!empty($product->brand))<span>@lang('Brand'): {{ __($product->brand->name ?? '') }}</span>@endif
            <span class="product-list-row__stock badge {{ $qty > 0 ? 'bg-success' : 'bg-danger' }}">{{ $qty > 0 ? __('In Stock') : __('Out of Stock') }}</span>
        </div>
    </td>
    <td class="product-list-row__price align-middle text-end">
        <span class="product-list-row__price-val">{{ $general->cur_sym }}{{ showAmount($price) }}</span>
    </td>
    <td class="product-list-row__rating align-middle text-center">
        <span class="ratings d-inline-block">{!! showProductRatings($product->avg_rate ?? 0) !!}</span>
        <span class="product-list-row__review-count">({{ $product->reviews_count ?? ($product->reviews->count() ?? 0) }})</span>
    </td>
    <td class="product-list-row__actions align-middle text-end">
        <div class="product-list-row__action-btns">
            <a href="{{ product_detail_url($product) }}" class="product-list-row__btn product-list-row__btn--view" title="@lang('View')" data-no-ajax>@include($activeTemplate . 'partials.icon', ['name' => 'external-link-alt']) @lang('View')</a>
            <a href="{{ route('cart.list.buy.now', $product->id) }}" class="product-list-row__btn product-list-row__btn--buy" title="@lang('Buy Now')" data-no-ajax>@include($activeTemplate . 'partials.icon', ['name' => 'bolt']) @lang('Buy Now')</a>
            <button type="button" class="product-list-row__btn product-list-row__btn--cart add-to-cart" data-product_id="{{ $product->id }}" title="@lang('Add to Cart')" aria-label="@lang('Add to Cart')">@include($activeTemplate . 'partials.icon', ['name' => 'cart-plus']) @lang('Cart')</button>
            @if($showRemove ?? true)
                <button type="button" class="product-list-row__btn product-list-row__btn--remove {{ $removeBtnClass ?? '' }}" data-product_id="{{ $product->id }}" title="@lang('Remove')" aria-label="@lang('Remove')">@include($activeTemplate . 'partials.icon', ['name' => 'trash-alt']) @lang('Remove')</button>
            @endif
        </div>
    </td>
</tr>
