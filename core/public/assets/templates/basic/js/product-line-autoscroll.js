/**
 * Home / storefront product lines: auto-scroll right→left, then left→right at end.
 * Works on any page with .product-line-flex-row[data-auto-scroll="1"].
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

    function initHomeHorizontalAutoScroll() {
        var rows = [
            { sel: '.product-line-flex-row[data-auto-scroll="1"]', card: '.product-card-col' },
            { sel: '.home-category-section__grid[data-auto-scroll="1"]', card: '.home-category-section__card' }
        ];

        rows.forEach(function (cfg) {
            document.querySelectorAll(cfg.sel).forEach(function (grid) {
                if (grid.dataset.staylAutoBound === '1' && grid._staylScrollTimer) {
                    return;
                }
                if (grid._staylScrollTimer) {
                    clearInterval(grid._staylScrollTimer);
                    grid._staylScrollTimer = null;
                }

                var cards = grid.querySelectorAll(cfg.card);
                if (cards.length < 2) return;

                var sec = parseFloat(grid.getAttribute('data-interval-sec'), 10);
                if (!sec || sec < 2) sec = 4;
                if (sec > 30) sec = 30;
                var intervalMs = Math.round(sec * 1000);
                var paused = false;
                var direction = 1;

                function maxScroll() {
                    return Math.max(0, grid.scrollWidth - grid.clientWidth);
                }

                function tick() {
                    if (paused) return;
                    var mx = maxScroll();
                    if (mx <= 4) return;
                    var step = stepForGrid(grid, cfg.card);
                    if (step < 48) step = Math.min(200, mx);
                    var cur = grid.scrollLeft;

                    if (direction === 1) {
                        if (cur >= mx - 10) {
                            direction = -1;
                            grid.scrollTo({ left: Math.max(0, cur - step), behavior: 'smooth' });
                        } else {
                            grid.scrollTo({ left: Math.min(cur + step, mx), behavior: 'smooth' });
                        }
                    } else {
                        if (cur <= 10) {
                            direction = 1;
                            grid.scrollTo({ left: Math.min(step, mx), behavior: 'smooth' });
                        } else {
                            grid.scrollTo({ left: Math.max(0, cur - step), behavior: 'smooth' });
                        }
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
            });
        });
    }

    function runWhenReady() {
        if (document.readyState === 'complete') {
            requestAnimationFrame(function () {
                requestAnimationFrame(initHomeHorizontalAutoScroll);
            });
        } else {
            window.addEventListener('load', function () {
                requestAnimationFrame(function () {
                    requestAnimationFrame(initHomeHorizontalAutoScroll);
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runWhenReady);
    } else {
        runWhenReady();
    }

    var resizeT;
    window.addEventListener('resize', function () {
        window.clearTimeout(resizeT);
        resizeT = window.setTimeout(function () {
            document.querySelectorAll('.product-line-flex-row[data-auto-scroll="1"]').forEach(function (el) {
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
