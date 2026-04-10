@props(['icon', 'title', 'subtitle', 'color' => 'zenis-primary'])

@php
    $colorClass = [
        'zenis-primary' => 'bg-zenis-primary/10 text-zenis-primary group-hover:bg-zenis-primary group-hover:text-white',
        'zenis-secondary' => 'bg-zenis-secondary/10 text-zenis-secondary group-hover:bg-zenis-secondary group-hover:text-white',
        'emerald' => 'bg-emerald-500/10 text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white',
        'rose' => 'bg-rose-500/10 text-rose-500 group-hover:bg-rose-500 group-hover:text-white',
    ][$color] ?? 'bg-zenis-primary/10 text-zenis-primary group-hover:bg-zenis-primary group-hover:text-white';
@endphp

<div class="group flex items-center gap-6 p-8 bg-white rounded-2xl border border-transparent hover:border-gray-100 hover:shadow-xl hover:shadow-gray-500/5 transition-all duration-500 cursor-default">
    <div class="size-16 shrink-0 rounded-2xl flex items-center justify-center text-3xl transition-all duration-500 {{ $colorClass }}">
        <i class="hgi hgi-stroke {{ $icon }}"></i>
    </div>
    <div class="flex flex-col">
        <h5 class="text-lg font-black text-gray-800 mb-1 group-hover:text-{{ $color }} transition-colors">
            {{ __($title) }}
        </h5>
        <p class="text-sm font-bold text-gray-400">
            {{ __($subtitle) }}
        </p>
    </div>
</div>
