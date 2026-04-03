@php
    $onLabel = $onLabel ?? 'ON (চালু)';
    $offLabel = $offLabel ?? 'OFF (বন্ধ)';
    $isOn = (int)($value ?? 0) === 1;
@endphp
<div class="config-item">
    <div class="config-info">
        <div class="config-title">{!! lang_en_bn($title) !!}</div>
        <div class="config-desc">{{ $desc }}</div>
    </div>
    <div class="config-control">
        <div class="switch-wrap" data-on="{{ $onLabel }}" data-off="{{ $offLabel }}">
            <input type="hidden" name="{{ $name }}" value="0">
            <input type="checkbox" class="form-check-input config-toggle-input" name="{{ $name }}" value="1" id="cfg_{{ $name }}" {{ $isOn ? 'checked' : '' }}>
            <span class="switch-status {{ $isOn ? 'status-on' : 'status-off' }}" id="status_{{ $name }}">{{ $isOn ? $onLabel : $offLabel }}</span>
        </div>
    </div>
</div>
