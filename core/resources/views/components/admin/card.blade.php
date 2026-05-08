<div {{ $attributes->merge(['class' => 'admin-card']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/30 -mx-5 -mt-5 mb-5">
            <div class="flex items-center justify-between">
                <div class="font-bold text-slate-800">
                    {{ $header }}
                </div>
                @if(isset($header_action))
                    <div>{{ $header_action }}</div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="w-full">
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30 -mx-5 -mb-5 mt-5">
            {{ $footer }}
        </div>
    @endif
</div>

