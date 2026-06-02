/**
 * Home / storefront product lines: auto-scroll right→left (content moves left).
 * Targets .product-line-flex-row[data-auto-scroll="1"] and category grids.
 */
(function () {
    'use strict';

    function gapPx(el) {
        var st = window.getComputedStyle(el);
        var g = st.gap || st.columnGap;
        if (!g || g === 'normal') return 0;
        var n = parseFloat(g, 10);
        return isNaN(n) ? 0 : n;
    }

    function stepForGrid(grid, cardSel) {
        var first = grid.querySelector(cardSel);
        if (!first) return 0;
        return first.getBoundingClientRect().width + gapPx(grid);
    }

    function bindRow(grid, cardSel) {
        if (grid.dataset.staylAutoBound === '1' && grid._staylScrollTimer) {
            return;
        }
        if (grid._staylScrollTimer) {
            clearInterval(grid._staylScrollTimer);
            grid._staylScrollTimer = null;
        }

        var cards = grid.querySelectorAll(cardSel);
        if (cards.length < 2) return;

        var sec = parseFloat(grid.getAttribute('data-interval-sec'), 10);
        if (!sec || sec < 2) sec = 4;
        if (sec > 30) sec = 30;
        var intervalMs = Math.round(sec * 1000);
        var paused = false;

        function maxScroll() {
            return Math.max(0, grid.scrollWidth - grid.clientWidth);
        }

        function tick() {
            if (paused) return;
            var mx = maxScroll();
            if (mx <= 4) return;
            var step = stepForGrid(grid, cardSel);
            if (step < 48) step = Math.min(200, mx);
            var cur = grid.scrollLeft;
            if (cur >= mx - 8) {
                grid.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                grid.scrollTo({ left: Math.min(cur + step, mx), behavior: 'smooth' });
            }
        }

        function start() { paused = false; }
        function stop() { paused = true; }

        grid.addEventListener('mouseenter', stop);
        grid.addEventListener('mouseleave', start);
        grid.addEventListener('touchstart', stop, { passive: true });
        grid.addEventListener('touchend', function () {
            window.setTimeout(start, 2500);
        }, { passive: true });

        grid._staylScrollTimer = setInterval(tick, intervalMs);
        grid.dataset.staylAutoBound = '1';
    }

    function initHomeHorizontalAutoScroll() {
        [
            { sel: '.product-line-flex-row[data-auto-scroll="1"]', card: '.product-card-col' },
            { sel: '.home-category-section__grid[data-auto-scroll="1"]', card: '.home-category-section__card' }
        ].forEach(function (cfg) {
            document.querySelectorAll(cfg.sel).forEach(function (grid) {
                bindRow(grid, cfg.card);
            });
        });
    }

    function scheduleInit() {
        requestAnimationFrame(function () {
            requestAnimationFrame(initHomeHorizontalAutoScroll);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleInit);
    } else {
        scheduleInit();
    }

    window.addEventListener('load', scheduleInit);
    // User dashboard AJAX no-reload navigation: re-bind when content swaps.
    window.addEventListener('dashboard-content-updated', scheduleInit);

    var resizeT;
    window.addEventListener('resize', function () {
        window.clearTimeout(resizeT);
        resizeT = window.setTimeout(function () {
            document.querySelectorAll('.product-line-flex-row[data-auto-scroll="1"], .home-category-section__grid[data-auto-scroll="1"]').forEach(function (el) {
                if (el._staylScrollTimer) {
                    clearInterval(el._staylScrollTimer);
                    el._staylScrollTimer = null;
                }
                delete el.dataset.staylAutoBound;
            });
            initHomeHorizontalAutoScroll();
        }, 350);
    });

    window.staylInitProductLineAutoScroll = initHomeHorizontalAutoScroll;
})();
