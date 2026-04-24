<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Header Draft Preview</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', Arial, sans-serif; background: #f3f4f6; overflow-x: hidden; padding: 20px; }
        .preview-label { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 8px; }
        .header-container { 
            width: 100%; 
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15); 
            border-radius: 12px; 
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .bar { 
            width: 100%; 
            display: flex; 
            align-items: center; 
            padding: 0 40px; 
            box-sizing: border-box; 
            transition: all 0.3s ease;
            position: relative;
            z-index: 10;
        }
        .muted { opacity: 0.85; font-size: 12px; font-weight: 600; letter-spacing: 0.3px; display: flex; align-items: center; gap: 8px; }
        .pill { 
            margin-left: 12px; 
            padding: 5px 12px; 
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.15); 
            border-radius: 6px; 
            font-size: 11px; 
            font-weight: 700;
            white-space: nowrap;
            text-transform: uppercase;
        }
        .main-bar { border-bottom: 1px solid rgba(0,0,0,0.06); }
        .logo-box { font-weight: 900; font-size: 22px; letter-spacing: -1px; margin-right: 30px; color: #0f172a; }
        .search-mock { 
            flex-grow: 1; 
            max-width: 480px; 
            height: 40px; 
            background: #f1f5f9; 
            border: 1px solid #e2e8f0;
            border-radius: 20px; 
            margin: 0 30px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            color: #94a3b8;
            font-size: 13px;
        }
        .icon-mock-group { display: flex; align-items: center; gap: 15px; }
        .icon-mock { width: 34px; height: 34px; background: #f1f5f9; border-radius: 50%; border: 1px solid #e2e8f0; }
        .nav-link { 
            margin-right: 24px; 
            font-size: 12px; 
            font-weight: 700; 
            display: flex; 
            align-items: center; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .dropdown-mark { margin-left: 6px; opacity: 0.6; font-size: 9px; }
        
        /* Pro Look Colors */
        .bar-top { background: #020617 !important; color: #ffffff !important; }
        .bar-menu { background: #0f172a !important; color: #ffffff !important; }
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

<div class="preview-label">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
    Live Frontend Preview (Professional Mode)
</div>

<div class="header-container">
    @if(!empty($top['enabled']))
    <div class="bar bar-top" style="height: {{ (int)($a['top_height'] ?? 46) }}px;">
        <span class="muted"><i class="las la-phone"></i> {{ $top['support_label'] ?? 'Support' }}: {{ $top['support_phone'] ?? '888-777-999' }}</span>
        <div style="flex-grow: 1;"></div>
        @foreach((array) ($top['custom_buttons'] ?? []) as $btn)
            @if((int)($btn['is_active'] ?? 1) === 1)
                <span class="pill">{{ $btn['label'] ?? 'Link' }}</span>
            @endif
        @endforeach
    </div>
    @endif

    @if(!empty($main['enabled']))
    <div class="bar main-bar" style="height: {{ (int)($a['main_height'] ?? 72) }}px; background: #ffffff;">
        <div class="logo-box">STAYLBD</div>
        <div class="search-mock">Search for products, brands and more...</div>
        <div style="flex-grow: 1;"></div>
        <div class="icon-mock-group">
            <div class="icon-mock"></div>
            <div class="icon-mock"></div>
            <div class="icon-mock"></div>
        </div>
    </div>
    @endif

    @if(!empty($menu['enabled']))
    <div class="bar bar-menu" style="height: {{ (int)($a['menu_height'] ?? 48) }}px;">
        @if(!empty($menu['show_sidebar_trigger']))
            <span class="nav-link" style="margin-right: 30px;"><i class="las la-bars" style="font-size: 20px;"></i></span>
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

