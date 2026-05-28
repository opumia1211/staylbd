{{-- ========================================================= --}}
{{-- Next-Gen Features Integration (Gap List Resolutions) --}}
{{-- ========================================================= --}}
@php
    $libraryOnly = feature_enabled('assets.library_only_mode', true);
    $allowExternalObs = feature_enabled('assets.allow_external_observability', false);
@endphp

{{-- 1. PWA & Service Worker --}}
<link rel="manifest" href="{{ asset('manifest.json') }}">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/staylbd/sw.js').then(function(registration) {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
            }, function(err) {
                console.log('ServiceWorker registration failed: ', err);
            });
        });
    }
</script>

{{-- 2. Sentry / Centralized Frontend Error Tracking --}}
@if(!$libraryOnly && $allowExternalObs)
    <script
      src="https://browser.sentry-cdn.com/7.99.0/bundle.tracing.min.js"
      crossorigin="anonymous"
    ></script>
    <script>
        try {
            if (typeof Sentry !== 'undefined') {
                Sentry.init({
                    dsn: "{{ env('SENTRY_LARAVEL_DSN', '') }}",
                    release: "staylbd@{{ config('app.asset_version', '1.0.0') }}",
                    environment: "{{ app()->environment() }}",
                    integrations: [new Sentry.BrowserTracing()],
                    tracesSampleRate: 0.2,
                });
            }
        } catch (e) {}
    </script>
@endif

{{-- 3. Core Web Vitals Continuous Monitoring --}}
<script>
    (function () {
        try {
            window.dataLayer = window.dataLayer || [];
            var ttfb = Math.round(performance.timeOrigin ? (performance.now()) : 0);
            window.dataLayer.push({
                event: 'web_vitals_local',
                metric: 'TTFB',
                value: ttfb
            });
            if ('PerformanceObserver' in window) {
                try {
                    var lcpObserver = new PerformanceObserver(function (entryList) {
                        var entries = entryList.getEntries();
                        var last = entries[entries.length - 1];
                        if (last) {
                            window.dataLayer.push({
                                event: 'web_vitals_local',
                                metric: 'LCP',
                                value: Math.round(last.startTime)
                            });
                        }
                    });
                    lcpObserver.observe({ type: 'largest-contentful-paint', buffered: true });
                } catch (e) {}
            }
        } catch (e) {}
    })();
</script>

{{-- 4. Checkout Funnel Analytics (Structured Setup) --}}
<script>
    window.dataLayer = window.dataLayer || [];
    window.StaylAnalytics = {
        trackCheckoutStep: function(stepNumber, stepName, data = {}) {
            window.dataLayer.push({
                event: 'checkout_progress',
                ecommerce: {
                    checkout: {
                        actionField: {
                            step: stepNumber,
                            option: stepName
                        },
                        products: data.products || []
                    }
                }
            });
            console.log(`[Analytics] Tracked Checkout Step ${stepNumber}: ${stepName}`);
        },
        
        trackPurchase: function(transactionId, revenue, tax, shipping, products) {
            window.dataLayer.push({
                event: 'purchase',
                ecommerce: {
                    purchase: {
                        actionField: {
                            id: transactionId,
                            revenue: revenue,
                            tax: tax,
                            shipping: shipping
                        },
                        products: products || []
                    }
                }
            });
            console.log(`[Analytics] Tracked Purchase ${transactionId}`);
        }
    };
</script>
