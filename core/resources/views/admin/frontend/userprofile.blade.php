@extends('admin.layouts.app')
@section('panel')
<div class="user-profile-control-wrapper">
    {{-- Top Action Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h5 class="fw-bold mb-0">@lang('User Profile Architecture')</h5>
            <p class="text-muted small mb-0">@lang('Control which data users can view and update in their account settings.')</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="btn-group shadow-sm">
                <a href="{{ route('admin.frontend.sections.register') }}" class="btn btn-outline-primary btn-sm px-3"><i class="las la-cog me-1"></i> @lang('Registration')</a>
                <a href="{{ route('admin.frontend.sections.userprofile') }}" class="btn btn-primary btn-sm active px-3"><i class="las la-user-circle me-1"></i> @lang('Profile Edit')</a>
            </div>
            <button type="submit" form="userprofileForm" class="btn btn--success btn-sm px-4 ms-2 shadow-sm"><i class="las la-save me-1"></i> @lang('Deploy Configuration')</button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Configuration Column --}}
        <div class="col-xl-8 col-lg-7">
            <form action="{{ route('admin.frontend.sections.userprofile.save') }}" method="POST" id="userprofileForm">
                @csrf

                {{-- Search & Bulk Actions --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <div class="input-group input-group-sm search-fields-box shadow-none border rounded-pill overflow-hidden">
                                    <span class="input-group-text bg-white border-0"><i class="las la-search text-muted"></i></span>
                                    <input type="text" class="form-control border-0 ps-0" id="fieldSearch" placeholder="@lang('Search profile fields...')">
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary px-3" id="checkAll">@lang('Select All')</button>
                                    <button type="button" class="btn btn-outline-secondary px-3" id="uncheckAll">@lang('Reset')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach(registrationFieldsListGrouped() as $groupKey => $group)
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden border-top-premium-{{ $groupKey === 'basic' ? 'primary' : 'warning' }} field-group-card" data-group="{{ $groupKey }}">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom-0">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-label-{{ $groupKey === 'basic' ? 'primary' : 'warning' }} rounded me-3 d-flex align-items-center justify-content-center">
                                    <i class="{{ $group['icon'] }} fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $group['title'] }}</h6>
                                    <small class="text-muted">@lang('Visibility for profile settings')</small>
                                </div>
                            </div>
                            <div class="form-check form-switch modern-switch">
                                <input class="form-check-input group-master-toggle" type="checkbox" data-group="{{ $groupKey }}">
                            </div>
                        </div>
                        <div class="card-body pt-0 px-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-top">
                                    <thead class="bg-light-premium">
                                        <tr>
                                            <th class="ps-4 py-2 small fw-bold">@lang('FIELD NAME')</th>
                                            <th class="py-2 small fw-bold d-none d-sm-table-cell">@lang('KEY')</th>
                                            <th class="text-end pe-4 py-2 small fw-bold">@lang('STATUS')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group['fields'] as $fkey => $label)
                                            @if(in_array($fkey, ['captcha', 'password', 'agree', 'referBy'])) @continue @endif
                                            <tr class="field-row transition-all" data-search="{{ strtolower($label) }} {{ strtolower($fkey) }}">
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="field-indicator rounded-circle me-3 {{ isProfileFieldEnabled($fkey) ? 'bg-primary shadow-premium' : 'bg-gray' }}"></div>
                                                        <span class="fw-semibold text-dark">{{ $label }}</span>
                                                    </div>
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    <span class="badge bg-label-secondary small rounded-pill">{{ $fkey }}</span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="form-check form-switch modern-switch d-inline-block">
                                                        <input type="hidden" name="profile_fields[{{ $fkey }}]" value="0">
                                                        <input type="checkbox" class="form-check-input profile-field-cb"
                                                            name="profile_fields[{{ $fkey }}]" value="1"
                                                            id="profile_field_{{ $fkey }}" {{ isProfileFieldEnabled($fkey) ? 'checked' : '' }} data-group="{{ $groupKey }}">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>

        {{-- Sticky Preview Column --}}
        <div class="col-xl-4 col-lg-5">
            <div class="sticky-preview-container">
                <div class="preview-header mb-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="las la-mobile-alt me-2 text-primary"></i>@lang('Profile Device Preview')</h6>
                    <span class="badge bg-primary rounded-pill shadow-sm" id="activeProfileFieldsBadge">0 @lang('Fields')</span>
                </div>
                
                {{-- Phone Mockup --}}
                <div class="phone-mockup shadow-lg mx-auto">
                    <div class="phone-frame">
                        <div class="phone-speaker"></div>
                        <div class="phone-screen bg-white">
                            <div class="app-status-bar d-flex justify-content-between px-3 pt-2 small text-muted">
                                <span>12:30</span>
                                <div class="d-flex gap-1">
                                    <i class="las la-signal"></i>
                                    <i class="las la-wifi"></i>
                                    <i class="las la-battery-full"></i>
                                </div>
                            </div>
                            
                            <div class="app-content-scrollable">
                                <div class="profile-header-mockup bg-primary p-4 text-center text-white pb-5">
                                    <div class="avatar-placeholder mx-auto mb-3 rounded-circle shadow border border-3 border-white d-flex align-items-center justify-content-center bg-light text-primary" style="width: 80px; height: 80px;">
                                        <i class="las la-user fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-white">John Doe</h6>
                                    <p class="small opacity-75 mb-0">@lang('User Dashboard')</p>
                                </div>
                                
                                <div class="profile-body-mockup p-3 bg-white rounded-top shadow-sm" style="margin-top: -20px;">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="small fw-bold text-dark mb-0"><i class="las la-user-edit me-2 text-primary"></i>@lang('Profile Settings')</h6>
                                        <i class="las la-ellipsis-h text-muted"></i>
                                    </div>

                                    <div id="profileMockupFields" class="mockup-form-fields">
                                        {{-- Fields injected here via JS --}}
                                    </div>

                                    <div class="mt-4 pt-2">
                                        <button type="button" class="btn btn-primary w-100 rounded shadow-sm py-2 fw-bold small" style="font-size: 0.85rem;">@lang('UPDATE PROFILE')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="phone-home-button"></div>
                    </div>
                </div>
                
                <div class="alert alert-warning border-0 shadow-sm mt-4 p-3 d-flex align-items-start rounded-3">
                    <i class="las la-exclamation-circle fs-4 text-warning me-3 mt-1"></i>
                    <div>
                        <h6 class="alert-heading mb-1 text-warning fs-6 fw-bold">@lang('Security Warning')</h6>
                        <p class="mb-0 small text-dark opacity-75">@lang('Only enable fields that users actually need to change. Sensitive data should be managed by administrators.')</p>
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
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
    .bg-light-premium { background-color: #f8f9fa; }
    
    .border-top-premium-primary { border-top: 4px solid var(--premium-primary) !important; }
    .border-top-premium-warning { border-top: 4px solid var(--premium-warning) !important; }

    .avatar-sm { width: 40px; height: 40px; }
    .transition-all { transition: all 0.3s ease; }
    
    .shadow-premium { box-shadow: 0 0 8px var(--premium-primary); }
    .bg-gray { background-color: #e0e0e0; }

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

    /* Table Hover */
    .field-row:hover {
        background-color: #fdfdff !important;
    }
    .field-indicator {
        width: 8px;
        height: 8px;
        transition: all 0.3s ease;
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
        scrollbar-width: none;
    }
    .app-content-scrollable::-webkit-scrollbar { display: none; }

    .mockup-field-skeleton {
        margin-bottom: 0.75rem;
    }
    .mockup-field-label {
        font-size: 0.65rem;
        font-weight: 600;
        color: #888;
        margin-bottom: 2px;
        display: block;
    }
    .mockup-field-input {
        height: 32px;
        background: #fcfdfe;
        border: 1px solid #d9dee3;
        border-radius: 6px;
        width: 100%;
        padding: 0 10px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        color: #444;
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

    /* Responsive */
    @media (max-width: 991px) {
        .sticky-preview-container {
            position: relative;
            top: 0;
            margin-top: 2rem;
        }
    }
</style>
@endpush

@push('script')
<script>
(function ($) {
    'use strict';

    // 1. Update Live Preview
    function updatePreview() {
        const $mockupContainer = $('#profileMockupFields');
        $mockupContainer.empty();
        
        const $checked = $('.profile-field-cb:checked');
        $('#activeProfileFieldsBadge').text($checked.length + ' ' + ($checked.length === 1 ? '@lang("Field")' : '@lang("Fields")'));

        if ($checked.length === 0) {
            $mockupContainer.html('<div class="text-center py-4 opacity-50"><i class="las la-ghost fs-1"></i><p class="small">@lang("No fields enabled")</p></div>');
            return;
        }

        $checked.each(function (index) {
            if (index >= 8) return; // Only show first 8 to keep mockup clean
            const label = $(this).closest('.field-row').find('.fw-semibold').text();
            
            const html = `
                <div class="mockup-field-skeleton">
                    <span class="mockup-field-label">${label}</span>
                    <div class="mockup-field-input">John Doe...</div>
                </div>
            `;
            $mockupContainer.append(html);
        });

        if ($checked.length > 8) {
            $mockupContainer.append(`<div class="text-center small text-muted opacity-50">+ ${$checked.length - 8} @lang("more fields")...</div>`);
        }

        // Update Indicators
        $('.profile-field-cb').each(function() {
            const $indicator = $(this).closest('.field-row').find('.field-indicator');
            if ($(this).is(':checked')) {
                $indicator.addClass('bg-primary shadow-premium').removeClass('bg-gray');
            } else {
                $indicator.removeClass('bg-primary shadow-premium').addClass('bg-gray');
            }
        });
    }

    // 2. Field Search Logic
    $('#fieldSearch').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.field-row').each(function() {
            const $row = $(this);
            const text = $row.data('search');
            if (text.includes(query)) {
                $row.show();
            } else {
                $row.hide();
            }
        });

        // Hide empty groups
        $('.field-group-card').each(function() {
            const visibleRows = $(this).find('.field-row:visible').length;
            $(this).toggle(visibleRows > 0);
        });
    });

    // 3. Bulk Actions
    $('#checkAll').on('click', function() {
        $('.profile-field-cb').prop('checked', true).trigger('change');
    });

    $('#uncheckAll').on('click', function() {
        $('.profile-field-cb').prop('checked', false).trigger('change');
    });

    // 4. Group Master Toggle
    $('.group-master-toggle').on('change', function() {
        const group = $(this).data('group');
        $(`.profile-field-cb[data-group="${group}"]`).prop('checked', this.checked).trigger('change');
    });

    // 5. Initial Call & Event Binding
    $(document).on('change', '.profile-field-cb', updatePreview);
    updatePreview();

})(jQuery);
</script>
@endpush