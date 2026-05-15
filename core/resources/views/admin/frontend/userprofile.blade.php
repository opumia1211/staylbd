@extends('admin.layouts.app')
@section('panel')
<div class="row g-4">
    <!-- Tactical Matrix: Field Controls -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="las la-id-card"></i></span>
                    </div>
                    <div>
                        <h5 class="mb-0">@lang('Profile Intelligence Matrix')</h5>
                        <small class="text-muted">@lang('Configure enterprise-grade profile visibility')</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.frontend.sections.register') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="las la-undo-alt me-1"></i> @lang('Registration Control')
                    </a>
                    <a href="{{ route('user.profile.setting') }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="las la-external-link-alt me-1"></i> @lang('Live Profile')
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.frontend.sections.userprofile.save') }}" method="POST" id="userprofileForm">
            @csrf
            
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="input-group input-group-merge w-50 shadow-sm border rounded">
                    <span class="input-group-text border-0 bg-white"><i class="las la-search text-muted"></i></span>
                    <input type="text" id="fieldSearch" class="form-control border-0 px-1" placeholder="@lang('Search fields... (e.g. phone, address)')">
                </div>
                <div class="btn-group shadow-sm">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="checkAll">@lang('Select All')</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="uncheckAll">@lang('Reset')</button>
                </div>
            </div>

            @foreach(registrationFieldsListGrouped() as $groupKey => $group)
            <div class="card border-0 shadow-sm mb-4 field-group-card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-{{ $groupKey === 'basic' ? 'primary' : 'warning' }} p-2 me-3 rounded">
                            <i class="{{ $group['icon'] }} fs-4"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 text-uppercase fw-bold ls-1">{{ $group['title'] }}</h6>
                            <small class="text-muted d-none d-md-block">@lang('Manage visibility for') {{ strtolower($group['title']) }}</small>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input group-master-toggle" type="checkbox" data-group="{{ $groupKey }}">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 border-top">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-2 small fw-bold">@lang('FIELD NAME')</th>
                                    <th class="py-2 small fw-bold">@lang('IDENTIFIER')</th>
                                    <th class="text-end pe-4 py-2 small fw-bold">@lang('VISIBILITY')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['fields'] as $fkey => $label)
                                @if(in_array($fkey, ['captcha', 'password', 'agree', 'referBy'])) @continue @endif
                                <tr class="field-row" data-search="{{ strtolower($label) }} {{ strtolower($fkey) }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2 text-muted"><i class="las la-grip-vertical"></i></div>
                                            <span class="fw-semibold text-heading">{{ $label }}</span>
                                        </div>
                                    </td>
                                    <td><code class="small text-muted">{{ $fkey }}</code></td>
                                    <td class="text-end pe-4">
                                        <div class="form-check form-switch d-inline-block">
                                            <input type="hidden" name="profile_fields[{{ $fkey }}]" value="0">
                                            <input type="checkbox" class="form-check-input profile-field-cb" name="profile_fields[{{ $fkey }}]" value="1" id="profile_field_{{ $fkey }}" {{ isProfileFieldEnabled($fkey) ? 'checked' : '' }} data-group="{{ $groupKey }}">
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

            <div class="card border-0 shadow-sm sticky-bottom-custom bg-white p-3 rounded-top">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted small d-none d-md-block">
                        <i class="las la-info-circle me-1"></i> @lang('Changes will reflect on user profile immediately.')
                    </div>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="las la-save me-2"></i> @lang('Deploy Configuration')
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Live Tactical Preview -->
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm sticky-top" style="top: 130px; z-index: 1 !important;">
            <div class="card-header bg-dark text-white py-3">
                <div class="d-flex align-items-center">
                    <i class="las la-eye me-2 fs-4 text-warning"></i>
                    <h6 class="mb-0 text-white">@lang('Tactical Preview')</h6>
                </div>
            </div>
            <div class="card-body p-4 bg-lighter profile-preview-container">
                <div class="mock-profile rounded bg-white shadow-sm overflow-hidden border">
                    <div class="mock-header p-3 bg-primary text-white d-flex align-items-center">
                        <div class="avatar avatar-md me-3 border border-2 border-white rounded-circle bg-light d-flex align-items-center justify-content-center text-primary">
                            <i class="las la-user fs-3"></i>
                        </div>
                        <div>
                            <div class="fw-bold h6 mb-0 text-white">John Doe</div>
                            <div class="tiny opacity-75">@john_doe</div>
                        </div>
                    </div>
                    <div class="mock-body p-3">
                        <div class="section-title mb-3 border-bottom pb-1">
                            <span class="tiny fw-bold text-muted text-uppercase ls-1">@lang('Profile Settings')</span>
                        </div>
                        <div id="preview-fields-list">
                            <!-- JS will populate this -->
                        </div>
                        <div class="mt-4 pt-2 border-top">
                            <div class="btn btn-primary btn-sm w-100 rounded-pill disabled opacity-50">@lang('Update Profile')</div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3 mb-0 p-2 border-0 shadow-none">
                    <div class="d-flex align-items-start">
                        <i class="las la-exclamation-triangle me-2 fs-5"></i>
                        <div class="tiny">
                            @lang('This is a structural mockup. The actual frontend design depends on your active template.')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    .ls-1 { letter-spacing: 0.5px; }
    .tiny { font-size: 0.75rem; }
    .bg-lighter { background-color: #f8f9fa; }
    .field-row:hover { background-color: rgba(var(--bs-primary-rgb), 0.03); }
    .sticky-bottom-custom {
        position: sticky;
        bottom: 1rem;
        z-index: 1 !important;
        box-shadow: 0 -10px 20px rgba(0,0,0,0.05) !important;
    }
    .mock-profile { min-height: 400px; font-family: 'Public Sans', sans-serif; }
    .mock-field-item {
        margin-bottom: 1rem;
    }
    .mock-field-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        color: #697a8d;
        margin-bottom: 0.25rem;
    }
    .mock-field-input {
        height: 32px;
        background: #fcfdfe;
        border: 1px solid #d9dee3;
        border-radius: 4px;
        width: 100%;
    }
    .registration-field-group:last-child { border-bottom: none !important; }
