@props(['label' => null, 'error' => null, 'icon' => null])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label {{ $attributes->has('id') ? 'for='.$attributes->get('id') : '' }} class="block text-sm font-semibold text-slate-700 mb-1.5 ml-0.5">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <i class="{{ $icon }}"></i>
            </div>
        @endif
        
        <input {{ $attributes->except('class')->merge(['class' => 'admin-input' . ($icon ? ' pl-11' : '') . ($error ? ' border-rose-500 focus:ring-rose-100' : '')]) }}>
        
        @if($error)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <i class="las la-exclamation-circle text-rose-500 text-lg"></i>
            </div>
        @endif
    </div>
    
    @if($error)
        <p class="mt-1.5 text-[0.8rem] font-medium text-rose-600 ml-0.5">{{ $error }}</p>
    @endif
</div>

