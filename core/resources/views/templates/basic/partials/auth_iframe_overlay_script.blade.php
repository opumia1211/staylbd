<script>
(function() {
    'use strict';
    if (window.self !== window.top) return;

    window.openSocialPopup = function(u) {
        var w = 520, h = 650;
        var y = ((window.top.outerHeight || 600) / 2) + (window.top.screenY || 0) - h / 2;
        var x = ((window.top.outerWidth || 800) / 2) + (window.top.screenX || 0) - w / 2;
        return window.open(u, 'social_login', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=' + w + ',height=' + h + ',top=' + y + ',left=' + x) || null;
    };

    var overlay = null;
    var frame = null;
    /** True after we pushed a history entry for the auth iframe (close = history.back) */
    var authModalHistoryActive = false;

    function hideOverlayVisualOnly() {
        if (!overlay) return;
        overlay.style.display = 'none';
        document.body.classList.remove('auth-iframe-open');
    }

    function toSameOriginPath(fullUrl) {
        try {
            var u = new URL(fullUrl, window.location.origin);
            if (u.origin !== window.location.origin) return null;
            return u.pathname + u.search + u.hash;
        } catch (err) {
            return null;
        }
    }

    function stripOpenAuthQuery(search) {
        if (!search || search === '?') return '';
        var q = search.replace(/^\?/, '');
        var parts = q.split('&').filter(function (seg) {
            return !/^open=(login|register)$/.test(decodeURIComponent(seg.split('=')[0] || ''));
        });
        var kept = parts.filter(Boolean);
        return kept.length ? '?' + kept.join('&') : '';
    }

    function userCloseAuthOverlay() {
        if (authModalHistoryActive) {
            authModalHistoryActive = false;
            try {
                history.back();
            } catch (e) {
                hideOverlayVisualOnly();
            }
            return;
        }
        hideOverlayVisualOnly();
    }

    function createOverlay(url) {
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'pageAuthOverlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.background = 'transparent';
            overlay.style.display = 'flex';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = '100120';
            overlay.style.padding = '10px';
            overlay.style.background = 'rgba(0, 0, 0, 0.15)';
            overlay.style.backdropFilter = 'none';
            overlay.style.webkitBackdropFilter = 'none';
            frame = document.createElement('iframe');
            frame.id = 'pageAuthFrame';
            frame.style.border = '0';
            frame.style.width = '100%';
            frame.style.maxWidth = '420px';
            frame.style.height = '95vh';
            frame.style.borderRadius = '12px';
            frame.style.background = 'transparent';
            frame.style.boxShadow = 'none';
            frame.setAttribute('allowtransparency', 'true');
            overlay.appendChild(frame);
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) userCloseAuthOverlay();
            });
            document.body.appendChild(overlay);
        }
        if (frame) frame.src = url;
        overlay.style.display = 'flex';
        document.body.classList.add('auth-iframe-open');
    }

    function openAuthModalWithHistory(fullUrl) {
        var path = toSameOriginPath(fullUrl);
        if (!path) {
            createOverlay(String(fullUrl).split('#')[0]);
            return;
        }
        var scrollY = window.scrollY || window.pageYOffset || 0;
        try {
            sessionStorage.setItem('__stAuthScroll', String(scrollY));
        } catch (e0) {}
        try {
            history.pushState({ stAuthModal: 1 }, '', path);
            authModalHistoryActive = true;
        } catch (e1) {
            authModalHistoryActive = false;
        }
        createOverlay(String(fullUrl).split('#')[0]);
    }

    window.openAuthModalInIframe = function(url) {
        if (!url) return;
        openAuthModalWithHistory(String(url).split('#')[0]);
    };

    window.addEventListener('message', function(ev) {
        if (!ev || !ev.data) return;
        if (ev.data === 'close-auth-overlay') {
            userCloseAuthOverlay();
            return;
        }
        if (ev.data.type === 'st-auth-url' && typeof ev.data.url === 'string') {
            var p = toSameOriginPath(ev.data.url.indexOf('http') === 0 ? ev.data.url : (window.location.origin + ev.data.url));
            if (!p) return;
            var st = history.state && typeof history.state === 'object' ? Object.assign({}, history.state) : {};
            st.stAuthModal = 1;
            try {
                history.replaceState(st, '', p);
            } catch (e2) {}
        }
    });

    window.addEventListener('popstate', function() {
        if (!overlay || overlay.style.display !== 'flex') return;
        authModalHistoryActive = false;
        hideOverlayVisualOnly();
        var sy = null;
        try {
            var raw = sessionStorage.getItem('__stAuthScroll');
            if (raw !== null) sy = parseInt(raw, 10);
            sessionStorage.removeItem('__stAuthScroll');
        } catch (e4) {}
        if (sy !== null && !isNaN(sy)) {
            window.requestAnimationFrame(function() {
                window.scrollTo(0, sy);
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay && overlay.style.display === 'flex') {
            userCloseAuthOverlay();
        }
    });

    document.addEventListener('click', function(e) {
        var link = e.target.closest('a[href]');
        if (!link) return;
        if (link.classList.contains('js-auth-navigate')) return;
        var hrefAttr = link.getAttribute('href') || '';
        if (hrefAttr === '#' || hrefAttr.indexOf('javascript:') === 0) return;
        if (link.getAttribute('target') === '_blank') return;

        var path = '';
        try {
            path = (link.pathname || new URL(link.href, window.location.origin).pathname || '').replace(/\/+$/, '') || '/';
        } catch (err) {
            path = '';
        }
        var isAuthPath = /\/user\/login$/.test(path) || /\/user\/register$/.test(path);
        var isTrigger = link.classList.contains('glass-login-btn')
            || link.classList.contains('js-footer-floating-login')
            || link.classList.contains('js-footer-floating-register')
            || link.classList.contains('js-open-floating-login')
            || link.classList.contains('js-open-floating-register')
            || link.classList.contains('js-floating-login-link')
            || link.classList.contains('js-floating-register-link');
        if (!isAuthPath && !isTrigger) return;

        var u = link.href || hrefAttr;
        if (!u) return;
        e.preventDefault();
        e.stopPropagation();
        openAuthModalWithHistory(String(u).split('#')[0]);
    }, true);

    function tryOpenAuthFromQuery() {
        try {
            var p = (window.location.pathname || '').replace(/\/+$/, '') || '/';
            if (/\/user\/login$/.test(p) || /\/user\/register$/.test(p)) return;
        } catch (e2) {}
        var m = window.location.search.match(/[?&]open=(login|register)/);
        if (!m) return;
        var loginRoute = @json(route('user.login'));
        var regRoute = @json(route('user.register'));
        var qs = window.location.search.replace(/^\?/, '');
        var base = m[1] === 'register' ? regRoute : loginRoute;
        var join = base.indexOf('?') >= 0 ? '&' : '?';
        var iframeSrc = qs ? base + join + qs : base;
        var newSearch = stripOpenAuthQuery(window.location.search);
        var cleanUrl = window.location.pathname + newSearch;
        try {
            history.replaceState(history.state && typeof history.state === 'object' ? history.state : {}, '', cleanUrl);
        } catch (e3) {}
        openAuthModalWithHistory(iframeSrc);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tryOpenAuthFromQuery);
    } else {
        tryOpenAuthFromQuery();
    }
})();
</script>
