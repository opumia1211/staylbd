@props(['product', 'general', 'activeTemplate'])

@php
    $price = productPrice($product);
    $saveAmount = $product->price - $price;
    $savePercent = $product->price > 0 ? (int) round(($saveAmount / $product->price) * 100) : 0;
    $hasDiscount = ($product->discount != 0 || $product->today_deals == 1) && $saveAmount > 0;
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(14));
    
    $primaryImg = getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product'));
    $hoverImg = null;
    if (is_array($product->gallery ?? null) && count($product->gallery) > 0) {
        $hoverImg = getImageWebP(getFilePath('productGallery') . '/' . $product->gallery[0], getFileSize('productGallery'));
    }
    
    $qty = $product->has_variants && $product->activeVariants->isNotEmpty()
        ? (int) $product->activeVariants->sum('quantity')
        : (int) ($product->quantity ?? 0);
    $canPurchase = $qty > 0;
    $displayName = __($product->name);

    $rating = 0;
    if ($product->reviews_count > 0) {
        $rating = $product->reviews_sum_rating / $product->reviews_count;
    }
@endphp

<div class="group relative bg-white overflow-hidden rounded-xl border border-gray-100 flex flex-col h-full transition-shadow duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
    <!-- Image Wrapper -->
    <div class="relative bg-[#f5f6f8] aspect-[4/5] flex-shrink-0 overflow-hidden">
        <!-- Badges -->
        <div class="absolute top-4 left-4 z-20 flex flex-col gap-2 items-start">
            @if($hasDiscount)
                <span class="bg-[#e24a4a] text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-sm">- {{ $savePercent }}%</span>
            @endif
            @if($isNew)
                <span class="bg-[#1266e3] text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-sm">New</span>
            @endif
        </div>

        <!-- Float Actions (Top Right) -->
        <div class="absolute right-4 top-4 z-20 flex flex-col gap-2 transform translate-x-12 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
            <!-- Add to Cart -->
            @if($canPurchase)
            <button type="button" class="add-to-cart size-9 rounded-full bg-white text-gray-800 shadow-md flex items-center justify-center hover:bg-zenis-primary hover:text-white transition-colors" data-product_id="{{ $product->id }}" data-qty="1" title="Add to Cart">
                <i class="fa-solid fa-cart-plus text-sm"></i>
            </button>
            @endif
            <!-- Compare -->
            <button type="button" class="size-9 rounded-full bg-white text-gray-800 shadow-md flex items-center justify-center hover:bg-zenis-primary hover:text-white transition-colors" title="Compare">
                <i class="fa-solid fa-right-left text-sm"></i>
            </button>
            <!-- Wishlist -->
            <button type="button" class="add-wishlist size-9 rounded-full bg-white text-gray-800 shadow-md flex items-center justify-center hover:bg-zenis-primary hover:text-white transition-colors" data-product_id="{{ $product->id }}" title="Wishlist">
                <i class="fa-regular fa-heart text-sm"></i>
            </button>
            <!-- Quick View -->
            <button type="button" class="quickView size-9 rounded-full bg-white text-gray-800 shadow-md flex items-center justify-center hover:bg-zenis-primary hover:text-white transition-colors" data-product_id="{{ $product->id }}" title="Quick View">
                <i class="fa-regular fa-eye text-sm"></i>
            </button>
        </div>

        <!-- Product Image -->
        <a href="{{ product_detail_url($product) }}" class="absolute inset-0 block w-full h-full">
            <img src="{{ $primaryImg }}" alt="{{ $displayName }}" class="w-full h-full object-cover object-top transition-all duration-700 group-hover:scale-110 {{ $hoverImg ? 'group-hover:opacity-0' : '' }}">
            @if($hoverImg)
                <img src="{{ $hoverImg }}" alt="{{ $displayName }}" class="absolute top-0 left-0 w-full h-full object-cover object-top opacity-0 group-hover:opacity-100 transition-all duration-700">
            @endif
        </a>
    </div>

    <!-- Content Wrapper -->
    <div class="p-4 flex flex-col flex-1 bg-white">
        <!-- Title -->
        <h3 class="text-[#333333] font-bold text-[15px] mb-2 leading-tight line-clamp-1 hover:text-zenis-primary transition-colors">
            <a href="{{ product_detail_url($product) }}" title="{{ $displayName }}">{{ $displayName }}</a>
        </h3>

        <!-- Price -->
        <div class="flex items-center gap-2 mb-2">
            <span class="text-[15px] font-bold text-[#fbaf00]">
                {{ $general->cur_sym }}{{ showAmount($price) }}
            </span>
            @if($hasDiscount)
                <span class="text-[13px] text-gray-500 line-through">
                    {{ $general->cur_sym }}{{ showAmount($product->price) }}
                </span>
            @endif
        </div>

        <!-- Rating -->
        <div class="flex items-center gap-1.5 mb-3">
            <div class="flex text-[#fbaf00] text-[13px]">
                @for($i=1; $i<=5; $i++)
                    @if($i <= $rating)
                        <i class="fa-solid fa-star"></i>
                    @else
                        <i class="fa-regular fa-star"></i>
                    @endif
                @endfor
            </div>
            <span class="text-[13px] text-gray-500">({{ $product->reviews_count ?? 0 }} Reviews)</span>
        </div>

        <!-- Color Swatches (Dummy for now, as it needs variant data) -->
        <div class="flex items-center gap-1.5 mt-auto">
            <div class="size-4 rounded-full bg-[#df4f4f] text-white flex items-center justify-center text-[10px]">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="size-4 rounded-full bg-[#5f9c34]"></div>
            <div class="size-4 rounded-full bg-[#1b5df9]"></div>
            <div class="size-4 rounded-full bg-[#fab206]"></div>
        </div>
    </div>
</div>
