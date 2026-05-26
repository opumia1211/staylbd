/**
 * Applies staylbd:product-updated payloads to listing rows/cards ([data-product-id] roots).
 */
(function () {
    'use strict';

    function listingCfg() {
        return window.__staylbdListingRt || {};
    }

    function escapeId(id) {
        var s = String(id);
        if (typeof window.CSS !== 'undefined' && window.CSS.escape) {
            return window.CSS.escape(s);
        }
        return s.replace(/\\/g, '\\\\').replace(/"/g, '\\"');
    }

    function stockTierFromPayload(payload) {
        var action = payload.action || 'updated';
        var p = payload.product;
        var stockQty = parseInt(p.stock_qty, 10) || 0;
        if (action === 'deleted' || stockQty <= 0) {
            return 'out';
        }
        var lowMax = listingCfg().lowStockMax || 20;
        if (stockQty > lowMax) {
            return 'in';
        }
        return 'low';
    }

    function canPurchaseFromPayload(payload) {
        var action = payload.action || 'updated';
        var p = payload.product;
        if (action === 'deleted') {
            return false;
        }
        var stockQty = parseInt(p.stock_qty, 10) || 0;
        var maxOrder = parseInt(p.max_order_qty, 10) || 0;
        return stockQty > 0 && maxOrder > 0;
    }

    function applyStockVisual(el, tier) {
        if (el.classList.contains('stayl-card-stock')) {
            el.setAttribute('data-stock-tier', tier);
            el.classList.remove('stayl-card-stock--in', 'stayl-card-stock--low', 'stayl-card-stock--out');
            el.classList.add('stayl-card-stock--' + tier);
            return;
        }
        var isBadge = el.classList.contains('badge');
        el.setAttribute('data-stock-tier', tier);
        if (isBadge) {
            el.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'text-dark');
            if (tier === 'in') {
                el.classList.add('bg-success');
            } else if (tier === 'low') {
                el.classList.add('bg-warning', 'text-dark');
            } else {
                el.classList.add('bg-danger');
            }
        } else {
            el.classList.remove('text-emerald-600', 'text-amber-600', 'text-rose-600');
            if (tier === 'in') {
                el.classList.add('text-emerald-600');
            } else if (tier === 'low') {
                el.classList.add('text-amber-600');
            } else {
                el.classList.add('text-rose-600');
            }
        }
    }

    function stockLabelForTier(tier) {
        var str = listingCfg().strings || {};
        if (tier === 'in') {
            return str.inStock || 'In Stock';
        }
        if (tier === 'low') {
            return str.lowStock || 'Low Stock';
        }
        return str.outOfStock || 'Out of Stock';
    }

    function applyToRoot(root, payload) {
        var d = payload.display || {};
        var curSym = d.cur_sym != null && d.cur_sym !== '' ? d.cur_sym : listingCfg().curSym || '';
        var effFormatted = d.effective_formatted;
        if (effFormatted === undefined || effFormatted === null) {
            effFormatted = d.effective != null ? String(d.effective) : '';
        }

        var tier = stockTierFromPayload(payload);
        var canCart = canPurchaseFromPayload(payload);
        var sLabel = stockLabelForTier(tier);
        var str = listingCfg().strings || {};

        root.querySelectorAll('.staylbd-rt-price').forEach(function (el) {
            if (d.effective != null && d.effective !== '') {
                el.setAttribute('data-base-price', String(d.effective));
            }
            el.textContent = curSym + effFormatted;
        });

        root.querySelectorAll('.staylbd-rt-price-compare').forEach(function (el) {
            if (d.has_savings && d.compare_formatted) {
                if (d.compare != null && d.compare !== '') {
                    el.setAttribute('data-base-price', String(d.compare));
                }
                el.textContent = curSym + d.compare_formatted;
                el.classList.remove('hidden');
            } else {
                el.textContent = '';
                el.classList.add('hidden');
            }
        });

        root.querySelectorAll('.staylbd-rt-stock').forEach(function (el) {
            el.textContent = sLabel;
            applyStockVisual(el, tier);
        });

        var cardAtcLabel = root.querySelector('.product-card-atc-label');
        root.querySelectorAll('.staylbd-rt-atc').forEach(function (btn) {
            if (canCart) {
                btn.removeAttribute('disabled');
                btn.setAttribute('aria-disabled', 'false');
            } else {
                btn.setAttribute('disabled', 'disabled');
                btn.setAttribute('aria-disabled', 'true');
            }

            var isBootstrap = btn.classList.contains('btn');
            if (!isBootstrap) {
                var labelInBtn = btn.querySelector('.product-card-atc-label') || btn.querySelector('.stayl-card-cta-text');
                var labelEl = labelInBtn || cardAtcLabel;
                if (labelEl) {
                    labelEl.textContent = canCart ? (str.addCart || 'Add to Cart') : (str.outOfStock || 'Out of Stock');
                }
                btn.setAttribute('aria-label', canCart ? (str.addCart || 'Add to Cart') : (str.outOfStock || 'Out of Stock'));
                if (btn.classList.contains('stayl-card-cta')) {
                    return;
                }
                /* Home glass card: slate CTA — only toggle disabled state, keep palette */
                if (btn.classList.contains('bg-slate-900')) {
                    if (canCart) {
                        btn.classList.remove('cursor-not-allowed', 'opacity-50');
                        btn.classList.add('cursor-pointer');
                    } else {
                        btn.classList.add('cursor-not-allowed', 'opacity-50');
                        btn.classList.remove('cursor-pointer');
                    }
                    return;
                }
                if (canCart) {
                    btn.classList.remove('cursor-not-allowed', 'bg-slate-200', 'text-slate-500', 'opacity-80');
                    btn.classList.add('cursor-pointer', 'bg-sky-600', 'text-white');
                } else {
                    btn.classList.add('cursor-not-allowed', 'bg-slate-200', 'text-slate-500', 'opacity-80');
                    btn.classList.remove('cursor-pointer', 'bg-sky-600', 'text-white');
                }
            }
        });

        root.querySelectorAll('.staylbd-rt-buynow').forEach(function (a) {
            if (!canCart) {
                a.classList.add('pointer-events-none', 'opacity-50');
                a.setAttribute('aria-disabled', 'true');
            } else {
                a.classList.remove('pointer-events-none', 'opacity-50');
                a.removeAttribute('aria-disabled');
            }
        });
    }

    function onProductUpdated(ev) {
        var payload = ev && ev.detail;
        if (!payload || !payload.product) {
            return;
        }
        var esc = escapeId(payload.product.id);
        var roots = document.querySelectorAll('[data-product-id="' + esc + '"]:not(body)');
        if (!roots.length) {
            return;
        }
        roots.forEach(function (root) {
            try {
                applyToRoot(root, payload);
            } catch (err) {
                console.error('[staylbd] listing realtime update failed', err);
            }
        });
    }

    window.addEventListener('staylbd:product-updated', onProductUpdated);
})();
