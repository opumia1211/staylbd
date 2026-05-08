@props([
    'disabled' => false,
    'label' => '',
    'id' => null,
    'type' => 'text',
    'error' => null,
])

@php
    $id = $id ?? md5($attributes->get('name', mt_rand()));
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
            {{ $label }}
            @if($attributes->has('required'))
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <input 
        {{ $disabled ? 'disabled' : '' }} 
        id="{{ $id }}"
        type="{{ $type }}"
        {!! $attributes->merge([
            'class' => 'w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors' . ($error ? ' border-rose-500 focus:border-rose-500 focus:ring-rose-500' : '')
        ]) !!}
    >

    @if($error)
        <p class="mt-1 text-sm text-rose-500">{{ $error }}</p>
    @endif
</div>
