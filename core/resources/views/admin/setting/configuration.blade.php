@extends('admin.layouts.app')

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-none bg-transparent mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                    <div>
                        <h4 class="mb-1 fw-extrabold text-heading">@lang('Platform Engineering')</h4>
                        <p class="mb-0 text-muted small">@lang('Calibrate core system vectors and feature accessibility.')</p>
                    </div>
                    <div class="avatar bg-label-primary p-2 rounded-circle shadow-sm">
                        <i class="bx bx-cog fs-3 bx-spin-slow"></i>
                    </div>
                </div>
                
                <form action="{{ route('admin.setting.system.configuration.submit') }}" method="post" id="systemConfigForm">
                    @csrf
                    <div class="row g-4">
                        {{-- Security & Governance --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                                <div class="card-header py-3 px-4 bg-label-primary border-bottom border-primary border-opacity-10 d-flex align-items-center">
                                    <i class="bx bx-shield-quarter me-2 fs-4"></i>
                                    <h6 class="mb-0 fw-bold text-primary">@lang('Security & Governance')</h6>
                                </div>
                                <div class="list-group list-group-flush">
                                    @include('admin.setting.partials.config_switch', ['name' => 'force_ssl', 'value' => $general->force_ssl, 'title' => 'SSL Encryption', 'desc' => 'Enforce mandatory HTTPS for all data transmission.'])
                                    @include('admin.setting.partials.config_switch', ['name' => 'secure_password', 'value' => $general->secure_password, 'title' => 'Entropy Engine', 'desc' => 'Require high-entropy passwords for user authentication.'])
                                    @include('admin.setting.partials.config_switch', ['name' => 'registration', 'value' => $general->registration, 'title' => 'Onboarding', 'desc' => 'Enable public registration for new ecosystem participants.'])
                                    @include('admin.setting.partials.config_switch', ['name' => 'agree', 'value' => $general->agree, 'title' => 'Consent Policy', 'desc' => 'Enforce mandatory agreement to terms during signup.'])
                                </div>
                                <div class="card-header py-3 px-4 bg-label-info border-bottom border-top border-info border-opacity-10 d-flex align-items-center">
                                    <i class="bx bx-fingerprint me-2 fs-4"></i>
                                    <h6 class="mb-0 fw-bold text-info">@lang('Identity Protocols')</h6>
                                </div>
                                <div class="list-group list-group-flush">
                                    @include('admin.setting.partials.config_switch', ['name' => 'ev', 'value' => $general->ev, 'title' => 'Email Verification', 'desc' => 'Validate user identity via asynchronous email challenge.'])
                                    @include('admin.setting.partials.config_switch', ['name' => 'sv', 'value' => $general->sv, 'title' => 'SMS Verification', 'desc' => 'Hardware-level identity validation via mobile OTP.'])
                                </div>
                            </div>
                        </div>

                        {{-- Automation & UX --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                                <div class="card-header py-3 px-4 bg-label-warning border-bottom border-warning border-opacity-10 d-flex align-items-center">
                                    <i class="bx bx-broadcast me-2 fs-4"></i>
                                    <h6 class="mb-0 fw-bold text-warning">@lang('Communication Channels')</h6>
                                </div>
                                <div class="list-group list-group-flush">
                                    @include('admin.setting.partials.config_switch', ['name' => 'en', 'value' => $general->en, 'title' => 'Email Relays', 'desc' => 'Enable automated platform-to-user email synchronization.'])
                                    @include('admin.setting.partials.config_switch', ['name' => 'sn', 'value' => $general->sn, 'title' => 'SMS Relays', 'desc' => 'Enable critical real-time mobile push notifications.'])
                                </div>
                                <div class="card-header py-3 px-4 bg-label-success border-bottom border-top border-success border-opacity-10 d-flex align-items-center">
                                    <i class="bx bx-window-alt me-2 fs-4"></i>
                                    <h6 class="mb-0 fw-bold text-success">@lang('Ecosystem Experience')</h6>
                                </div>
                                <div class="list-group list-group-flush">
                                    @include('admin.setting.partials.config_switch', ['name' => 'display_stock', 'value' => $general->display_stock, 'title' => 'Inventory Pulse', 'desc' => 'Show real-time inventory levels on product storefronts.'])
                                    @if(has_gs_column('display_view_count'))
                                    @include('admin.setting.partials.config_switch', ['name' => 'display_view_count', 'value' => $general->display_view_count ?? 1, 'title' => 'Traffic Visibility', 'desc' => 'Publicly display engagement metrics for active products.'])
                                    @endif
                                    @include('admin.setting.partials.config_switch', ['name' => 'multi_language', 'value' => $general->multi_language, 'title' => 'Localization Engine', 'desc' => 'Enable multi-region linguistic support for the storefront.'])
                                    
                                    @if(isset($general->floating_login))
                                    @include('admin.setting.partials.config_switch', ['name' => 'floating_login', 'value' => $general->floating_login, 'title' => 'Modal Auth', 'desc' => 'Execute login/signup flows via high-performance overlays.'])
                                    @endif
                                    @if(isset($general->admin_online_status))
                                    @include('admin.setting.partials.config_switch', ['name' => 'admin_online_status', 'value' => $general->admin_online_status, 'title' => 'Presence Indicator', 'desc' => 'Show live administrator availability for support sessions.'])
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                                <div class="card-body py-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-white p-2 rounded me-3">
                                            <i class="bx bx-check-shield text-primary fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-primary">@lang('Ready to Sync Changes?')</h6>
                                            <p class="mb-0 text-muted small">@lang('Applying changes will refresh system cache and optimize configurations.')</p>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm hover-elevate" id="saveConfigBtn">
                                        <i class="bx bx-save me-2"></i> @lang('Commit Configuration')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .text-heading { color: #2b3a4a !important; }
    .fw-extrabold { font-weight: 800 !important; }
    .tiny { font-size: 0.7rem !important; }
    .list-group-item { transition: all 0.2s ease; border-color: #f1f5f9 !important; }
    .list-group-item:hover { background-color: #f8fafc !important; }
    .bx-spin-slow { animation: spin 4s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .hover-elevate:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(105, 108, 255, 0.2) !important; }
</style>
@endsection

@push('script')
<script>
    "use strict";
    $(function() {
        $('#systemConfigForm').on('submit', function() {
            const btn = $('#saveConfigBtn');
            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-2"></i> @lang("Synchronizing...")');
        });
    });
</script>
@endpush
