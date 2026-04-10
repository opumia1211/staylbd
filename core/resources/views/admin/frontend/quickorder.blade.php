@extends('admin.layouts.app')
@section('panel')
<div class="quickorder-control-board">
    <div class="card border-0 shadow-sm mb-3 qo-compact-header">
        <div class="card-body py-2 px-3 d-flex flex-column gap-2">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="badge bg-primary-soft text-primary d-inline-flex align-items-center gap-1">
                        <i class="las la-bolt"></i>
                        <span>@lang('Quick Order')</span>
                    </span>
                    <span class="small text-muted">
                        {{ $enabledCount }} / {{ $totalCount }} @lang('fields enabled')
                    </span>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="qo-url small text-truncate">
                        <i class="las la-link text-muted me-1"></i>
                        <code class="text-break">{{ $quickOrderUrl }}</code>
                    </span>
                    <button type="button" class="btn btn-outline-primary btn-xs qo-copy-url" data-url="{{ $quickOrderUrl }}" title="@lang('Copy link')">
                        <i class="las la-copy"></i>
                    </button>
                    <a href="{{ $quickOrderUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-xs">
                        <i class="las la-external-link-alt me-1"></i> @lang('Open')
                    </a>
                    <a href="{{ route('admin.frontend.sections.general') }}" class="btn btn-outline-secondary btn-xs">
                        <i class="las la-arrow-left me-1"></i> @lang('Manage Section')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.frontend.quickorder.save') }}" method="POST" id="quickOrderForm">
        @csrf
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 mb-1 mb-lg-0">
                        <i class="las la-sliders-h text-primary"></i>
                        <span class="fw-semibold">@lang('Form fields')</span>
                    </div>
                    <div class="d-flex flex-column flex-md-row align-items-md-start gap-2 flex-grow-1">
                        <div class="flex-grow-1">
                            <label class="small text-muted mb-1" for="quick_order_subtitle">@lang('Header text on Quick Order popup')</label>
                            <textarea name="quick_order_subtitle"
                                      id="quick_order_subtitle"
                                      class="form-control form-control-sm qo-header-input"
                                      rows="2"
                                      maxlength="255"
                                      placeholder="@lang('Example: Place your order in seconds — no account needed. Our team will confirm by phone.')">{{ $settings['subtitle'] ?? '' }}</textarea>
                            <small class="text-muted d-block mt-1 qo-header-help">
                                @lang('Keep it short, friendly and clear. This line appears under “Quick Order” on the customer popup.')
                            </small>
                        </div>
                        <div class="d-flex gap-2 align-items-center justify-content-md-end pt-1 pt-md-0">
                    <div class="form-check form-switch d-flex align-items-center gap-1 me-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="show_register_link" name="show_register_link" value="1" {{ !isset($settings['show_register_link']) || $settings['show_register_link'] ? 'checked' : '' }}>
                        <label class="form-check-label small" for="show_register_link">@lang('Show register button on popup')</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary qo-select-all">@lang('Select all')</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary qo-deselect-all">@lang('Deselect all')</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="las la-save me-1"></i> @lang('Save')
                    </button>
                        </div>
                </div>
            </div>
            <div class="card-body p-2">
                @php $hasGrouped = !empty($grouped); @endphp
                @if($hasGrouped)
                <div class="row g-2 qo-group-grid">
                    @foreach($grouped as $groupKey => $group)
                    <div class="col-12 col-md-4">
                        <div class="qo-group border rounded-3 border-light h-100">
                            <div class="qo-group-header px-3 py-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $group['icon'] }} text-primary"></i>
                                    <h2 class="h6 mb-0 fw-semibold text-dark">{{ $group['title'] }}</h2>
                                </div>
                                <p class="small text-muted mb-0 mt-1 text-truncate" title="{{ $group['summary'] }}">{{ $group['summary'] }}</p>
                            </div>
                            <div class="qo-group-fields px-2 pb-2 pt-1">
                                <div class="row g-1">
                                    @foreach($group['fields'] as $fkey => $meta)
                                    @php $label = is_array($meta) ? ($meta['label'] ?? $fkey) : $meta; $required = is_array($meta) && !empty($meta['required']); @endphp
                                    <div class="col-12">
                                        <label class="qo-field-item {{ in_array($fkey, $enabled, true) ? 'qo-field-item--on' : '' }}" for="qo_field_{{ $fkey }}">
                                            <input type="hidden" name="quick_order_fields[{{ $fkey }}]" value="0">
                                            <input type="checkbox" class="form-check-input qo-field-cb" name="quick_order_fields[{{ $fkey }}]" value="1" id="qo_field_{{ $fkey }}" {{ in_array($fkey, $enabled, true) ? 'checked' : '' }} data-field="{{ $fkey }}">
                                            <span class="qo-field-label">{{ $label }}</span>
                                            @if($required)
                                            <span class="qo-field-badge badge bg-light text-dark border">@lang('Required')</span>
                                            @endif
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="qo-group p-2">
                    <div class="row g-1">
                        @foreach($fields as $fkey => $label)
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="qo-field-item {{ in_array($fkey, $enabled, true) ? 'qo-field-item--on' : '' }}" for="qo_field_{{ $fkey }}">
                                <input type="hidden" name="quick_order_fields[{{ $fkey }}]" value="0">
                                <input type="checkbox" class="form-check-input qo-field-cb" name="quick_order_fields[{{ $fkey }}]" value="1" id="qo_field_{{ $fkey }}" {{ in_array($fkey, $enabled, true) ? 'checked' : '' }}>
                                <span class="qo-field-label">{{ $label }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm bg-light">
        <div class="card-body py-3 px-4 small text-muted">
            <i class="las la-info-circle me-1"></i>
            @lang('Enabled fields appear on the public Quick Order popup in the same order as above. Header text and register button visibility are controlled from this page.')
        </div>
    </div>
</div>

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function() {
    document.querySelectorAll('.qo-select-all').forEach(function(btn) {
        btn.addEventListener('click', function() { document.querySelectorAll('.qo-field-cb').forEach(function(cb) { cb.checked = true; cb.closest('.qo-field-item').classList.add('qo-field-item--on'); }); });
    });
    document.querySelectorAll('.qo-deselect-all').forEach(function(btn) {
        btn.addEventListener('click', function() { document.querySelectorAll('.qo-field-cb').forEach(function(cb) { cb.checked = false; cb.closest('.qo-field-item').classList.remove('qo-field-item--on'); }); });
    });
    document.querySelectorAll('.qo-field-cb').forEach(function(cb) {
        cb.addEventListener('change', function() { this.closest('.qo-field-item').classList.toggle('qo-field-item--on', this.checked); });
    });
    document.querySelectorAll('.qo-copy-url').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-url');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() { /* copied */ });
            } else {
                var ta = document.createElement('textarea'); ta.value = url; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
            }
        });
    });
})();
</script>
@endpush
@endsection
