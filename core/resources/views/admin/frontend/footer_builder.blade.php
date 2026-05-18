@extends('admin.layouts.app')
@section('panel')
<div class="footer-builder-wrapper animate__animated animate__fadeIn">
    {{-- Top Action Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-primary shadow-sm"><i class="las la-shoe-prints fs-3"></i></span>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">@lang('Footer Architecture')</h5>
                    <p class="text-muted small mb-0">@lang('Orchestrate the global footer ecosystem, navigation nodes, and trust signals.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.frontend.sections.footer') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill">
                <i class="las la-list me-1"></i> @lang('Section Matrix')
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="row g-0">
                {{-- Sidebar Navigation --}}
                <div class="col-xl-3 col-lg-4 border-end bg-light-soft">
                    <div class="p-4">
                        <h6 class="text-uppercase text-muted tiny fw-bold mb-3" style="letter-spacing: 1px;">@lang('Navigation Hub')</h6>
                        <div class="nav nav-pills flex-column modern-pills" id="footerTabs" role="tablist">
                            <button class="nav-link active d-flex align-items-center py-3 px-3 mb-2" data-bs-toggle="tab" data-bs-target="#company" type="button">
                                <i class="las la-building fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('Company Profile')</span>
                                    <span class="tiny text-muted">@lang('Identity & Mission')</span>
                                </div>
                            </button>
                            <button class="nav-link d-flex align-items-center py-3 px-3 mb-2" data-bs-toggle="tab" data-bs-target="#quicklinks" type="button">
                                <i class="las la-link fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('Navigation Nodes')</span>
                                    <span class="tiny text-muted">@lang('Quick access links')</span>
                                </div>
                            </button>
                            <button class="nav-link d-flex align-items-center py-3 px-3 mb-2" data-bs-toggle="tab" data-bs-target="#support" type="button">
                                <i class="las la-headset fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('Support Matrix')</span>
                                    <span class="tiny text-muted">@lang('Help & policies')</span>
                                </div>
                            </button>
                            <button class="nav-link d-flex align-items-center py-3 px-3 mb-2" data-bs-toggle="tab" data-bs-target="#badges" type="button">
                                <i class="las la-certificate fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('Trust Signals')</span>
                                    <span class="tiny text-muted">@lang('Security badges')</span>
                                </div>
                            </button>
                            <button class="nav-link d-flex align-items-center py-3 px-3 mb-2" data-bs-toggle="tab" data-bs-target="#payment" type="button">
                                <i class="las la-credit-card fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('Payment & Logistics')</span>
                                    <span class="tiny text-muted">@lang('Icons & delivery')</span>
                                </div>
                            </button>
                            <button class="nav-link d-flex align-items-center py-3 px-3 mb-2" data-bs-toggle="tab" data-bs-target="#app" type="button">
                                <i class="las la-mobile fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('App Deployment')</span>
                                    <span class="tiny text-muted">@lang('Store promotions')</span>
                                </div>
                            </button>
                            <button class="nav-link d-flex align-items-center py-3 px-3 mb-2" data-bs-toggle="tab" data-bs-target="#customads" type="button">
                                <i class="las la-ad fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('Internal Ads')</span>
                                    <span class="tiny text-muted">@lang('Custom promotions')</span>
                                </div>
                            </button>
                            <button class="nav-link d-flex align-items-center py-3 px-3 mb-2" data-bs-toggle="tab" data-bs-target="#returnpolicy" type="button">
                                <i class="las la-undo fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('Return Protocol')</span>
                                    <span class="tiny text-muted">@lang('Request form flow')</span>
                                </div>
                            </button>
                            <button class="nav-link d-flex align-items-center py-3 px-3" data-bs-toggle="tab" data-bs-target="#newsletter" type="button">
                                <i class="las la-paper-plane fs-4 me-2"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold small">@lang('Communication')</span>
                                    <span class="tiny text-muted">@lang('Newsletter & Copyright')</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Content Area --}}
                <div class="col-xl-9 col-lg-8">
                    <div class="tab-content p-4 p-xl-5">
                        {{-- 1. Company Profile --}}
                        <div class="tab-pane fade show active animate__animated animate__fadeIn" id="company">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-primary p-2 me-3 rounded-3">
                                    <i class="las la-building fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Company Profile')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Configure the primary identity block for the global footer.')</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
                                @csrf
                                <input type="hidden" name="section" value="company_info">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Show Block')</label>
                                        <select name="show" class="form-select rounded-3">
                                            <option value="1" {{ (optional($companyInfo)->data_values->show ?? 1) ? 'selected' : '' }}>@lang('Yes, visible')</option>
                                            <option value="0" {{ !(optional($companyInfo)->data_values->show ?? 1) ? 'selected' : '' }}>@lang('No, hidden')</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark small">@lang('Identity Description')</label>
                                        <textarea name="about_text" class="form-control rounded-4" rows="3" placeholder="@lang('Short overview of the company...')">{{ optional($companyInfo)->data_values->about_text ?? '' }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark small">@lang('Brand Mission')</label>
                                        <textarea name="mission_text" class="form-control rounded-4" rows="2" placeholder="@lang('Your brand promise...')">{{ optional($companyInfo)->data_values->mission_text ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Registration Info')</label>
                                        <input type="text" name="registration_info" class="form-control rounded-3" value="{{ optional($companyInfo)->data_values->registration_info ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Trade License')</label>
                                        <input type="text" name="business_license" class="form-control rounded-3" value="{{ optional($companyInfo)->data_values->business_license ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Primary Phone')</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="las la-phone"></i></span>
                                            <input type="text" name="contact_phone" class="form-control" value="{{ optional($companyInfo)->data_values->contact_phone ?? '+1 202-555-0178' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Public Email')</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="las la-envelope"></i></span>
                                            <input type="email" name="contact_email" class="form-control" value="{{ optional($companyInfo)->data_values->contact_email ?? 'support@staylbd.com' }}">
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">@lang('Update Identity')</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- 2. Navigation Nodes --}}
                        <div class="tab-pane fade animate__animated animate__fadeIn" id="quicklinks">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-success p-2 me-3 rounded-3">
                                    <i class="las la-link fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Navigation Nodes')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Manage quick access links for site navigation.')</p>
                                </div>
                            </div>
                            
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveQuickLink') }}" class="mb-4 p-4 bg-label-success rounded-4 border border-success border-opacity-10 shadow-sm">
                                @csrf
                                <input type="hidden" name="id" id="quick_link_id">
                                <h6 class="tiny fw-bold text-success text-uppercase mb-3">@lang('Node Configuration')</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Label')</label>
                                        <input type="text" name="title" class="form-control form-control-sm rounded-3" placeholder="@lang('About Us')" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Target URL')</label>
                                        <input type="text" name="url" class="form-control form-control-sm rounded-3" placeholder="@lang('/about-us')">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Order')</label>
                                        <input type="number" name="display_order" class="form-control form-control-sm rounded-3" value="0">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill shadow-sm">@lang('Save Node')</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3 ps-4 text-uppercase tiny fw-bold">@lang('Order')</th>
                                            <th class="py-3 text-uppercase tiny fw-bold">@lang('Label')</th>
                                            <th class="py-3 text-uppercase tiny fw-bold">@lang('Destination')</th>
                                            <th class="py-3 pe-4 text-center text-uppercase tiny fw-bold">@lang('Actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($quickLinks as $link)
                                            @php $dv = $link->data_values ?? (object)[]; @endphp
                                            <tr>
                                                <td class="ps-4"><span class="badge bg-label-secondary rounded-pill">{{ $dv->display_order ?? 0 }}</span></td>
                                                <td><span class="fw-bold text-dark small">{{ __($dv->title ?? '') }}</span></td>
                                                <td><code class="tiny text-muted">{{ $dv->url ?? '#' }}</code></td>
                                                <td class="pe-4 text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-icon btn-sm btn-label-primary edit-quick-link" data-id="{{ $link->id }}" data-title="{{ $dv->title ?? '' }}" data-url="{{ $dv->url ?? '' }}" data-order="{{ $dv->display_order ?? 0 }}">
                                                            <i class="las la-pen"></i>
                                                        </button>
                                                        <form action="{{ route('admin.frontend.sections.footer.deleteQuickLink', $link->id) }}" method="POST" class="d-inline confirm-delete">
                                                            @csrf
                                                            <button type="submit" class="btn btn-icon btn-sm btn-label-danger"><i class="las la-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-5 text-muted opacity-50">@lang('No active nodes identified.')</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 3. Support Matrix --}}
                        <div class="tab-pane fade animate__animated animate__fadeIn" id="support">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-info p-2 me-3 rounded-3">
                                    <i class="las la-headset fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Support Matrix')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Link critical help channels and organizational policies.')</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
                                @csrf
                                <input type="hidden" name="section" value="support_center">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Matrix Visibility')</label>
                                        <select name="enabled" class="form-select rounded-3">
                                            <option value="1" {{ (optional($supportCenter)->data_values->enabled ?? 1) ? 'selected' : '' }}>@lang('Active')</option>
                                            <option value="0" {{ !(optional($supportCenter)->data_values->enabled ?? 1) ? 'selected' : '' }}>@lang('Offline')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Support Email')</label>
                                        <input type="email" name="support_email" class="form-control rounded-3" value="{{ optional($supportCenter)->data_values->support_email ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Help Center Node')</label>
                                        <input type="url" name="help_center_url" class="form-control rounded-3" value="{{ optional($supportCenter)->data_values->help_center_url ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Order Tracking Node')</label>
                                        <input type="url" name="track_order_url" class="form-control rounded-3" value="{{ optional($supportCenter)->data_values->track_order_url ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Return Policy Node')</label>
                                        <input type="url" name="return_policy_url" class="form-control rounded-3" value="{{ optional($supportCenter)->data_values->return_policy_url ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Refund Policy Node')</label>
                                        <input type="url" name="refund_policy_url" class="form-control rounded-3" value="{{ optional($supportCenter)->data_values->refund_policy_url ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-4 border border-dashed text-center">
                                            <h6 class="tiny fw-bold text-muted text-uppercase mb-2">@lang('Support Ticket Flow')</h6>
                                            <div class="form-check form-switch modern-switch d-inline-block">
                                                <input type="hidden" name="support_ticket_enabled" value="0">
                                                <input class="form-check-input" type="checkbox" name="support_ticket_enabled" value="1" id="support_ticket_enabled" {{ (optional($supportCenter)->data_values->support_ticket_enabled ?? 1) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-label-primary rounded-4 border border-primary border-opacity-10 text-center">
                                            <h6 class="tiny fw-bold text-primary text-uppercase mb-2">@lang('Live Conversational Agent')</h6>
                                            <div class="form-check form-switch modern-switch d-inline-block">
                                                <input type="hidden" name="live_chat_enabled" value="0">
                                                <input class="form-check-input" type="checkbox" name="live_chat_enabled" value="1" id="live_chat_enabled" {{ (optional($supportCenter)->data_values->live_chat_enabled ?? 0) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-info text-white px-4 rounded-pill shadow-sm">@lang('Sync Support Matrix')</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- 4. Trust Signals --}}
                        <div class="tab-pane fade animate__animated animate__fadeIn" id="badges">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-warning p-2 me-3 rounded-3">
                                    <i class="las la-certificate fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Trust Signals')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Deploy security and authenticity badges to build user confidence.')</p>
                                </div>
                            </div>
                            
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSecurityBadge') }}" enctype="multipart/form-data" class="mb-4 p-4 bg-label-warning rounded-4 border border-warning border-opacity-10 shadow-sm">
                                @csrf
                                <input type="hidden" name="id" id="badge_id">
                                <h6 class="tiny fw-bold text-warning text-uppercase mb-3">@lang('Signal Configuration')</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Asset Upload')</label>
                                        <input type="file" name="image" class="form-control form-control-sm rounded-3" accept=".jpg,.jpeg,.png,.webp,.gif">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Signal Title')</label>
                                        <input type="text" name="title" class="form-control form-control-sm rounded-3" placeholder="@lang('SSL Secure')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Verification URL')</label>
                                        <input type="url" name="url" class="form-control form-control-sm rounded-3" placeholder="https://...">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Order')</label>
                                        <input type="number" name="display_order" class="form-control form-control-sm rounded-3" value="0">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill shadow-sm">@lang('Deploy Signal')</button>
                                    </div>
                                </div>
                            </form>

                            <div class="row g-4">
                                @forelse($securityBadges as $badge)
                                    @php $dv = $badge->data_values ?? (object)[]; $img = $dv->image ?? null; @endphp
                                    <div class="col-md-6 col-xl-4">
                                        <div class="trust-signal-card p-3 rounded-4 border bg-white shadow-sm transition-all position-relative overflow-hidden">
                                            <div class="d-flex align-items-center">
                                                <div class="signal-asset me-3 border rounded-3 p-1" style="width: 60px; height: 60px;">
                                                    @if($img)
                                                        <img src="{{ getImage('assets/images/frontend/footer/' . $img, '80x80') }}" class="w-100 h-100 object-fit-contain">
                                                    @else
                                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light rounded-2"><i class="las la-image opacity-25"></i></div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-grow-1">
                                                    <h6 class="mb-0 small fw-bold text-dark text-truncate">{{ $dv->title ?? 'Untitled Signal' }}</h6>
                                                    <span class="badge bg-label-secondary tiny rounded-pill">Pos: {{ $dv->display_order ?? 0 }}</span>
                                                </div>
                                                <div class="d-flex gap-1 flex-shrink-0">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary edit-badge" data-id="{{ $badge->id }}" data-title="{{ $dv->title ?? '' }}" data-url="{{ $dv->url ?? '' }}" data-order="{{ $dv->display_order ?? 0 }}">
                                                        <i class="las la-pen"></i>
                                                    </button>
                                                    <form action="{{ route('admin.frontend.sections.footer.deleteSecurityBadge', $badge->id) }}" method="POST" class="d-inline confirm-delete">
                                                        @csrf
                                                        <button type="submit" class="btn btn-icon btn-sm btn-label-danger"><i class="las la-trash"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5 text-muted opacity-50">@lang('No trust signals deployed.')</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- 5. Payment & Logistics --}}
                        <div class="tab-pane fade animate__animated animate__fadeIn" id="payment">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-secondary p-2 me-3 rounded-3">
                                    <i class="las la-truck fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Payment & Logistics')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Configure payment node visibility and logistics information.')</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
                                @csrf
                                <input type="hidden" name="section" value="shipping_payment">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card bg-label-secondary border-0 p-3 rounded-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="small fw-bold text-dark mb-0">@lang('Payment Channels')</h6>
                                                <div class="form-check form-switch modern-switch">
                                                    <input type="hidden" name="show_payment_icons" value="0">
                                                    <input class="form-check-input" type="checkbox" name="show_payment_icons" value="1" id="show_payment_icons" {{ (optional($shippingPayment)->data_values->show_payment_icons ?? 1) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <p class="tiny text-muted mb-0">@lang('Display active payment gateways in the global footer.')</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-label-info border-0 p-3 rounded-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="small fw-bold text-dark mb-0">@lang('Logistics Info')</h6>
                                                <div class="form-check form-switch modern-switch">
                                                    <input type="hidden" name="show_shipping_info" value="0">
                                                    <input class="form-check-input" type="checkbox" name="show_shipping_info" value="1" id="show_shipping_info" {{ (optional($shippingPayment)->data_values->show_shipping_info ?? 1) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <p class="tiny text-muted mb-0">@lang('Show estimated delivery and partner information.')</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small">@lang('Cash on Delivery Flow')</label>
                                        <select name="cod_enabled" class="form-select rounded-3">
                                            <option value="1" {{ (optional($shippingPayment)->data_values->cod_enabled ?? 1) ? 'selected' : '' }}>@lang('Enabled')</option>
                                            <option value="0" {{ !(optional($shippingPayment)->data_values->cod_enabled ?? 1) ? 'selected' : '' }}>@lang('Disabled')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold text-dark small">@lang('Estimated Delivery window')</label>
                                        <input type="text" name="estimated_delivery_text" class="form-control rounded-3" value="{{ optional($shippingPayment)->data_values->estimated_delivery_text ?? '' }}" placeholder="e.g. 3-5 Business Days">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Shipping Partners Text')</label>
                                        <input type="text" name="shipping_partners_text" class="form-control rounded-3" value="{{ optional($shippingPayment)->data_values->shipping_partners_text ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Delivery Regions Node')</label>
                                        <input type="text" name="delivery_zones_text" class="form-control rounded-3" value="{{ optional($shippingPayment)->data_values->delivery_zones_text ?? '' }}">
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-secondary px-4 rounded-pill shadow-sm">@lang('Update Logistics')</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- 6. App Deployment --}}
                        <div class="tab-pane fade animate__animated animate__fadeIn" id="app">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-dark p-2 me-3 rounded-3">
                                    <i class="las la-mobile fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('App Deployment')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Deploy mobile application markers and cross-platform store links.')</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="section" value="app_promotion">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Promotion Visibility')</label>
                                        <select name="enabled" class="form-select rounded-3">
                                            <option value="1" {{ (optional($appPromotion)->data_values->enabled ?? 0) ? 'selected' : '' }}>@lang('Live')</option>
                                            <option value="0" {{ !(optional($appPromotion)->data_values->enabled ?? 0) ? 'selected' : '' }}>@lang('Archived')</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-dark small">@lang('Google Play Node')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white"><i class="lab la-android"></i></span>
                                                    <input type="url" name="android_url" class="form-control" value="{{ optional($appPromotion)->data_values->android_url ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-dark small">@lang('Apple App Store Node')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white"><i class="lab la-apple"></i></span>
                                                    <input type="url" name="ios_url" class="form-control" value="{{ optional($appPromotion)->data_values->ios_url ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-4 bg-light rounded-4 border border-dashed">
                                            <label class="form-label fw-bold text-dark small">@lang('Interaction QR Asset')</label>
                                            <div class="d-flex align-items-center gap-4">
                                                <div class="qr-asset-preview bg-white p-2 rounded-3 shadow-sm border" style="width: 100px; height: 100px;">
                                                    @if(!empty(optional($appPromotion)->data_values->qr_image))
                                                        <img src="{{ getImage('assets/images/frontend/footer/' . optional($appPromotion)->data_values->qr_image) }}" class="w-100 h-100 object-fit-contain">
                                                    @else
                                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="las la-qrcode fs-1"></i></div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="file" name="qr_image" class="form-control rounded-3" accept=".jpg,.jpeg,.png,.webp,.gif">
                                                    <small class="text-muted tiny mt-2 d-block">@lang('Recommended: High-resolution PNG with transparent background.')</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-dark px-4 rounded-pill shadow-sm">@lang('Update App Context')</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- 7. Internal Ads --}}
                        <div class="tab-pane fade animate__animated animate__fadeIn" id="customads">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-primary p-2 me-3 rounded-3">
                                    <i class="las la-ad fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Internal Ads')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Deploy localized marketing assets within the footer ecosystem.')</p>
                                </div>
                            </div>
                            
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveCustomAd') }}" enctype="multipart/form-data" class="mb-4 p-4 bg-label-primary rounded-4 border border-primary border-opacity-10 shadow-sm">
                                @csrf
                                <input type="hidden" name="id" id="custom_ad_id">
                                <h6 class="tiny fw-bold text-primary text-uppercase mb-3">@lang('Ad configuration')</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Visual asset')</label>
                                        <input type="file" name="image" class="form-control form-control-sm rounded-3">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Internal Label')</label>
                                        <input type="text" name="title" class="form-control form-control-sm rounded-3">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Redirect Target')</label>
                                        <input type="url" name="url" class="form-control form-control-sm rounded-3" placeholder="https://...">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label fw-bold text-dark tiny">@lang('Order')</label>
                                        <input type="number" name="display_order" class="form-control form-control-sm rounded-3" value="0">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill shadow-sm">@lang('Inject Ad')</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3 ps-4 text-uppercase tiny fw-bold">@lang('Visual')</th>
                                            <th class="py-3 text-uppercase tiny fw-bold">@lang('Internal Node')</th>
                                            <th class="py-3 text-uppercase tiny fw-bold">@lang('Destination')</th>
                                            <th class="py-3 pe-4 text-center text-uppercase tiny fw-bold">@lang('Actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($customAds ?? [] as $ad)
                                            @php $dv = $ad->data_values ?? (object)[]; $img = $dv->image ?? null; @endphp
                                            <tr>
                                                <td class="ps-4">
                                                    @if($img)
                                                        <img src="{{ getImage('assets/images/frontend/footer/' . $img) }}" class="rounded-2 border" style="width: 80px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-2 bg-light d-flex align-items-center justify-content-center border" style="width: 80px; height: 40px;"><i class="las la-image opacity-25"></i></div>
                                                    @endif
                                                </td>
                                                <td><span class="fw-bold text-dark small">{{ $dv->title ?? 'Untitled Ad' }}</span></td>
                                                <td><code class="tiny text-muted">{{ $dv->url ?? '—' }}</code></td>
                                                <td class="pe-4 text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-icon btn-sm btn-label-primary edit-custom-ad" data-id="{{ $ad->id }}" data-title="{{ $dv->title ?? '' }}" data-url="{{ $dv->url ?? '' }}" data-order="{{ $dv->display_order ?? 0 }}">
                                                            <i class="las la-pen"></i>
                                                        </button>
                                                        <form action="{{ route('admin.frontend.sections.footer.deleteCustomAd', $ad->id) }}" method="POST" class="d-inline confirm-delete">
                                                            @csrf
                                                            <button type="submit" class="btn btn-icon btn-sm btn-label-danger"><i class="las la-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-5 text-muted opacity-50">@lang('No marketing assets active.')</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 8. Return Protocol --}}
                        <div class="tab-pane fade animate__animated animate__fadeIn" id="returnpolicy">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-danger p-2 me-3 rounded-3">
                                    <i class="las la-undo fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Return Protocol')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Automate return requests through a dedicated interactive interface.')</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveReturnPolicy') }}">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Interface Status')</label>
                                        <select name="show_form" class="form-select rounded-3">
                                            <option value="1" {{ (optional($returnPolicy)->data_values->show_form ?? 1) ? 'selected' : '' }}>@lang('Publicly accessible')</option>
                                            <option value="0" {{ !(optional($returnPolicy)->data_values->show_form ?? 1) ? 'selected' : '' }}>@lang('Internal only')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold text-dark small">@lang('Interface Heading')</label>
                                        <input type="text" name="form_title" class="form-control rounded-3" value="{{ optional($returnPolicy)->data_values->form_title ?? __('Product Return Request') }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold text-dark small">@lang('Success Confirmation Script')</label>
                                        <textarea name="success_message" class="form-control rounded-4" rows="3">{{ optional($returnPolicy)->data_values->success_message ?? __('We have received your return request. Our team will contact you shortly.') }}</textarea>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-danger px-4 rounded-pill shadow-sm">@lang('Sync Return Logic')</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- 9. Communication --}}
                        <div class="tab-pane fade animate__animated animate__fadeIn" id="newsletter">
                            <div class="d-flex align-items-center mb-4">
                                <div class="badge bg-label-dark p-2 me-3 rounded-3">
                                    <i class="las la-paper-plane fs-3 text-dark"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Communication Architecture')</h6>
                                    <p class="text-muted tiny mb-0">@lang('Govern newsletter integration, copyright nodes, and account logic.')</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
                                @csrf
                                <input type="hidden" name="section" value="footer_content">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Newsletter Node Title')</label>
                                        <input type="text" name="subscribe_title" class="form-control rounded-3" value="{{ optional($footerContent)->data_values->subscribe_title ?? __('Subscribe to our newsletter') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Interaction Prompt')</label>
                                        <input type="text" name="subscribe_subtitle" class="form-control rounded-3" value="{{ optional($footerContent)->data_values->subscribe_subtitle ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Legal Copyright Node')</label>
                                        <input type="text" name="copyright_text" class="form-control rounded-3" value="{{ optional($footerContent)->data_values->copyright_text ?? '' }}" placeholder="Copyright © {year} All Rights Reserved">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">@lang('Social Connection Hub Title')</label>
                                        <input type="text" name="connect_title" class="form-control rounded-3" value="{{ optional($footerContent)->data_values->connect_title ?? '' }}">
                                    </div>
                                    
                                    <div class="col-12 pt-3 border-top mt-5">
                                        <div class="card bg-label-dark border-0 rounded-4">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="badge bg-white p-2 rounded-3 shadow-sm"><i class="las la-user-tie fs-3 text-dark"></i></div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="small fw-bold text-dark">@lang('Enterprise Merchant Portal')</h6>
                                                        <p class="tiny text-muted mb-3">@lang('Enable third-party merchant applications through the footer.')</p>
                                                        <div class="form-check form-switch modern-switch mb-3">
                                                            <input type="checkbox" name="seller_account_enabled" value="1" class="form-check-input" id="fb_seller_account_enabled" {{ (int)(optional($footerContent)->data_values->seller_account_enabled ?? 0) === 1 ? 'checked' : '' }}>
                                                            <label class="form-check-label text-dark fw-bold tiny" for="fb_seller_account_enabled">@lang('Activate Merchant Application Node')</label>
                                                        </div>
                                                        <input type="text" name="seller_account_url" class="form-control form-control-sm rounded-3" value="{{ optional($footerContent)->data_values->seller_account_url ?? '' }}" placeholder="@lang('Target URL for application...')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-dark px-4 rounded-pill shadow-sm">@lang('Commit Communication Nodes')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('style')
<style>
    :root {
        --premium-primary: #696cff;
        --pill-bg: rgba(105, 108, 255, 0.05);
    }

    .footer-builder-wrapper { font-family: 'Public Sans', sans-serif; }

    /* Sneat Style Tabs */
    .modern-pills .nav-link {
        border-radius: 8px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        color: #697a8d;
    }
    .modern-pills .nav-link:hover { background-color: var(--pill-bg); color: var(--premium-primary); }
    .modern-pills .nav-link.active {
        background-color: var(--premium-primary) !important;
        color: #fff !important;
        box-shadow: 0 2px 4px 0 rgba(105, 108, 255, 0.4);
    }
    .modern-pills .nav-link.active .tiny { color: rgba(255,255,255,0.7) !important; }
    .modern-pills .nav-link.active i { color: #fff !important; }

    .bg-light-soft { background-color: #fcfcfd; }
    .bg-label-primary { background-color: #e7e7ff !important; color: var(--premium-primary) !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-warning { background-color: #fff2e6 !important; color: #ff9f43 !important; }
    .bg-label-danger { background-color: #ffe5e5 !important; color: #ff3e1d !important; }
    .bg-label-secondary { background-color: #f5f5f9 !important; color: #8592a3 !important; }
    .bg-label-dark { background-color: #e1e4e8 !important; color: #435971 !important; }

    .avatar-md { width: 48px; height: 48px; }
    .avatar-xs { width: 28px; height: 28px; }

    .tiny { font-size: 0.75rem; }
    .modern-switch .form-check-input { width: 2.8rem; height: 1.4rem; cursor: pointer; }
    .modern-switch .form-check-input:checked { background-color: var(--premium-primary); border-color: var(--premium-primary); }

    .btn-label-primary { background: #e7e7ff; color: #696cff; border: none; }
    .btn-label-primary:hover { background: #696cff; color: #fff; }
    .btn-label-danger { background: #ffe5e5; color: #ff3e1d; border: none; }
    .btn-label-danger:hover { background: #ff3e1d; color: #fff; }

    .trust-signal-card:hover { transform: translateY(-3px); border-color: var(--premium-primary) !important; }

    @media (max-width: 991px) {
        .col-lg-4.border-end { border-end: 0 !important; border-bottom: 1px solid #d9dee3 !important; }
    }
</style>
@endpush

@push('script')
<script>
(function($){
    'use strict';

    // Edit Quick Link
    $('.edit-quick-link').on('click', function(){
        const id = $(this).data('id'), 
              title = $(this).data('title'), 
              url = $(this).data('url'), 
              order = $(this).data('order');
        
        const $form = $('#quicklinks form');
        $form.find('input[name="id"]').val(id);
        $form.find('input[name="title"]').val(title);
        $form.find('input[name="url"]').val(url);
        $form.find('input[name="display_order"]').val(order);
        
        $form.addClass('animate__animated animate__pulse bg-white border-primary border-opacity-50');
        $('html, body').animate({ scrollTop: $form.offset().top - 150 }, 300);
    });

    // Edit Badge
    $('.edit-badge').on('click', function(){
        const id = $(this).data('id'), 
              title = $(this).data('title'), 
              url = $(this).data('url'), 
              order = $(this).data('order');
        
        const $form = $('#badges form');
        $form.find('input[name="id"]').val(id);
        $form.find('input[name="title"]').val(title);
        $form.find('input[name="url"]').val(url);
        $form.find('input[name="display_order"]').val(order);
        
        $form.addClass('animate__animated animate__pulse');
        $('html, body').animate({ scrollTop: $form.offset().top - 150 }, 300);
    });

    // Edit Custom Ad
    $('.edit-custom-ad').on('click', function(){
        const id = $(this).data('id'), 
              title = $(this).data('title'), 
              url = $(this).data('url'), 
              order = $(this).data('order');
        
        const $form = $('#customads form');
        $form.find('input[name="id"]').val(id);
        $form.find('input[name="title"]').val(title || '');
        $form.find('input[name="url"]').val(url || '');
        $form.find('input[name="display_order"]').val(order || 0);
        
        $form.addClass('animate__animated animate__pulse');
        $('html, body').animate({ scrollTop: $form.offset().top - 150 }, 300);
    });

    // Confirm Delete
    $('.confirm-delete').on('submit', function(e) {
        if(!confirm('@lang("Are you sure to remove this element from the architecture?")')) {
            e.preventDefault();
        }
    });

})(jQuery);
</script>
@endpush

