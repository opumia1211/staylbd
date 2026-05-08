@props(['product', 'general', 'activeTemplate'])

@php
    $price = productPrice($product);
    $saveAmount = $product->price - $price;
    $savePercent = $product->price > 0 ? (int) round(($saveAmount / $product->price) * 100) : 0;
    $hasDiscount = ($product->discount != 0 || $product->today_deals == 1) && $saveAmount > 0;
    
    $primaryImg = getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product'));
    $qty = $product->has_variants && $product->activeVariants->isNotEmpty()
        ? (int) $product->activeVariants->sum('quantity')
        : (int) ($product->quantity ?? 0);
    $canPurchase = $qty > 0;
    $displayName = __($product->name);

    $rating = 0;
    if (($product->reviews_count ?? 0) > 0) {
        $rating = ($product->reviews_sum_rating ?? 0) / $product->reviews_count;
    }
    
    $lowStockMax = (int) config('product_upload.low_stock_max', 20);
    $stockTier = $qty > $lowStockMax ? 'in' : ($qty >= 1 ? 'low' : 'out');
    $stockLabel = $stockTier === 'in' ? __('In Stock') : ($stockTier === 'low' ? __('Low Stock') : __('Out of Stock'));
@endphp

{{-- Elite Premium Product Card (Ryans/Compact Style) --}}
<div class="stayl-product-card group bg-white dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 relative flex flex-col h-full overflow-hidden" data-product-id="{{ $product->id }}">
    
    {{-- Discount Badge (Top Left) --}}
    @if($hasDiscount)
        <div class="stayl-card-badge absolute top-3 left-3 z-10 pointer-events-none">
            <span class="stayl-badge-promo bg-red-500 text-white text-[11px] font-bold px-1.5 py-0.5 rounded-sm uppercase tracking-wide">
                -{{ $savePercent }}%
            </span>
        </div>
    @endif

    {{-- Hover Actions (Top Right) --}}
    <div class="stayl-card-actions absolute top-3 right-3 flex flex-col gap-1.5 z-10 opacity-0 translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0">
        <button type="button" class="w-8 h-8 rounded-full bg-white/90 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center transition-all duration-300 hover:bg-[var(--stayl-color-primary)] hover:border-[var(--stayl-color-primary)] hover:text-white quickView" data-product_id="{{ $product->id }}" title="{{ __('Quick View') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
        <button type="button" class="w-8 h-8 rounded-full bg-white/90 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center transition-all duration-300 hover:bg-[var(--stayl-color-primary)] hover:border-[var(--stayl-color-primary)] hover:text-white add-wishlist" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </button>
    </div>

    {{-- Product Image --}}
    <div class="stayl-card-media relative p-4 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden aspect-square border-b border-slate-100 dark:border-slate-800">
        <a href="{{ product_detail_url($product) }}" class="d-block w-100 h-100 flex-center">
            <img src="{{ $primaryImg }}" alt="{{ $displayName }}" class="stayl-card-img w-full h-full object-contain transition-transform duration-500 group-hover:scale-110" loading="lazy">
        </a>
    </div>

    {{-- Product Information --}}
    <div class="stayl-card-content p-4 flex flex-col grow text-left">
        <span class="text-[11px] text-slate-500 dark:text-slate-400 mb-1.5 uppercase font-semibold tracking-wide">{{ $product->category->name ?? __('General') }}</span>

        <h3 class="stayl-card-title text-[14px] font-semibold leading-tight mb-2.5 text-slate-800 dark:text-slate-100 line-clamp-2 h-[40px]">
            <a href="{{ product_detail_url($product) }}" class="transition-colors duration-200 hover:text-[var(--stayl-color-primary)]">{{ $displayName }}</a>
        </h3>

        {{-- Micro-data list (Ryans style bullet points / compact data) --}}
        <div class="stayl-card-micro-data flex items-center gap-3 text-[11px] font-medium mb-3 flex-wrap">
            <span class="stayl-micro-item flex items-center gap-1 {{ $stockTier === 'in' ? 'text-green-600' : ($stockTier === 'low' ? 'text-yellow-600' : 'text-red-600') }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                {{ $stockLabel }}
            </span>
            @if($rating > 0)
            <span class="stayl-micro-item flex items-center gap-1 text-yellow-500">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                {{ number_format($rating, 1) }}
            </span>
            @endif
        </div>

        {{-- Pricing --}}
        <div class="stayl-card-pricing flex items-end justify-between mt-auto pt-2.5 border-t border-slate-200 dark:border-slate-800 border-dotted">
            <div class="stayl-price-stack flex flex-col leading-tight">
                @if($hasDiscount)
                    <span class="stayl-price-old text-[11px] text-slate-400 line-through mb-0.5 staylbd-rt-price-compare notranslate" data-base-price="{{ $product->price }}">
                        {{ currency_symbol() }}{{ showAmount($product->price) }}
                    </span>
                @else
                    <span class="stayl-price-spacer text-[11px] mb-0.5 invisible">&nbsp;</span>
                @endif
                <span class="stayl-price-current text-[16px] font-bold text-red-500 staylbd-rt-price notranslate" data-base-price="{{ $price }}">
                    {{ currency_symbol() }}{{ showAmount($price) }}
                </span>
            </div>
            
            {{-- Cart Button injected inline for max space efficiency --}}
            <button type="button" class="add-to-cart stayl-compact-atc w-9 h-9 rounded-md bg-transparent border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:bg-[var(--stayl-color-primary)] hover:border-[var(--stayl-color-primary)] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed staylbd-rt-atc" 
                data-product_id="{{ $product->id }}" 
                data-qty="1" 
                {{ $canPurchase ? '' : 'disabled' }}
                title="{{ __('Add to Cart') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </button>
        </div>
    </div>
</div>
