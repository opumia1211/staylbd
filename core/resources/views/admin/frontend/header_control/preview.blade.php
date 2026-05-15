@php
    $assetVersion = Cache::get('asset_version') ?? config('app.version');
    $activeTemplate = activeTemplate();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Header Preview</title>
    
    <link rel="stylesheet" href="{{ storefront_compiled_stylesheet_url('tailwind-product') }}">
    <link rel="stylesheet" href="{{ asset('assets/templates/basic/css/stayl-elite-core.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ storefront_compiled_stylesheet_url('critical-storefront') }}">
    
    @include('partials.storefront_ui_variables')
    
    <style>
        body { 
            padding: 0; 
            margin: 0; 
            background: #f8fafc; 
            overflow-x: hidden;
            /* Adjust padding-top to match header height to prevent content jump, 
               though in preview it doesn't matter as much as visual correctness */
            padding-top: var(--stayl-dynamic-header-height, 140px);
        }
        
        .preview-body {
            min-height: 200vh;
            padding: 40px 20px;
        }
        
        .preview-placeholder {
            max-width: 1200px;
            margin: 0 auto;
            border: 2px dashed #e2e8f0;
            border-radius: 24px;
            height: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            background: white;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }
        
        .preview-tag {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
        }
    </style>
</head>
<body class="antialiased">
    
    {{-- We pass the $headerControl (draft) explicitly to the partial --}}
    @include($activeTemplate . 'partials.header', ['headerControl' => $headerControl])
    
    <div class="preview-body">
        <div class="preview-placeholder">
            <div class="preview-tag">Live Preview Mode</div>
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px; opacity: 0.2;">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            <h2 style="color: #1e293b; font-size: 24px; font-weight: 800; margin-bottom: 8px;">Header Editor Preview</h2>
            <p style="font-size: 16px; max-width: 400px; text-align: center; line-height: 1.6;">
                This area simulates your website content. Scroll down to test how the header behaves when sticky.
            </p>
        </div>
        
        <div style="height: 1000px;"></div>
    </div>

    {{-- Essential scripts for header interactivity --}}
    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ url('serve-js/glass-header') }}?v={{ $assetVersion }}" defer></script>
    
    <script>
        // Simple theme toggle support for preview
        document.addEventListener('DOMContentLoaded', function() {
            const themeBtn = document.getElementById('staylThemeToggle');
            if (themeBtn) {
                themeBtn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    document.body.classList.toggle('dark-mode', isDark);
                });
            }
        });
    </script>
</body>
</html>
