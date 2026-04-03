@extends('admin.layouts.app')
@section('panel')
@php
    $g = $general ?? null;
    $hasSocial = $hasSocialColumns ?? false;
    $userMsg = $hasColumns && trim((string)($g->stock_out_user_message ?? '')) !== '' ? $g->stock_out_user_message : ($defaults['stock_out_user_message'] ?? '');
    $adminMsg = $hasColumns && trim((string)($g->stock_out_admin_message ?? '')) !== '' ? $g->stock_out_admin_message : ($defaults['stock_out_admin_message'] ?? '');
    $restockMsg = $hasColumns && trim((string)($g->restock_message_template ?? '')) !== '' ? $g->restock_message_template : ($defaults['restock_message_template'] ?? '');
    $waMsg = $hasSocial && trim((string)($g->restock_whatsapp_message ?? '')) !== '' ? $g->restock_whatsapp_message : ($defaults['restock_whatsapp_message'] ?? '');
    $tgMsg = $hasSocial && trim((string)($g->restock_telegram_message ?? '')) !== '' ? $g->restock_telegram_message : ($defaults['restock_telegram_message'] ?? '');
@endphp
<div class="row">
    <div class="col-lg-10 col-xl-8">
        <p class="small text-muted mb-3">@lang('This page only edits message text. Notifications (e.g. stock-out alerts) are listed in') <a href="{{ route('admin.notifications') }}">@lang('Manage Orders') → @lang('Notifications')</a>, @lang('not here.')</p>
        @if(!$hasColumns)
        <div class="alert alert-warning mb-4" role="alert">
            <strong>@lang('Save not available yet')</strong> — @lang('Run') <code>php artisan migrate</code> @lang('once to save your messages.')
        </div>
        @endif

        <form action="{{ route('admin.setting.stock.order.messages.submit') }}" method="post" id="stockOrderMessagesForm">
            @csrf

            {{-- Section 1: When order fails (stock out) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-exclamation-circle text-warning me-2"></i>@lang('When customer tries to order but product is out of stock')</h6>
                </div>
                <div class="card-body">
                    <label class="form-label fw-semibold">@lang('Message shown to customer')</label>
                    <p class="text-muted small mb-2">@lang('Shown on checkout, cart, or when they click Buy Now.')</p>
                    <textarea name="stock_out_user_message" class="form-control" rows="3" placeholder="{{ $defaults['stock_out_user_message'] ?? '' }}">{{ $userMsg }}</textarea>
                </div>
            </div>

            {{-- Section 2: Admin notification (text only; list is on Notifications page) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-bell text-primary me-2"></i>@lang('Admin notification when customer tries to order out-of-stock')</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">@lang('This page only edits the message text. The list of notifications is not here — view it in') <a href="{{ route('admin.notifications') }}" class="fw-semibold">@lang('Manage Orders') → @lang('Notifications')</a>.</p>
                    <label class="form-label fw-semibold">@lang('Message text (product name is added automatically before this)')</label>
                    <input type="text" name="stock_out_admin_message" class="form-control" value="{{ $adminMsg }}" placeholder="{{ $defaults['stock_out_admin_message'] ?? '' }}" maxlength="500">
                </div>
            </div>

            {{-- Section 3: Restock – In-app --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-box text-success me-2"></i>@lang('When product is back in stock – In-app notification')</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="restock_notify_enable" value="0">
                        <input type="checkbox" class="form-check-input" name="restock_notify_enable" value="1" id="restock_notify_enable" {{ ($hasColumns && ($g->restock_notify_enable ?? 1)) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="restock_notify_enable">@lang('Notify users (Cart / Compare / Wishlist) when stock is added')</label>
                    </div>
                    <label class="form-label">@lang('Message template')</label>
                    <p class="text-muted small mb-2">@lang('Use') <code>{product_name}</code> @lang('and') <code>{product_url}</code></p>
                    <textarea name="restock_message_template" class="form-control" rows="2" placeholder="{{ $defaults['restock_message_template'] ?? '' }}">{{ $restockMsg }}</textarea>
                </div>
            </div>

            {{-- Section 4: WhatsApp & Telegram (after migration) --}}
            @if($hasSocial)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="lab la-whatsapp text-success me-2"></i><i class="lab la-telegram text-info me-2"></i>@lang('Restock alert via WhatsApp & Telegram')</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-4">@lang('When you add stock to a product, users who have it in Cart/Compare/Wishlist can receive a message on WhatsApp or Telegram. You need to configure API keys (e.g. Twilio for WhatsApp, Telegram Bot token) in your app or env; this page only controls the message text and on/off.')</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="restock_whatsapp_enable" value="0">
                                <input type="checkbox" class="form-check-input" name="restock_whatsapp_enable" value="1" id="restock_whatsapp_enable" {{ ($g->restock_whatsapp_enable ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="restock_whatsapp_enable">@lang('Send restock alert via WhatsApp')</label>
                            </div>
                            <textarea name="restock_whatsapp_message" class="form-control form-control-sm" rows="2" placeholder="{{ $defaults['restock_whatsapp_message'] ?? '' }}">{{ $waMsg }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="restock_telegram_enable" value="0">
                                <input type="checkbox" class="form-check-input" name="restock_telegram_enable" value="1" id="restock_telegram_enable" {{ ($g->restock_telegram_enable ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="restock_telegram_enable">@lang('Send restock alert via Telegram')</label>
                            </div>
                            <textarea name="restock_telegram_message" class="form-control form-control-sm" rows="2" placeholder="{{ $defaults['restock_telegram_message'] ?? '' }}">{{ $tgMsg }}</textarea>
                        </div>
                    </div>
                    <p class="small mb-0">@lang('Placeholders') <code>{product_name}</code>, <code>{product_url}</code>. @lang('WhatsApp/Telegram sending will work after API integration is set up.')</p>
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">
                    <button type="submit" class="btn btn--primary btn-lg" id="stockOrderMessagesSubmitBtn"><i class="las la-save me-1"></i> @lang('Save all messages')</button>
                </div>
            </div>
        </form>

        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <h6 class="fw-bold mb-2">@lang('Where these are used')</h6>
                <ul class="small mb-0 text-muted">
                    <li>@lang('User message') → @lang('Checkout, Cart, Guest checkout, Payment – when order fails due to stock out.')</li>
                    <li>@lang('Admin stock-out alerts') → @lang('Notifications are listed in') <a href="{{ route('admin.notifications') }}">@lang('Manage Orders') → @lang('Notifications')</a> @lang('(not on this page). This page only edits the message text.')</li>
                    <li>@lang('Restock') → @lang('When you add stock via') <a href="{{ route('admin.product.index') }}">@lang('Product')</a> @lang('(e.g. product/edit/8), users with that product in cart/wishlist/compare get in-app notification automatically.')</li>
                    @if($hasSocial)
                    <li>@lang('WhatsApp/Telegram') → @lang('If enabled above, restock message is sent to users who have WhatsApp/Telegram in profile; requires API setup.')</li>
                    @else
                    <li><span class="text-muted">@lang('Run') <code>php artisan migrate</code> @lang('to see WhatsApp & Telegram options above.')</span></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
