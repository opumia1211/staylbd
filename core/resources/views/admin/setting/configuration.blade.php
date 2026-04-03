@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm system-config-card">
            <div class="card-header system-config-header py-4 px-4">
                <h5 class="mb-1 system-config-title">System Configuration <span class="header-bn">(সিস্টেম কনফিগারেশন)</span></h5>
                <p class="mb-0 system-config-desc">Enable or disable system-wide features. Changes take effect immediately. / সিস্টেম জুড়ে ফিচার চালু বা বন্ধ করুন।</p>
            </div>
            <form action="{{ route('admin.setting.system.configuration.submit') }}" method="post" id="systemConfigForm">
                @csrf
                <div class="card-body p-4">
                    @php $cfg = $general; @endphp

                    {{-- Security & Access --}}
                    <div class="config-block mb-4">
                        <h6 class="config-block-title"><i class="las la-shield-alt me-2"></i>{!! lang_en_bn('Security & Access') !!}</h6>
                        <div class="config-list">
                            @include('admin.setting.partials.config_switch', ['name' => 'force_ssl', 'value' => $cfg->force_ssl, 'title' => 'Force SSL', 'desc' => 'Force HTTPS. / ভিজিটরদের HTTPS এ যেতে বাধ্য করুন।'])
                            @include('admin.setting.partials.config_switch', ['name' => 'secure_password', 'value' => $cfg->secure_password, 'title' => 'Force Secure Password', 'desc' => 'Strong password required on signup/change. / শক্ত পাসওয়ার্ড বাধ্যতামূলক।'])
                            @include('admin.setting.partials.config_switch', ['name' => 'registration', 'value' => $cfg->registration, 'title' => 'User Registration', 'desc' => 'Allow new user signup. / নতুন রেজিস্ট্রেশন চালু।'])
                            @include('admin.setting.partials.config_switch', ['name' => 'agree', 'value' => $cfg->agree, 'title' => 'Agree Policy', 'desc' => 'User must agree to policy on register. / রেজিস্ট্রেশনে নীতিমালায় সম্মতি।'])
                        </div>
                    </div>

                    {{-- Verification --}}
                    <div class="config-block mb-4">
                        <h6 class="config-block-title"><i class="las la-user-check me-2"></i>{!! lang_en_bn('Verification') !!}</h6>
                        <div class="config-list">
                            @include('admin.setting.partials.config_switch', ['name' => 'ev', 'value' => $cfg->ev, 'title' => 'Email Verification', 'desc' => '6-digit code via email. Enable Email Notification too. / ইমেইল ভেরিফিকেশন।'])
                            @include('admin.setting.partials.config_switch', ['name' => 'sv', 'value' => $cfg->sv, 'title' => 'Mobile Verification', 'desc' => '6-digit code via SMS. Enable SMS Notification too. / মোবাইল ভেরিফিকেশন।'])
                        </div>
                    </div>

                    {{-- Notifications --}}
                    <div class="config-block mb-4">
                        <h6 class="config-block-title"><i class="las la-bell me-2"></i>{!! lang_en_bn('Notifications') !!}</h6>
                        <div class="config-list">
                            @include('admin.setting.partials.config_switch', ['name' => 'en', 'value' => $cfg->en, 'title' => 'Email Notification', 'desc' => 'System sends emails when needed. / প্রয়োজন হলে ইমেইল পাঠানো।'])
                            @include('admin.setting.partials.config_switch', ['name' => 'sn', 'value' => $cfg->sn, 'title' => 'SMS Notification', 'desc' => 'System sends SMS when needed. / প্রয়োজন হলে এসএমএস পাঠানো।'])
                        </div>
                    </div>

                    {{-- Product, Language & UX --}}
                    <div class="config-block mb-4">
                        <h6 class="config-block-title"><i class="las la-globe me-2"></i>{!! lang_en_bn('Product, Language & UX') !!}</h6>
                        <div class="config-list">
                            @include('admin.setting.partials.config_switch', ['name' => 'display_stock', 'value' => $cfg->display_stock, 'title' => 'Display Stock Quantity', 'desc' => 'Show product stock on site. / সাইটে স্টক সংখ্যা দেখান।'])
                            @if(has_gs_column('display_view_count'))
                            @include('admin.setting.partials.config_switch', ['name' => 'display_view_count', 'value' => $cfg->display_view_count ?? 1, 'title' => 'Display View Count', 'desc' => 'Show "X people viewed in 24h" on product page. / প্রোডাক্ট পেজে ভিউ কাউন্ট দেখান।'])
                            @endif
                            @include('admin.setting.partials.config_switch', ['name' => 'multi_language', 'value' => $cfg->multi_language, 'title' => 'Language Option', 'desc' => 'Users can change language. / ইউজার ভাষা বদলাতে পারবে।'])
                            @if($hasFloatingLogin ?? false)
                            @include('admin.setting.partials.config_switch', ['name' => 'floating_login', 'value' => $cfg->floating_login ?? 1, 'title' => 'Floating Login', 'desc' => 'Login in overlay on same page. / একই পেজে লগইন পপআপ।'])
                            @endif
                            @if($hasFloatingRegister ?? false)
                            @include('admin.setting.partials.config_switch', ['name' => 'floating_register', 'value' => $cfg->floating_register ?? 1, 'title' => 'Floating Register', 'desc' => 'Register in overlay on same page. / একই পেজে রেজিস্ট্রেশন পপআপ।'])
                            @endif
                            @if($hasAdminOnlineStatus ?? false)
                            @include('admin.setting.partials.config_switch', ['name' => 'admin_online_status', 'value' => $cfg->admin_online_status ?? 0, 'title' => 'Admin Online (Green Light)', 'desc' => 'Green/red in chat. / চ্যাটে এডমিন অনলাইন সবুজ, অফলাইন লাল।', 'onLabel' => 'Online / অনলাইন', 'offLabel' => 'Offline / অফলাইন'])
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-4 px-4">
                    <button type="submit" class="btn btn--primary btn-lg w-100 py-3" id="saveConfigBtn">
                        <i class="las la-save me-2"></i> {!! lang_en_bn('Save Configuration') !!}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
