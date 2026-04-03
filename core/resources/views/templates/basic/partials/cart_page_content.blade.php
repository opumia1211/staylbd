@php
    $carts = $carts ?? [];
    if (!is_countable($carts)) {
        $carts = is_array($carts) ? $carts : (is_object($carts) ? array_values((array) $carts) : []);
    }
    $isUserDashboard = ($cartPageContext ?? '') === 'user_dashboard';
    $cartCount = count($carts);
@endphp
@if($isUserDashboard)
<style>
/* User dashboard cart – original green header style */
#dashboard-ajax-content .cart-page--user-dashboard .cart-page__toolbar,
#user-dashboard-root .cart-page--user-dashboard .cart-page__toolbar,
.cart-page--user-dashboard .cart-page__toolbar,
.cart-page--user-dashboard .cart-page__footer,
.cart-page--user-dashboard .cart-page__heading { display: none !important; }

/* খালি কার্ট ব্লক সাইডবারের মাঝের কন্টেন্ট এরিয়ায় সেন্টার (গ্রিন জোনে) */
#dashboard-ajax-content .cart-page--user-dashboard .cart-page__empty-outer--dashboard,
#user-dashboard-root .cart-page--user-dashboard .cart-page__empty-outer--dashboard,
.cart-page--user-dashboard .cart-page__empty-outer--dashboard {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    min-height: 280px !important;
    width: 100% !important;
    padding: 2rem 1rem !important;
    box-sizing: border-box !important;
}
#dashboard-ajax-content .cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty,
#user-dashboard-root .cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty,
.cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty {
    max-width: 420px !important;
    width: 100% !important;
    text-align: center !important;
    margin: 0 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
}
#dashboard-ajax-content .cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty .card-body,
#user-dashboard-root .cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty .card-body,
.cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty .card-body {
    padding: 2rem 1.5rem !important;
}
#dashboard-ajax-content .cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty-icon,
#user-dashboard-root .cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty-icon,
.cart-page--user-dashboard .cart-page__empty-outer--dashboard .cart-page__empty-icon {
    font-size: 3.5rem !important;
    color: #94a3b8 !important;
    margin-bottom: 0.75rem !important;
}

#dashboard-ajax-content .cart-page--user-dashboard .cart-page__card,
#user-dashboard-root .cart-page--user-dashboard .cart-page__card,
.cart-page--user-dashboard .cart-page__card {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: visible !important;
    padding-top: 0 !important;
    padding-bottom: 0.5rem !important;
}

#dashboard-ajax-content .cart-page--user-dashboard .cart-page .container,
#user-dashboard-root .cart-page--user-dashboard .cart-page .container,
.cart-page--user-dashboard .cart-page .container {
    width: 100% !important;
    max-width: 100% !important;
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
    box-sizing: border-box !important;
}

#dashboard-ajax-content .cart-page--user-dashboard .cart-page__table-wrap,
#user-dashboard-root .cart-page--user-dashboard .cart-page__table-wrap,
.cart-page--user-dashboard .cart-page__table-wrap {
    overflow-x: auto !important;
    overflow-y: visible !important;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
    padding-bottom: 0.25rem !important;
}
#dashboard-ajax-content .cart-page--user-dashboard .cart-page__table-wrap table,
.cart-page--user-dashboard .cart-page__table-wrap table {
    margin-bottom: 0 !important;
}

#dashboard-ajax-content .cart-page--user-dashboard .cart-page__table,
#user-dashboard-root .cart-page--user-dashboard .cart-page__table,
.cart-page--user-dashboard .cart-page__table {
    min-width: 850px;
    max-width: 100%;
    table-layout: fixed;
    width: 100%;
    box-sizing: border-box;
}

#dashboard-ajax-content .cart-page--user-dashboard .cart-page__thead tr,
#user-dashboard-root .cart-page--user-dashboard .cart-page__thead tr,
.cart-page--user-dashboard .cart-page__thead tr {
    background: #198754 !important;
    color: #fff !important;
}

