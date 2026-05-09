@php
    $authModalEnabled = true;
    if (isset($disableAuthModal) && $disableAuthModal) {
        $authModalEnabled = false;
    }
@endphp

<script>
(function() {
    'use strict';
    // Prevent double execution if included twice
    if (window.__stAuthOverlayInitialized) return;
    window.__stAuthOverlayInitialized = true;

    var authModalEnabled = @json($authModalEnabled);
    if (window.location.search.indexOf('no_auth_modal=1') !== -1) {
        authModalEnabled = false;
    }

    var overlay = null;
    var container = null;
    var frame = null;
    var currentIframeUrl = '';
    var lastOpenAuthUrl = '';
    var lastOpenAuthAt = 0;
    var authModalHistoryActive = false;
    var authOverlayIsOpen = false;

    function createOverlay() {
        if (overlay) return;
        overlay = document.createElement('div');
        overlay.id = 'st-auth-overlay';
        /* Keep public page fully visible: no dim/blur backdrop for auth modal */
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:transparent;backdrop-filter:none;-webkit-backdrop-filter:none;z-index:100000;display:none;opacity:1;align-items:center;justify-content:center;padding:clamp(8px, 3vw, 20px);box-sizing:border-box;touch-action:none;';

        container = document.createElement('div');
        container.style.cssText = 'position:relative;width:100%;max-width:none;height:100%;min-height:0;background:transparent;border-radius:0;overflow:visible;box-shadow:none;display:flex;align-items:center;justify-content:center;box-sizing:border-box;';

        /* Close control lives on .auth-card inside iframe (correct position on modal) */
        frame = document.createElement('iframe');
        /* Full width so the shell fills the viewport (no narrow “strip” beside blurred page) */
        frame.style.cssText = 'width:100%;max-width:100%;height:92vh;max-height:92vh;border:0;background:transparent;display:block;';
        frame.setAttribute('allow', 'payment');
        frame.setAttribute('title', 'Account');

        container.appendChild(frame);
        overlay.appendChild(container);
        document.body.appendChild(overlay);

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) userCloseAuthOverlay();
        });
    }

    function toSameOriginPath(url) {
        try {
            var origin = window.location.origin;
            var u = new URL(url, origin);
            if (u.origin !== origin) return null;
            return u.pathname + u.search + u.hash;
        } catch (e) { return null; }
    }

    function normalizePath(path) {
        return (path || '').replace(/\/+$/, '') || '/';
    }

    /** URLs that load the same minimal auth_modal shell in the iframe */
    function isAuthIframeShellPath(path) {
        var p = normalizePath(path);
        // Supports both /user/login and /user/en/login style paths
        var isAuth = /\/user\/(?:[a-z]{2}\/)?(?:login|register)$/.test(p)
            || /\/user\/(?:[a-z]{2}\/)?password\/(?:reset|code-verify)$/.test(p)
            || /\/user\/(?:[a-z]{2}\/)?password\/reset\/.+/.test(p);
        return isAuth;
    }

    function openAuthModalWithHistory(cleanUrl) {
        if (!authModalEnabled) return false;
        
        var now = Date.now();
        // Strict anti-flicker: if already open and showing this URL, do nothing
        if (overlay && overlay.style.display === 'flex' && currentIframeUrl === cleanUrl) return true;
        // Debounce rapid double-clicks
        if (cleanUrl === lastOpenAuthUrl && now - lastOpenAuthAt < 600) return true;
        
        lastOpenAuthUrl = cleanUrl;
        lastOpenAuthAt = now;

        createOverlay();
        currentIframeUrl = cleanUrl;
        frame.src = cleanUrl;
        overlay.style.display = 'flex';
        authOverlayIsOpen = true;
        document.body.style.overflow = 'hidden';

        // Close any potentially open guest/account modals to prevent layering issues
        if (window.StaylModal && typeof window.StaylModal.hide === 'function') {
            document.querySelectorAll('.modal.is-open, .modal.show').forEach(function(m) {
                try { window.StaylModal.hide(m); } catch (e) {}
            });
        }

        var path = toSameOriginPath(cleanUrl);
        if (path && window.location.pathname + window.location.search !== path) {
            try {
                history.pushState({ stAuthModal: 1 }, '', path);
                authModalHistoryActive = true;
            } catch (e) {
                console.error('Auth state push failed', e);
            }
        } else if (path) {
            authModalHistoryActive = true;
        }
        return true;
    }

    function userCloseAuthOverlay() {
        if (!overlay || !authOverlayIsOpen) return;
        authOverlayIsOpen = false;
        document.body.style.overflow = '';
        overlay.style.display = 'none';
        try {
            if (frame) frame.src = 'about:blank';
        } catch (e) {}
        currentIframeUrl = '';
        if (authModalHistoryActive) {
            authModalHistoryActive = false;
            if (window.history.state && window.history.state.stAuthModal) {
                window.history.back();
            }
        }
    }

    window.addEventListener('popstate', function(e) {
        if (!overlay || !authOverlayIsOpen) return;
        if (!e.state || !e.state.stAuthModal) {
            authModalHistoryActive = false;
            userCloseAuthOverlay();
            return;
        }
        var path = window.location.pathname + window.location.search;
        var next = window.location.origin + path;
        if (frame && isAuthIframeShellPath(window.location.pathname) && currentIframeUrl !== next) {
            currentIframeUrl = next;
            frame.src = next;
        }
    });

    window.addEventListener('message', function(ev) {
        if (!ev.data) return;
        if (ev.data === 'close-auth-overlay') { userCloseAuthOverlay(); return; }
        if (typeof ev.data !== 'object') return;

        if (ev.data.type === 'st-auth-nav' && typeof ev.data.url === 'string') {
            var rel = ev.data.url;
            var pathOnly = rel.split('#')[0];
            var full = window.location.origin + pathOnly;
            if (!authModalEnabled) return;
            createOverlay();
            currentIframeUrl = full;
            frame.src = full;
            overlay.style.display = 'flex';
            authOverlayIsOpen = true;
            document.body.style.overflow = 'hidden';
            lastOpenAuthUrl = full;
            lastOpenAuthAt = Date.now();
            try {
                history.pushState({ stAuthModal: 1 }, '', pathOnly);
                authModalHistoryActive = true;
            } catch (e) {}
            return;
        }

        if (ev.data.type === 'st-auth-url' && typeof ev.data.url === 'string') {
            var p = ev.data.url;
            var st = { stAuthModal: 1 };
            try {
                history.replaceState(st, '', p);
                currentIframeUrl = window.location.origin + p;
            } catch (e) {}
        }
    });

    document.addEventListener('click', function(e) {
        if (e.defaultPrevented) return;
        
        var link = e.target.closest('a[href], [data-guest-auth]');
        if (!link) return;

        var isAuthPath = false;
        var path = '';
        var href = link.getAttribute('href') || link.getAttribute('data-href');
        
        // If it's a data-guest-auth button, determine the URL
        if (link.hasAttribute('data-guest-auth')) {
            var mode = link.getAttribute('data-guest-auth');
            href = mode === 'register' ? '{{ route("user.register") }}' : '{{ route("user.login") }}';
        }

        if (!href || href.indexOf('#') === 0 || href.indexOf('javascript:') === 0) return;

        try {
            var urlObj = new URL(href, window.location.origin);
            path = urlObj.pathname;
            isAuthPath = isAuthIframeShellPath(path);
        } catch (err) {
            path = href.split('?')[0].split('#')[0];
            isAuthPath = isAuthIframeShellPath(path);
        }

        var isTrigger = link.classList.contains('glass-login-btn')
            || link.classList.contains('js-footer-floating-login')
            || link.classList.contains('js-footer-floating-register')
            || link.hasAttribute('data-guest-auth');

        if (!isAuthPath && !isTrigger) return;

        // Take complete ownership of the event to prevent secondary handler conflicts
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) e.stopImmediatePropagation();

        var iframeUrl = String(new URL(href, window.location.origin).href).split('#')[0];
        openAuthModalWithHistory(iframeUrl);
    }, true);

    function tryOpenAuthFromQuery() {
        if (!authModalEnabled || authModalHistoryActive) return;
        var search = window.location.search;
        if (search.indexOf('open=login') === -1 && search.indexOf('open=register') === -1) return;

        var m = search.match(/[?&]open=(login|register)/);
        if (!m) return;

        var iframeSrc = (m[1] === 'register' ? '{{ route("user.register") }}' : '{{ route("user.login") }}');
        
        // Preserve other query params for redirects, but strip open= to avoid loops
        var newSearch = search.replace(/[?&]open=(login|register)/g, '').replace(/^[&]/, '?');
        if (newSearch && newSearch !== '?') {
            iframeSrc += (iframeSrc.indexOf('?') === -1 ? '?' : '&') + newSearch.substring(newSearch.indexOf('?') === 0 ? 1 : 0);
        }
        
        lastOpenAuthAt = 0; // Forced open
        openAuthModalWithHistory(iframeSrc);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tryOpenAuthFromQuery);
    } else {
        tryOpenAuthFromQuery();
    }

    window.openAuthModalInIframe = openAuthModalWithHistory;
})();
</script>
