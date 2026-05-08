{{-- ========================================================= --}}
{{-- Next-Gen Features Integration (Gap List Resolutions) --}}
{{-- ========================================================= --}}

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
<script
  src="https://browser.sentry-cdn.com/7.99.0/bundle.tracing.min.js"
  crossorigin="anonymous"
></script>
<script>
    if (typeof Sentry !== 'undefined') {
        Sentry.init({
            dsn: "{{ env('SENTRY_LARAVEL_DSN', '') }}", // This should be populated in .env
            release: "staylbd@{{ config('app.asset_version', '1.0.0') }}",
            environment: "{{ app()->environment() }}",
            integrations: [new Sentry.BrowserTracing()],
            tracesSampleRate: 0.2, // Adjust as per error budget
        });
    }
</script>

{{-- 3. Core Web Vitals Continuous Monitoring --}}
<script type="module">
    import {onLCP, onFID, onCLS, onINP, onFCP, onTTFB} from 'https://unpkg.com/web-vitals@3/dist/web-vitals.js?module';

    function sendToAnalytics(metric) {
        // Here we push to our Analytics endpoint or GTM DataLayer
        const body = JSON.stringify(metric);
        
        // Push to datalayer
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'web_vitals',
            web_vital_name: metric.name,
            web_vital_value: metric.value,
            web_vital_id: metric.id,
            web_vital_delta: metric.delta
        });

        // Uncomment to send to an actual API endpoint:
        /*
        (navigator.sendBeacon && navigator.sendBeacon('/api/vitals', body)) ||
        fetch('/api/vitals', {body, method: 'POST', keepalive: true});
        */
        
        // Console log for debugging
        if(window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
             // console.log(metric);
        }
    }

    onCLS(sendToAnalytics);
    onFID(sendToAnalytics);
    onLCP(sendToAnalytics);
    onINP(sendToAnalytics);
    onFCP(sendToAnalytics);
    onTTFB(sendToAnalytics);
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