#dashboard-ajax-content .cart-page--user-dashboard .cart-page__thead th,
#user-dashboard-root .cart-page--user-dashboard .cart-page__thead th,
.cart-page--user-dashboard .cart-page__thead th {
    color: #fff !important;
    border: none !important;
    padding: 0.2rem 0.3rem !important;
    font-weight: 600 !important;
    font-size: 0.68rem !important;
    line-height: 1.2 !important;
}
.cart-page--user-dashboard .cart-page__thead .cart-row-user__discount { width: 72px !important; min-width: 72px !important; }
.cart-page--user-dashboard .cart-page__thead .cart-row-user__price { width: 76px !important; min-width: 76px !important; }
.cart-page--user-dashboard .cart-page__thead .cart-row-user__qty { width: 150px !important; min-width: 150px !important; }
.cart-page--user-dashboard .cart-page__thead .cart-row-user__subtotal { width: 90px !important; min-width: 90px !important; }
.cart-page--user-dashboard .cart-page__thead .cart-row-user__action { width: 420px !important; min-width: 420px !important; max-width: 420px !important; }

/* CRITICAL: checkbox column never hidden on any device */
.cart-page--user-dashboard .cart-page__table th:first-child,
.cart-page--user-dashboard .cart-page__table td:first-child,
.cart-page--user-dashboard .cart-row-user__check {
    display: table-cell !important;
    width: 45px !important;
    min-width: 45px !important;
}
.cart-page--user-dashboard .cart-row-user__img { width: 5%; }
.cart-page--user-dashboard .cart-row-user__name { width: 11%; }
.cart-page--user-dashboard .cart-row-user__sku { width: 7%; }
.cart-page--user-dashboard .cart-row-user__category { width: 6%; }
.cart-page--user-dashboard .cart-row-user__brand { width: 6%; }
/* Category & Brand কলামের মাঝে পরিষ্কার গ্যাপ/ডিভাইডার */
.cart-page--user-dashboard .cart-page__thead .cart-row-user__category,
.cart-page--user-dashboard .cart-page__tbody .cart-row-user__category {
    border-right: 1px solid #e5e7eb !important;
    padding-right: 0.45rem !important;
}
.cart-page--user-dashboard .cart-page__thead .cart-row-user__brand,
.cart-page--user-dashboard .cart-page__tbody .cart-row-user__brand {
    padding-left: 0.45rem !important;
}
.cart-page--user-dashboard .cart-row-user__stock { width: 5%; }
.cart-page--user-dashboard .cart-row-user__discount { width: 72px !important; min-width: 72px !important; max-width: 72px !important; box-sizing: border-box; }
.cart-page--user-dashboard .cart-row-user__price { width: 76px !important; min-width: 76px !important; max-width: 76px !important; box-sizing: border-box; }
.cart-page--user-dashboard .cart-row-user__rating { width: 6%; }
.cart-page--user-dashboard .cart-row-user__qty { width: 150px !important; min-width: 150px !important; max-width: none !important; box-sizing: border-box; overflow: visible !important; }
.cart-page--user-dashboard .cart-row-user__subtotal { width: 90px !important; min-width: 90px !important; max-width: 90px !important; box-sizing: border-box; }
.cart-page--user-dashboard .cart-row-user__action { width: 420px !important; min-width: 420px !important; max-width: 420px !important; box-sizing: border-box !important; overflow: visible !important; }

#dashboard-ajax-content .cart-page--user-dashboard .cart-page__tbody .cart-row--user td,
#user-dashboard-root .cart-page--user-dashboard .cart-page__tbody .cart-row--user td,
.cart-page--user-dashboard .cart-page__tbody .cart-row--user td {
    padding: 4px 6px !important;
    vertical-align: middle !important;
    border-color: #e5e7eb;
    font-size: 0.72rem !important;
    line-height: 1.15 !important;
}

.cart-page--user-dashboard .cart-page__tbody .cart-row--user:hover {
    background: #f8fafc !important;
}

/* সব প্রোডাক্ট রো একই ভাবে দেখাতে + শেষ রো ঘরের ভেতরে */
.cart-page--user-dashboard .cart-page__tbody .cart-row--user:first-child td,
.cart-page--user-dashboard .cart-page__tbody .cart-row--user:last-child td,
.cart-page--user-dashboard .cart-page__tbody .cart-row--user td {
    box-sizing: border-box !important;
}
.cart-page--user-dashboard .cart-page__tbody tr.cart-row--user:last-child td {
    border-bottom: 1px solid #e5e7eb;
}

.cart-page--user-dashboard .cart-row-user__discount,
.cart-page--user-dashboard .cart-row-user__price {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis;
}
.cart-page--user-dashboard .cart-row-user__discount .badge,
.cart-page--user-dashboard .cart-row-user__price .price {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
}

