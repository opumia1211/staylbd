@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                <h5 class="card-title mb-0"><i class="las la-shoe-prints me-2"></i>{{ $pageTitle }}</h5>
                <a href="{{ route('admin.frontend.sections.footer') }}" class="btn btn-sm btn-outline--primary"><i class="las la-list me-1"></i> @lang('Section list')</a>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">@lang('Control all footer sections from here. Each tab saves independently. Or use the section list to edit one section per page.')</p>

                <ul class="nav nav-tabs nav-tabs--primary mb-4" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#company">@lang('Company Info')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#quicklinks">@lang('Quick Links')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#support">@lang('Support Center')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#badges">@lang('Security Badges')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">@lang('Payment & Shipping')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#app">@lang('App Promotion')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#customads">@lang('Custom Ads')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#returnpolicy">@lang('Return Policy Form')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#newsletter">@lang('Newsletter & Copyright')</a></li>
                </ul>

                <div class="tab-content">
                    {{-- 1. Company Info --}}
                    <div class="tab-pane fade show active" id="company">
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
                            @csrf
                            <input type="hidden" name="section" value="company_info">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Show Company Block')</label>
                                <select name="show" class="form-select">
                                    <option value="1" {{ (optional($companyInfo)->data_values->show ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                                    <option value="0" {{ !(optional($companyInfo)->data_values->show ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('About Company (Short Description)')</label>
                                <textarea name="about_text" class="form-control" rows="3">{{ optional($companyInfo)->data_values->about_text ?? '' }}</textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Brand Mission / Trust Message')</label>
                                <textarea name="mission_text" class="form-control" rows="2">{{ optional($companyInfo)->data_values->mission_text ?? '' }}</textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Company Registration Info')</label>
                                <input type="text" name="registration_info" class="form-control" value="{{ optional($companyInfo)->data_values->registration_info ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Business / Trade License')</label>
                                <input type="text" name="business_license" class="form-control" value="{{ optional($companyInfo)->data_values->business_license ?? '' }}">
                            </div>
                            <button type="submit" class="btn btn--primary">@lang('Save Company Info')</button>
                        </form>
                    </div>

                    {{-- 2. Quick Links --}}
                    <div class="tab-pane fade" id="quicklinks">
                        <p class="text-muted small mb-3">@lang('Add footer navigation links (About Us, Contact, FAQ, etc.).')</p>
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveQuickLink') }}" class="mb-4 p-3 bg-light rounded">
                            @csrf
                            <input type="hidden" name="id" id="quick_link_id">
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" name="title" class="form-control" placeholder="@lang('Title')" required></div>
                                <div class="col-md-4"><input type="text" name="url" class="form-control" placeholder="@lang('URL')"></div>
                                <div class="col-md-2"><input type="number" name="display_order" class="form-control" placeholder="@lang('Order')" value="0"></div>
                                <div class="col-md-2"><button type="submit" class="btn btn--primary w-100">@lang('Add / Update')</button></div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table--light">
                                <thead><tr><th>@lang('Order')</th><th>@lang('Title')</th><th>@lang('URL')</th><th>@lang('Action')</th></tr></thead>
                                <tbody>
                                    @forelse($quickLinks as $link)
                                        @php $dv = $link->data_values ?? (object)[]; @endphp
                                        <tr>
                                            <td>{{ $dv->display_order ?? 0 }}</td>
                                            <td>{{ __($dv->title ?? '') }}</td>
                                            <td><small>{{ $dv->url ?? '#' }}</small></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline--primary edit-quick-link" data-id="{{ $link->id }}" data-title="{{ $dv->title ?? '' }}" data-url="{{ $dv->url ?? '' }}" data-order="{{ $dv->display_order ?? 0 }}"><i class="las la-pen"></i></button>
                                                <form action="{{ route('admin.frontend.sections.footer.deleteQuickLink', $link->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Remove this link?')');">@csrf @method('POST')<button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button></form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted">@lang('No quick links yet.')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 3. Support Center --}}
                    <div class="tab-pane fade" id="support">
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
                            @csrf
                            <input type="hidden" name="section" value="support_center">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Enable Support Center in Footer')</label>
                                <select name="enabled" class="form-select">
                                    <option value="1" {{ (optional($supportCenter)->data_values->enabled ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                                    <option value="0" {{ !(optional($supportCenter)->data_values->enabled ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Help Center URL')</label>
                                <input type="url" name="help_center_url" class="form-control" value="{{ optional($supportCenter)->data_values->help_center_url ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Return Policy URL')</label>
                                <input type="url" name="return_policy_url" class="form-control" value="{{ optional($supportCenter)->data_values->return_policy_url ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Refund Policy URL')</label>
                                <input type="url" name="refund_policy_url" class="form-control" value="{{ optional($supportCenter)->data_values->refund_policy_url ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Track Order URL')</label>
                                <input type="url" name="track_order_url" class="form-control" value="{{ optional($supportCenter)->data_values->track_order_url ?? '' }}" placeholder="{{ route('user.order.index') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Support Email (shown to users)')</label>
                                <input type="email" name="support_email" class="form-control" value="{{ optional($supportCenter)->data_values->support_email ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Show Support Ticket Link')</label>
                                <select name="support_ticket_enabled" class="form-select">
                                    <option value="1" {{ (optional($supportCenter)->data_values->support_ticket_enabled ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                                    <option value="0" {{ !(optional($supportCenter)->data_values->support_ticket_enabled ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Live Chat Button (Enable)')</label>
                                <select name="live_chat_enabled" class="form-select">
                                    <option value="0" {{ !(optional($supportCenter)->data_values->live_chat_enabled ?? 0) ? 'selected' : '' }}>@lang('No')</option>
                                    <option value="1" {{ (optional($supportCenter)->data_values->live_chat_enabled ?? 0) ? 'selected' : '' }}>@lang('Yes')</option>
                                </select>
                                <small class="text-muted">@lang('If you use a live chat widget, enable here; widget script can be added in Custom CSS/JS or theme.')</small>
                            </div>
                            <button type="submit" class="btn btn--primary">@lang('Save Support Center')</button>
                        </form>
                    </div>

                    {{-- 4. Security Badges --}}
                    <div class="tab-pane fade" id="badges">
                        <p class="text-muted small mb-3">@lang('Upload trust badges (SSL Secure, Buyer Protection, etc.). Accepted: JPG, PNG, WebP, GIF (max 2MB).')</p>
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSecurityBadge') }}" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded">
                            @csrf
                            <input type="hidden" name="id" id="badge_id">
                            <div class="row g-2">
                                <div class="col-md-2"><input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"></div>
                                <div class="col-md-2"><input type="text" name="title" class="form-control" placeholder="@lang('Title')"></div>
                                <div class="col-md-4"><input type="url" name="url" class="form-control" placeholder="@lang('Verification URL')"></div>
                                <div class="col-md-2"><input type="number" name="display_order" class="form-control" value="0"></div>
                                <div class="col-md-2"><button type="submit" class="btn btn--primary w-100">@lang('Add / Update')</button></div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table--light">
                                <thead><tr><th>@lang('Image')</th><th>@lang('Title')</th><th>@lang('Order')</th><th>@lang('Action')</th></tr></thead>
                                <tbody>
                                    @forelse($securityBadges as $badge)
                                        @php $dv = $badge->data_values ?? (object)[]; $img = $dv->image ?? null; @endphp
                                        <tr>
                                            <td>@if($img)<img src="{{ getImage('assets/images/frontend/footer/' . $img, '80x80') }}" alt="" width="50">@else &mdash; @endif</td>
                                            <td>{{ $dv->title ?? '—' }}</td>
                                            <td>{{ $dv->display_order ?? 0 }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline--primary edit-badge" data-id="{{ $badge->id }}" data-title="{{ $dv->title ?? '' }}" data-url="{{ $dv->url ?? '' }}" data-order="{{ $dv->display_order ?? 0 }}"><i class="las la-pen"></i></button>
                                                <form action="{{ route('admin.frontend.sections.footer.deleteSecurityBadge', $badge->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Remove this badge?')');">@csrf @method('POST')<button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button></form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted">@lang('No security badges yet.')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 5. Payment & Shipping --}}
                    <div class="tab-pane fade" id="payment">
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
                            @csrf
                            <input type="hidden" name="section" value="shipping_payment">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Show Payment Icons in Footer')</label>
                                <select name="show_payment_icons" class="form-select">
                                    <option value="1" {{ (optional($shippingPayment)->data_values->show_payment_icons ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                                    <option value="0" {{ !(optional($shippingPayment)->data_values->show_payment_icons ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                                </select>
                            </div>
                            <p class="text-muted small">@lang('Payment method icons are in') <a href="{{ route('admin.frontend.sections.footer.section', 'payment-shipping') }}#payment-methods">@lang('Payment & Shipping')</a>.</p>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Show Shipping / Delivery Info')</label>
                                <select name="show_shipping_info" class="form-select">
                                    <option value="1" {{ (optional($shippingPayment)->data_values->show_shipping_info ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                                    <option value="0" {{ !(optional($shippingPayment)->data_values->show_shipping_info ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Show Cash on Delivery')</label>
                                <select name="cod_enabled" class="form-select">
                                    <option value="1" {{ (optional($shippingPayment)->data_values->cod_enabled ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                                    <option value="0" {{ !(optional($shippingPayment)->data_values->cod_enabled ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Estimated Delivery Time (e.g. 3-5 days)')</label>
                                <input type="text" name="estimated_delivery_text" class="form-control" value="{{ optional($shippingPayment)->data_values->estimated_delivery_text ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Shipping Partners Text')</label>
                                <input type="text" name="shipping_partners_text" class="form-control" value="{{ optional($shippingPayment)->data_values->shipping_partners_text ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Delivery Zones Text')</label>
                                <input type="text" name="delivery_zones_text" class="form-control" value="{{ optional($shippingPayment)->data_values->delivery_zones_text ?? '' }}">
                            </div>
                            <button type="submit" class="btn btn--primary">@lang('Save Payment & Shipping')</button>
                        </form>
                    </div>

                    {{-- 6. App Promotion --}}
                    <div class="tab-pane fade" id="app">
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="section" value="app_promotion">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Show App Promotion Section')</label>
                                <select name="enabled" class="form-select">
                                    <option value="0" {{ !(optional($appPromotion)->data_values->enabled ?? 0) ? 'selected' : '' }}>@lang('No')</option>
                                    <option value="1" {{ (optional($appPromotion)->data_values->enabled ?? 0) ? 'selected' : '' }}>@lang('Yes')</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Android App (Play Store) URL')</label>
                                <input type="url" name="android_url" class="form-control" value="{{ optional($appPromotion)->data_values->android_url ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('iOS App (App Store) URL')</label>
                                <input type="url" name="ios_url" class="form-control" value="{{ optional($appPromotion)->data_values->ios_url ?? '' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('QR Code Image')</label>
                                <div class="d-flex align-items-center gap-3">
                                    @if(!empty(optional($appPromotion)->data_values->qr_image))
                                        <img src="{{ getImage('assets/images/frontend/footer/' . optional($appPromotion)->data_values->qr_image) }}" alt="QR" width="80">
                                    @endif
                                    <input type="file" name="qr_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                                    <small class="text-muted d-block">@lang('JPG, PNG, WebP, GIF — max 2MB')</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary">@lang('Save App Promotion')</button>
                        </form>
                    </div>

                    {{-- 7. Custom Ads --}}
                    <div class="tab-pane fade" id="customads">
                        <p class="text-muted small mb-3">@lang('Run custom ads in the footer. Upload image, optional link and title. Accepted: JPG, PNG, WebP, GIF (max 2MB).')</p>
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveCustomAd') }}" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded">
                            @csrf
                            <input type="hidden" name="id" id="custom_ad_id">
                            <div class="row g-2">
                                <div class="col-md-2"><input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"></div>
                                <div class="col-md-2"><input type="text" name="title" class="form-control" placeholder="@lang('Title')"></div>
                                <div class="col-md-4"><input type="url" name="url" class="form-control" placeholder="@lang('Ad URL')"></div>
                                <div class="col-md-2"><input type="number" name="display_order" class="form-control" value="0" placeholder="@lang('Order')"></div>
                                <div class="col-md-2"><button type="submit" class="btn btn--primary w-100">@lang('Add / Update')</button></div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table--light">
                                <thead><tr><th>@lang('Image')</th><th>@lang('Title')</th><th>@lang('URL')</th><th>@lang('Order')</th><th>@lang('Action')</th></tr></thead>
                                <tbody>
                                    @forelse($customAds ?? [] as $ad)
                                        @php $dv = $ad->data_values ?? (object)[]; $img = $dv->image ?? null; @endphp
                                        <tr>
                                            <td>@if($img)<img src="{{ getImage('assets/images/frontend/footer/' . $img) }}" alt="" width="60" height="40">@else &mdash; @endif</td>
                                            <td>{{ $dv->title ?? '—' }}</td>
                                            <td><small>{{ $dv->url ?? '—' }}</small></td>
                                            <td>{{ $dv->display_order ?? 0 }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline--primary edit-custom-ad" data-id="{{ $ad->id }}" data-title="{{ $dv->title ?? '' }}" data-url="{{ $dv->url ?? '' }}" data-order="{{ $dv->display_order ?? 0 }}"><i class="las la-pen"></i></button>
                                                <form action="{{ route('admin.frontend.sections.footer.deleteCustomAd', $ad->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Remove this ad?')');">@csrf @method('POST')<button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button></form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-muted">@lang('No custom ads yet.')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 8. Return Policy Form --}}
                    <div class="tab-pane fade" id="returnpolicy">
                        <p class="text-muted small mb-3">@lang('Show a professional return request form in the footer. Submissions create a support ticket (Product Return Request).')</p>
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveReturnPolicy') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Show Return Policy Form in Footer')</label>
                                <select name="show_form" class="form-select">
                                    <option value="1" {{ (optional($returnPolicy)->data_values->show_form ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                                    <option value="0" {{ !(optional($returnPolicy)->data_values->show_form ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Form Title')</label>
                                <input type="text" name="form_title" class="form-control" value="{{ optional($returnPolicy)->data_values->form_title ?? __('Product Return Request') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Success Message (after submit)')</label>
                                <input type="text" name="success_message" class="form-control" value="{{ optional($returnPolicy)->data_values->success_message ?? __('We have received your return request. Our team will contact you shortly.') }}">
                            </div>
                            <button type="submit" class="btn btn--primary">@lang('Save Return Policy Settings')</button>
                        </form>
                    </div>

                    {{-- 9. Newsletter & Social + Payment/Bank Icons --}}
                    <div class="tab-pane fade" id="newsletter">
                        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
                            @csrf
                            <input type="hidden" name="section" value="footer_content">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Newsletter Title')</label>
                                <input type="text" name="subscribe_title" class="form-control" value="{{ optional($footerContent)->data_values->subscribe_title ?? __('Subscribe to our newsletter') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Newsletter Subtitle')</label>
                                <input type="text" name="subscribe_subtitle" class="form-control" value="{{ optional($footerContent)->data_values->subscribe_subtitle ?? __('Subscribe for new Offers and updates') }}" placeholder="@lang('e.g. Subscribe for new Offers and updates')">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Connect / Social Title')</label>
                                <input type="text" name="connect_title" class="form-control" value="{{ optional($footerContent)->data_values->connect_title ?? __('Find Us') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Copyright Text (Bottom Bar)')</label>
                                <input type="text" name="copyright_text" class="form-control" value="{{ optional($footerContent)->data_values->copyright_text ?? '' }}" placeholder="@lang('e.g. Copyright © {year} All Right Reserved')">
                                <small class="text-muted">@lang('Use {year} for current year. Leave blank for default.')</small>
                            </div>
                            <div class="form-group mb-3 border-top pt-3">
                                <label class="form-label">@lang('Footer — Seller account')</label>
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="seller_account_enabled" value="1" class="form-check-input" id="fb_seller_account_enabled" {{ (int)(optional($footerContent)->data_values->seller_account_enabled ?? 0) === 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fb_seller_account_enabled">@lang('Enable seller signup (custom URL or built-in placeholder page)')</label>
                                </div>
                                <input type="text" name="seller_account_url" class="form-control" value="{{ optional($footerContent)->data_values->seller_account_url ?? '' }}" placeholder="@lang('Optional: full URL or path, e.g. seller/apply')">
                                <small class="text-muted">@lang('When disabled, Seller account opens live contact. External URLs open in a new tab.')</small>
                            </div>
                            <button type="submit" class="btn btn--primary">@lang('Save Newsletter, Social & Copyright')</button>
                        </form>
                        <p class="mt-3 text-muted small mb-0">@lang('Payment method / bank icons:') <a href="{{ route('admin.frontend.sections.footer.section', 'payment-shipping') }}#payment-methods">@lang('Payment & Shipping')</a>. @lang('Social icons:') <a href="{{ route('admin.frontend.sections.social_icon') }}">@lang('Social Icons')</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('script')
<script>
(function($){
    $('.edit-quick-link').on('click', function(){
        var id = $(this).data('id'), title = $(this).data('title'), url = $(this).data('url'), order = $(this).data('order');
        $('#quicklinks input[name="id"]').val(id);
        $('#quicklinks input[name="title"]').val(title);
        $('#quicklinks input[name="url"]').val(url);
        $('#quicklinks input[name="display_order"]').val(order);
        $('a[href="#quicklinks"]').tab('show');
    });
    $('.edit-badge').on('click', function(){
        var id = $(this).data('id'), title = $(this).data('title'), url = $(this).data('url'), order = $(this).data('order');
        $('#badge_id').val(id);
        $('#badges input[name="title"]').val(title);
        $('#badges input[name="url"]').val(url);
        $('#badges input[name="display_order"]').val(order);
        $('a[href="#badges"]').tab('show');
    });
    $('.edit-custom-ad').on('click', function(){
        var id = $(this).data('id'), title = $(this).data('title'), url = $(this).data('url'), order = $(this).data('order');
        $('#custom_ad_id').val(id);
        $('#customads input[name="title"]').val(title || '');
        $('#customads input[name="url"]').val(url || '');
        $('#customads input[name="display_order"]').val(order || 0);
        $('a[href="#customads"]').tab('show');
    });
})(jQuery);
</script>
@endpush
