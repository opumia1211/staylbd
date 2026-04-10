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

{{-- inline style moved to critical-admin.css --}}

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
