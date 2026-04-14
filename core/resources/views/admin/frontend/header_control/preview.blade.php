<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Header Draft Preview</title>
    <style>
        body { margin: 0; font-family: Inter, Arial, sans-serif; background: #f1f5f9; }
        .bar { width: 100%; display: flex; align-items: center; padding: 0 14px; box-sizing: border-box; border-bottom: 1px solid rgba(15,23,42,.12); }
        .muted { opacity: .82; font-size: 12px; }
        .pill { margin-left: 10px; padding: 4px 8px; border: 1px solid rgba(15,23,42,.18); border-radius: 6px; font-size: 12px; }
    </style>
</head>
<body>
@php
    $a = (array) ($config['appearance'] ?? []);
    $top = (array) ($config['top_bar'] ?? []);
    $main = (array) ($config['main_bar'] ?? []);
    $menu = (array) ($config['menu_bar'] ?? []);
@endphp
<div class="bar" style="height: {{ (int)($a['top_height'] ?? 38) }}px; background: {{ $a['top_bg'] ?? '#0f172a' }}; color: #fff;">
    <span class="muted">{{ $top['support_label'] ?? '24/7 Support' }} {{ $top['support_phone'] ?? '' }}</span>
    @foreach((array) ($top['custom_buttons'] ?? []) as $btn)
        <span class="pill">{{ $btn['label'] ?? 'Button' }}</span>
    @endforeach
</div>
<div class="bar" style="height: {{ (int)($a['main_height'] ?? 56) }}px; background: {{ $a['main_bg'] ?? '#f8fafc' }}; color: #0f172a;">
    <strong>Logo</strong>
    <span class="pill">Logo {{ (int)($main['logo_max_height'] ?? 48) }}px</span>
    <span class="pill">Icons {{ (int)($main['icon_size'] ?? 48) }}px</span>
</div>
<div class="bar" style="height: {{ (int)($a['menu_height'] ?? 38) }}px; background: {{ $a['menu_bg'] ?? '#c7eafe' }}; color: #0f172a;">
    <span class="muted">Menu Links</span>
    @foreach((array) ($menu['custom_buttons'] ?? []) as $btn)
        <span class="pill">{{ $btn['label'] ?? 'Link' }}</span>
    @endforeach
</div>
</body>
</html>