.cart-page--user-dashboard .cart-row-user__img-link {
    width: 34px;
    height: 34px;
    min-width: 34px;
    min-height: 34px;
    display: flex !important;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    overflow: hidden;
}

.cart-page--user-dashboard .cart-row-user__thumb {
    width: 34px !important;
    height: 34px !important;
    object-fit: cover;
}

.cart-page--user-dashboard .cart-row-user__name-link {
    color: #0d6efd !important;
    word-break: break-word;
    line-height: 1.2 !important;
    font-size: 0.75rem !important;
    display: block;
    font-weight: 600;
}

.cart-page--user-dashboard .cart-row-user__price .price {
    font-size: 0.75rem !important;
}

.cart-page--user-dashboard .cart-row-user__qty {
    position: relative;
    z-index: 1;
}
.cart-page--user-dashboard .cart-row-user__qty .cart-qty-control {
    pointer-events: auto;
}
.cart-page--user-dashboard .cart-row-user__qty .cart-quantity-input {
    width: 32px !important;
    min-width: 32px !important;
    font-size: 0.7rem !important;
}

.cart-page--user-dashboard .cart-row-user__qty .qty-btn {
    padding: 0.2rem 0.35rem !important;
    cursor: pointer;
    flex-shrink: 0;
}

.cart-page--user-dashboard .cart-row-user__qty,
.cart-page--user-dashboard .cart-row-user__subtotal {
    white-space: nowrap !important;
    overflow: visible !important;
    text-align: center !important;
}
.cart-page--user-dashboard .cart-row-user__qty .cart-qty-control {
    display: inline-flex !important;
    flex-shrink: 0 !important;
    min-width: 130px;
    overflow: visible !important;
}
.cart-page--user-dashboard .cart-row-user__qty .qty-btn.cart-decrease,
.cart-page--user-dashboard .cart-row-user__qty .qty-btn.cart-increase {
    width: 38px !important;
    min-width: 38px !important;
    height: 36px !important;
    min-height: 36px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    visibility: visible !important;
    opacity: 1 !important;
}
.cart-page--user-dashboard .cart-row-user__qty .cart-quantity-input {
    width: 40px !important;
    min-width: 40px !important;
    height: 36px !important;
    min-height: 36px !important;
    font-size: 0.8rem !important;
}
.cart-page--user-dashboard .cart-row-user__subtotal .subtotal {
    display: inline-block !important;
}

.cart-page--user-dashboard .cart-row-user__action-btns {
    display: flex !important;
    flex-wrap: nowrap !important;
    gap: 6px !important;
    align-items: center !important;
    justify-content: flex-end !important;
    min-width: 0 !important;
}
.cart-page--user-dashboard .cart-row-user__action-btns .btn,
.cart-page--user-dashboard .cart-row-user__action-btns a.btn,
.cart-page--user-dashboard .cart-row-user__action-btns button {
    min-height: 36px !important;
    height: 36px !important;
    min-width: 88px !important;
    width: 88px !important;
    max-width: 88px !important;
    padding: 0.35rem 0.4rem !important;
    font-size: 0.75rem !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    box-sizing: border-box !important;
}

/* Text buttons (.list-page-action-btn) sized in user-list-pages-common.css */
.cart-page--user-dashboard .cart-row-user__action-btns .cart-remove-btn-user {
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1 !important;
    border-radius: 6px !important;
    background: #fff !important;
    color: #dc3545 !important;
    border: 1px solid #dc3545 !important;
}

.cart-page--user-dashboard .cart-row-user__action-btns .cart-remove-btn-user:hover {
    background: #dc3545 !important;
    color: #fff !important;
}

/* Dashboard cart: make stock/discount compact and non-wrapping */
.cart-page--user-dashboard .cart-row-user__stock .badge {
    font-size: 0.58rem !important;
    padding: 0.1rem 0.28rem !important;
    line-height: 1.1 !important;
    white-space: nowrap !important;
}
.cart-page--user-dashboard .cart-row-user__stock .small {
    font-size: 0.58rem !important;
}
.cart-page--user-dashboard .cart-row-user__discount .badge {
    font-size: 0.58rem !important;
    padding: 0.1rem 0.28rem !important;
    line-height: 1.1 !important;
    white-space: nowrap !important;
}
.cart-page--user-dashboard .cart-row-user__name .cart-row-user__variant {
    font-size: 0.6rem !important;
}

