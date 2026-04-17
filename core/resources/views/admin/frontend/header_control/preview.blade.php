<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Header Draft Preview</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', Arial, sans-serif; background: #fff; overflow-x: hidden; }
        .header-container { width: 100%; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .bar { 
            width: 100%; 
            display: flex; 
            align-items: center; 
            padding: 0 5%; 
            box-sizing: border-box; 
            transition: all 0.3s ease;
            position: relative;
            z-index: 10;
        }
        .muted { opacity: 0.8; font-size: 11px; font-weight: 500; letter-spacing: 0.3px; }
        .pill { 
            margin-left: 12px; 
            padding: 4px 10px; 
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2); 
            border-radius: 4px; 
            font-size: 11px; 
            font-weight: 600;
            white-space: nowrap;
        }
        .main-bar { border-bottom: 1px solid rgba(0,0,0,0.05); }
        .logo-box { font-weight: 800; font-size: 18px; letter-spacing: -0.5px; margin-right: 20px; }
        .search-mock { 
            flex-grow: 1; 
            max-width: 400px; 
            height: 34px; 
            background: rgba(0,0,0,0.04); 
            border-radius: 6px; 
            margin: 0 20px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: #94a3b8;
            font-size: 13px;
        }
        .icon-mock { width: 32px; height: 32px; background: rgba(0,0,0,0.05); border-radius: 50%; margin-left: 10px; }
        .nav-link { margin-right: 18px; font-size: 13px; font-weight: 600; display: flex; align-items: center; }
        .dropdown-mark { margin-left: 4px; opacity: 0.5; font-size: 10px; }
    </style>
</head>
<body>
@php
    $a = (array) ($config['appearance'] ?? []);
    $top = (array) ($config['top_bar'] ?? []);
    $main = (array) ($config['main_bar'] ?? []);
    $menu = (array) ($config['menu_bar'] ?? []);
    
    $navLinks = is_array($menu['nav_links'] ?? null) ? $menu['nav_links'] : [];
    $customBtns = is_array($menu['custom_buttons'] ?? null) ? $menu['custom_buttons'] : [];
    $allLinks = array_merge($navLinks, $customBtns);
    
    usort($allLinks, static function (array $x, array $y): int {
        return (int) ($x['display_order'] ?? 0) <=> (int) ($y['display_order'] ?? 0);
    });
@endphp

<div class="header-container">
    @if(!empty($top['enabled']))
    <div class="bar" style="height: {{ (int)($a['top_height'] ?? 38) }}px; background: {{ $a['top_bg'] ?? '#0f172a' }}; color: #fff;">
        <span class="muted"><i class="las la-phone"></i> {{ $top['support_label'] ?? '24/7 Support' }}: {{ $top['support_phone'] ?? '' }}</span>
        <div style="flex-grow: 1;"></div>
        @foreach((array) ($top['custom_buttons'] ?? []) as $btn)
            @if((int)($btn['is_active'] ?? 1) === 1)
                <span class="pill">{{ $btn['label'] ?? 'Link' }}</span>
            @endif
        @endforeach
    </div>
    @endif

    @if(!empty($main['enabled']))
    <div class="bar main-bar" style="height: {{ (int)($a['main_height'] ?? 56) }}px; background: {{ $a['main_bg'] ?? '#f8fafc' }}; color: #0f172a;">
        <div class="logo-box">STAYLBD</div>
        <div class="search-mock">Search products...</div>
        <div style="flex-grow: 1;"></div>
        <div class="icon-mock"></div>
        <div class="icon-mock"></div>
        <div class="icon-mock"></div>
    </div>
    @endif

    @if(!empty($menu['enabled']))
    <div class="bar" style="height: {{ (int)($a['menu_height'] ?? 38) }}px; background: {{ $a['menu_bg'] ?? '#c7eafe' }}; color: #0f172a;">
        @if(!empty($menu['show_sidebar_trigger']))
            <span class="nav-link" style="margin-right: 25px;"><i class="las la-bars"></i></span>
        @endif
        @foreach($allLinks as $btn)
            @if((int)($btn['is_active'] ?? 1) === 1)
                <div class="nav-link">
                    {{ $btn['label'] ?? 'Link' }}
                    @if(($btn['type'] ?? 'link') === 'dropdown')
                        <span class="dropdown-mark">▼</span>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
    @endif
</div>
</body>
</html>

