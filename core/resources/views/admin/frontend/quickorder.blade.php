@extends('admin.layouts.app')
@section('panel')
<div class="row g-4">
    <!-- Quick Order Intelligence: Field Controls -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded bg-label-success"><i class="las la-bolt"></i></span>
                    </div>
                    <div>
                        <h5 class="mb-0">@lang('Quick Order Intelligence Hub')</h5>
                        <small class="text-muted">@lang('Manage lightning-fast checkout fields')</small>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-label-primary rounded-pill px-3 py-2">
                        {{ $enabledCount }} / {{ $totalCount }} @lang('Fields Active')
                    </span>
                    <div class="btn-group shadow-sm">
                        <button type="button" class="btn btn-outline-secondary btn-sm qo-copy-url" data-url="{{ $quickOrderUrl }}" title="@lang('Copy link')">
                            <i class="las la-link"></i>
                        </button>
                        <a href="{{ $quickOrderUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="las la-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.frontend.quickorder.save') }}" method="POST" id="quickOrderForm">
            @csrf
            
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="input-group input-group-merge w-50 shadow-sm border rounded">
                    <span class="input-group-text border-0 bg-white"><i class="las la-search text-muted"></i></span>
                    <input type="text" id="qoSearch" class="form-control border-0 px-1" placeholder="@lang('Search fields... (e.g. phone, address)')">
                </div>
                <div class="btn-group shadow-sm">
                    <button type="button" class="btn btn-outline-secondary btn-sm qo-select-all">@lang('Select All')</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm qo-deselect-all">@lang('Reset')</button>
                </div>
            </div>

            @foreach($grouped as $groupKey => $group)
            <div class="card border-0 shadow-sm mb-4 qo-group-card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-primary p-2 me-3 rounded">
                            <i class="{{ $group['icon'] }} fs-4"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 text-uppercase fw-bold ls-1">{{ $group['title'] }}</h6>
                            <small class="text-muted d-none d-md-block">{{ $group['summary'] }}</small>
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
                                    <th class="py-2 small fw-bold">@lang('TYPE')</th>
                                    <th class="text-end pe-4 py-2 small fw-bold">@lang('VISIBILITY')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['fields'] as $fkey => $meta)
                                @php 
                                    $label = is_array($meta) ? ($meta['label'] ?? $fkey) : $meta; 
                                    $required = is_array($meta) && !empty($meta['required']); 
                                @endphp
                                <tr class="qo-field-row" data-search="{{ strtolower($label) }} {{ strtolower($fkey) }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2 text-muted"><i class="las la-grip-vertical"></i></div>
                                            <div>
                                                <span class="fw-semibold text-heading">{{ $label }}</span>
                                                @if($required)
                                                <span class="ms-2 badge bg-label-danger tiny-badge">@lang('Mandatory')</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><code class="small text-muted">{{ $fkey }}</code></td>
                                    <td class="text-end pe-4">
                                        <div class="form-check form-switch d-inline-block">
                                            <input type="hidden" name="quick_order_fields[{{ $fkey }}]" value="0">
                                            <input type="checkbox" class="form-check-input qo-field-cb" name="quick_order_fields[{{ $fkey }}]" value="1" id="qo_field_{{ $fkey }}" {{ in_array($fkey, $enabled, true) ? 'checked' : '' }} data-group="{{ $groupKey }}">
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
                        <i class="las la-info-circle me-1"></i> @lang('Public Quick Order popup will update instantly.')
                    </div>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="las la-save me-2"></i> @lang('Deploy Form')
                    </button>
                </div>
            </div>

            <!-- Hidden settings to be managed in the sidebar but submitted with the form -->
            <input type="hidden" name="quick_order_subtitle" id="hidden_subtitle">
            <input type="hidden" name="show_register_link" id="hidden_register_link" value="0">
        </form>
    </div>

    <!-- Tactical Preview & Settings -->
    <div class="col-xl-4 col-lg-5">
        <!-- Settings Panel -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="las la-cog me-2 fs-4 text-primary"></i>
                    <h6 class="mb-0">@lang('Hub Settings')</h6>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-1">@lang('Popup Subtitle')</label>
                    <textarea id="sidebar_subtitle" class="form-control form-control-sm" rows="2" maxlength="255" placeholder="@lang('Enter subtitle...')">{{ $settings['subtitle'] ?? '' }}</textarea>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="sidebar_show_register" {{ !isset($settings['show_register_link']) || $settings['show_register_link'] ? 'checked' : '' }}>
                    <label class="form-check-label small fw-bold text-muted" for="sidebar_show_register">@lang('Show Register Link')</label>
                </div>
            </div>
        </div>

        <!-- Tactical Preview -->
        <div class="card border-0 shadow-sm sticky-top" style="top: 130px; z-index: 1 !important;">
            <div class="card-header bg-dark text-white py-3">
                <div class="d-flex align-items-center">
                    <i class="las la-eye me-2 fs-4 text-success"></i>
                    <h6 class="mb-0 text-white">@lang('Tactical Preview')</h6>
                </div>
            </div>
            <div class="card-body p-4 bg-lighter">
                <div class="mock-popup rounded bg-white shadow-lg overflow-hidden border">
                    <div class="mock-header p-3 text-center border-bottom bg-light">
                        <h5 class="mb-1 fw-bold text-dark">@lang('Quick Order')</h5>
                        <div id="preview-subtitle" class="tiny text-muted px-2"></div>
                    </div>
                    <div class="mock-body p-3">
                        <div id="preview-fields-list">
                            <!-- JS will populate this -->
                        </div>
                        <div class="mt-3">
                            <div class="btn btn-success btn-sm w-100 fw-bold py-2 shadow-sm disabled opacity-75">@lang('Confirm Order')</div>
                        </div>
                        <div id="preview-register" class="text-center mt-3 tiny">
                            <span class="text-muted">@lang('New customer?')</span> <span class="text-primary fw-bold">@lang('Register here')</span>
                        </div>
                    </div>
                </div>
                <div class="alert bg-label-primary mt-3 mb-0 p-2 border-0">
                    <div class="d-flex align-items-start">
                        <i class="las la-info-circle me-2 fs-5"></i>
                        <div class="tiny">
                            @lang('This mockup reflects the structural layout of the Quick Order popup.')
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
    .tiny-badge { font-size: 0.65rem; padding: 0.2rem 0.5rem; }
    .bg-lighter { background-color: #f8f9fa; }
    .qo-field-row:hover { background-color: rgba(var(--bs-primary-rgb), 0.03); }
    .sticky-bottom-custom {
        position: sticky;
        bottom: 1rem;
        z-index: 1 !important;
        box-shadow: 0 -10px 20px rgba(0,0,0,0.05) !important;
    }
    .mock-popup { min-height: 400px; font-family: 'Public Sans', sans-serif; border-radius: 12px !important; }
    .mock-field-item { margin-bottom: 0.75rem; }
    .mock-field-label { display: block; font-size: 0.65rem; font-weight: 700; color: #566a7f; margin-bottom: 0.2rem; text-transform: uppercase; }
    .mock-field-input { height: 32px; background: #fff; border: 1px solid #d9dee3; border-radius: 6px; width: 100%; }
</style>
@endpush

@push('script')
<script>
(function () {
    'use strict';
    
    const subtitleInput = document.getElementById('sidebar_subtitle');
    const registerToggle = document.getElementById('sidebar_show_register');
    const hiddenSubtitle = document.getElementById('hidden_subtitle');
    const hiddenRegister = document.getElementById('hidden_register_link');

    // Sync settings to hidden fields
    function syncSettings() {
        hiddenSubtitle.value = subtitleInput.value;
        hiddenRegister.value = registerToggle.checked ? 1 : 0;
        
        // Update preview
        document.getElementById('preview-subtitle').innerText = subtitleInput.value || '@lang('Order in seconds')';
        document.getElementById('preview-register').style.display = registerToggle.checked ? 'block' : 'none';
    }

    subtitleInput.addEventListener('input', syncSettings);
    registerToggle.addEventListener('change', syncSettings);
    syncSettings(); // Initial sync

    // Search Filter
    const searchInput = document.getElementById('qoSearch');
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.qo-field-row').forEach(row => {
            const text = row.getAttribute('data-search');
            row.style.display = text.includes(query) ? '' : 'none';
        });
        
        document.querySelectorAll('.qo-group-card').forEach(card => {
            const visibleRows = card.querySelectorAll('.qo-field-row:not([style*="display: none"])').length;
            card.style.display = visibleRows > 0 ? '' : 'none';
        });
    });

    // Bulk Actions
    document.querySelector('.qo-select-all').addEventListener('click', () => {
        document.querySelectorAll('.qo-field-cb').forEach(cb => {
            cb.checked = true;
            updatePreview();
        });
    });
    
    document.querySelector('.qo-deselect-all').addEventListener('click', () => {
        document.querySelectorAll('.qo-field-cb').forEach(cb => {
            cb.checked = false;
            updatePreview();
        });
    });

    // Group Master Toggle
    document.querySelectorAll('.group-master-toggle').forEach(master => {
        master.addEventListener('change', function() {
            const group = this.getAttribute('data-group');
            document.querySelectorAll(`.qo-field-cb[data-group="${group}"]`).forEach(cb => {
                cb.checked = this.checked;
            });
            updatePreview();
        });
    });

    // Preview Logic
    function updatePreview() {
        const container = document.getElementById('preview-fields-list');
        container.innerHTML = '';
        
        const checked = document.querySelectorAll('.qo-field-cb:checked');
        if (checked.length === 0) {
            container.innerHTML = '<div class="text-center py-5 text-muted small">@lang('No fields selected')</div>';
            return;
        }

        let count = 0;
        checked.forEach(cb => {
            if (count >= 5) return;
            const label = cb.closest('.qo-field-row').querySelector('.fw-semibold').innerText;
            const item = document.createElement('div');
            item.className = 'mock-field-item animate__animated animate__fadeInSmall';
            item.innerHTML = `
                <span class="mock-field-label">${label}</span>
                <div class="mock-field-input"></div>
            `;
            container.appendChild(item);
            count++;
        });
        
        if (checked.length > 5) {
            const more = document.createElement('div');
            more.className = 'text-center text-muted tiny pt-1';
            more.innerHTML = `+ ${checked.length - 5} @lang('more fields')...`;
            container.appendChild(more);
        }
    }

    document.querySelectorAll('.qo-field-cb').forEach(cb => {
        cb.addEventListener('change', updatePreview);
    });

    // Copy URL
    document.querySelector('.qo-copy-url').addEventListener('click', function() {
        const url = this.getAttribute('data-url');
        const el = document.createElement('textarea');
        el.value = url;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        notify('success', '@lang('Link copied to clipboard')');
    });

    updatePreview();

})();
</script>
@endpush
@endsection
