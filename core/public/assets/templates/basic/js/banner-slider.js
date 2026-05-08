/**
 * Premium Banner Slider Library
 * Optimized for real-time responsiveness and Inter font rendering.
 */
(function() {
    "use strict";

    function initBannerSlider() {
        const sliders = document.querySelectorAll('.js-banner-slider');
        sliders.forEach(slider => {
            if (slider.dataset.sliderInitialized === 'true') return;
            slider.dataset.sliderInitialized = 'true';

            const slides = Array.from(slider.querySelectorAll('.banner-slide-inner'));
            if (!slides.length) return;

            const interval = parseInt(slider.dataset.slideIntervalMs) || 5000;
            const autoplay = slider.dataset.autoplay === '1';
            const dotsContainer = document.getElementById('banner-slider-dots');
            
            let currentIndex = slides.findIndex(s => s.classList.contains('banner-slide-active'));
            if (currentIndex === -1) {
                currentIndex = 0;
                slides[0].classList.add('banner-slide-active');
            }

            let tid = null;

            // Create dots
            if (dotsContainer && slides.length > 1) {
                dotsContainer.innerHTML = '';
                slides.forEach((_, i) => {
                    const dot = document.createElement('button');
                    dot.className = 'banner-slider-dot' + (i === currentIndex ? ' is-active' : '');
                    dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                    dot.addEventListener('click', () => goToSlide(i));
                    dotsContainer.appendChild(dot);
                });
            }

            function updateDots() {
                if (!dotsContainer) return;
                const dots = dotsContainer.querySelectorAll('.banner-slider-dot');
                dots.forEach((dot, i) => {
                    dot.classList.toggle('is-active', i === currentIndex);
                });
            }

            function goToSlide(index) {
                if (index === currentIndex) return;
                
                slides[currentIndex].classList.remove('banner-slide-active');
                currentIndex = (index + slides.length) % slides.length;
                slides[currentIndex].classList.add('banner-slide-active');
                
                updateDots();
                if (autoplay) resetTimer();
            }

            function nextSlide() {
                goToSlide(currentIndex + 1);
            }

            function resetTimer() {
                if (tid) clearInterval(tid);
                tid = setInterval(nextSlide, interval);
            }

            if (autoplay && slides.length > 1) {
                resetTimer();
            }

            // Pause on hover
            slider.addEventListener('mouseenter', () => { if (tid) clearInterval(tid); });
            slider.addEventListener('mouseleave', () => { if (autoplay && slides.length > 1) resetTimer(); });

            // Touch support
            let startX = 0;
            slider.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, {passive: true});
            slider.addEventListener('touchend', e => {
                const diff = startX - e.changedTouches[0].clientX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) nextSlide();
                    else goToSlide(currentIndex - 1);
                }
            }, {passive: true});
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBannerSlider);
    } else {
        initBannerSlider();
    }

    window.refreshBannerSliders = initBannerSlider;
})();
