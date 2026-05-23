/**
 * Fly To Header Animation – Wishlist / Compare / Cart
 * Global, event-delegation friendly. Product image flies from card to header icon.
 * GPU accelerated: transform + opacity only. Short duration = feels instant with server latency hidden by optimistic start from the page script.
 */
(function () {
    'use strict';

    var DURATION_MS = 420;
    var EASING = 'cubic-bezier(0.4, 0, 0.2, 1)';
    var CLONE_SIZE = 60;

    /**
     * Reusable: fly product image to header icon.
     * @param {HTMLImageElement|HTMLElement} productImage - Source image (e.g. .product-card__img--main, .product-image)
     * @param {HTMLElement} targetIcon - Header link (#header-wishlist, #header-compare, #header-cart)
     * @param {function} onComplete - Called when animation ends (refresh count, bounce icon)
     */
    function flyToHeader(productImage, targetIcon, onComplete) {
        if (!targetIcon) {
            if (typeof onComplete === 'function') onComplete();
            return;
        }

        var start, imgSrc = '';
        var el = productImage;
        if (el && el.getBoundingClientRect) {
            start = el.getBoundingClientRect();
            if (el.src !== undefined || (el.getAttribute && el.getAttribute('src'))) {
                imgSrc = el.src || el.getAttribute('src') || '';
            }
        }
        if (!start && targetIcon.getBoundingClientRect) {
            var t = targetIcon.getBoundingClientRect();
            start = { left: t.left - 30, top: t.top - 80, width: 60, height: 60 };
        }
        if (!start) {
            if (typeof onComplete === 'function') onComplete();
            return;
        }

        var end = targetIcon.getBoundingClientRect();
        var endCenterX = end.left + end.width / 2;
        var endCenterY = end.top + end.height / 2;
        var startCenterX = start.left + start.width / 2;
        var startCenterY = start.top + start.height / 2;
        var translateX = endCenterX - startCenterX;
        var translateY = endCenterY - startCenterY;

        var clone = document.createElement('div');
        clone.className = 'fly-to-header-clone';
        clone.setAttribute('aria-hidden', 'true');

        if (imgSrc) {
            var img = document.createElement('img');
            img.src = imgSrc;
            img.alt = '';
            clone.appendChild(img);
        } else {
            var icon = document.createElement('span');
            icon.innerHTML = '&#128722;';
            icon.style.fontSize = '26px';
            icon.style.lineHeight = '60px';
            clone.appendChild(icon);
        }

        clone.style.left = (startCenterX - CLONE_SIZE / 2) + 'px';
        clone.style.top = (startCenterY - CLONE_SIZE / 2) + 'px';
        clone.style.transform = 'translate(0, 0) scale(1)';
        clone.style.opacity = '1';
        clone.style.transition = 'transform ' + (DURATION_MS / 1000) + 's ' + EASING + ', opacity ' + (DURATION_MS / 1000) + 's ' + EASING;

        document.body.appendChild(clone);
        clone.offsetHeight;

        var done = false;
        function finish() {
            if (done) return;
            done = true;
            if (clone.parentNode) clone.parentNode.removeChild(clone);
            if (typeof onComplete === 'function') onComplete();
        }

        clone.addEventListener('transitionend', function handler(e) {
            if (e.propertyName !== 'transform' && e.propertyName !== 'opacity') return;
            clone.removeEventListener('transitionend', handler);
            finish();
        });

        requestAnimationFrame(function () {
            clone.style.transform = 'translate(' + translateX + 'px, ' + translateY + 'px) scale(0.2)';
            clone.style.opacity = '0.5';
        });

        setTimeout(finish, DURATION_MS + 100);
    }

    /**
     * Add bounce animation to header icon (and badge).
     */
    function bounceHeaderIcon(headerIcon) {
        if (!headerIcon) return;
        var icon = headerIcon.querySelector('svg.action-icon') || headerIcon.querySelector('svg.ui-icon') || headerIcon.querySelector('img.ui-icon') || headerIcon.querySelector('svg') || headerIcon.querySelector('i');
        var badge = headerIcon.querySelector('.stayl-badge') || headerIcon.querySelector('.glass-badge') || headerIcon.querySelector('.show-cart-count') || headerIcon.querySelector('.show-wishlist-count') || headerIcon.querySelector('.show-compare-count');
        if (icon) {
            icon.classList.remove('header-icon-bounce');
            icon.offsetHeight;
            icon.classList.add('header-icon-bounce');
            setTimeout(function () { icon.classList.remove('header-icon-bounce'); }, 320);
        }
        if (badge) {
            badge.classList.remove('counter-updated');
            badge.offsetHeight;
            badge.classList.add('counter-updated');
            setTimeout(function () { badge.classList.remove('counter-updated'); }, 280);
        }
    }

    /**
     * Get product image from button context (card, detail page, quick-view modal, or .product-image).
     * Supports: .product-card, .product-image, #productmodalView (.qv-main-img), .pro-detail-page (#proDetailMainImg).
     */
    function getProductImageFromButton(btn) {
        if (!btn || !btn.closest) return null;
        var card = btn.closest('.product-card');
        var wrap = btn.closest('.product-image');
        var modal = btn.closest('#productmodalView');
        var detailPage = btn.closest('.pro-detail-page') || btn.closest('.single-add-cart-area');

        var staylCard = btn.closest('.stayl-product-card');
        if (staylCard) {
            var sImg = staylCard.querySelector('.stayl-card-img') || staylCard.querySelector('.stayl-card-media img') || staylCard.querySelector('img');
            if (sImg) return sImg;
        }
        var col = btn.closest('.product-card-col');
        if (col) {
            var cImg = col.querySelector('.stayl-card-img') || col.querySelector('.product-card img') || col.querySelector('img');
            if (cImg) return cImg;
        }
        if (card) {
            var img = card.querySelector('.product-card__img--main') || card.querySelector('img.product-image') || card.querySelector('.product-card__img') || card.querySelector('.product-image img');
            if (img) return img;
            return card.querySelector('img') || null;
        }
        if (modal) {
            var qvImg = modal.querySelector('.qv-main-img') || modal.querySelector('.qv-main-img-wrap img') || modal.querySelector('#qvMainImg');
            if (qvImg) return qvImg;
            return modal.querySelector('.product-card__img--main') || modal.querySelector('.product-card__img') || modal.querySelector('.product-image img') || modal.querySelector('img') || null;
        }
        if (detailPage) {
            var mainImg = document.getElementById('proDetailMainImg');
            if (mainImg) return mainImg;
            var gallery = (detailPage.querySelector && detailPage.querySelector('.pro-detail-gallery-row')) || (document.querySelector && document.querySelector('.pro-detail-page .pro-detail-gallery-row'));
            if (gallery) {
                var first = gallery.querySelector('.pro-detail-main-image-wrap img') || gallery.querySelector('.main-img-inner img') || gallery.querySelector('img');
                if (first) return first;
            }
            var page = btn.closest('.pro-detail-page');
            if (page) {
                var anyImg = page.querySelector('#proDetailMainImg') || page.querySelector('.pro-detail-main-image-wrap img') || page.querySelector('.pro-detail-gallery img');
                if (anyImg) return anyImg;
            }
        }
        if (wrap) {
            var wimg = wrap.querySelector('img');
            if (wimg) return wimg;
        }
        return null;
    }

    /**
     * Get header target by type: 'wishlist' | 'compare' | 'cart'
     * Always use the icon inside the floating header (.glass-header) so animation goes to top, not footer.
     */
    function getHeaderTarget(type) {
        var id = type === 'wishlist' ? 'header-wishlist' : (type === 'compare' ? 'header-compare' : 'header-cart');
        var byId = document.getElementById(id);
        if (byId) return byId;

        var staylHeader = document.querySelector('.stayl-fixed-master, .stayl-header-side, header');
        if (staylHeader) {
            var inStayl = document.getElementById(id);
            if (inStayl) return inStayl;
        }

        if (type === 'cart') {
            var cartBadge = document.querySelector('.show-cart-count');
            if (cartBadge) return cartBadge.closest('a') || cartBadge;
            var mobileCart = document.querySelector('.mobile-bottom-nav__item .show-cart-count');
            if (mobileCart) return mobileCart.closest('.mobile-bottom-nav__item') || mobileCart;
        }
        if (type === 'wishlist') {
            var wlBadge = document.querySelector('.show-wishlist-count');
            if (wlBadge) return wlBadge.closest('a') || wlBadge;
        }
        if (type === 'compare') {
            var cmpBadge = document.querySelector('.show-compare-count');
            if (cmpBadge) return cmpBadge.closest('a') || cmpBadge;
        }

        var header = document.querySelector('header.glass-header');
        if (header) {
            var el = header.querySelector('#' + id);
            if (el) return el;
            var classFallback = type === 'wishlist' ? '.glass-wishlist-btn' : (type === 'compare' ? '.glass-compare-btn' : '.glass-cart-btn');
            el = header.querySelector(classFallback);
            if (el) return el;
        }
        return document.querySelector(type === 'wishlist' ? '.glass-wishlist-btn' : (type === 'compare' ? '.glass-compare-btn' : '.glass-cart-btn'));
    }

    window.flyToHeader = flyToHeader;
    window.bounceHeaderIcon = bounceHeaderIcon;
    window.getProductImageFromButton = getProductImageFromButton;
    window.getHeaderTarget = getHeaderTarget;
})();
