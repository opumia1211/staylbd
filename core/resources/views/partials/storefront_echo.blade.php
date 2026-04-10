@php
    $general = $general ?? gs();
    $conn = config('broadcasting.connections.pusher', []);
    $opts = $conn['options'] ?? [];
    $customHost = ! empty($opts['host'] ?? null);
    $useTls = (bool) ($opts['useTLS'] ?? true);
    $scheme = $opts['scheme'] ?? ($useTls ? 'https' : 'http');
    $port = (int) ($opts['port'] ?? ($useTls ? 443 : 80));
    $pusherReady = config('broadcasting.default') === 'pusher' && ! empty($conn['key']);
    $pollPlaceholderId = 999999999;
    $pollUrlTemplate = str_replace((string) $pollPlaceholderId, '__ID__', route('storefront.realtime.product', ['id' => $pollPlaceholderId]));

    $staylbdRealtimePayload = [
        'pollUrlTemplate' => $pollUrlTemplate,
        'batchPollUrl' => route('api.v1.products.realtime'),
        'batchPollMaxIds' => \App\Http\Controllers\Api\ProductsRealtimeController::MAX_IDS,
        'pollIntervalMs' => 12000,
        'wsDeadBeforePollMs' => 15000,
        'wsResumeProbeMs' => 45000,
        'labels' => [
            'idle' => __('Live updates'),
            'connecting' => __('Live updates: connecting…'),
            'live' => __('Live updates: connected'),
            'reconnecting' => __('Live updates: reconnecting…'),
            'polling' => __('Live updates: polling (backup)'),
            'offline' => __('Live updates: offline — retrying'),
            'off' => '',
        ],
    ];

    $staylbdListingRtPayload = [
        'lowStockMax' => (int) config('product_upload.low_stock_max', 20),
        'curSym' => $general->cur_sym ?? '',
        'strings' => [
            'inStock' => __('In Stock'),
            'lowStock' => __('Low Stock'),
            'outOfStock' => __('Out of Stock'),
            'addCart' => __('Cart'),
        ],
    ];

    $staylbdEchoPayload = null;
    if ($pusherReady) {
        $staylbdEchoPayload = [
            'key' => $conn['key'],
            'cluster' => $opts['cluster'] ?? 'mt1',
            'customHost' => $customHost,
            'wsHost' => $customHost ? (string) ($opts['host'] ?? '') : '',
            'wsPort' => $customHost ? $port : null,
            'wssPort' => $customHost ? $port : null,
            'scheme' => $scheme,
            'forceTLS' => $useTls,
        ];
    }
@endphp
<script>
window.__staylbdRealtime = @json($staylbdRealtimePayload);
window.__staylbdListingRt = @json($staylbdListingRtPayload);
@if($pusherReady)
window.__staylbdEcho = @json($staylbdEchoPayload);
@endif
</script>
<style id="staylbd-rt-status-styles">
.staylbd-rt-status {
    position: fixed;
    z-index: 9998;
    bottom: max(12px, env(safe-area-inset-bottom));
    right: max(12px, env(safe-area-inset-right));
    max-width: min(280px, calc(100vw - 24px));
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    line-height: 1.35;
    box-shadow: 0 2px 12px rgba(0,0,0,.12);
    pointer-events: none;
    transition: opacity .2s ease, transform .2s ease;
}
.staylbd-rt-status--hidden { display: none !important; }
.staylbd-rt-status--idle,
.staylbd-rt-status--connecting,
.staylbd-rt-status--reconnecting {
    background: rgba(30, 41, 59, 0.92);
    color: #f8fafc;
}
.staylbd-rt-status--live {
    background: rgba(22, 101, 52, 0.92);
    color: #f0fdf4;
}
.staylbd-rt-status--polling {
    background: rgba(120, 53, 15, 0.92);
    color: #fffbeb;
}
.staylbd-rt-status--offline {
    background: rgba(127, 29, 29, 0.92);
    color: #fef2f2;
}
</style>
<script src="{{ mix('js/storefront-echo.js') }}" defer></script>
<script src="{{ mix('js/storefront-listing-realtime.js') }}" defer></script>