.cart-page--user-dashboard .cart-sidebar { border: 1px solid #e5e7eb; border-radius: 8px; }
.cart-page--user-dashboard .cart-sidebar__title { padding: 10px 12px; font-size: 0.95rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
.cart-page--user-dashboard .cart-sidebar__body { padding: 12px; }
.cart-page--user-dashboard .cart-sidebar__row { padding: 6px 0; font-size: 0.9rem; }
.cart-page--user-dashboard .cart-sidebar__row--total { border-top: 1px solid #e5e7eb; margin-top: 8px; padding-top: 10px; font-weight: 700; }
.cart-page--user-dashboard .cart-sidebar__cta { margin-top: 12px; width: 100%; padding: 10px; font-weight: 600; border-radius: 6px; }
.cart-page--user-dashboard .cart-sidebar__title-badge { display: none; }

/* Order Summary – below table in dashboard */
.cart-page--user-dashboard .cart-sidebar--below { border: 1px solid #e5e7eb !important; border-radius: 8px !important; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 0.75rem 1rem !important; }
.cart-page--user-dashboard .cart-sidebar__title-inline { font-size: 1.05rem; color: #1f2937; }
.cart-page--user-dashboard .cart-sidebar__row--inline { display: inline-flex; align-items: center; }
.cart-page--user-dashboard #proceedToCheckoutBtn,
.cart-page--user-dashboard .cart-sidebar__cta--link.btn { min-height: 48px; font-weight: 600; border-radius: 8px; white-space: nowrap; }

@media (max-width: 575.98px) {
    .cart-page--user-dashboard .cart-sidebar--below .card-body { padding: 0.75rem !important; }
    .cart-page--user-dashboard .cart-sidebar--below .d-flex { flex-direction: column !important; align-items: stretch !important; gap: 0.75rem !important; }
    .cart-page--user-dashboard .cart-sidebar__title-inline { text-align: center; margin-bottom: 0.25rem; }
    .cart-page--user-dashboard .cart-sidebar--below .d-flex .d-flex { justify-content: space-between; flex-wrap: wrap; }
    .cart-page--user-dashboard #proceedToCheckoutBtn,
    .cart-page--user-dashboard .cart-sidebar__cta--link.btn { width: 100%; justify-content: center; }
}
@media (min-width: 576px) and (max-width: 767.98px) {
    .cart-page--user-dashboard .cart-sidebar--below .d-flex { gap: 0.5rem !important; }
    .cart-page--user-dashboard #proceedToCheckoutBtn,
    .cart-page--user-dashboard .cart-sidebar__cta--link.btn { padding-left: 1.25rem; padding-right: 1.25rem; }
}
@media (min-width: 768px) and (max-width: 991.98px) {
    .cart-page--user-dashboard .cart-sidebar--below { padding: 1rem 1.25rem !important; }
}
@media (min-width: 992px) {
    .cart-page--user-dashboard .cart-page__card { padding: 0 0.5rem 0.5rem 0.5rem !important; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-radius: 10px; }
    .cart-page--user-dashboard .cart-sidebar--below { padding: 1rem 1.5rem !important; }
}
@media (max-width: 380px) {
    .cart-page--user-dashboard .cart-sidebar__title-inline { font-size: 0.95rem; }
    .cart-page--user-dashboard .cart-sidebar__row--inline { font-size: 0.9rem; }
    .cart-page--user-dashboard #proceedToCheckoutBtn,
    .cart-page--user-dashboard .cart-sidebar__cta--link.btn { font-size: 0.9rem; padding: 0.6rem 0.75rem; min-height: 44px; }
}

/* ========== Mobile & Tablet: ~half row height, buttons visible + one line ========== */
.cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions,
#dashboard-ajax-content .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions,
#user-dashboard-root .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
    flex-wrap: nowrap !important;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions .btn,
.cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions a.btn,
#dashboard-ajax-content .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions .btn,
#dashboard-ajax-content .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions a.btn,
#user-dashboard-root .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions .btn,
#user-dashboard-root .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions a.btn {
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1 !important;
}

@media (max-width: 479px) {
    .cart-page--user-dashboard .cart-page .container { padding-left: 0.4rem !important; padding-right: 0.4rem !important; }
    .cart-page--user-dashboard .cart-page__mobile { padding: 0.2rem 0.3rem !important; }
    .cart-page--user-dashboard .cart-row-mobile { padding: 0.22rem 0.35rem !important; margin-bottom: 0.22rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__img-link,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__img,
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__img-link img,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__img img { width: 36px !important; height: 36px !important; min-width: 36px !important; min-height: 36px !important; }
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__mark { margin-bottom: 0.15rem !important; padding-bottom: 0.15rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__name,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__name { font-size: 0.75rem !important; -webkit-line-clamp: 2; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__meta,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__meta { font-size: 0.6rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__price-val,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__price,
    .cart-page--user-dashboard .cart-row-mobile .subtotal { font-size: 0.75rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__qty-subtotal { margin-top: 0.2rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions { margin-top: 0.25rem !important; padding-top: 0.25rem !important; gap: 0.2rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .cart-qty-control .qty-btn,
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__btn,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions .btn,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions a.btn { min-width: 26px !important; min-height: 26px !important; padding: 0.18rem 0.28rem !important; font-size: 0.6rem !important; }
}

@media (min-width: 480px) and (max-width: 767.98px) {
    .cart-page--user-dashboard .cart-page .container { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
    .cart-page--user-dashboard .cart-page__mobile { padding: 0.28rem 0.4rem !important; }
    .cart-page--user-dashboard .cart-row-mobile { padding: 0.28rem 0.4rem !important; margin-bottom: 0.28rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__img-link,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__img,
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__img-link img,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__img img { width: 42px !important; height: 42px !important; min-width: 42px !important; min-height: 42px !important; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__name,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__name { font-size: 0.82rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions .btn,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions a.btn,
    .cart-page--user-dashboard .cart-row-mobile .list-page-action-btn { min-height: 28px !important; min-width: 28px !important; padding: 0.2rem 0.35rem !important; font-size: 0.65rem !important; }
}

@media (min-width: 768px) and (max-width: 991.98px) {
    .cart-page--user-dashboard .cart-page .container { padding-left: 0.6rem !important; padding-right: 0.6rem !important; }
    .cart-page--user-dashboard .cart-page__mobile { padding: 0.28rem 0.5rem !important; max-width: 100%; }
    .cart-page--user-dashboard .cart-row-mobile { padding: 0.28rem 0.5rem !important; margin-bottom: 0.28rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__img-link,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__img,
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__img-link img,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__img img { width: 44px !important; height: 44px !important; min-width: 44px !important; min-height: 44px !important; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__name,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__name { font-size: 0.85rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .product-list-row__meta,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__meta { font-size: 0.68rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions { margin-top: 0.28rem !important; padding-top: 0.28rem !important; }
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions .btn,
    .cart-page--user-dashboard .cart-row-mobile .cart-row-mobile__actions a.btn,
    .cart-page--user-dashboard .cart-row-mobile .list-page-action-btn { min-height: 28px !important; font-size: 0.68rem !important; }
}

/* ========== 1024px / 1280px+: laptop & desktop (table visible) ========== */
@media (min-width: 1024px) {
    .cart-page--user-dashboard .cart-page .container { padding-left: 0.5rem !important; padding-right: 0.5rem !important; max-width: 100% !important; }
}
@media (min-width: 1280px) {
    .cart-page--user-dashboard .cart-page .container { max-width: 1400px !important; padding-left: 1rem !important; padding-right: 1rem !important; }
    .cart-page--user-dashboard .cart-page__table { min-width: 1100px; }
}
@media (min-width: 992px) {
    .cart-page--user-dashboard .cart-page__table-wrap { -webkit-overflow-scrolling: touch; }
}
</style>
@else
<style>
.cart-page:not(.cart-page--user-dashboard) .container { width: 100%; max-width: 100%; padding-left: 0.75rem; padding-right: 0.75rem; box-sizing: border-box; }
.cart-page__row .col-lg-9 { min-width: 0; max-width: 100%; }
.cart-page__row .col-lg-3 { min-width: 0; max-width: 100%; }
.cart-page .cart-page__table-wrap { overflow-x: auto; width: 100%; max-width: 100%; -webkit-overflow-scrolling: touch; }
.cart-page .cart-page__table { width: 100%; max-width: 100%; min-width: 280px; }
.cart-page .cart-page__card { border: 1px solid #e2e8f0; border-radius: 8px; max-width: 100%; }
.cart-page .cart-sidebar { border: 1px solid #e2e8f0; border-radius: 8px; max-width: 100%; }
</style>
@endif
<div class="cart-page cart-container {{ $isUserDashboard ? 'cart-page--user-dashboard pt-0 pb-3 dashboard-list-page' : '' }}">
    <div class="container">
        @if($cartCount > 0 && !$isUserDashboard)
        <header class="cart-page__heading">
            <h1>@lang('Shopping Cart')</h1>
            <p>@lang('Review your items and proceed to checkout')</p>
        </header>
        @endif
        @if(!$isUserDashboard)
        @guest
        <div class="alert alert-light border mb-4 d-flex flex-column flex-sm-row align-items-center gap-3 cart-page__login-prompt">
            @include($activeTemplate . 'partials.icon', ['name' => 'user-circle', 'class' => 'fs-2 text--base'])
            <div class="flex-grow-1 text-center text-sm-start">
                <strong>@lang('Login for faster checkout')</strong><br>
                <span class="small text-muted">@lang('Sign in to save your cart and use saved addresses.')</span>
            </div>
            <a href="{{ route('user.login') }}?redirect={{ urlencode(request()->url()) }}" class="btn btn--base btn-sm flex-shrink-0">@lang('Login')</a>
        </div>
        @endguest
        @endif

        @if($cartCount > 0)
        <div class="row g-3 cart-page__row">
            <div class="{{ $isUserDashboard ? 'col-12' : 'col-lg-9' }}">
                <div class="card cart-page__card">
                    @if(!$isUserDashboard)
                    <div class="cart-page__toolbar d-none d-md-flex">
                        <div class="cart-page__toolbar-info">
                            <strong><span class="cart-toolbar__count-num">{{ $cartCount }}</span> {{ $cartCount == 1 ? __('item') : __('items') }}</strong>
                            <span class="text-muted ms-2" style="font-size: 0.8rem;">— @lang('Tick items to include, then checkout below')</span>
                        </div>
                        <div class="cart-page__toolbar-actions">
                            <button type="button" class="btn btn-outline-danger btn-sm cart-remove-selected d-none">@include($activeTemplate . 'partials.icon', ['name' => 'trash-alt', 'class' => 'me-1'])@lang('Remove Selected')</button>
                            <a href="#cart-page__summary-card" class="btn btn--base btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-right', 'class' => 'me-1'])@lang('Proceed to Checkout')</a>
                        </div>
                    </div>
                    @endif
                    <div class="table-responsive cart-page__table-wrap d-none {{ $isUserDashboard ? 'd-lg-block' : 'd-md-block' }}">
                        <table class="table table-hover align-middle mb-0 cart-page__table table-auto">
                            <thead class="cart-page__thead">
                                @if($isUserDashboard)
                                <tr>
                                    <th class="cart-page__th cart-row-user__check"><label class="mb-0"><input type="checkbox" class="form-check-input cart-select-all" id="cartSelectAll" aria-label="@lang('Select all')"></label></th>
                                    <th class="cart-page__th cart-row-user__img">@lang('Image')</th>
                                    <th class="cart-page__th cart-row-user__name">@lang('Product')</th>
                                    <th class="cart-page__th cart-row-user__sku">@lang('SKU')</th>
                                    <th class="cart-page__th cart-row-user__category">@lang('Category')</th>
                                    <th class="cart-page__th cart-row-user__brand">@lang('Brand')</th>
                                    <th class="cart-page__th cart-row-user__stock">@lang('Stock')</th>
                                    <th class="cart-page__th cart-row-user__discount">@lang('Discount')</th>
                                    <th class="cart-page__th cart-row-user__price">@lang('Price')</th>
                                    <th class="cart-page__th cart-row-user__rating">@lang('Rating')</th>
                                    <th class="cart-page__th cart-row-user__qty">@lang('Qty')</th>
                                    <th class="cart-page__th cart-row-user__subtotal">@lang('Subtotal')</th>
                                    <th class="cart-page__th cart-row-user__action">@lang('Action')</th>
                                </tr>
                                @else
                                <tr>
                                    <th class="cart-page__th cart-page__th--check">
                                        <label class="cart-page__check-label mb-0">
                                            <input type="checkbox" class="form-check-input cart-select-all" id="cartSelectAll" aria-label="@lang('Select all')">
                                        </label>
                                    </th>
                                    <th class="cart-page__th cart-page__th--img"></th>
                                    <th class="cart-page__th cart-page__th--product">@lang('Product & Size')</th>
                                    <th class="cart-page__th text-end">@lang('Price')</th>
                                    <th class="cart-page__th text-end">@lang('Quantity')</th>
                                    <th class="cart-page__th text-end">@lang('Subtotal')</th>
                                    <th class="cart-page__th text-end">@lang('Remove')</th>
                                </tr>
                                @endif
                            </thead>
                            <tbody class="cart-page__tbody">
                                @foreach($carts as $cart)
                                    @if($isUserDashboard)
                                        @include($activeTemplate . 'partials.cart_row_user', ['cart' => $cart])
                                    @else
                                        @include($activeTemplate . 'partials.cart_row', ['cart' => $cart, 'simpleCart' => false])
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(!$isUserDashboard)
                    <div class="cart-page__footer d-none d-md-flex">
                        <a href="{{ route('products') }}" class="btn btn-outline-primary btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-left', 'class' => 'me-1'])@lang('Continue Shopping')</a>
                        <a href="#cart-page__summary-card" class="btn btn--base btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-right', 'class' => 'me-1'])@lang('Proceed to Checkout')</a>
                    </div>
                    @endif
                    <div class="cart-page__mobile {{ $isUserDashboard ? 'cart-mobile-cards d-lg-none' : 'd-md-none' }}">
                        @if($cartCount > 0)
                        <div class="cart-page__mobile-select-all mb-2 py-2 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <label class="d-flex align-items-center gap-2 mb-0 cursor-pointer">
                                <input type="checkbox" class="form-check-input cart-select-all-mobile" id="cartSelectAllMobile" aria-label="@lang('Select all')">
                                <span class="small fw-medium">@lang('Select all for order')</span>
                            </label>
                            <button type="button" class="btn btn-outline-danger btn-sm cart-remove-selected d-none">
                                @include($activeTemplate . 'partials.icon', ['name' => 'trash-alt', 'class' => 'me-1'])@lang('Remove Selected')
                            </button>
                        </div>
                        @endif
                        @foreach($carts as $cart)
                            @include($activeTemplate . 'partials.cart_row_mobile', ['cart' => $cart])
                        @endforeach
                        <a href="{{ route('products') }}" class="btn btn-outline-primary btn-sm mt-2">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-left', 'class' => 'me-1'])@lang('Continue Shopping')</a>
                    </div>
                </div>
            </div>
            @if(!$isUserDashboard)
            <aside class="col-lg-3" id="cart-page__summary-card">
                <div class="cart-sidebar">
                    <h3 class="cart-sidebar__title">
                        <span>@lang('Order Summary')</span>
                    </h3>
                    <div class="cart-sidebar__body">
                        <div class="cart-sidebar__row">
                            <span class="cart-sidebar__label">@lang('Subtotal')</span>
                            <span class="cart-sidebar__value subtotal-price">{{ $general->cur_sym }}0.00</span>
                        </div>
                        <div class="cart-sidebar__row coupon-show d-none">
                            <span class="cart-sidebar__label">@lang('Discount')</span>
                            <span class="cart-sidebar__value cart-sidebar__value--discount discount-price">-{{ $general->cur_sym }}0.00</span>
                        </div>
                        <div class="cart-sidebar__divider"></div>
                        <div class="cart-sidebar__row cart-sidebar__row--total total-show">
                            <span class="cart-sidebar__label">@lang('Total')</span>
                            <span class="cart-sidebar__value total-price">{{ $general->cur_sym }}0.00</span>
                        </div>
                        <div class="cart-sidebar__coupon mt-2">
                            <form class="coupon-form d-flex gap-2" role="search">
                                <label for="cart-coupon-input" class="visually-hidden">@lang('Coupon code')</label>
                                <input type="text" id="cart-coupon-input" class="form-control cart-sidebar__coupon-input coupon" name="coupon" placeholder="@lang('Coupon code')" autocomplete="off" aria-label="@lang('Coupon code')">
                                <button type="button" class="btn btn--base cart-sidebar__coupon-btn coupon-apply flex-shrink-0" aria-label="@lang('Apply coupon')">@lang('Apply')</button>
                            </form>
                        </div>
                        @auth
                        <form id="checkoutSelectionForm" action="{{ route('cart.list.set.checkout.selection') }}" method="POST" class="cart-sidebar__form">
                            @csrf
                            <div id="checkout-cart-ids-container"></div>
                            <button type="submit" class="btn btn--base w-100 cart-sidebar__cta" id="proceedToCheckoutBtn">@include($activeTemplate . 'partials.icon', ['name' => 'lock', 'class' => 'me-1'])@lang('Proceed to Checkout')</button>
                        </form>
                        <p class="cart-sidebar__note">@lang('Only selected items above will be included in the order.')</p>
                        @else
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('user.cart.quickorder') }}"
                               class="btn btn--base w-100 cart-sidebar__cta"
                               id="openGuestCheckoutBtn"
                               data-bs-toggle="modal"
                               data-bs-target="#guestCheckoutModal">
                                @include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1'])@lang('Quick Order')
                            </a>
                            <a href="{{ route('user.login') }}?redirect={{ urlencode(route('user.checkout.index')) }}" class="btn btn-outline-secondary btn-sm w-100 text-decoration-none">@include($activeTemplate . 'partials.icon', ['name' => 'sign-in-alt', 'class' => 'me-1'])@lang('Login to Checkout')</a>
                        </div>
                        @endauth
                    </div>
                </div>
            </aside>
            @else
            {{-- User dashboard: Order Summary outside sidebar, below table --}}
            <div class="col-12 mt-3" id="cart-page__summary-card">
                <div class="card cart-sidebar cart-sidebar--below border rounded">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <h5 class="mb-0 cart-sidebar__title-inline fw-bold">@lang('Order Summary')</h5>
                            <div class="d-flex flex-wrap align-items-center gap-4">
                                <div class="cart-sidebar__row cart-sidebar__row--inline">
                                    <span class="cart-sidebar__label me-2">@lang('Subtotal')</span>
                                    <span class="cart-sidebar__value subtotal-price fw-semibold">{{ $general->cur_sym }}0.00</span>
                                </div>
                                <div class="cart-sidebar__divider-vertical d-none d-sm-block" style="width:1px;height:24px;background:#dee2e6;"></div>
                                <div class="cart-sidebar__row cart-sidebar__row--total cart-sidebar__row--inline">
                                    <span class="cart-sidebar__label me-2">@lang('Total')</span>
                                    <span class="cart-sidebar__value total-price fw-bold">{{ $general->cur_sym }}0.00</span>
                                </div>
                                @auth
                                <form id="checkoutSelectionForm" action="{{ route('cart.list.set.checkout.selection') }}" method="POST" class="d-inline">
                                    @csrf
                                    <div id="checkout-cart-ids-container"></div>
                                    <button type="submit" class="btn btn-success btn-lg px-4" id="proceedToCheckoutBtn">@include($activeTemplate . 'partials.icon', ['name' => 'lock', 'class' => 'me-1'])@lang('Proceed to Checkout')</button>
                                </form>
                                @else
                                <a href="{{ route('user.cart.quickorder') }}"
                                   class="btn btn-success btn-lg px-4"
                                   id="openGuestCheckoutBtnInline"
                                   data-bs-toggle="modal"
                                   data-bs-target="#guestCheckoutModal">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1'])@lang('Quick Order')
                                </a>
                                <a href="{{ route('user.login') }}?redirect={{ urlencode(route('user.checkout.index')) }}" class="btn btn-outline-secondary btn-lg px-4 text-decoration-none ms-2">@lang('Login')</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @else
        {{-- User dashboard: খালি কার্ট ব্লক সাইডবারের মাঝের কন্টেন্ট এরিয়ায় সেন্টার করে দেখানো --}}
        <div class="cart-page__empty-outer {{ $isUserDashboard ? 'cart-page__empty-outer--dashboard' : '' }}">
            <div class="card cart-page__empty list-page-empty">
                <div class="card-body p-0 text-center py-5 px-4">
                    @include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart', 'class' => 'cart-page__empty-icon list-page-empty__icon d-block'])
                    <h5 class="mb-2 list-page-empty__title text-dark">@lang('Your cart is empty')</h5>
                    <p class="list-page-empty__text text-muted mb-4 small">{{ __($emptyMessage ?? 'Your cart is empty. Start adding products now!') }}</p>
                    <a href="{{ route('products') }}" class="btn btn--base">@lang('Start Shopping')</a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="removeCartModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Remove item?')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">@lang('Are you sure to remove this product?')</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                <button type="button" class="btn btn--base remove-product">@lang('Remove')</button>
            </div>
        </div>
    </div>
</div>
