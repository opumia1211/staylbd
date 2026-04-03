@php
    $position = $position ?? null;
    $pageKey = $page ?? null;
    $options = $options ?? [];
    if ($pageKey) {
        $options['page'] = $pageKey;
    }
    $bars = $bars ?? getScrollbars($position, $options);
@endphp
@if($bars->isNotEmpty())
    <section class="scrollbar-section scrollbar-section--{{ $position ?? 'custom' }}" aria-label="@lang('Headline ticker')">
        @foreach($bars as $bar)
                @php
                    $dv = $bar->data_values ?? (object)[];
                    $dv = is_array($dv) ? $dv : (array) $dv;
                    $items = $dv['items'] ?? [];
                    if (is_object($items)) {
                        $items = (array) $items;
                    }
                    $items = array_values(array_filter(array_map(function ($v) { return is_array($v) ? $v : (array) $v; }, $items), function ($it) {
                        $active = $it['is_active'] ?? 1;
                        return (int) $active !== 0;
                    }));
                    $template = $dv['template'] ?? 'glass';
                    $speedLevel = (int) ($dv['scroll_speed'] ?? 45); // 1..100 (1=slow, 100=fast)
                    if ($speedLevel < 1 || $speedLevel > 100) {
                        $speedLevel = 45;
                    }
                    $pageSpeeds = $dv['page_speeds'] ?? [];
                    if (is_object($pageSpeeds)) {
                        $pageSpeeds = (array) $pageSpeeds;
                    }
                    $pageSpeeds = is_array($pageSpeeds) ? $pageSpeeds : [];
                    $routeName = request()->route() ? (request()->route()->getName() ?? '') : '';
                    $path = request()->path();
                    $currentPage = null;
                    if (str_contains($routeName, 'home') || $routeName === 'frontend.home' || $path === '' || $path === '/') {
                        $currentPage = 'home';
                    } elseif ($routeName === 'product.detail' || preg_match('#^product/details/#', $path) || preg_match('#^product/[a-zA-Z0-9][a-zA-Z0-9\-]*-\d+$#', $path)) {
                        $currentPage = 'product_detail';
                    } elseif ($routeName === 'products' || $routeName === 'products.featured' || $routeName === 'products.best.selling' || $routeName === 'all.products.filter' || $path === 'all/products') {
                        $currentPage = 'all_products';
                    } elseif (str_contains($routeName, 'category') || str_contains($routeName, 'brand.') || str_contains($routeName, 'subcategory.') || preg_match('#^(category|brand|subcategory)/#', $path)) {
                        $currentPage = 'category';
                    } elseif (str_contains($routeName, 'cart.list') || str_contains($path, 'cart-list')) {
                        $currentPage = 'cart';
                    } elseif (str_contains($routeName, 'checkout') || str_contains($path, 'checkout')) {
                        $currentPage = 'checkout';
                    }
                    if ($currentPage && isset($pageSpeeds[$currentPage]) && $pageSpeeds[$currentPage] !== '' && $pageSpeeds[$currentPage] !== null) {
                        $pageLevel = (int) $pageSpeeds[$currentPage];
                        if ($pageLevel >= 1 && $pageLevel <= 100) {
                            $speedLevel = $pageLevel;
                        }
                    }
                    // Human-readable speed curve: even high speed stays readable.
                    // 1 => ~130s (very slow), 100 => ~18s (very fast but readable).
                    $scrollDuration = max(18, 130 - (1.12 * $speedLevel));
                    $scrollDirection = $dv['scroll_direction'] ?? 'ltr';
                    $directionMode = in_array($scrollDirection, ['ttb', 'btt'], true) ? 'vertical' : 'horizontal';
                    $loopMode = $dv['loop_mode'] ?? 'infinite';
                    $pauseOnHover = (int)($dv['pause_on_hover'] ?? 1) ? 'scrollbar-pause-on-hover' : '';
                    $hoverEffect = $dv['hover_effect'] ?? 'pause';
                    if ($hoverEffect === 'dim') {
                        $pauseOnHover .= ' scrollbar-hover-dim';
                    } elseif ($hoverEffect === 'speed_down') {
                        $pauseOnHover .= ' scrollbar-hover-slow';
                    }
                    $gap = (int) ($dv['gap_between_items'] ?? 8);
                    $animationType = $dv['animation_type'] ?? 'linear';
                    $itemAnimation = $dv['item_animation'] ?? 'none';
                    $loopDelay = (float)($dv['loop_delay'] ?? 0);
                    $barHeight = (int) ($dv['bar_height'] ?? 52);
                    $barHeight = max(8, min(150, $barHeight));
                    $barPadding = $dv['bar_padding'] ?? null;
                    $widthType = $dv['width_type'] ?? 'full';
                    $widthValue = $dv['width_value'] ?? '';
                    $maxWidth = $dv['max_width'] ?? '';
                    $barBgType = $dv['bar_background_type'] ?? null;
                    $barBgValue = $dv['bar_background_value'] ?? null;
                    $barBorder = $dv['bar_border'] ?? null;
                    $barShadow = $dv['bar_shadow'] ?? null;
                    $hideMobile = !empty($dv['hide_on_mobile']) ? 'scrollbar-hide-mobile' : '';
                    $hideDesktop = !empty($dv['hide_on_desktop']) ? 'scrollbar-hide-desktop' : '';
                    $barSize = $dv['bar_size'] ?? 'medium';
                    $barThickness = str_replace('_', '-', (string)($dv['bar_thickness'] ?? 'normal'));
                    $defaultTextSize = str_replace('_', '-', (string)($dv['default_text_size'] ?? 'normal'));
                    $defaultTextWeight = $dv['default_text_weight'] ?? 'normal';
                    $alignBar = $dv['align'] ?? 'center';
                    $zIndexBar = (int)($dv['z_index'] ?? 10);
                    $stickyBar = !empty($dv['sticky']);
                    $offsetTopBar = $dv['offset_top'] ?? '0px';
                    $containerModeBar = $dv['container_mode'] ?? 'full';
                    $customXPercent = (float)($dv['custom_x_percent'] ?? 0);
                    $customYpx = (int)($dv['custom_y_px'] ?? 0);
                    $customWidthPercent = (int)($dv['custom_width_percent'] ?? 100);
                    if ($customWidthPercent < 10) $customWidthPercent = 10;
                    if ($customWidthPercent > 100) $customWidthPercent = 100;
                    $sanitizeFontSize = static function ($raw, $fallback = '1rem') {
                        $raw = trim((string) $raw);
                        if ($raw === '') return $fallback;
                        if (preg_match('/^(\d+(?:\.\d+)?)px$/i', $raw, $m)) {
                            $px = (float) $m[1];
                            return max(10, min(24, $px)) . 'px';
                        }
                        if (preg_match('/^(\d+(?:\.\d+)?)rem$/i', $raw, $m)) {
                            $rem = (float) $m[1];
                            return max(0.625, min(1.5, $rem)) . 'rem';
                        }
                        if (preg_match('/^(\d+(?:\.\d+)?)em$/i', $raw, $m)) {
                            $em = (float) $m[1];
                            return max(0.625, min(1.5, $em)) . 'em';
                        }
                        return $fallback;
                    };
                    $wrapperStyle = 'min-height:' . $barHeight . 'px; max-height:' . $barHeight . 'px; z-index:' . $zIndexBar . '; --scrollbar-bar-height:' . $barHeight . 'px;';
                    if ($directionMode === 'vertical') {
                        // Prevent vertical ticker from expanding layout on public pages.
                        $wrapperStyle .= ' height:' . $barHeight . 'px; overflow:hidden;';
                    }
                    if ($alignBar !== 'center') {
                        $wrapperStyle .= ' text-align:' . e($alignBar) . ';';
                    }
                    if ($stickyBar) {
                        $wrapperStyle .= ' position:sticky; top:' . e($offsetTopBar) . ';';
                    }
                    if ($widthType === 'custom' && $widthValue !== '') {
                        $wrapperStyle .= ' width:' . e($widthValue) . ';';
                    }
                    if ($maxWidth !== '') {
                        $wrapperStyle .= ' max-width:' . e($maxWidth) . ';';
                    }
                    if ($barPadding) $wrapperStyle .= ' padding:' . e($barPadding) . ';';
                    if ($barBgType === 'solid' && $barBgValue) $wrapperStyle .= ' background:' . e($barBgValue) . ';';
                    if ($barBgType === 'gradient' && $barBgValue) $wrapperStyle .= ' background:' . e($barBgValue) . ';';
                    if ($barBgType === 'image' && $barBgValue) $wrapperStyle .= ' background-image:url(' . e($barBgValue) . '); background-size:cover;';
                    if ($barBorder) $wrapperStyle .= ' border:' . e($barBorder) . ';';
                    if ($barShadow) $wrapperStyle .= ' box-shadow:' . e($barShadow) . ';';
                    if (($dv['position'] ?? '') === 'custom') {
                        $wrapperStyle .= ' position:fixed; left:50%; transform:translateX(-50%); top:' . e($customYpx) . 'px; width:' . e($customWidthPercent) . 'vw; max-width:100vw; z-index:9999;';
                    }
                @endphp
                @if(!empty($items))
                <div class="{{ (($containerModeBar ?? 'full') === 'container' && (($dv['position'] ?? '') !== 'custom')) ? 'container' : 'container-fluid' }} scrollbar-bar-outer">
                <div class="scrollbar-wrapper scrollbar--{{ $template }} scrollbar--{{ $animationType }} scrollbar-item--{{ $itemAnimation }} scrollbar--size-{{ $barSize }} scrollbar--thickness-{{ $barThickness }} scrollbar--text-size-{{ $defaultTextSize }} scrollbar--text-weight-{{ $defaultTextWeight }} scrollbar--dir-{{ $directionMode }} {{ $pauseOnHover }} {{ $hideMobile }} {{ $hideDesktop }}" data-position="{{ $dv['position'] ?? 'header_below' }}" data-direction="{{ $scrollDirection }}" data-loop="{{ $loopMode }}" data-loop-delay="{{ $loopDelay }}" style="{{ $wrapperStyle }}" title="{{ $pauseOnHover ? __('Hover to pause') : '' }}">
                    <div class="scrollbar-track scrollbar-track--{{ $directionMode }}" style="--scrollbar-gap: {{ $gap }}px;">
                        <div class="scrollbar-content scrollbar-content--{{ $directionMode }}" style="animation-duration: {{ $scrollDuration }}s; animation-delay: {{ $loopDelay }}s;">
                            @foreach(array_merge($items, $items) as $item)
                                @php
                                    $it = is_array($item) ? $item : (array) $item;
                                    $type = $it['type'] ?? 'text';
                                    $content = $it['content'] ?? $it['content_text'] ?? '';
                                    $segments = $it['segments'] ?? [];
                                    $hasSegments = !empty($segments) && is_array($segments);
                                @endphp
                                @if($hasSegments)
                                    @foreach($segments as $seg)
                                        @php
                                            $seg = is_array($seg) ? $seg : (array)$seg;
                                            $segText = str_replace(["\r\n", "\r", "\n", "\t"], ' ', (string)($seg['text'] ?? ''));
                                            $segColor = $seg['color'] ?? '#333333';
                                            $segWeight = $seg['weight'] ?? $seg['font_weight'] ?? '';
                                            $segFamily = $seg['font_family'] ?? 'inherit';
                                            $segSize = trim((string)($seg['font_size'] ?? ''));
                                            $segStyle = 'color:' . e($segColor) . '; font-family:' . e($segFamily) . ';';
                                            if ((string)$segWeight !== '') {
                                                $segStyle .= ' font-weight:' . e($segWeight) . ';';
                                            }
                                            if ($segSize !== '') {
                                                $segStyle .= ' font-size:' . e($sanitizeFontSize($segSize, '1rem')) . ';';
                                            }
                                        @endphp
                                        @if($segText !== '')
                                            <span class="scrollbar-rich-chunk scrollbar-segment--text" style="{{ $segStyle }}">{{ $segText }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    @php
                                        $color = $it['color'] ?? '#333333';
                                        $fontFamily = $it['font_family'] ?? 'inherit';
                                        $fontStyle = $it['font_style'] ?? 'normal';
                                        $weightMap = ['light' => '300', 'normal' => '400', 'medium' => '500', 'semibold' => '600', 'bold' => '700', 'extrabold' => '800'];
                                        $fontWeight = ($it['font_weight'] ?? '') !== '' ? (string)($it['font_weight']) : ($weightMap[$defaultTextWeight] ?? '400');
                                        $fontSize = trim((string)($it['font_size'] ?? ''));
                                        if ($fontSize === '' && $defaultTextSize !== 'normal') {
                                            $sizeMap = ['extra_small' => '0.75rem', 'small' => '0.875rem', 'large' => '1.25rem', 'extra_large' => '1.4rem'];
                                            $fontSize = $sizeMap[$defaultTextSize] ?? '1rem';
                                        }
                                        $letterSpacing = $it['letter_spacing'] ?? '';
                                        $textTransform = $it['text_transform'] ?? 'none';
                                        $segStyle = 'color: ' . e($color) . '; font-family: ' . e($fontFamily) . '; font-weight: ' . e($fontWeight) . '; font-style: ' . ($fontStyle === 'italic' ? 'italic' : 'normal') . ';';
                                        if ($fontSize !== '') $segStyle .= ' font-size: ' . e($sanitizeFontSize($fontSize, '1rem')) . ';';
                                        if ($letterSpacing !== '') $segStyle .= ' letter-spacing: ' . e($letterSpacing) . ';';
                                        if ($textTransform !== 'none') $segStyle .= ' text-transform: ' . e($textTransform) . ';';
                                    @endphp
                                    @if($type === 'text' && $content !== '')
                                        <span class="scrollbar-segment scrollbar-segment--text" style="{{ $segStyle }}">{{ $content }}</span>
                                    @elseif($type === 'emoji' && $content !== '')
                                        <span class="scrollbar-segment scrollbar-segment--emoji" style="{{ $fontSize ? 'font-size: ' . e($sanitizeFontSize($fontSize, '1.05rem')) . ';' : '' }}{{ $letterSpacing ? ' letter-spacing: ' . e($letterSpacing) . ';' : '' }}">{{ $content }}</span>
                                    @elseif($type === 'image' && $content !== '')
                                        <span class="scrollbar-segment scrollbar-segment--image">
                                            <img src="{{ asset('assets/images/frontend/scrollbar/' . $content) }}" alt="" class="scrollbar-inline-img" loading="lazy" decoding="async">
                                        </span>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                </div>
                @endif
            @endforeach
    </section>
@endif
@once
    @push('style')
        {{-- Removed Google Fonts to use Inter exclusively loaded from main layout for 000.001ms fast loading target --}}
    @endpush
    @push('script')
        <script>
            (function () {
                function clamp(v, min, max) {
                    return Math.max(min, Math.min(max, v));
                }

                function initScrollbar(wrapper) {
                    if (!wrapper || wrapper.dataset.scrollbarInit === '1') return;
                    var track = wrapper.querySelector('.scrollbar-track');
                    var content = wrapper.querySelector('.scrollbar-content');
                    if (!track || !content) return;
                    wrapper.dataset.scrollbarInit = '1';

                    // Respect configured direction.
                    var direction = (wrapper.getAttribute('data-direction') || 'rtl').toLowerCase();
                    var isVertical = (direction === 'ttb' || direction === 'btt');
                    if (isVertical) {
                        content.style.animationName = 'scrollbar-scroll-vertical';
                        content.style.animationDirection = direction === 'ttb' ? 'reverse' : 'normal';
                        track.style.overflow = 'hidden';
                    } else {
                        content.style.animationName = 'scrollbar-scroll';
                        content.style.animationDirection = direction === 'ltr' ? 'reverse' : 'normal';
                    }

                    // Ensure ticker has enough track length to visibly move in every section.
                    var loops = 0;
                    while ((isVertical ? content.scrollHeight < (track.clientHeight * 2) : content.scrollWidth < (track.clientWidth * 2)) && loops < 3) {
                        content.innerHTML += content.innerHTML;
                        loops++;
                    }

                    // Keep configured speed stable; never auto-speed-up long text.
                    var inlineDur = parseFloat((content.style.animationDuration || '').replace('s', ''));
                    if (!isFinite(inlineDur) || inlineDur <= 0) {
                        inlineDur = 45;
                        content.style.animationDuration = '45s';
                    }
                    content.style.setProperty('--scrollbar-duration', inlineDur + 's');
                }

                function initAll() {
                    document.querySelectorAll('.scrollbar-wrapper').forEach(initScrollbar);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initAll);
                } else {
                    initAll();
                }

                window.addEventListener('load', initAll);
            })();
        </script>
    @endpush
@endonce
