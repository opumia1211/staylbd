<script>
    (function() {
        "use strict";

        /**
         * Real-time Experience Enhancements
         * Handles: 
         * 1. Pulse animations on product updates (price/stock)
         * 2. Header icon bounce triggers for Cart/Wishlist/Compare
         * 3. Premium button loading states
         */

        // 1. Helper for Animations
        const triggerPulse = (el) => {
            if (!el) return;
            el.classList.remove('stayl-pulse');
            void el.offsetWidth; // Trigger reflow
            el.classList.add('stayl-pulse');
        };

        const triggerBounce = (selector) => {
            const icons = document.querySelectorAll(selector);
            icons.forEach(icon => {
                icon.classList.remove('stayl-bounce');
                void icon.offsetWidth;
                icon.classList.add('stayl-bounce');
            });
        };

        // 2. Real-time Listeners (Suplementing core JS)
        window.addEventListener('staylbd:product-updated', function(e) {
            const payload = e.detail;
            if (!payload || !payload.product) return;

            const productId = payload.product.id;
            const roots = document.querySelectorAll(`[data-product-id="${productId}"]:not(body)`);
            
            roots.forEach(root => {
                // Pulse the price and stock elements
                const priceEl = root.querySelector('.staylbd-rt-price');
                const stockEl = root.querySelector('.staylbd-rt-stock');
                if (priceEl) triggerPulse(priceEl);
                if (stockEl) triggerPulse(stockEl);
                
                // If product is in cart, we might want to pulse the cart icon too if price changed
                // (Advanced: compare previous price if available)
            });
        });

        // 3. Header Icon Bounce Hooks
        // Hooking into global staylbd events dispatched by components
        window.addEventListener('staylbd:cart-updated', () => triggerBounce('.stayl-header-icon-cart'));
        window.addEventListener('staylbd:wishlist-updated', () => triggerBounce('.stayl-header-icon-wishlist'));
        window.addEventListener('staylbd:compare-updated', () => triggerBounce('.stayl-header-icon-compare'));
        window.addEventListener('staylbd:orders-updated', () => triggerBounce('.stayl-header-icon-orders'));

        // 4. Intercept Notify to clear loading states
        const originalNotify = window.notify;
        window.notify = function(type, msg) {
            // Clear all button loading states globally on any notification
            document.querySelectorAll('.btn-loading, .btn-loading-state').forEach(b => {
                b.classList.remove('btn-loading');
            });
            
            if (typeof originalNotify === 'function') originalNotify(type, msg);
            
            // Trigger bouncers based on message content for legacy support
            const lowerMsg = (msg || '').toLowerCase();
            if (lowerMsg.includes('cart')) triggerBounce('.stayl-header-icon-cart');
            if (lowerMsg.includes('wishlist')) triggerBounce('.stayl-header-icon-wishlist');
            if (lowerMsg.includes('compare')) triggerBounce('.stayl-header-icon-compare');
        };

        // 5. Button Loading State Trigger
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('button[type="submit"], .add-to-cart, .btn-cart, .add-wishlist, .btn-wishlist, .btn-loading-state');
            if (btn && !btn.classList.contains('btn-loading')) {
                // If it's a form submit, check validity
                if (btn.type === 'submit' && btn.form && !btn.form.checkValidity()) return;
                
                btn.classList.add('btn-loading');
                
                // Safety timeout
                setTimeout(() => btn.classList.remove('btn-loading'), 8000);
            }
        }, true);

    })();
</script>
