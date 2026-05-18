@extends('admin.layouts.app')
@section('panel')
<div class="user-profile-control-wrapper animate__animated animate__fadeIn">
    {{-- Top Action Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-info shadow-sm"><i class="las la-user-cog fs-3"></i></span>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">@lang('User Profile Architecture')</h5>
                    <p class="text-muted small mb-0">@lang('Configure the data nodes available for user-side modifications and visibility.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex gap-2">
                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                    <a href="{{ route('admin.frontend.sections.register') }}" class="btn btn-outline-primary btn-sm px-3 border-0 bg-white"><i class="las la-cog me-1"></i> @lang('Registration')</a>
                    <a href="{{ route('admin.frontend.sections.userprofile') }}" class="btn btn-primary btn-sm px-3 active border-0"><i class="las la-user-circle me-1"></i> @lang('Profile Edit')</a>
                </div>
                <button type="submit" form="userprofileForm" class="btn btn-primary btn-sm px-4 shadow-md rounded-pill">
                    <i class="las la-save me-1"></i> @lang('Deploy Configuration')
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Configuration Column --}}
        <div class="col-xl-8 col-lg-7">
            <form action="{{ route('admin.frontend.sections.userprofile.save') }}" method="POST" id="userprofileForm">
                @csrf

                {{-- Global Controls --}}
                <div class="card border-0 shadow-sm mb-4 bg-white overflow-hidden">
                    <div class="card-body p-3">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <div class="input-group input-group-merge search-box shadow-none border rounded-pill px-2">
                                    <span class="input-group-text border-0 bg-transparent"><i class="las la-search text-muted"></i></span>
                                    <input type="text" class="form-control border-0 bg-transparent ps-0" id="fieldSearch" placeholder="@lang('Filter profile elements...')">
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                                    <button type="button" class="btn btn-light px-3" id="checkAll">@lang('Select All')</button>
                                    <button type="button" class="btn btn-light px-3 border-start" id="uncheckAll">@lang('Reset')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach(registrationFieldsListGrouped() as $groupKey => $group)
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden field-group-card animate__animated animate__fadeInUp" data-group="{{ $groupKey }}">
                        <div class="card-header border-bottom py-3 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-3">
                                        <span class="avatar-initial rounded-circle bg-label-{{ $groupKey === 'basic' ? 'primary' : 'warning' }}"><i class="{{ $group['icon'] }}"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $group['title'] }}</h6>
                                        <small class="text-muted">@lang('User-side edit permissions')</small>
                                    </div>
                                </div>
                                <div class="form-check form-switch modern-switch">
                                    <input class="form-check-input group-master-toggle" type="checkbox" data-group="{{ $groupKey }}" id="master_{{ $groupKey }}">
                                    <label class="form-check-label tiny fw-bold text-muted" for="master_{{ $groupKey }}">@lang('TOGGLE GROUP')</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light-premium">
                                        <tr>
                                            <th class="ps-4 py-3 small fw-bold text-muted">@lang('DATA NODE')</th>
                                            <th class="py-3 small fw-bold text-muted d-none d-sm-table-cell">@lang('SYSTEM KEY')</th>
                                            <th class="text-end pe-4 py-3 small fw-bold text-muted">@lang('VISIBILITY')</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @foreach($group['fields'] as $fkey => $label)
                                            @if(in_array($fkey, ['captcha', 'password', 'agree', 'referBy'])) @continue @endif
                                            <tr class="field-row transition-all" data-search="{{ strtolower($label) }} {{ strtolower($fkey) }}">
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="field-status-dot rounded-circle me-3 {{ isProfileFieldEnabled($fkey) ? 'bg-primary shadow-premium' : 'bg-secondary' }}"></div>
                                                        <span class="fw-semibold text-dark">{{ $label }}</span>
                                                    </div>
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    <code class="small text-primary bg-label-primary px-2 py-1 rounded">{{ $fkey }}</code>
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

        {{-- Interactive Dashboard Preview --}}
        <div class="col-xl-4 col-lg-5">
            <div class="sticky-preview-wrapper">
                <div class="preview-header mb-3 d-flex align-items-center justify-content-between px-2">
                    <div class="d-flex align-items-center">
                        <i class="las la-eye text-primary fs-4 me-2"></i>
                        <h6 class="mb-0 fw-bold">@lang('Dashboard Render')</h6>
                    </div>
                    <span class="badge bg-label-primary px-3 py-2 rounded-pill" id="activeProfileFieldsBadge">0 @lang('Enabled Nodes')</span>
                </div>
                
                {{-- Phone Shell --}}
                <div class="phone-shell shadow-2xl mx-auto">
                    <div class="phone-bezel">
                        <div class="phone-sensor-strip"></div>
                        <div class="phone-screen-container">
                            <div class="phone-header-bar d-flex justify-content-between align-items-center px-4 pt-3">
                                <span class="fw-bold small clock-real-time">12:30</span>
                                <div class="d-flex gap-1 align-items-center">
                                    <i class="las la-signal text-white" style="font-size: 0.7rem;"></i>
                                    <i class="las la-wifi text-white" style="font-size: 0.7rem;"></i>
                                    <div class="battery-icon border-white"></div>
                                </div>
                            </div>
                            
                            <div class="phone-content-body custom-scrollbar">
                                <div class="profile-hero-mockup bg-primary p-4 text-center text-white pb-5 position-relative">
                                    <div class="mockup-avatar-wrapper mx-auto mb-3">
                                        <div class="avatar avatar-xl">
                                            <img src="{{ asset('assets/images/default-user.png') }}" class="rounded-circle border border-3 border-white shadow-lg" onerror="this.src='https://ui-avatars.com/api/?name=John+Doe&background=fff&color=696cff'">
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-white">John Doe</h6>
                                    <p class="tiny text-white-50 mb-0 text-uppercase tracking-wider">@lang('Customer Platinum')</p>
                                    <div class="hero-decoration"></div>
                                </div>
                                
                                <div class="profile-card-mockup mx-3 p-4 bg-white rounded-4 shadow-premium-soft position-relative" style="margin-top: -30px; z-index: 5;">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-label-primary p-2 rounded-3 me-2">
                                                <i class="las la-user-edit"></i>
                                            </div>
                                            <h6 class="small fw-bold text-dark mb-0">@lang('Account Identity')</h6>
                                        </div>
                                        <i class="las la-cog text-muted small"></i>
                                    </div>

                                    <div id="profileMockupFields" class="mockup-field-stack">
                                        {{-- Dynamic nodes --}}
                                    </div>

                                    <div class="mt-4">
                                        <button type="button" class="btn btn-primary btn-sm w-100 rounded-3 shadow-md py-2 fw-bold text-uppercase" style="font-size: 0.7rem;">@lang('Update Account')</button>
                                    </div>
                                </div>

                                <div class="p-3 mt-2">
                                    <div class="card border-0 bg-label-secondary p-3 rounded-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="las la-shield-alt me-2 text-dark"></i>
                                            <span class="tiny fw-bold text-dark">@lang('SECURITY PRIVACY')</span>
                                        </div>
                                        <div class="mockup-placeholder-row w-100 mb-2"></div>
                                        <div class="mockup-placeholder-row w-75"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="phone-home-indicator"></div>
                    </div>
                </div>
                
                <div class="alert alert-warning border-0 shadow-sm mt-4 p-4 rounded-4 d-flex align-items-start">
                    <div class="badge bg-warning p-2 rounded-circle me-3">
                        <i class="las la-exclamation-triangle text-white"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading mb-1 text-dark fw-bold fs-6">@lang('Data Governance')</h6>
                        <p class="mb-0 small text-muted lh-base">@lang('Enable only fields that require frequent updates. Sensitive identifiers should be locked to prevent unauthorized identity shifts.')</p>
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
        --phone-frame: #343444;
    }

    .user-profile-control-wrapper { font-family: 'Public Sans', sans-serif; }

    /* Sneat Colors */
    .bg-label-primary { background-color: #e7e7ff !important; color: var(--premium-primary) !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-secondary { background-color: #f5f5f9 !important; color: #8592a3 !important; }
    .bg-light-premium { background-color: #f8f9fa; }
    
    .avatar-md { width: 48px; height: 48px; }
    .avatar-xs { width: 28px; height: 28px; }
    .avatar-xl { width: 80px; height: 80px; }

    .shadow-premium { box-shadow: 0 0 10px rgba(105, 108, 255, 0.4); }
    .shadow-premium-soft { box-shadow: 0 10px 30px rgba(105, 108, 255, 0.08); }

    /* Modern Switch */
    .modern-switch .form-check-input { width: 3rem; height: 1.5rem; cursor: pointer; }
    .modern-switch .form-check-input:checked { background-color: var(--premium-primary); border-color: var(--premium-primary); }

    /* Table Styles */
    .field-row { cursor: default; }
    .field-row:hover { background-color: #fcfdfe !important; }
    .field-status-dot { width: 8px; height: 8px; transition: all 0.3s; }

    /* Search Box */
    .search-box { transition: all 0.3s; }
    .search-box:focus-within { border-color: var(--premium-primary) !important; box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1) !important; }

    /* Phone Shell Styling */
    .sticky-preview-wrapper { position: sticky; top: 120px; }
    
    .phone-shell {
        width: 300px;
        height: 620px;
        background: var(--phone-frame);
        padding: 12px;
        border-radius: 45px;
        position: relative;
        box-shadow: 0 50px 100px -20px rgba(50, 50, 93, 0.25), 0 30px 60px -30px rgba(0, 0, 0, 0.3);
    }

    .phone-bezel {
        width: 100%;
        height: 100%;
        background: #fff;
        border-radius: 35px;
        overflow: hidden;
        position: relative;
        border: 4px solid #000;
    }

    .phone-sensor-strip {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 120px;
        height: 25px;
        background: #000;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
        z-index: 100;
    }

    .phone-screen-container {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .phone-content-body {
        flex: 1;
        overflow-y: auto;
    }

    .hero-decoration {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 40px;
        background: linear-gradient(to top, rgba(255,255,255,0.1), transparent);
    }

    .mockup-field-node {
        margin-bottom: 12px;
        animation: slideInUp 0.3s ease;
    }

    .mockup-n-label { font-size: 0.65rem; color: #8592a3; font-weight: 600; margin-bottom: 3px; display: block; }
    .mockup-n-value {
        height: 34px;
        background: #f8f9fb;
        border-bottom: 1px solid #d9dee3;
        width: 100%;
        padding: 0 4px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        color: #566a7f;
        font-weight: 500;
    }

    .mockup-placeholder-row { height: 8px; background: rgba(0,0,0,0.05); border-radius: 4px; }
    
    .battery-icon { width: 18px; height: 9px; border: 1px solid #fff; border-radius: 2px; position: relative; }
    .battery-icon::after { content: ''; position: absolute; right: -3px; top: 2px; width: 2px; height: 3px; background: #fff; }

    .phone-home-indicator {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: #000;
        border-radius: 2px;
        opacity: 0.2;
    }

    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

    @keyframes slideInUp {
        from { transform: translateY(10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 1199px) {
        .sticky-preview-wrapper { position: relative; top: 0; margin-top: 3rem; }
    }
</style>
@endpush

@push('script')
<script>
(function ($) {
    'use strict';

    // 1. Real-time Clock
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        $('.clock-real-time').text(hours + ':' + minutes);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Preview Synchronizer
    function syncDashboardPreview() {
        const $mockupStack = $('#profileMockupFields');
        $mockupStack.empty();
        
        const $checked = $('.profile-field-cb:checked');
        $('#activeProfileFieldsBadge').text($checked.length + ' ' + ($checked.length === 1 ? '@lang("Enabled Node")' : '@lang("Enabled Nodes")'));

        if ($checked.length === 0) {
            $mockupStack.html('<div class="text-center py-4 opacity-50"><i class="las la-fingerprint fs-1"></i><p class="tiny fw-bold text-uppercase mt-2">@lang("No visible data")</p></div>');
            return;
        }

        $checked.each(function (index) {
            if (index >= 6) return; // Keep mockup concise
            const label = $(this).closest('.field-row').find('.fw-semibold').text();
            
            const html = `
                <div class="mockup-field-node">
                    <span class="mockup-n-label">${label}</span>
                    <div class="mockup-n-value">John Doe...</div>
                </div>
            `;
            $mockupStack.append(html);
        });

        if ($checked.length > 6) {
            $mockupStack.append(`<div class="text-center tiny text-muted fw-bold py-1 bg-light rounded mt-2">+ ${$checked.length - 6} @lang("ADDITIONAL NODES")</div>`);
        }

        // Sync Table Status Dots
        $('.profile-field-cb').each(function() {
            const $dot = $(this).closest('.field-row').find('.field-status-dot');
            if ($(this).is(':checked')) {
                $dot.addClass('bg-primary shadow-premium').removeClass('bg-secondary');
            } else {
                $dot.removeClass('bg-primary shadow-premium').addClass('bg-secondary');
            }
        });
    }

    // 3. Advanced Filtering
    $('#fieldSearch').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.field-row').each(function() {
            const $row = $(this);
            const text = $row.data('search');
            $row.toggle(text.includes(query));
        });

        $('.field-group-card').each(function() {
            const visible = $(this).find('.field-row:visible').length;
            $(this).toggle(visible > 0);
        });
    });

    // 4. Global Operations
    $('#checkAll').on('click', function() {
        $('.profile-field-cb').prop('checked', true).trigger('change');
        $('.group-master-toggle').prop('checked', true);
    });

    $('#uncheckAll').on('click', function() {
        $('.profile-field-cb').prop('checked', false).trigger('change');
        $('.group-master-toggle').prop('checked', false);
    });

    // 5. Group Logic
    $('.group-master-toggle').on('change', function() {
        const group = $(this).data('group');
        $(`.profile-field-cb[data-group="${group}"]`).prop('checked', this.checked).trigger('change');
    });

    // Initial Sync
    $(document).on('change', '.profile-field-cb', syncDashboardPreview);
    syncDashboardPreview();

})(jQuery);
</script>
@endpush