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
        str_contains($nameLower, 'sync') => 'exchange',
        in_array($nameLower, ['th-large', 'th_large', 'th'], true) || str_contains($nameLower, 'grid-') => 'grid',
        in_array($nameLower, ['list-alt', 'clipboard-list', 'tasks', 'list'], true) => 'list',
        str_contains($nameLower, 'money') || str_contains($nameLower, 'bill-wave') => 'money',
        in_array($nameLower, ['sign-in-alt', 'sign-in'], true) => 'signin',
        str_contains($nameLower, 'sign-out') || str_contains($nameLower, 'signout') => 'signout',
        $nameLower === 'key' => 'key',
        $nameLower === 'eye' => 'eye',
        str_contains($nameLower, 'bolt') => 'bolt',
        $nameLower === 'haykal' => 'star',
        str_contains($nameLower, 'home') => 'home',
        str_contains($nameLower, 'times-circle') || str_contains($nameLower, 'timescircle') => 'times-circle',
        str_contains($nameLower, 'times') || str_contains($nameLower, 'close') || $nameLower === 'x' => 'times',
        str_contains($nameLower, 'bars') || str_contains($nameLower, 'menu') || $nameLower === 'hamburger' => 'bars',
        in_array($nameLower, ['tag', 'tags'], true) => 'tag',
        str_contains($nameLower, 'credit-card') || $nameLower === 'creditcard' => 'creditcard',
        str_contains($nameLower, 'angle-double') => 'angles-up',
        str_contains($nameLower, 'angle-left') => 'chevron-left',
        str_contains($nameLower, 'angle-right') => 'chevron-right',
        str_contains($nameLower, 'angle-up') => 'chevron-up',
        str_contains($nameLower, 'angle-down') => 'chevron-down',
        in_array($nameLower, ['minus', 'subtract', 'remove'], true) => 'minus',
        in_array($nameLower, ['plus', 'add', 'plus-circle'], true) => 'plus',
        str_contains($nameLower, 'whatsapp') || $nameLower === 'wa' => 'whatsapp',
        str_contains($nameLower, 'facebook') => 'facebook',
        $nameLower === 'twitter' || str_contains($nameLower, 'x-twitter') => 'twitter',
        in_array($nameLower, ['link', 'chain'], true) || $nameLower === 'fa-link' => 'link',
        $nameLower === 'paperclip' || str_contains($nameLower, 'paperclip') => 'paperclip',
        in_array($nameLower, ['check', 'checkmark'], true) => 'check',
        str_contains($nameLower, 'check-circle') => 'check-circle',
        str_contains($nameLower, 'exclamation-circle') || str_contains($nameLower, 'exclamationcircle') => 'exclamation-circle',
        in_array($nameLower, ['print', 'printer'], true) => 'print',
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
@elseif($mappedName == 'times')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet" aria-hidden="true"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg>
@elseif($mappedName == 'times-circle')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet" aria-hidden="true"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"></path></svg>
@elseif($mappedName == 'bars')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet" aria-hidden="true"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path></svg>
@elseif($mappedName == 'mobile')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M17 1H7c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 18H7V5h10v14zm-5 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path></svg>
@elseif($mappedName == 'exchange')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M6.99 11L3 15l3.99 4v-3h7.99a2 2 0 0 0 2-2v-5H14.99v5H6.99v-3zm10.02-3h-7.99a2 2 0 0 0-2 2v5H8.99v-5h8.02v3L21 9l-3.99-4v3z"></path></svg>
@elseif($mappedName == 'comments')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"></path></svg>
@elseif($mappedName == 'grid')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"></path></svg>
@elseif($mappedName == 'list')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h10v2H4v-2z"></path></svg>
@elseif($mappedName == 'money')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93V19h-2v.93c-3.95-.49-7-3.85-7-7.93h2c0 3.31 2.69 6 6 6s6-2.69 6-6h2c0 4.08-3.05 7.44-7 7.93zM13 7h-2V5.08c-1.72.45-3 2-3 3.92h2c0-.55.45-1 1-1s1 .45 1 1c0 2-3 1.75-3 5h2c0-1.25.75-1.63 1.47-2.09.68-.43 1.53-.91 1.53-2.41 0-1.86-1.27-3.44-3-3.92V7z"></path></svg>
@elseif($mappedName == 'signin')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"></path></svg>
@elseif($mappedName == 'signout')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"></path></svg>
@elseif($mappedName == 'key')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12.65 10A5.99 5.99 0 0 0 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6a5.99 5.99 0 0 0 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"></path></svg>
@elseif($mappedName == 'eye')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path></svg>
@elseif($mappedName == 'bolt')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M11 21h-1l1-7H7.5c-.58 0-.57-.32-.38-.66.19-.34.05-.08.07-.11C8.48 10.94 10.42 7.87 13 3h1l-1 7h3.5c.49 0 .56.33.47.51l-.07.15C12.96 17.55 11 21 11 21z"></path></svg>
@elseif($mappedName == 'star')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
@elseif($mappedName == 'tag')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"></path></svg>
@elseif($mappedName == 'creditcard')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"></path></svg>
@elseif($mappedName == 'angles-up')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6zm0-5L12 5.83l4.59 4.58L18 9l-6-6-6 6z"></path></svg>
@elseif($mappedName == 'chevron-left')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path></svg>
@elseif($mappedName == 'chevron-right')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path></svg>
@elseif($mappedName == 'chevron-up')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"></path></svg>
@elseif($mappedName == 'chevron-down')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"></path></svg>
@elseif($mappedName == 'minus')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M19 13H5v-2h14v2z"></path></svg>
@elseif($mappedName == 'plus')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
@elseif($mappedName == 'whatsapp')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.03-1.38l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884M8.46 6.82L7.91 6.296a.716.716 0 00-.995.007.704.704 0 00-.007.995l.52.573c.127-.012.255-.02.385-.02.003 0 .005 0 .007.002a.717.717 0 01.254-.53z"></path></svg>
@elseif($mappedName == 'facebook')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.99 3.66 9.13 8.44 9.88v-6.99h-2.54v-2.89h2.54V9.84c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.89h-2.33v6.99C18.34 21.13 22 16.98 22 12z"></path></svg>
@elseif($mappedName == 'twitter')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 01-1.93.07 4.28 4.28 0 004 2.98 8.521 8.521 0 01-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"></path></svg>
@elseif($mappedName == 'link')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"></path></svg>
@elseif($mappedName == 'print')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-11c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zM18 3H6v4h12V3z"></path></svg>
@elseif($mappedName == 'paperclip')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5a2.5 2.5 0 0 1 5 0v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5a2.5 2.5 0 0 0 5 0V5a4 4 0 0 0-8 0v12.5a5 5 0 0 0 10 0V6h-1.5z"></path></svg>
@elseif($mappedName == 'check')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path></svg>
@elseif($mappedName == 'check-circle')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
@elseif($mappedName == 'exclamation-circle')
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path></svg>
@else
    {{-- Super-clean fallback: SOLID Info Circle --}}
    <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" fill="currentColor" preserveAspectRatio="xMidYMid meet"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
@endif
