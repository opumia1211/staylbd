/**
 * Product auto-scroll carousel: right → left, infinite loop, hover pause, touch-friendly.
 * Optimized: rAF for layout reads, minimal stagger delay, no layout thrashing.
 */
(function () {
    'use strict';

    function initCarousel(wrap) {
        if (wrap.getAttribute('data-staylbd-pc-bound')) return;
        var track = wrap.querySelector('.product-carousel-track-inner');
        var items = wrap.querySelectorAll('.product-carousel__item');
        if (!track || items.length === 0) return;

        var count = parseInt(wrap.getAttribute('data-count'), 10) || 0;
        if (count === 0) return;
        wrap.setAttribute('data-staylbd-pc-bound', '1');
        var intervalSec = Math.max(2, Math.min(30, parseInt(wrap.getAttribute('data-interval-sec'), 10) || 5));
        var speedMs = Math.max(700, Math.min(2600, parseInt(wrap.getAttribute('data-speed-ms'), 10) || 900));
        var sectionId = wrap.closest('[data-section-id]') ? wrap.closest('[data-section-id]').getAttribute('data-section-id') : '';

        function hashString(input) {
            var str = String(input || '');
            var hash = 0;
            for (var i = 0; i < str.length; i++) {
                hash = ((hash << 5) - hash) + str.charCodeAt(i);
                hash |= 0;
            }
            return Math.abs(hash);
        }
        var sectionSeed = hashString(sectionId || ('idx-' + Math.random()));
        // Keep per-row timing independent (deterministic by section id).
        var intervalMs = (intervalSec * 1000) + (sectionSeed % 1200); // +0..1199ms

        var currentIndex = 0;
        var timerId = null;
        var itemWidth = 0;
        var dragging = false;
        var dragStartX = 0;
        var dragStartPx = 0;
        var lastClientX = 0;

        function getItemWidth() {
            var first = items[0];
            if (!first) return 0;
            var style = window.getComputedStyle(first);
            return first.offsetWidth + (parseFloat(style.marginRight) || 0) + (parseFloat(style.marginLeft) || 0);
        }

        function updateWidths() {
            itemWidth = getItemWidth();
        }

        function setTransition(enable) {
            track.style.transitionDuration = enable ? (speedMs / 1000) + 's' : '0s';
        }

        function applyTransform(px) {
            track.style.transform = 'translate3d(' + (-px) + 'px, 0, 0)';
        }

        function getMaxPx() {
            updateWidths();
            if (!itemWidth) return 0;
            var totalItems = items.length;
            var visibleApprox = Math.max(1, Math.round(wrap.clientWidth / itemWidth));
            var maxIndex = Math.max(0, totalItems - visibleApprox);
            return maxIndex * itemWidth;
        }

        function clamp(n, min, max) {
            if (n < min) return min;
            if (n > max) return max;
            return n;
        }

        function setDragging(on) {
            dragging = on;
            wrap.classList.toggle('is-dragging', on);
            setTransition(!on);
        }

        function snapToNearest(px) {
            updateWidths();
            if (!itemWidth) return;
            var maxPx = getMaxPx();
            var clamped = clamp(px, 0, maxPx);
            currentIndex = Math.round(clamped / itemWidth);
            applyTransform(currentIndex * itemWidth);
        }

        function step() {
            updateWidths();
            if (!itemWidth) return;

            // সব প্রোডাক্ট যেন ধীরে ধীরে ডান→বাম এবং আবার বাম→ডানে দেখা যায়,
            // কোন পর্যায়েই UI খালি না থাকে – তাই পূর্ণ ভিউ রেঞ্জের উপর bounce ইফেক্ট।
            var totalItems = items.length; // ডুপ্লিকেট সহ
            var visibleApprox = Math.max(1, Math.round(wrap.clientWidth / itemWidth));
            var maxIndex = Math.max(0, totalItems - visibleApprox);
            if (maxIndex === 0) return;

            var direction = wrap.__pcDirection || 1; // 1 = right→left, -1 = left→right

            currentIndex += direction;
            if (direction === 1 && currentIndex >= maxIndex) {
                currentIndex = maxIndex;
                direction = -1;
            } else if (direction === -1 && currentIndex <= 0) {
                currentIndex = 0;
                direction = 1;
            }

            wrap.__pcDirection = direction;
            applyTransform(currentIndex * itemWidth);
        }

        function startTimer() {
            if (timerId) return;
            timerId = setInterval(step, intervalMs);
        }

        function stopTimer() {
            if (timerId) {
                clearInterval(timerId);
                timerId = null;
            }
        }

        wrap.addEventListener('mouseenter', function () {
            wrap.classList.add('is-paused');
            stopTimer();
        });
        wrap.addEventListener('mouseleave', function () {
            wrap.classList.remove('is-paused');
            startTimer();
        });

        // Touch + mouse drag (manual control)
        wrap.style.touchAction = 'pan-y';
        wrap.addEventListener('touchstart', function (e) {
            if (e.touches.length !== 1) return;
            stopTimer();
            updateWidths();
            dragStartX = e.touches[0].clientX;
            lastClientX = dragStartX;
            dragStartPx = currentIndex * itemWidth;
            setDragging(true);
        }, { passive: true });
        wrap.addEventListener('touchmove', function (e) {
            if (!dragging || e.touches.length !== 1) return;
            var x = e.touches[0].clientX;
            var dx = x - dragStartX;
            lastClientX = x;
            var nextPx = dragStartPx - dx; // move finger right -> content moves left
            applyTransform(clamp(nextPx, 0, getMaxPx()));
            e.preventDefault();
        }, { passive: false });
        wrap.addEventListener('touchend', function () {
            if (!dragging) return;
            setDragging(false);
            // snap
            var approxPx = dragStartPx - (lastClientX - dragStartX);
            snapToNearest(approxPx);
            startTimer();
        }, { passive: true });

        wrap.addEventListener('mousedown', function (e) {
            // left click only; ignore form elements
            if (e.button !== 0) return;
            var tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
            if (tag === 'a' || tag === 'button' || tag === 'input' || tag === 'select' || tag === 'textarea') return;
            stopTimer();
            updateWidths();
            dragStartX = e.clientX;
            lastClientX = dragStartX;
            dragStartPx = currentIndex * itemWidth;
            setDragging(true);
            try { wrap.setPointerCapture && wrap.setPointerCapture(e.pointerId); } catch (err) {}
            e.preventDefault();
        });
        window.addEventListener('mousemove', function (e) {
            if (!dragging) return;
            var x = e.clientX;
            var dx = x - dragStartX;
            lastClientX = x;
            var nextPx = dragStartPx - dx;
            applyTransform(clamp(nextPx, 0, getMaxPx()));
        });
        window.addEventListener('mouseup', function () {
            if (!dragging) return;
            setDragging(false);
            var approxPx = dragStartPx - (lastClientX - dragStartX);
            snapToNearest(approxPx);
            startTimer();
        });

        function init() {
            updateWidths();
            if (itemWidth <= 0) return;
            setTransition(true);
            applyTransform(0);
            startTimer();
        }

        var allWraps = document.querySelectorAll('.js-product-carousel');
        var staggerIndex = Array.prototype.indexOf.call(allWraps, wrap);
        var staggerDelayMs = Math.min(staggerIndex * 220, 1600) + (sectionSeed % 500);

        function runInit() {
            var delay = staggerDelayMs;
            function doInit() {
                requestAnimationFrame(function () {
                    init();
                });
            }
            if (document.readyState === 'complete') {
                if (delay > 0) setTimeout(doInit, delay);
                else doInit();
            } else {
                window.addEventListener('load', function () {
                    if (delay > 0) setTimeout(doInit, delay);
                    else doInit();
                });
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runInit);
        } else {
            runInit();
        }

        var resizeTick;
        window.addEventListener('resize', function () {
            if (resizeTick) cancelAnimationFrame(resizeTick);
            resizeTick = requestAnimationFrame(function () {
                resizeTick = 0;
                updateWidths();
                setTransition(false);
                currentIndex = currentIndex % count;
                if (currentIndex < 0) currentIndex = 0;
                applyTransform(currentIndex * itemWidth);
                setTransition(true);
            });
        });
    }

    function initAll() {
        requestAnimationFrame(function () {
            document.querySelectorAll('.js-product-carousel').forEach(initCarousel);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    window.staylbdBindProductCarousels = function () {
        requestAnimationFrame(function () {
            document.querySelectorAll('.js-product-carousel:not([data-staylbd-pc-bound])').forEach(initCarousel);
        });
    };
})();
