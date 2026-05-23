{{-- Canonical storefront Compare icon (arrow-left-right) — same everywhere on public pages --}}
@php
    $size = isset($size) ? (int) $size : null;
    $class = trim(($class ?? 'action-icon'));
@endphp
<svg @if($size) width="{{ $size }}" height="{{ $size }}" @endif viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}" aria-hidden="true">
    <path d="M8 3 4 7l4 4"/>
    <path d="M4 7h16"/>
    <path d="m16 21 4-4-4-4"/>
    <path d="M20 17H4"/>
</svg>
