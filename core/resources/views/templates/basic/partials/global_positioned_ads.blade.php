@php
    use Illuminate\Support\Facades\Schema;

    $ads = collect();
    if (Schema::hasTable('homepage_ad_slots')) {
        $currentPath = '/' . ltrim(request()->path(), '/');
        $isHome = request()->routeIs('home') || $currentPath === '/';
        $q = \App\Models\HomepageAdSlot::query()->where('is_active', true);
        if (Schema::hasColumn('homepage_ad_slots', 'position')) {
            // Only truly global/sticky ads belong here.
            // "custom" is in-section placement and should not be rendered as fixed overlay.
            $q->whereIn('position', ['fixed', 'floating']);
        } else {
            // Legacy schema fallback: no global positioned ads yet.
            $q->whereRaw('1 = 0');
        }
        $ads = $q->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(function ($ad) use ($isHome, $currentPath) {
                $mode = Schema::hasColumn('homepage_ad_slots', 'display_pages')
                    ? (string) ($ad->display_pages ?? 'all')
                    : 'all';
                if ($mode === 'homepage' && !$isHome) return false;
                if ($mode === 'non_home' && $isHome) return false;
                if ($mode === 'custom_path') {
                    $p = Schema::hasColumn('homepage_ad_slots', 'custom_path')
                        ? trim((string) ($ad->custom_path ?? ''))
                        : '';
                    if ($p === '') return false;
                    $regex = '#^' . str_replace('\*', '.*', preg_quote($p, '#')) . '$#i';
                    return (bool) preg_match($regex, $currentPath);
                }
                return true;
            })
            ->values();
    }
@endphp

@if($ads->isNotEmpty())
    @foreach($ads as $ad)
        @php
            $sourceType = Schema::hasColumn('homepage_ad_slots', 'source_type')
                ? (string) ($ad->source_type ?? 'upload')
                : 'upload';
            $src = $sourceType === 'upload' ? $ad->imageUrl() : trim((string) ($ad->external_url ?? ''));
            $side = Schema::hasColumn('homepage_ad_slots', 'side') ? (string) ($ad->side ?? 'bottom-right') : 'bottom-right';
            $style = 'z-index:' . (int) (Schema::hasColumn('homepage_ad_slots', 'z_index') ? ($ad->z_index ?? 1100) : 1100) . ';';
            if (str_contains($side, 'top') || $side === 'top') $style .= 'top:' . ((int) (Schema::hasColumn('homepage_ad_slots', 'top') ? ($ad->top ?? 12) : 12)) . 'px;';
            if (str_contains($side, 'bottom') || $side === 'bottom') $style .= 'bottom:' . ((int) (Schema::hasColumn('homepage_ad_slots', 'bottom') ? ($ad->bottom ?? 12) : 12)) . 'px;';
            if (str_contains($side, 'left') || $side === 'left') $style .= 'left:' . ((int) (Schema::hasColumn('homepage_ad_slots', 'left') ? ($ad->left ?? 12) : 12)) . 'px;';
            if (str_contains($side, 'right') || $side === 'right') $style .= 'right:' . ((int) (Schema::hasColumn('homepage_ad_slots', 'right') ? ($ad->right ?? 12) : 12)) . 'px;';
            if ($side === 'top' || $side === 'bottom' || $side === 'center') $style .= 'left:50%;transform:translateX(-50%);';
            if ($side === 'left' || $side === 'right') $style .= 'top:50%;transform:translateY(-50%);';
            if ($side === 'center') $style .= 'top:50%;transform:translate(-50%,-50%);';
            if (Schema::hasColumn('homepage_ad_slots', 'size_type') && (string) ($ad->size_type ?? 'auto') === 'custom') {
                if (!empty($ad->custom_width)) $style .= 'width:' . e($ad->custom_width) . ';';
                if (!empty($ad->custom_height)) $style .= 'height:' . e($ad->custom_height) . ';';
            } else {
                $widthMode = Schema::hasColumn('homepage_ad_slots', 'width_mode')
                    ? (string) ($ad->width_mode ?? 'full')
                    : 'full';
                if ($widthMode === 'wide') {
                    $style .= 'width:min(90vw,var(--stayl-content-max,1920px));';
                } elseif ($widthMode === 'half') {
                    $style .= 'width:min(50vw,var(--stayl-content-max,1920px));';
                } elseif ($widthMode === 'third') {
                    $style .= 'width:min(33.3333vw,var(--stayl-content-max,1920px));';
                } elseif ($widthMode === 'quarter') {
                    $style .= 'width:min(25vw,var(--stayl-content-max,1920px));';
                } else {
                    // Full width on all devices while respecting project shell max-width.
                    $style .= 'width:min(100vw,var(--stayl-content-max,1920px));';
                }
            }
            if (Schema::hasColumn('homepage_ad_slots', 'max_height_px') && !empty($ad->max_height_px)) $style .= 'max-height:' . (int) $ad->max_height_px . 'px;';
        @endphp
        <div class="glb-pos-ad" style="{{ $style }}">
            @if($sourceType === 'embed_url')
                <iframe src="{{ $ad->external_url }}" class="glb-pos-ad__iframe" title="{{ $ad->admin_title }}"></iframe>
            @elseif($src)
                @if(!empty($ad->link_url))
                    <a href="{{ $ad->link_url }}" target="{{ $ad->open_new_tab ? '_blank' : '_self' }}" rel="{{ $ad->open_new_tab ? 'noopener noreferrer' : '' }}">
                        <img src="{{ $src }}" alt="{{ $ad->admin_title }}" class="glb-pos-ad__img">
                    </a>
                @else
                    <img src="{{ $src }}" alt="{{ $ad->admin_title }}" class="glb-pos-ad__img">
                @endif
            @endif
        </div>
    @endforeach

    @push('style')
    <style>
        .glb-pos-ad { position: fixed; max-width: min(100vw, var(--stayl-content-max, 1920px)); box-sizing: border-box; }
        .glb-pos-ad__img { display: block; width: 100%; height: auto; max-width: 100%; border: 0; border-radius: 0; box-shadow: none; }
        .glb-pos-ad__iframe { width: 100%; min-height: 120px; border: 0; }
        @media (max-width: 575px) { .glb-pos-ad { width: 100vw !important; max-width: 100vw !important; } }
    </style>
    @endpush
@endif
