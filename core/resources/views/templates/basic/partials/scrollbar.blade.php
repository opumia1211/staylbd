@php
    // If $bars is not already passed (e.g., from admin preview), fetch filtered bars for the given position
    if (!isset($bars)) {
        $bars = getScrollbars($position ?? null);
    }
    if (empty($bars) || $bars->isEmpty()) {
        return;
    }
@endphp

@once
    @push('style')
        <link rel="stylesheet" href="{{ url('serve-css/scrollbar') }}?v={{ is_file(public_path('assets/templates/basic/css/scrollbar.css')) ? filemtime(public_path('assets/templates/basic/css/scrollbar.css')) : '1' }}">
    @endpush
@endonce

@foreach($bars as $bar)
    @php
        $dv = (array) ($bar->data_values ?? []);
        
        // Skip draft or private scrollbars (already filtered by getScrollbars helper, but keep for safety / manual preview)
        if (isset($dv['status']) && (int) $dv['status'] !== 1) continue;
        if (isset($dv['visibility']) && $dv['visibility'] === 'private') continue;

        $barPosition = $dv['position'] ?? 'header_below';
        $template = $dv['template'] ?? 'glass';
        $scrollSpeed = (int) ($dv['scroll_speed'] ?? 45);
        $scrollDirection = $dv['scroll_direction'] ?? 'ltr';
        $loopMode = $dv['loop_mode'] ?? 'infinite';
        $pauseOnHover = (int) ($dv['pause_on_hover'] ?? 1);
        $gapBetweenItems = (int) ($dv['gap_between_items'] ?? 8);
        $animationType = $dv['animation_type'] ?? 'linear';
        $barHeight = (int) ($dv['bar_height'] ?? 52);
        
        $barSize = $dv['bar_size'] ?? 'medium';
        $barThickness = $dv['bar_thickness'] ?? 'normal';
        $defaultTextSize = $dv['default_text_size'] ?? 'normal';
        $defaultTextWeight = $dv['default_text_weight'] ?? 'normal';
        
        $hideOnMobile = (int) ($dv['hide_on_mobile'] ?? 0);
        $hideOnDesktop = (int) ($dv['hide_on_desktop'] ?? 0);
        $containerMode = $dv['container_mode'] ?? 'full';
        
        $zIndex = (int) ($dv['z_index'] ?? 10);
        $sticky = (int) ($dv['sticky'] ?? 0);
        $offsetTop = $dv['offset_top'] ?? '0px';
        
        $hoverEffect = $dv['hover_effect'] ?? 'pause';
        $itemAnimation = $dv['item_animation'] ?? 'none';
        
        $items = $dv['items'] ?? [];
        if (is_object($items)) {
            $items = (array) $items;
        }
        if (empty($items)) continue;

        // Double the items list to ensure seamless looping marquee
        $renderItems = array_merge($items, $items);
        if (count($renderItems) < 10) {
            $renderItems = array_merge($renderItems, $renderItems);
        }

        // Setup outer inline styling
        $outerStyle = '';
        if ($sticky) {
            $outerStyle .= "position: sticky; top: {$offsetTop}; z-index: {$zIndex}; ";
        } else {
            $outerStyle .= "position: relative; z-index: {$zIndex}; ";
        }
    @endphp

    <section class="scrollbar-section scrollbar-section--{{ $barPosition }} {{ $hideOnMobile ? 'scrollbar-hide-mobile' : '' }} {{ $hideOnDesktop ? 'scrollbar-hide-desktop' : '' }}" style="{{ $outerStyle }}">
        <div class="scrollbar-bar-outer {{ $containerMode === 'box' ? 'container' : '' }}">
            <div class="scrollbar-wrapper scrollbar--{{ $template }} scrollbar--size-{{ $barSize }} scrollbar--thickness-{{ $barThickness }} scrollbar--text-size-{{ $defaultTextSize }} scrollbar--text-weight-{{ $defaultTextWeight }} {{ $pauseOnHover ? 'scrollbar-pause-on-hover' : '' }} scrollbar-hover-{{ $hoverEffect }}" 
                 data-loop="{{ $loopMode }}"
                 style="min-height: {{ $barHeight }}px; height: {{ $barHeight }}px; --scrollbar-gap: {{ $gapBetweenItems }}px;">
                
                <div class="scrollbar-track scrollbar--{{ $animationType }}">
                    <div class="scrollbar-content scrollbar-item--{{ $itemAnimation }}" 
                         style="animation-duration: {{ $scrollSpeed }}s !important; --scrollbar-duration: {{ $scrollSpeed }}s !important; animation-direction: {{ $scrollDirection === 'rtl' ? 'reverse' : 'normal' }} !important;">
                        
                        @foreach($renderItems as $item)
                            @php
                                $item = (array) $item;
                                $type = $item['type'] ?? 'text';
                                $content = trim((string) ($item['content'] ?? ''));
                                
                                // Build custom styling for non-rich items
                                $itemStyle = '';
                                if (!empty($item['color'])) {
                                    $itemStyle .= 'color: ' . $item['color'] . '; ';
                                }
                                if (!empty($item['font_size'])) {
                                    $itemStyle .= 'font-size: ' . $item['font_size'] . '; ';
                                }
                                if (!empty($item['font_weight'])) {
                                    $itemStyle .= 'font-weight: ' . $item['font_weight'] . '; ';
                                }
                                if (!empty($item['font_family']) && $item['font_family'] !== 'inherit') {
                                    $itemStyle .= 'font-family: ' . $item['font_family'] . '; ';
                                }
                                if (!empty($item['font_style']) && $item['font_style'] !== 'normal') {
                                    if ($item['font_style'] === 'bold') {
                                        $itemStyle .= 'font-weight: bold; ';
                                    } else {
                                        $itemStyle .= 'font-style: ' . $item['font_style'] . '; ';
                                    }
                                }
                                if (!empty($item['letter_spacing'])) {
                                    $itemStyle .= 'letter-spacing: ' . $item['letter_spacing'] . '; ';
                                }
                                if (!empty($item['text_transform']) && $item['text_transform'] !== 'none') {
                                    $itemStyle .= 'text-transform: ' . $item['text_transform'] . '; ';
                                }
                            @endphp

                            @if(!empty($item['segments']) && is_array($item['segments']))
                                {{-- Rich Text Editor Segments --}}
                                <div class="scrollbar-segment scrollbar-segment--rich" style="{{ $itemStyle }}">
                                    @foreach($item['segments'] as $seg)
                                        @php
                                            $seg = (array) $seg;
                                            $segStyle = '';
                                            if (!empty($seg['color'])) {
                                                $segStyle .= 'color: ' . $seg['color'] . '; ';
                                            }
                                            if (!empty($seg['weight'])) {
                                                $segStyle .= 'font-weight: ' . $seg['weight'] . '; ';
                                            }
                                            if (!empty($seg['font_size'])) {
                                                $segStyle .= 'font-size: ' . $seg['font_size'] . '; ';
                                            }
                                            if (!empty($seg['font_family']) && $seg['font_family'] !== 'inherit') {
                                                $segStyle .= 'font-family: ' . $seg['font_family'] . '; ';
                                            }
                                        @endphp
                                        <span class="scrollbar-rich-chunk" style="{{ $segStyle }}">{{ $seg['text'] }}</span>
                                    @endforeach
                                </div>
                            @else
                                {{-- Plain Text / Emoji / Image Items --}}
                                @if($type === 'image')
                                    @if($content !== '')
                                        <div class="scrollbar-segment scrollbar-segment--image">
                                            <img class="scrollbar-inline-img" src="{{ getImage('assets/images/frontend/scrollbar/' . $content) }}" alt="">
                                        </div>
                                    @endif
                                @elseif($type === 'emoji')
                                    @if($content !== '')
                                        <div class="scrollbar-segment scrollbar-segment--emoji" style="{{ $itemStyle }}">
                                            <span class="scrollbar-segment--emoji">{{ $content }}</span>
                                        </div>
                                    @endif
                                @else
                                    @if($content !== '')
                                        <div class="scrollbar-segment scrollbar-segment--text" style="{{ $itemStyle }}">
                                            <span class="scrollbar-segment--text">{{ __($content) }}</span>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                        
                    </div>
                </div>
                
            </div>
        </div>
    </section>
@endforeach
