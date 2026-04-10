{{-- Admin-driven CSS variables (must stay server-rendered) --}}
<style id="stayl-storefront-ui-vars">
        :root{
            --footer-bg-image:url('{{ url("serve-css/img/footer-bg.png") }}?v={{ $assetVersion }}');
            --product-card-bg:{{ optional($uiSettings)->product_card_bg ?? $general->product_card_color ?? '#ffffff' }};
            --product-button-color:{{ optional($uiSettings)->product_button_color ?? $general->button_color ?? '#1f2937' }};
            --product-button-hover:{{ optional($uiSettings)->product_buy_now_hover ?? $general->button_hover_color ?? '#374151' }};
            --product-buy-now-color:{{ optional($uiSettings)->product_buy_now_color ?? '#0e9f90' }};
            --product-buy-now-hover:{{ optional($uiSettings)->product_buy_now_hover ?? '#0c8a7d' }};
            --product-price-color:{{ optional($uiSettings)->product_price_color ?? optional($uiSettings)->product_buy_now_color ?? '#0e9f90' }};
            --product-rating-color:{{ optional($uiSettings)->rating_color ?? $general->rating_star_color ?? '#f59e0b' }};
            --product-discount-badge:{{ optional($uiSettings)->discount_badge_color ?? $general->discount_badge_color ?? '#dc2626' }};
            --product-stock-color:{{ optional($uiSettings)->stock_color ?? '#16a34a' }};
            --product-shipping-color:{{ optional($uiSettings)->shipping_badge_color ?? '#2563eb' }};
            @if(!empty(optional($uiSettings)->header_bg))--header-bg:{{ optional($uiSettings)->header_bg }};@endif
            @if(!empty(optional($uiSettings)->footer_bg))--footer-bg-color:{{ optional($uiSettings)->footer_bg }};@endif
        }
        @if(optional($uiSettings)->theme_template && optional($uiSettings)->theme_template !== 'default')
        body[data-theme="{{ optional($uiSettings)->theme_template }}"] { }
        @endif
</style>
