@php
    $strokeWidth = $strokeWidth ?? '0';
    $class = $class ?? '';
    $rawName = (string) $name;
    $rawName = str_replace(['fa ', 'fas ', 'far ', 'fab ', 'fa-'], '', $rawName);
    $nameLower = strtolower(trim($rawName));
    
    // Mapping - REORDERED for specific matches first
    $mappedName = match(true) {
        str_contains($nameLower, 'microphone') || $nameLower === 'mic' => 'mic',
        str_contains($nameLower, 'apple') || in_array($nameLower, ['ios', 'mac', 'iphone']) => 'apple',
        str_contains($nameLower, 'android') => 'android',
        str_contains($nameLower, 'windows') || str_contains($nameLower, 'microsoft') => 'windows',
        str_contains($nameLower, 'truck') || in_array($nameLower, ['shipping-fast', 'delivery', 'truck-moving', 'truck-loading']) => 'truck',
        str_contains($nameLower, 'phone') || str_contains($nameLower, 'mobile') || in_array($nameLower, ['cell', 'mobile-alt', 'mobile-device']) => 'mobile',
        str_contains($nameLower, 'cart') || in_array($nameLower, ['shopping-cart', 'shopping-cart-alt']) => 'cart',
        str_contains($nameLower, 'bag') || in_array($nameLower, ['shopping-bag']) => 'bag',
        str_contains($nameLower, 'box') || in_array($nameLower, ['package', 'box-open']) => 'box',
        str_contains($nameLower, 'envelope') || in_array($nameLower, ['mail', 'mail-bulk']) => 'envelope',
        str_contains($nameLower, 'marker') || str_contains($nameLower, 'map-pin') || str_contains($nameLower, 'location') || $nameLower === 'map-marker-alt' => 'marker',
        str_contains($nameLower, 'lang') || str_contains($nameLower, 'globe') || $nameLower === 'language' => 'globe',
        str_contains($nameLower, 'user') => 'user',
        str_contains($nameLower, 'comment') || in_array($nameLower, ['message', 'chat', 'comments']) => 'comments',
        str_contains($nameLower, 'search') => 'search',
        str_contains($nameLower, 'camera') || str_contains($nameLower, 'scan') || $nameLower === 'camera-retro' => 'camera',
        str_contains($nameLower, 'desktop') || str_contains($nameLower, 'monitor') => 'desktop',
        str_contains($nameLower, 'heart') => 'heart',
        str_contains($nameLower, 'bell') || $nameLower === 'notification' => 'bell',
        str_contains($nameLower, 'exchange') || str_contains($nameLower, 'compare') || str_contains($nameLower, 'arrow-h') || $nameLower === 'arrows-h' => 'exchange',
        str_contains($nameLower, 'home') => 'home',
        str_contains($nameLower, 'times') || str_contains($nameLower, 'close') || $nameLower === 'x' => 'times',
        str_contains($nameLower, 'bars') || str_contains($nameLower, 'menu') || $nameLower === 'hamburger' => 'bars',
        default => $nameLower
    };
@endphp

@if($mappedName == 'mic')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 14c1.66 0 3.01-1.34 3.01-3L15 5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"></path></svg>
@elseif($mappedName == 'search')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></svg>
@elseif($mappedName == 'camera')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path><path d="M7 2L5.17 4H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3.17L17 2H7zM5.83 6h12.34l1.83 2H22v10H2V8h4.17l-.34-2z"></path></svg>
@elseif($mappedName == 'apple')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.1 2.48-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .76-3.27.82-1.31.05-2.31-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"></path></svg>
@elseif($mappedName == 'android')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M7 11c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm10 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm.67-4.18l1.39-2.41a.48.48 0 0 0-.17-.66.48.48 0 0 0-.66.17l-1.42 2.47a9.92 9.92 0 0 0-9.61 0l-1.41-2.47a.501.501 0 1 0-.85.49L6.33 6.82A10.05 10.05 0 0 0 2 15.5c0 .35.03.7.07 1.04h19.86c.04-.34.07-.69.07-1.04 0-3.66-1.95-6.87-4.83-8.68z"></path></svg>
@elseif($mappedName == 'windows')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M3 11.83l9.08-1.24V2L3 3.2v8.63zm9.08 1.13l-9.08-1.24V20.8L12.08 22V12.96zm1.14-12.28v11.16L21 12.96V1.5l-7.78-.82zM21 18.06V14.1l-7.78-.83V23.2L21 21.8v-3.74z"></path></svg>
@elseif($mappedName == 'desktop')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7l-2 3v1h8v-1l-2-3h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 12H3V4h18v10z"></path></svg>
@elseif($mappedName == 'cart')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12 1.1-2h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49a1 1 0 0 0-.87-1.48L5.21 4 4.27 2 1 2v2h2l3.6 7.59-1.35 2.44C4.83 14.75 5.34 16 6.5 16H19v-2H7.42c-.14 0-.25-.11-.25-.25z"></path></svg>
@elseif($mappedName == 'box')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M21 16.5c0 .38-.21.71-.53.88l-7.97 4.43a1.002 1.002 0 0 1-.94 0L3.53 17.38C3.21 17.21 3 16.89 3 16.5V7.5c0-.38.21-.71.53-.88l7.97-4.43a1.002 1.002 0 0 1 .94 0l7.97 4.43c.32.17.53.5.53.88v9zM12 4.15L6.04 7.5 12 10.85l5.96-3.35L12 4.15zM5 15.91l6 3.33V12.4L5 9.07v6.84zm14 0V9.07l-6 3.33v6.84l6-3.33z"></path></svg>
@elseif($mappedName == 'heart')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
@elseif($mappedName == 'marker')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.17-7-7-7zm0 9.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"></path></svg>
@elseif($mappedName == 'envelope')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path></svg>
@elseif($mappedName == 'truck')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM17 12V9.5h2.5l1.97 2.5H17z"></path></svg>
@elseif($mappedName == 'globe')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95a15.65 15.65 0 0 0-1.38-3.56A8.03 8.03 0 0 1 18.92 8zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM2.08 12c.16.64.26 1.31.26 2s-.1 1.36-.26 2H4.26c-.16-.64-.26-1.31-.26-2s.1-1.36.26-2H2.08zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96z"></path></svg>
@elseif($mappedName == 'user')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
@elseif($mappedName == 'bell')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4a1.5 1.5 0 0 0-3 0v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"></path></svg>
@elseif($mappedName == 'home')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg>
@elseif($mappedName == 'mobile')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M17 1H7c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 18H7V5h10v14zm-5 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path></svg>
@elseif($mappedName == 'exchange')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M6.99 11L3 15l3.99 4v-3h7.99a2 2 0 0 0 2-2v-5H14.99v5H6.99v-3zm10.02-3h-7.99a2 2 0 0 0-2 2v5H8.99v-5h8.02v3L21 9l-3.99-4v3z"></path></svg>
@elseif($mappedName == 'comments')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"></path></svg>
@else
    {{-- Super-clean fallback: SOLID Info Circle --}}
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
@endif