.system-config-card { border-radius: 0.5rem; overflow: hidden; }
.system-config-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    border: none;
}
.system-config-header .system-config-title,
.system-config-header .system-config-desc,
.system-config-header .header-bn { color: #fff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
.system-config-header .system-config-title { font-weight: 700; font-size: 1.25rem; }
.system-config-header .system-config-desc { opacity: 1; font-size: 0.9rem; }
.system-config-header .header-bn { font-weight: 500; opacity: 0.95; }
.config-block { background: #fff; border: 1px solid #e9ecef; border-radius: 0.5rem; overflow: hidden; }
.config-block-title { margin: 0; padding: 0.75rem 1rem; background: #f8f9fa; border-bottom: 1px solid #e9ecef; font-weight: 600; color: #212529; }
.config-list { padding: 0; }
.config-item { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1rem; border-bottom: 1px solid #f0f0f0; }
.config-item:last-child { border-bottom: none; }
.config-item:hover { background: #fafafa; }
.config-item .config-info { flex: 1 1 280px; }
.config-item .config-info .config-title { font-weight: 600; color: #212529; }
.config-item .config-info .config-desc { font-size: 0.8125rem; color: #6c757d; margin-top: 0.25rem; }
.config-item .config-control { flex-shrink: 0; display: flex; align-items: center; gap: 0.75rem; }
.switch-wrap { display: inline-flex; align-items: center; gap: 0.5rem; }
.switch-wrap input[type="checkbox"] { width: 3.5rem; height: 1.85rem; cursor: pointer; accent-color: #198754; flex-shrink: 0; }
.switch-status { min-width: 110px; text-align: center; font-weight: 700; font-size: 0.875rem; padding: 0.4rem 0.65rem; border-radius: 0.375rem; }
.switch-status.status-on { background: #198754; color: #fff; }
.switch-status.status-off { background: #6c757d; color: #fff; }
</style>
@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('systemConfigForm');
    if (form) {
        form.querySelectorAll('.config-toggle-input').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var wrap = this.closest('.switch-wrap');
                if (!wrap) return;
                var statusEl = wrap.querySelector('.switch-status');
                var onLabel = wrap.getAttribute('data-on') || 'ON (চালু)';
                var offLabel = wrap.getAttribute('data-off') || 'OFF (বন্ধ)';
                if (statusEl) {
                    statusEl.textContent = this.checked ? onLabel : offLabel;
                    statusEl.className = 'switch-status ' + (this.checked ? 'status-on' : 'status-off');
                }
            });
        });
        form.addEventListener('submit', function() {
            var btn = document.getElementById('saveConfigBtn');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="las la-spinner la-spin me-2"></i> Saving... / সংরক্ষণ হচ্ছে...'; }
        });
    }
});
</script>
@endpush