</style>
@endpush

@push('script')
<script>
(function () {
    'use strict';
    
    // Search Filter
    const searchInput = document.getElementById('fieldSearch');
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.field-row').forEach(row => {
            const text = row.getAttribute('data-search');
            row.style.display = text.includes(query) ? '' : 'none';
        });
        
        // Hide empty cards
        document.querySelectorAll('.field-group-card').forEach(card => {
            const visibleRows = card.querySelectorAll('.field-row:not([style*="display: none"])').length;
            card.style.display = visibleRows > 0 ? '' : 'none';
        });
    });

    // Bulk Actions
    document.getElementById('checkAll').addEventListener('click', () => {
        document.querySelectorAll('.profile-field-cb').forEach(cb => {
            cb.checked = true;
            updatePreview();
        });
    });
    
    document.getElementById('uncheckAll').addEventListener('click', () => {
        document.querySelectorAll('.profile-field-cb').forEach(cb => {
            cb.checked = false;
            updatePreview();
        });
    });

    // Group Master Toggle
    document.querySelectorAll('.group-master-toggle').forEach(master => {
        master.addEventListener('change', function() {
            const group = this.getAttribute('data-group');
            document.querySelectorAll(`.profile-field-cb[data-group="${group}"]`).forEach(cb => {
                cb.checked = this.checked;
            });
            updatePreview();
        });
    });

    // Preview Logic
    function updatePreview() {
        const container = document.getElementById('preview-fields-list');
        container.innerHTML = '';
        
        const checked = document.querySelectorAll('.profile-field-cb:checked');
        if (checked.length === 0) {
            container.innerHTML = '<div class="text-center py-5"><i class="las la-ghost fs-1 text-muted opacity-25"></i><div class="text-muted small mt-2">@lang('No fields enabled')</div></div>';
            return;
        }

        // Only show first 6 fields in preview to keep it clean
        let count = 0;
        checked.forEach(cb => {
            if (count >= 8) return;
            const label = cb.closest('.field-row').querySelector('.fw-semibold').innerText;
            const item = document.createElement('div');
            item.className = 'mock-field-item animate__animated animate__fadeInSmall';
            item.innerHTML = `
                <span class="mock-field-label">${label}</span>
                <div class="mock-field-input"></div>
            `;
            container.appendChild(item);
            count++;
        });
        
        if (checked.length > 8) {
            const more = document.createElement('div');
            more.className = 'text-center text-muted tiny pt-2';
            more.innerHTML = `+ ${checked.length - 8} more fields...`;
            container.appendChild(more);
        }
    }

    document.querySelectorAll('.profile-field-cb').forEach(cb => {
        cb.addEventListener('change', updatePreview);
    });

    // Initial Preview
    updatePreview();

})();
</script>
@endpush
@endsection
