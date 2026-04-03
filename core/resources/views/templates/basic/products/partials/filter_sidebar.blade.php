{{-- একই প্রোফেশনাল ফিল্টার সাইডবার সব প্রোডাক্ট লিস্টিং পেজে: category, brand, subcategory, all/products, search, featured, hot-deal, best-selling, today-deal, discount ইত্যাদি --}}
@php
    $data = $data ?? [];
    $data['categoryList'] = $data['categoryList'] ?? collect();
    $data['brands'] = $data['brands'] ?? collect();
    $subcategoryList = $subcategoryList ?? collect();
    $sizeList = $sizeList ?? collect();
    $colorList = $colorList ?? collect();
    $priceRanges = $priceRanges ?? [];
    $minP = $minP ?? 0;
    $maxP = $maxP ?? 0;
@endphp
<aside class="filter--sidebar product-filter-panel product-filter-panel--pro" id="productsFilterSidebar">
    <div class="close--sidebar d-lg-none" aria-label="@lang('Close')">@include($activeTemplate . 'partials.icon', ['name' => 'times'])</div>
    <div class="filter--sidebar-inner product-filter--pro">
        <div class="product-filter-header product-filter-header--pro">
            @include($activeTemplate . 'partials.icon', ['name' => 'filter'])
            <span>@lang('Filters')</span>
            <span class="product-filter-badge" id="filterActiveCount" aria-hidden="true"></span>
        </div>

        <div class="filter-widget filter-widget--pro">
            <h6 class="filter-widget__title">@include($activeTemplate . 'partials.icon', ['name' => 'search']) @lang('Search Products')</h6>
            <div class="filter-widget__body">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">@include($activeTemplate . 'partials.icon', ['name' => 'search'])</span>
                    <input type="text" class="form-control" id="filterProductSearch" name="search" placeholder="@lang('Search in category...')" value="{{ request()->search }}" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="filter-widget filter-widget--pro">
            <h6 class="filter-widget__title filter-widget__title--row">
                <span>@include($activeTemplate . 'partials.icon', ['name' => 'dollar-sign']) @lang('Price Range')</span>
                <a href="#" class="filter-widget__reset" id="filterPriceReset">@lang('Reset')</a>
            </h6>
            <div class="filter-widget__body">
                <div class="filter-price-radios">
                    @foreach ($priceRanges as $idx => $range)
                        <div class="form-check form-check-inline-filter">
                            <input class="form-check-input filter-price-radio" type="radio" name="price_range" id="priceRange{{ $idx }}" data-min="{{ $range['min'] }}" data-max="{{ $range['max'] }}">
                            <label class="form-check-label" for="priceRange{{ $idx }}">{{ $range['label'] }}</label>
                        </div>
                    @endforeach
                </div>
                <div class="filter-price-inputs mt-2">
                    <div class="product-filter-price-row">
                        <div class="product-filter-price-group">
                            <label class="product-filter-price-label" for="priceMinInput">@lang('Min')</label>
                            <input type="number" id="priceMinInput" class="form-control form-control-sm product-filter-price-input product-filter-price-input--no-spin" min="{{ $minP }}" max="{{ $maxP }}" step="1" value="{{ getAmount($minP) }}">
                        </div>
                        <div class="product-filter-price-group">
                            <label class="product-filter-price-label" for="priceMaxInput">@lang('Max')</label>
                            <input type="number" id="priceMaxInput" class="form-control form-control-sm product-filter-price-input product-filter-price-input--no-spin" min="{{ $minP }}" max="{{ $maxP }}" step="1" value="{{ getAmount($maxP) }}">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="min_price" value="{{ getAmount($minP) }}">
                <input type="hidden" name="max_price" value="{{ getAmount($maxP) }}">
            </div>
        </div>

        <div class="filter-widget filter-widget--pro">
            <h6 class="filter-widget__title">@include($activeTemplate . 'partials.icon', ['name' => 'th-large']) @lang('Category')</h6>
            <div class="filter-widget__body filter-widget__body--scroll">
                <div class="form-check form--check-compact">
                    <input class="form-check-input sortCategory" name="category" type="checkbox" id="cate-0" value="" {{ isset($currentCategoryId) ? '' : 'checked' }}>
                    <label class="form-check-label" for="cate-0">@lang('All')</label>
                </div>
                @foreach ($data['categoryList'] as $category)
                    <div class="form-check form--check-compact product-filter-category-item" data-name="{{ strtolower(__($category->name)) }}">
                        <input class="form-check-input sortCategory" type="checkbox" name="category" id="cate{{ $category->id }}" value="{{ $category->id }}" {{ (isset($currentCategoryId) && (int)$currentCategoryId === (int)$category->id) ? 'checked' : '' }}>
                        <label class="form-check-label" for="cate{{ $category->id }}">{{ __($category->name) }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        @if($subcategoryList->isNotEmpty())
        <div class="filter-widget filter-widget--pro">
            <h6 class="filter-widget__title filter-widget__title--row">
                <span>@include($activeTemplate . 'partials.icon', ['name' => 'layer-group']) @lang('Subcategory')</span>
                <a href="#" class="filter-widget__clear" data-clear="subcategory">@lang('Clear')</a>
            </h6>
            <div class="filter-widget__body filter-widget__body--scroll">
                @foreach ($subcategoryList as $sub)
                    <div class="form-check form--check-compact">
                        <input class="form-check-input sortSubcategory" type="checkbox" name="subcategory" id="sub{{ $sub->id }}" value="{{ $sub->id }}">
                        <label class="form-check-label" for="sub{{ $sub->id }}">{{ __($sub->name) }} ({{ $sub->products_count ?? 0 }})</label>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="filter-widget filter-widget--pro">
            <h6 class="filter-widget__title filter-widget__title--row">
                <span>@include($activeTemplate . 'partials.icon', ['name' => 'tags']) @lang('Brands')</span>
                <a href="#" class="filter-widget__clear" data-clear="brand">@lang('Clear')</a>
            </h6>
            <div class="filter-widget__body filter-widget__body--scroll">
                <div class="form-check form--check-compact">
                    <input class="form-check-input sortBrand" name="brand" type="checkbox" id="brand0" value="" checked>
                    <label class="form-check-label" for="brand0">@lang('All')</label>
                </div>
                @foreach ($data['brands'] as $brand)
                    <div class="form-check form--check-compact product-filter-brand-item" data-name="{{ strtolower(__($brand->name)) }}">
                        <input class="form-check-input sortBrand" name="brand" type="checkbox" id="brand{{ $brand->id }}" value="{{ $brand->id }}">
                        <label class="form-check-label" for="brand{{ $brand->id }}">{{ __($brand->name) }} ({{ $brand->products_count ?? 0 }})</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="filter-widget filter-widget--pro">
            <h6 class="filter-widget__title filter-widget__title--row">
                <span>@include($activeTemplate . 'partials.icon', ['name' => 'percent']) @lang('Special Offers')</span>
                <a href="#" class="filter-widget__clear filter-widget__clear--red" data-clear="discount">@lang('Clear')</a>
            </h6>
            <div class="filter-widget__body">
                <div class="form-check form--check-compact">
                    <input class="form-check-input filterDiscountOffer" type="checkbox" name="discount_offer" id="discount50" value="50+">
                    <label class="form-check-label" for="discount50">50% @lang('or more')</label>
                </div>
                <div class="form-check form--check-compact">
                    <input class="form-check-input filterDiscountOffer" type="checkbox" name="discount_offer" id="discount30" value="30-50">
                    <label class="form-check-label" for="discount30">30% - 50%</label>
                </div>
                <div class="form-check form--check-compact">
                    <input class="form-check-input filterDiscountOffer" type="checkbox" name="discount_offer" id="discount1" value="1-30">
                    <label class="form-check-label" for="discount1">1% - 30%</label>
                </div>
            </div>
        </div>

        @if($sizeList->isNotEmpty())
        <div class="filter-widget filter-widget--pro">
            <h6 class="filter-widget__title filter-widget__title--row">
                <span>@include($activeTemplate . 'partials.icon', ['name' => 'ruler-combined']) @lang('Size')</span>
                <a href="#" class="filter-widget__clear" data-clear="size">@lang('Clear')</a>
            </h6>
            <div class="filter-widget__body">
                @foreach ($sizeList as $size)
                    <div class="form-check form--check-compact">
                        <input class="form-check-input filterSize" type="checkbox" name="size" id="size{{ md5($size) }}" value="{{ $size }}">
                        <label class="form-check-label" for="size{{ md5($size) }}">{{ $size }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($colorList->isNotEmpty())
        <div class="filter-widget filter-widget--pro">
            <h6 class="filter-widget__title filter-widget__title--row">
                <span>@include($activeTemplate . 'partials.icon', ['name' => 'palette']) @lang('Color')</span>
                <a href="#" class="filter-widget__clear" data-clear="color">@lang('Clear')</a>
            </h6>
            <div class="filter-widget__body">
                @foreach ($colorList as $color)
                    <div class="form-check form--check-compact">
                        <input class="form-check-input filterColor" type="checkbox" name="color" id="color{{ md5($color) }}" value="{{ $color }}">
                        <label class="form-check-label" for="color{{ md5($color) }}">{{ $color }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="product-filter-actions product-filter-actions--pro">
            <button type="button" class="btn btn-sm btn--primary product-filter-apply-btn" id="productFilterApply">@lang('Apply')</button>
            <button type="button" class="btn btn-sm btn-outline-secondary product-filter-clear" id="productFilterClear">@lang('Clear all')</button>
        </div>
    </div>
</aside>
