@props([
    'iso' => '',
    'class' => 'stayl-flag-img w-5 h-4 shrink-0 rounded-sm object-cover',
    'alt' => '',
])

@php
    $flagUrl = country_flag_url((string) $iso);
    $flagAlt = $alt !== '' ? $alt : strtoupper(preg_replace('/[^A-Za-z]/', '', (string) $iso));
@endphp

@if($flagUrl !== '')
    <img
        src="{{ $flagUrl }}"
        alt="{{ $flagAlt }}"
        {{ $attributes->merge(['class' => $class]) }}
        width="20"
        height="15"
        loading="lazy"
        decoding="async"
    >
@endif
