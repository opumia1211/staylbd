@php
    $bars = \App\Models\Frontend::where('data_keys', 'header_scrollbar.element')->orderBy('id', 'asc')->get();
    if ($bars->isEmpty()) return;
@endphp

<section class="stayl-announcement-scrollbar-root professional-scrollbar-shell">
    @foreach($bars as $bar)
        @php
            $dv = (array) ($bar->data_values ?? []);
            if (empty($dv['is_active']) || (int) $dv['is_active'] !== 1) continue;
            
            $bg = $dv['background_color'] ?? '#0f172a';
            $textColor = $dv['text_color'] ?? '#ffffff';
            $speed = $dv['animation_speed'] ?? '15s';
            $direction = ($dv['direction'] ?? 'left') === 'right' ? 'reverse' : 'normal';
            $items = $dv['items'] ?? [];
            if (is_object($items)) $items = (array) $items;
            if (empty($items)) continue;
        @endphp

        <div class="stayl-announcement-scrollbar" style="background: {{ $bg }}; color: {{ $textColor }}; --stayl-scroll-speed: {{ $speed }}; --stayl-scroll-dir: {{ $direction }};">
            <div class="stayl-announcement-scrollbar__track">
                <div class="stayl-announcement-scrollbar__content">
                    @foreach(array_merge($items, $items) as $item)
                        @php
                            $type = $item['type'] ?? 'text';
                            $content = trim((string) ($item['content'] ?? ''));
                            if ($content === '') continue;
                        @endphp
                        
                        <div class="stayl-announcement-scrollbar__item">
                            @if($type === 'image')
                                <img src="{{ getImage('assets/images/frontend/header_scrollbar/' . $content) }}" alt="">
                            @elseif($type === 'emoji')
                                <span class="stayl-scrollbar-emoji">{{ $content }}</span>
                            @else
                                <span class="stayl-scrollbar-text">{{ __($content) }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</section>

@push('style')
<style>
    .stayl-announcement-scrollbar-root { width: 100%; overflow: hidden; position: relative; z-index: 50; }
    .stayl-announcement-scrollbar { height: 40px; display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .stayl-announcement-scrollbar__track { display: flex; width: 100%; overflow: hidden; }
    .stayl-announcement-scrollbar__content { display: flex; white-space: nowrap; animation: staylScroll var(--stayl-scroll-speed) linear infinite; animation-direction: var(--stayl-scroll-dir); }
    .stayl-announcement-scrollbar__item { display: inline-flex; align-items: center; gap: 10px; padding: 0 40px; font-size: 14px; font-weight: 500; }
    .stayl-announcement-scrollbar__item img { height: 20px; width: auto; object-fit: contain; }
    @keyframes staylScroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    .dark-mode .stayl-announcement-scrollbar { border-bottom-color: rgba(255,255,255,0.1); }
</style>
@endpush
