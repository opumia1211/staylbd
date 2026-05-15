@php
    $onLabel = $onLabel ?? trans('Enabled');
    $offLabel = $offLabel ?? trans('Disabled');
    $isOn = (int)($value ?? 0) === 1;
@endphp
<div class="list-group-item d-flex justify-content-between align-items-center py-3 border-0 border-bottom">
    <div class="me-3">
        <h6 class="mb-0 fw-bold text-dark">@lang($title)</h6>
        <small class="text-muted d-block mt-1" style="max-width: 80%;">@lang($desc)</small>
    </div>
    <div class="form-check form-switch mb-0">
        <input type="hidden" name="{{ $name }}" value="0">
        <input class="form-check-input config-toggle-input" type="checkbox" name="{{ $name }}" value="1" 
               id="cfg_{{ $name }}" {{ $isOn ? 'checked' : '' }} role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
        <label class="form-check-label d-none" for="cfg_{{ $name }}">@lang($title)</label>
    </div>
</div>
