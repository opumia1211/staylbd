@props(['variant' => 'primary', 'icon' => null])

@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 shadow-sm shadow-indigo-200/50',
        'secondary' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 active:bg-slate-300',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800 shadow-sm shadow-emerald-200/50',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 active:bg-rose-800 shadow-sm shadow-rose-200/50',
        'outline' => 'bg-transparent border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300',
        'dark' => 'bg-slate-800 text-white hover:bg-slate-900 active:bg-slate-950 shadow-sm shadow-slate-900/20',
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button {{ $attributes->merge(['class' => 'admin-button ' . $variantClass]) }}>
    @if($icon)
        <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'mr-2' }}"></i>
    @endif
    {{ $slot }}
</button>

