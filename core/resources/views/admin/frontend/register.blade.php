@extends('admin.layouts.app')
@section('panel')
<div class="registration-control-wrapper">
    {{-- Top Action Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h5 class="fw-bold mb-0">@lang('Registration & User Profile Architecture')</h5>
            <p class="text-muted small mb-0">@lang('Configure the fields, security, and visual flow of your user registration experience.')</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="btn-group shadow-sm">
                <a href="{{ route('admin.frontend.sections.register') }}" class="btn btn-primary btn-sm active px-3"><i class="las la-cog me-1"></i> @lang('Registration')</a>
                <a href="{{ route('admin.frontend.sections.userprofile') }}" class="btn btn-outline-primary btn-sm px-3"><i class="las la-user-circle me-1"></i> @lang('Profile Edit')</a>
            </div>
            <button type="submit" form="registerMasterForm" class="btn btn--success btn-sm px-4 ms-2 shadow-sm"><i class="las la-save me-1"></i> @lang('Save All Changes')</button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Configuration Column --}}
        <div class="col-xl-8 col-lg-7">
            <form action="{{ route('admin.frontend.sections.content.register') }}" method="POST" enctype="multipart/form-data" id="registerMasterForm" class="register-master-form">
                @csrf
                <input type="hidden" name="type" value="content">
                
                {{-- 1. Security & Core settings --}}
                <div class="card border-0 shadow-sm mb-4 overflow-hidden border-top-premium-warning">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-label-warning rounded me-3 d-flex align-items-center justify-content-center">
                                <i class="las la-shield-alt fs-4 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">@lang('Security & Bot Protection')</h6>
                                <small class="text-muted">@lang('Anti-spam measures for login and signup')</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            @php
                                $loginCaptchaEnabled = isset($content->data_values->login_captcha_enabled) ? (int) $content->data_values->login_captcha_enabled === 1 : true;
                                $registerCaptchaEnabled = isRegistrationFieldEnabled('captcha');
                            @endphp
                            <div class="col-md-6">
                                <div class="security-toggle-card p-3 rounded border bg-light-premium transition-all">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-semibold text-dark">@lang('Login Captcha')</span>
                                        <div class="form-check form-switch modern-switch">
                                            <input type="hidden" name="login_captcha_enabled" value="0">
                                            <input class="form-check-input" type="checkbox" name="login_captcha_enabled" value="1" id="login_captcha_enabled" {{ $loginCaptchaEnabled ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0">@lang('Show verification on both floating and full login pages.')</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="security-toggle-card p-3 rounded border bg-light-premium transition-all">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-semibold text-dark">@lang('Register Captcha')</span>
                                        <div class="form-check form-switch modern-switch">
                                            <input type="hidden" name="registration_fields[captcha]" value="0">
                                            <input class="form-check-input reg-field-cb" type="checkbox" name="registration_fields[captcha]" value="1" id="reg_captcha_switch" data-field="captcha" {{ $registerCaptchaEnabled ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0">@lang('Requires users to solve a captcha during registration.')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Registration Fields with Search --}}
                <div class="card border-0 shadow-sm mb-4 border-top-premium-success">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <div class="row align-items-center g-3">
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="avatar-sm bg-label-success rounded me-3 d-flex align-items-center justify-content-center">
                                    <i class="las la-list-ul fs-4 text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">@lang('Data Collection Architecture')</h6>
                                    <small class="text-muted">@lang('Select which fields should appear on the signup form')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm search-fields-box shadow-none border rounded-pill overflow-hidden">
                                    <span class="input-group-text bg-white border-0"><i class="las la-search text-muted"></i></span>
                                    <input type="text" class="form-control border-0 ps-0" id="fieldSearch" placeholder="@lang('Search fields...')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @foreach(registrationFieldsListGrouped() as $groupKey => $group)
                        <div class="registration-field-group mb-4" data-group="{{ $groupKey }}">
                            <div class="d-flex align-items-center justify-content-between mb-3 group-header p-2 bg-light rounded border-start border-3 border-{{ $groupKey == 'basic' ? 'primary' : 'info' }}">
                                <div class="d-flex align-items-center">
                                    <i class="{{ $group['icon'] }} me-2 text-primary fs-5"></i>
                                    <span class="fw-bold text-dark">{{ $group['title'] }}</span>
                                    <span class="badge bg-label-primary rounded-pill ms-2 small group-count">0</span>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary px-3 reg-group-action" data-action="all">@lang('All')</button>
                                    <button type="button" class="btn btn-outline-secondary px-3 reg-group-action" data-action="none">@lang('None')</button>
                                </div>
                            </div>
                            <div class="reg-fields-premium-grid">
                                @foreach($group['fields'] as $fkey => $label)
                                @if($fkey === 'captcha') @continue @endif
                                <div class="field-item-wrapper" data-label="{{ strtolower($label) }}" data-key="{{ strtolower($fkey) }}">
                                    <label class="premium-field-label" for="reg_field_{{ $fkey }}">
                                        <div class="d-flex align-items-center justify-content-between w-100">
                                            <div class="d-flex align-items-center overflow-hidden">
                                                <div class="field-indicator rounded-circle me-2"></div>
                                                <span class="text-truncate" title="{{ $label }}">{{ $label }}</span>
                                            </div>
                                            <div class="form-check modern-checkbox">
                                                <input type="hidden" name="registration_fields[{{ $fkey }}]" value="0">
                                                <input type="checkbox" class="form-check-input reg-field-cb" name="registration_fields[{{ $fkey }}]" value="1" id="reg_field_{{ $fkey }}" {{ isRegistrationFieldEnabled($fkey) ? 'checked' : '' }} data-field="{{ $fkey }}">
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Visual Branding Content --}}
                <div class="card border-0 shadow-sm mb-4 border-top-premium-primary">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom-0" data-bs-toggle="collapse" data-bs-target="#sectionContentCollapse" style="cursor: pointer;">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-label-primary rounded me-3 d-flex align-items-center justify-content-center">
                                <i class="las la-palette fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">@lang('Visual Branding & Content')</h6>
                                <small class="text-muted">@lang('Customize text and imagery on the registration page')</small>
                            </div>
                        </div>
                        <i class="las la-angle-down transition-all collapse-icon"></i>
                    </div>
                    <div class="collapse show" id="sectionContentCollapse">
                        <div class="card-body pt-0 mt-2">
                            <div class="row g-4">
                                @if(@$section->content)
                                    @foreach($section->content as $k => $item)
                                        @if($k == 'images')
                                            @foreach($item as $imgKey => $image)
                                                <div class="col-12">
                                                    <div class="image-upload-wrapper p-3 border rounded bg-light-premium">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-3">
                                                                <label class="form-label fw-semibold mb-2 mb-md-0">{{ __(keyToTitle($imgKey)) }}</label>
                                                                <div class="image-preview-container rounded shadow-sm overflow-hidden" style="height: 80px; width: 120px; background: #fff url({{ getImage('assets/images/frontend/register/'. (@$content->data_values->$imgKey ?? ''), @$section->content->images->$imgKey->size) }}) center/contain no-repeat;">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-9 mt-3 mt-md-0">
                                                                <input type="hidden" name="has_image" value="1">
                                                                <input type="file" class="form-control premium-file-input" name="image_input[{{ $imgKey }}]" accept=".png,.jpg,.jpeg">
                                                                <small class="text-muted mt-1 d-block"><i class="las la-info-circle me-1"></i> @lang('Recommended size'): {{ @$section->content->images->$imgKey->size }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif($k != 'images')
                                            <div class="{{ $item == 'textarea' ? 'col-12' : 'col-md-6' }}">
                                                <div class="form-group mb-0">
                                                    <label class="form-label fw-semibold">{{ __(keyToTitle($k)) }}</label>
                                                    @if($item == 'textarea')
                                                        <textarea rows="3" class="form-control premium-control" name="{{ $k }}" placeholder="@lang('Enter ' . keyToTitle($k))">{{ @$content->data_values->$k }}</textarea>
                                                    @else
                                                        <input type="text" class="form-control premium-control" name="{{ $k }}" value="{{ @$content->data_values->$k }}" placeholder="@lang('Enter ' . keyToTitle($k))">
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Sticky Preview Column --}}
        <div class="col-xl-4 col-lg-5">
            <div class="sticky-preview-container">
                <div class="preview-header mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="las la-mobile-alt me-2 text-primary"></i>@lang('Live Device Preview')</h6>
                    <span class="badge bg-primary rounded-pill shadow-sm" id="activeFieldsBadge">0 @lang('Fields')</span>
                </div>
                
                {{-- Phone Mockup --}}
                <div class="phone-mockup shadow-lg mx-auto">
                    <div class="phone-frame">
                        <div class="phone-speaker"></div>
                        <div class="phone-screen bg-white">
                            <div class="app-status-bar d-flex justify-content-between px-3 pt-2 small text-muted">
                                <span>9:41</span>
                                <div class="d-flex gap-1">
                                    <i class="las la-signal"></i>
                                    <i class="las la-wifi"></i>
                                    <i class="las la-battery-full"></i>
                                </div>
                            </div>
                            
                            <div class="app-content-scrollable p-3">
                                <div class="text-center mb-4 pt-2">
                                    <div class="app-logo-placeholder mx-auto mb-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-light text-primary">
                                        <i class="las la-user-plus fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1 mockup-heading text-dark">{{ @$content->data_values->heading ?? __('Join Us') }}</h6>
                                    <p class="small text-muted mockup-subheading">{{ @$content->data_values->subheading ?? __('Create your account today') }}</p>
                                </div>

                                <div id="mockupFormFields" class="mockup-form-fields">
                                    {{-- Fields injected here via JS --}}
                                </div>

                                <div class="mt-4 pt-2">
                                    <button type="button" class="btn btn-primary w-100 rounded-pill shadow-sm py-2 fw-bold small" style="font-size: 0.85rem;">@lang('REGISTER NOW')</button>
                                    <p class="text-center mt-3 small text-muted">
                                        @lang('Already have an account?') <a href="javascript:void(0)" class="text-primary fw-bold">@lang('Login')</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="phone-home-button"></div>
                    </div>
                </div>
                
                <div class="alert alert-info border-0 shadow-sm mt-4 p-3 d-flex align-items-start rounded-3">
                    <i class="las la-lightbulb fs-4 text-info me-3 mt-1"></i>
                    <div>
                        <h6 class="alert-heading mb-1 text-info fs-6 fw-bold">@lang('Pro Tip')</h6>
                        <p class="mb-0 small text-dark opacity-75">@lang('Keep the registration form short to increase your conversion rate. Only enable essential fields for the initial signup.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    /* Premium Architecture Styles */
    :root {
        --premium-primary: #696cff;
        --premium-success: #71dd37;
        --premium-warning: #ffab00;
        --premium-info: #03c3ec;
        --premium-gray-light: #f5f5f9;
        --premium-border: #d9dee3;
    }

    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-light-premium { background-color: #f8f9fa; }
    
    .border-top-premium-primary { border-top: 4px solid var(--premium-primary) !important; }
    .border-top-premium-success { border-top: 4px solid var(--premium-success) !important; }
    .border-top-premium-warning { border-top: 4px solid var(--premium-warning) !important; }

    .avatar-sm { width: 40px; height: 40px; }
    .transition-all { transition: all 0.3s ease; }
    
    /* Modern Switch */
    .modern-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        cursor: pointer;
    }
    .modern-switch .form-check-input:checked {
        background-color: var(--premium-primary);
        border-color: var(--premium-primary);
    }

    /* Grid Layout */
    .reg-fields-premium-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
    }

    .field-item-wrapper {
        perspective: 1000px;
    }

    .premium-field-label {
        display: block;
        padding: 0.75rem 1rem;
        border: 1px solid var(--premium-border);
        border-radius: 0.5rem;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 0;
        user-select: none;
    }

    .premium-field-label:hover {
        border-color: var(--premium-primary);
        background: #fdfdff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .field-item-wrapper input:checked + label,
    .field-item-wrapper label:has(input:checked) {
        border-color: var(--premium-primary);
        background-color: #f8f8ff;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.1);
    }

    .field-indicator {
        width: 8px;
        height: 8px;
        background-color: #e0e0e0;
        transition: background-color 0.3s ease;
    }
    .field-item-wrapper label:has(input:checked) .field-indicator {
        background-color: var(--premium-primary);
        box-shadow: 0 0 8px var(--premium-primary);
    }

    /* Modern Checkbox */
    .modern-checkbox .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 4px;
        border-width: 2px;
        cursor: pointer;
    }

    /* Search Box */
    .search-fields-box {
        transition: all 0.3s ease;
    }
    .search-fields-box:focus-within {
        border-color: var(--premium-primary) !important;
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15) !important;
    }

    /* Phone Mockup */
    .sticky-preview-container {
        position: sticky;
        top: 2rem;
    }

    .phone-mockup {
        width: 300px;
        height: 600px;
        background: #1e1e1e;
        border-radius: 40px;
        padding: 12px;
        position: relative;
        border: 4px solid #333;
    }

    .phone-frame {
        width: 100%;
        height: 100%;
        background: #fff;
        border-radius: 32px;
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .phone-speaker {
        position: absolute;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 5px;
        background: #333;
        border-radius: 10px;
        z-index: 10;
    }

    .phone-screen {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .app-content-scrollable {
        flex: 1;
        overflow-y: auto;
        scrollbar-width: none; /* Firefox */
    }
    .app-content-scrollable::-webkit-scrollbar { display: none; } /* Chrome/Safari */

    .app-logo-placeholder {
        width: 50px;
        height: 50px;
    }

    .mockup-field-skeleton {
        margin-bottom: 0.75rem;
    }
    .mockup-field-label {
        font-size: 0.65rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 2px;
        display: block;
    }
    .mockup-field-input {
        height: 32px;
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 6px;
        width: 100%;
        padding: 0 10px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        color: #999;
    }

    .phone-home-button {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: #333;
        border-radius: 10px;
        z-index: 10;
    }

    .premium-control {
        border-radius: 0.5rem;
        padding: 0.6rem 1rem;
        border-color: var(--premium-border);
    }
    .premium-control:focus {
        border-color: var(--premium-primary);
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1);
    }

    .premium-file-input {
        border-radius: 0.5rem;
        padding: 0.5rem;
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .field-item-wrapper {
        animation: fadeInUp 0.3s ease forwards;
    }

    .collapse-icon {
        transition: transform 0.3s ease;
    }
    [aria-expanded="true"] .collapse-icon {
        transform: rotate(180deg);
    }

    /* Responsive */
    @media (max-width: 991px) {
        .sticky-preview-container {
            position: relative;
            top: 0;
            margin-top: 2rem;
        }
        .phone-mockup {
            width: 100%;
            max-width: 320px;
        }
    }
</style>
@endpush

@push('script')
<script>
(function ($) {
    'use strict';

    // 1. Field Mapping for Preview
    const fieldLabels = {!! json_encode(registrationFieldsList()) !!};
    
    // 2. Update Live Preview
    function updatePreview() {
        const $mockupContainer = $('#mockupFormFields');
        $mockupContainer.empty();
        
        let activeCount = 0;
        let groupCounts = {};

        $('.reg-field-cb').each(function () {
            const $cb = $(this);
            const fieldKey = $cb.data('field');
            const fieldLabel = fieldLabels[fieldKey] || fieldKey;
            const $group = $cb.closest('.registration-field-group');
            const groupKey = $group.data('group');

            if (!groupCounts[groupKey]) groupCounts[groupKey] = 0;

            if ($cb.is(':checked')) {
                activeCount++;
                groupCounts[groupKey]++;
                
                // Add to Mockup
                let inputType = 'text';
                let placeholder = fieldLabel + '...';
                
                if (fieldKey === 'password') {
                    inputType = 'password';
                    placeholder = '••••••••';
                }

                let html = `
                    <div class="mockup-field-skeleton animate-fade-in" id="mockup_${fieldKey}">
                        <span class="mockup-field-label">${fieldLabel}</span>
                        <div class="mockup-field-input">
                            ${fieldKey === 'agree' || fieldKey === 'newsletter_subscribe' ? 
                                '<i class="las la-check-square me-2 text-primary"></i> <span style="font-size:0.6rem">' + fieldLabel + '</span>' : 
                                placeholder
                            }
                        </div>
                    </div>
                `;
                
                if (fieldKey === 'captcha') {
                    html = `
                        <div class="mockup-field-skeleton" id="mockup_${fieldKey}">
                            <div class="d-flex gap-2">
                                <div class="bg-light rounded p-1 flex-grow-1 text-center small opacity-50" style="font-size:0.6rem">1 + 4 = ?</div>
                                <div class="mockup-field-input" style="width: 60px">?</div>
                            </div>
                        </div>
                    `;
                }

                $mockupContainer.append(html);
            }
        });

        $('#activeFieldsBadge').text(activeCount + ' ' + (activeCount === 1 ? '@lang("Field")' : '@lang("Fields")'));
        
        // Update group badges
        Object.keys(groupCounts).forEach(key => {
            $(`.registration-field-group[data-group="${key}"] .group-count`).text(groupCounts[key]);
        });
    }

    // 3. Field Search Logic
    $('#fieldSearch').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.field-item-wrapper').each(function() {
            const $item = $(this);
            const text = $item.data('label') + ' ' + $item.data('key');
            if (text.includes(query)) {
                $item.show();
            } else {
                $item.hide();
            }
        });

        // Hide empty groups
        $('.registration-field-group').each(function() {
            const visibleFields = $(this).find('.field-item-wrapper:visible').length;
            $(this).toggle(visibleFields > 0);
        });
    });

    // 4. Batch Actions
    $('.reg-group-action').on('click', function() {
        const action = $(this).data('action');
        const $group = $(this).closest('.registration-field-group');
        const $checkboxes = $group.find('.reg-field-cb');
        
        if (action === 'all') {
            $checkboxes.prop('checked', true);
        } else if (action === 'none') {
            $checkboxes.prop('checked', false);
        }
        
        updatePreview();
    });

    // 5. Initial Call & Event Binding
    $(document).on('change', '.reg-field-cb', updatePreview);
    
    // Live update headings
    $('input[name="heading"]').on('input', function() { $('.mockup-heading').text($(this).val() || '@lang("Join Us")'); });
    $('input[name="subheading"]').on('input', function() { $('.mockup-subheading').text($(this).val() || '@lang("Create your account today")'); });

    updatePreview();

})(jQuery);
</script>
@endpush

