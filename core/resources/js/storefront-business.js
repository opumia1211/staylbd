/**
 * Staylbd Business Engine: behavioral tracking, exit intent (desktop only), growth hooks.
 */
class StaylbdBusinessEngine {
    constructor() {
        this.sessionId = this.getOrCreateSessionId();
        this.startTime = Date.now();
        this.maxScroll = 0;
        this._clickThrottle = 0;
        this.trackUrl = (typeof window.STAYL_TRACK_URL === 'string' && window.STAYL_TRACK_URL)
            ? window.STAYL_TRACK_URL
            : this.resolveTrackUrl();
        this.init();
    }

    resolveTrackUrl() {
        var base = (document.querySelector('meta[name="app-url"]') || {}).content || '';
        base = (base || window.location.origin).replace(/\/$/, '');
        return base + '/api/v1/track/event';
    }

    init() {
        this.trackScroll();
        if (!this.isTouchPrimary()) {
            this.trackExitIntent();
        }
        this.trackClicks();
        this.trackTimeOnPage();
        this.trackProductPage();
    }

    isTouchPrimary() {
        try {
            return window.matchMedia('(hover: none) and (pointer: coarse)').matches;
        } catch (e) {
            return 'ontouchstart' in window;
        }
    }

    getOrCreateSessionId() {
        var key = 'staylbd_sid';
        try {
            var sid = localStorage.getItem(key);
            if (!sid) {
                sid = 'sid_' + Math.random().toString(36).slice(2, 11) + '_' + Date.now();
                localStorage.setItem(key, sid);
            }
            return sid;
        } catch (e) {
            return 'sid_guest_' + Date.now();
        }
    }

    buildHeaders() {
        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        var tokenEl = document.querySelector('meta[name="csrf-token"]');
        if (tokenEl && tokenEl.getAttribute('content')) {
            headers['X-CSRF-TOKEN'] = tokenEl.getAttribute('content');
        }
        return headers;
    }

    logEvent(type, data) {
        data = data || {};
        var payload = JSON.stringify({
            type: type,
            session_id: this.sessionId,
            url: window.location.href,
            data: data
        });
        var url = this.trackUrl;

        try {
            if (navigator.sendBeacon && (type === 'time_on_page' || type === 'page_scroll_final')) {
                var blob = new Blob([payload], { type: 'application/json' });
                if (navigator.sendBeacon(url, blob)) return;
            }
        } catch (e) { /* fall through to fetch */ }

        fetch(url, {
            method: 'POST',
            headers: this.buildHeaders(),
            body: payload,
            credentials: 'same-origin',
            keepalive: true
        }).catch(function () { /* silent */ });
    }

    trackScroll() {
        var self = this;
        var ticking = false;
        window.addEventListener('scroll', function () {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(function () {
                var h = document.documentElement;
                var b = document.body;
                var st = (h.scrollTop || b.scrollTop);
                var sh = (h.scrollHeight || b.scrollHeight) - h.clientHeight;
                if (sh > 0) {
                    var percent = (st / sh) * 100;
                    if (percent > self.maxScroll) self.maxScroll = Math.round(percent);
                }
                ticking = false;
            });
        }, { passive: true });

        window.addEventListener('pagehide', function () {
            self.logEvent('page_scroll_final', { max_percent: self.maxScroll });
        });
    }

    trackExitIntent() {
        var self = this;
        document.addEventListener('mouseleave', function (e) {
            if (e.clientY < 0) self.showExitIntentPopup();
        });
    }

    showExitIntentPopup() {
        try {
            if (sessionStorage.getItem('staylbd_exit_shown')) return;
            sessionStorage.setItem('staylbd_exit_shown', '1');
        } catch (e) { return; }

        this.logEvent('exit_intent_trigger');

        var cartUrl = (document.querySelector('meta[name="cart-url"]') || {}).content || '/cart-list';
        var modal = document.createElement('div');
        modal.className = 'staylbd-business-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.innerHTML = '<div class="staylbd-business-modal__backdrop"></div><div class="staylbd-business-modal__panel"><h2>Wait! Don\'t Miss Out</h2><p>Finish your order now and get an extra 5% OFF on your entire cart!</p><div class="staylbd-business-modal__code">COMEBACK5</div><button type="button" class="staylbd-business-modal__claim">Claim My Discount</button><button type="button" class="staylbd-business-modal__close">No thanks</button></div>';
        document.body.appendChild(modal);

        var self = this;
        modal.querySelector('.staylbd-business-modal__claim').onclick = function () {
            self.logEvent('exit_intent_claim');
            window.location.href = cartUrl;
        };
        modal.querySelector('.staylbd-business-modal__close').onclick = function () { modal.remove(); };
        modal.querySelector('.staylbd-business-modal__backdrop').onclick = function () { modal.remove(); };
    }

    trackClicks() {
        var self = this;
        document.addEventListener('click', function (e) {
            var now = Date.now();
            if (now - self._clickThrottle < 800) return;
            var el = e.target.closest('button, a, [data-track-click]');
            if (!el) return;
            self._clickThrottle = now;
            self.logEvent('user_click', {
                tag: el.tagName,
                text: (el.innerText || '').trim().substring(0, 40),
                href: el.getAttribute('href') || null
            });
        }, { passive: true });
    }

    trackTimeOnPage() {
        var self = this;
        window.addEventListener('pagehide', function () {
            var duration = Math.round((Date.now() - self.startTime) / 1000);
            self.logEvent('time_on_page', { duration_seconds: duration });
        });
    }

    trackProductPage() {
        var body = document.body;
        if (!body) return;
        var pid = body.getAttribute('data-product-id');
        if (pid) {
            this.logEvent('product_view', { product_id: parseInt(pid, 10) || pid });
        }
    }
}

if (typeof window !== 'undefined' && !window.StaylbdBusiness) {
    window.StaylbdBusiness = new StaylbdBusinessEngine();
}
