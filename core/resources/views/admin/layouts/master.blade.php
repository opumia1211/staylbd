<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $general->siteName($pageTitle ?? '') }}</title>
    <script>
        window.adminSearchUrl = "{{ route('admin.search.index') }}";
    </script>

    @php $adminFavicon = getLogo('favicon'); @endphp
    @if($adminFavicon)
    <link rel="icon" sizes="32x32" href="{{ $adminFavicon }}">
    <link rel="icon" sizes="64x64" href="{{ $adminFavicon }}">
    <link rel="icon" sizes="180x180" href="{{ $adminFavicon }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $adminFavicon }}">
    <link rel="shortcut icon" href="{{ $adminFavicon }}">
    @else
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/default.png') }}">
    @endif
    <link rel="preconnect" href="https://rsms.me/" crossorigin>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" crossorigin>
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/bootstrap-toggle.min.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/all.min.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}?v={{ $assetVersion }}">

    @stack('style-lib')

    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/select2.min.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/app.css') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ url('serve-css/tailwind-admin') }}?v={{ $assetVersion }}">
    <link rel="stylesheet" href="{{ asset('assets/templates/basic/css/logo-effects.css') }}?v={{ $assetVersion }}">

    @stack('style')
</head>
<body>
{{-- Save sidebar scroll on nav link click; restore on load so menu does not roll to top --}}
<script>
(function() {
    if (typeof history !== 'undefined' && 'scrollRestoration' in history) history.scrollRestoration = 'manual';
    var KEY = 'adminSidebarScroll';
    document.addEventListener('click', function(e) {
        var link = e.target.closest && e.target.closest('.sidebar .nav-link[href]');
        if (link && link.href && link.href !== '#' && link.getAttribute('href') && link.getAttribute('href').indexOf('javascript') < 0) {
            var w = document.getElementById('sidebar__menuWrapper');
            if (w) try { sessionStorage.setItem(KEY, w.scrollTop); } catch (x) {}
        }
    }, true);
    function onLoad() {
        window.scrollTo(0, 0);
        var el = document.activeElement;
        var sidebar = document.querySelector('.sidebar');
        if (el && sidebar && sidebar.contains(el)) el.blur();
        var w = document.getElementById('sidebar__menuWrapper');
        if (w) {
            try {
                var saved = sessionStorage.getItem(KEY);
                if (saved !== null) {
                    var pos = parseInt(saved, 10) || 0;
                    w.scrollTop = pos;
                    requestAnimationFrame(function() { w.scrollTop = pos; });
                }
            } catch (x) {}
        }
    }
    window.addEventListener('load', onLoad);
    window.addEventListener('pageshow', onLoad);
    if (document.readyState === 'complete') onLoad(); else window.addEventListener('DOMContentLoaded', onLoad);
})();
</script>
@yield('content')

<script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}?v={{ $assetVersion }}"></script>
<script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}?v={{ $assetVersion }}"></script>
<script src="{{ asset('assets/admin/js/vendor/bootstrap-toggle.min.js') }}?v={{ $assetVersion }}"></script>
<script src="{{ asset('assets/admin/js/vendor/jquery.slimscroll.min.js') }}?v={{ $assetVersion }}"></script>


@include('partials.notify')
@stack('script-lib')

<script src="{{ asset('assets/admin/js/nicEdit.js') }}?v={{ $assetVersion }}"></script>

<script src="{{ asset('assets/admin/js/vendor/select2.min.js') }}?v={{ $assetVersion }}"></script>
<script src="{{ asset('assets/admin/js/app.js') }}?v={{ $assetVersion }}"></script>

{{-- Admin Hamburger: works on laptop, desktop, tablet, mobile (vanilla JS) --}}
<script>
(function() {
    function initAdminSidebarToggle() {
        var sidebar = document.querySelector('.sidebar');
        var openBtn = document.querySelector('.res-sidebar-open-btn');
        var closeBtn = document.querySelector('.res-sidebar-close-btn');
        var overlay = document.getElementById('adminSidebarOverlay');
        if (!sidebar || !openBtn) return;

        function isDesktop() { return window.innerWidth >= 992; }

        function openSidebar() {
            if (isDesktop()) {
                document.body.classList.remove('sidebar-collapsed');
            } else {
                sidebar.classList.add('open');
                document.body.classList.add('sidebar-open');
                if (overlay) { overlay.classList.add('active'); overlay.setAttribute('aria-hidden', 'false'); }
            }
        }
        function closeSidebar() {
            if (isDesktop()) {
                document.body.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('open');
                document.body.classList.remove('sidebar-open');
                if (overlay) { overlay.classList.remove('active'); overlay.setAttribute('aria-hidden', 'true'); }
            }
        }
        function toggleSidebar() {
            if (isDesktop()) {
                document.body.classList.toggle('sidebar-collapsed');
            } else {
                if (sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
        }

        openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isDesktop()) document.body.classList.add('sidebar-collapsed');
                else closeSidebar();
            });
        }
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (sidebar.classList.contains('open')) closeSidebar();
                else if (isDesktop()) document.body.classList.add('sidebar-collapsed');
            }
        });
        window.addEventListener('resize', function() {
            if (isDesktop()) {
                sidebar.classList.remove('open');
                document.body.classList.remove('sidebar-open');
                if (overlay) { overlay.classList.remove('active'); overlay.setAttribute('aria-hidden', 'true'); }
            } else {
                document.body.classList.remove('sidebar-collapsed');
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminSidebarToggle);
    } else {
        initAdminSidebarToggle();
    }
})();
</script>

{{-- LOAD NIC EDIT (only when nicEdit is available) --}}
<script>
    "use strict";
    if (typeof bkLib !== 'undefined') {
        bkLib.onDomLoaded(function() {
            $( ".nicEdit" ).each(function( index ) {
                $(this).attr("id","nicEditor"+index);
                try { new nicEditor({fullPanel : true}).panelInstance('nicEditor'+index,{hasPanel : true}); } catch(e) {}
            });
        });
    }
    (function($){
        {{-- Do not focus all nicEdit on mouseover – ছিলে প্রথম এডিটরে ফোকাস ও পেজ স্ক্রল হতো, একাধিক এডিটরে টাইপ করতে সমস্যা --}}
    })(jQuery);
</script>

@stack('script')

<script>
document.addEventListener('DOMContentLoaded', function() {
    setInterval(function() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (!meta) return;
        fetch('{{ route('admin.session.keepalive') }}', { credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(d) { if (d.csrf) meta.content = d.csrf; })
            .catch(function() {});
    }, 90000);
});
</script>
</body>
</html>
