@props(['product', 'general', 'activeTemplate'])

@php
    $pricing = productDisplayPricing($product);
    $price = $pricing['effective'];
    $basePrice = $pricing['compare_at'] ?? (float) ($product->price ?? 0);
    $saveAmount = $pricing['save_amount'];
    $savePercent = $pricing['save_percent'];
    $hasDiscount = $pricing['has_savings'];
    
    $primaryImg = getImageWebP(getFilePath('product') . '/' . $product->image, getFileSize('product'));
    $qty = $product->has_variants && $product->activeVariants->isNotEmpty()
        ? (int) $product->activeVariants->sum('quantity')
        : (int) ($product->quantity ?? 0);
    $canPurchase = $qty > 0;
    $displayName = __($product->name);
    $buyNowUrl = storefront_route('cart.list.buy.now', ['id' => $product->id]);

    $rating = 0;
    if (($product->reviews_count ?? 0) > 0) {
        $rating = ($product->reviews_sum_rating ?? 0) / $product->reviews_count;
    }
    
    $lowStockMax = (int) config('product_upload.low_stock_max', 20);
    $stockTier = $qty > $lowStockMax ? 'in' : ($qty >= 1 ? 'low' : 'out');
    $stockLabel = $stockTier === 'in' ? __('In Stock') : ($stockTier === 'low' ? __('Low Stock') : __('Out of Stock'));
@endphp

<div class="stayl-product-card group bg-white dark:bg-slate-950 rounded-xl border border-slate-200/80 dark:border-slate-800/80 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-lg transition-all duration-300 relative flex flex-col h-full overflow-hidden" data-product-id="{{ $product->id }}">
    
    @if($hasDiscount)
        <div class="stayl-card-badge absolute top-2 left-2 z-10 pointer-events-none">
            <span class="bg-red-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-md shadow-sm">
                -{{ $savePercent }}%
            </span>
        </div>
    @endif

    @if(in_array($product->product_type, ['digital', 'service']))
        <div class="stayl-card-type-badge absolute top-2 right-2 z-10 pointer-events-none">
            <span class="bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-sm uppercase tracking-wider">
                {{ ucfirst($product->product_type) }}
            </span>
        </div>
    @endif

    <div class="stayl-card-media relative bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden aspect-square w-full">
        <a href="{{ product_detail_url($product) }}" class="block w-full h-full p-2.5 flex items-center justify-center">
            <img src="{{ $primaryImg }}" alt="{{ $displayName }}" class="stayl-card-img max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-105" loading="lazy" decoding="async">
        </a>
        <button type="button"
                class="stayl-card-quickview quickView absolute top-2 right-2 z-20 w-8 h-8 rounded-full bg-white/95 dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                data-product_id="{{ $product->id }}"
                title="{{ __('Quick view') }}"
                aria-label="{{ __('Quick view') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
    </div>

    <div class="stayl-card-bezel p-2.5 bg-slate-50/80 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-1.5 text-left mt-auto">
        <h3 class="text-[12px] font-semibold leading-snug text-slate-800 dark:text-slate-100 line-clamp-2 m-0 min-h-[2.4em]">
            <a href="{{ product_detail_url($product) }}" class="hover:text-[var(--product-buy-now-color,#0e9f90)] transition-colors" title="{{ $displayName }}">{{ $displayName }}</a>
        </h3>

        <div class="flex items-center justify-between gap-1 text-[11px] leading-none">
            <div class="flex items-center gap-1 min-w-0">
                <span class="text-[14px] font-extrabold text-red-600 dark:text-red-400 staylbd-rt-price notranslate whitespace-nowrap" data-base-price="{{ $price }}">
                    {{ currency_symbol() }}{{ showAmount($price) }}
                </span>
                @if($hasDiscount)
                    <span class="text-[10px] text-slate-400 line-through staylbd-rt-price-compare notranslate truncate" data-base-price="{{ $basePrice }}">
                        {{ currency_symbol() }}{{ showAmount($basePrice) }}
                    </span>
                @endif
            </div>
            <span class="text-[9px] shrink-0 {{ $stockTier === 'in' ? 'text-emerald-600' : ($stockTier === 'low' ? 'text-amber-600' : 'text-red-600') }} font-bold uppercase">
                {{ $stockLabel }}
            </span>
        </div>

        <div class="stayl-card-actions grid grid-cols-4 gap-1.5 mt-0.5">
            <button type="button"
                    class="add-wishlist btn-wishlist stayl-card-action-btn flex flex-col items-center justify-center gap-0.5 min-h-[52px] rounded-lg border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors"
                    data-product_id="{{ $product->id }}"
                    title="{{ __('Wishlist') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                <span class="text-[9px] font-bold leading-none">{{ __('Wish') }}</span>
            </button>

            <button type="button"
                    class="add-to-compare btn-compare stayl-card-action-btn flex flex-col items-center justify-center gap-0.5 min-h-[52px] rounded-lg border border-violet-200 dark:border-violet-900/50 bg-violet-50 dark:bg-violet-950/40 text-violet-800 dark:text-violet-200 hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors"
                    data-product_id="{{ $product->id }}"
                    title="{{ __('Compare') }}">
                @include($activeTemplate . 'partials.icons.compare', ['size' => 14, 'class' => 'shrink-0'])
                <span class="text-[9px] font-bold leading-none">{{ __('Compare') }}</span>
            </button>

            <button type="button"
                    class="add-to-cart staylbd-rt-atc stayl-card-action-btn flex flex-col items-center justify-center gap-0.5 min-h-[52px] rounded-lg border border-amber-300 dark:border-amber-800 bg-amber-500 hover:bg-amber-600 text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    data-product_id="{{ $product->id }}"
                    data-qty="1"
                    {{ $canPurchase ? '' : 'disabled' }}
                    title="{{ __('Add to Cart') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                <span class="text-[9px] font-bold leading-none">{{ __('Cart') }}</span>
            </button>

            <a href="{{ $buyNowUrl }}"
               class="buy-now stayl-card-action-btn flex flex-col items-center justify-center gap-0.5 min-h-[52px] rounded-lg border border-red-600 bg-red-600 hover:bg-red-700 text-white transition-colors {{ $canPurchase ? '' : 'pointer-events-none opacity-50' }}"
               data-no-ajax
               data-product_id="{{ $product->id }}"
               title="{{ __('Buy Now') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                <span class="text-[9px] font-bold leading-none">{{ __('Buy') }}</span>
            </a>
        </div>
    </div>
</div>
