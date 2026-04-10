import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

function cfgEcho() {
    const c = window.__staylbdEcho;
    return c && c.key ? c : null;
}

function cfgRt() {
    return window.__staylbdRealtime || {};
}

function listingCfg() {
    return window.__staylbdListingRt || {};
}

function fingerprint(payload) {
    try {
        return JSON.stringify(payload);
    } catch {
        return String(Date.now());
    }
}

function dispatchProductUpdate(payload) {
    window.dispatchEvent(new CustomEvent('staylbd:product-updated', { detail: payload }));
}

function formatAmountClient(n) {
    const x = Math.round(parseFloat(n) * 100) / 100;
    if (Number.isNaN(x)) {
        return '0.00';
    }
    return x.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function batchRowToPayload(row, curSym) {
    const id = parseInt(row.id, 10);
    const price = parseFloat(row.price);
    const stock = parseInt(row.stock, 10) || 0;
    const priceNum = Number.isFinite(price) ? price : 0;
    const effFmt = formatAmountClient(priceNum);
    const delisted = stock <= 0 && priceNum <= 0;

    if (delisted) {
        return {
            action: 'deleted',
            product: {
                id,
                quantity: 0,
                stock_qty: 0,
                max_order_qty: 0,
                has_variants: false,
            },
            display: {
                cur_sym: curSym,
                effective: 0,
                effective_formatted: formatAmountClient(0),
                compare_at: null,
                compare_formatted: null,
                has_savings: false,
                save_percent: 0,
                save_amount_formatted: null,
            },
            variants: [],
        };
    }

    return {
        action: 'updated',
        product: {
            id,
            quantity: stock,
            stock_qty: stock,
            max_order_qty: stock,
            has_variants: false,
        },
        display: {
            cur_sym: curSym,
            effective: priceNum,
            effective_formatted: effFmt,
            compare_at: null,
            compare_formatted: null,
            has_savings: false,
            save_percent: 0,
            save_amount_formatted: null,
        },
        variants: [],
    };
}

function ensureStatusEl() {
    let el = document.getElementById('staylbd-rt-status');
    if (!el) {
        el = document.createElement('div');
        el.id = 'staylbd-rt-status';
        el.className = 'staylbd-rt-status';
        el.setAttribute('role', 'status');
        el.setAttribute('aria-live', 'polite');
        document.body.appendChild(el);
    }
    return el;
}

function setStatus(state) {
    const labels = cfgRt().labels || {};
    const map = {
        off: labels.off || '',
        idle: labels.idle || 'Live updates',
        connecting: labels.connecting || 'Live updates: connecting…',
        live: labels.live || 'Live updates: connected',
        reconnecting: labels.reconnecting || 'Live updates: reconnecting…',
        polling: labels.polling || 'Live updates: polling (backup)',
        offline: labels.offline || 'Live updates: offline — retrying',
    };
    const text = map[state] || state;
    if (state === 'off' || !text) {
        const el = document.getElementById('staylbd-rt-status');
        if (el) {
            el.classList.add('staylbd-rt-status--hidden');
            el.textContent = '';
        }
        return;
    }
    const el = ensureStatusEl();
    el.classList.remove('staylbd-rt-status--hidden');
    el.textContent = text;
    el.className = 'staylbd-rt-status staylbd-rt-status--' + state;
    el.dataset.state = state;
}

let pollTimer = null;
let wsDeadTimer = null;
let wsResumeTimer = null;
let lastPayloadFingerprint = '';
const lastBatchFingerprintById = {};
let pusherRef = null;

function productIdFromDom() {
    return (document.body && document.body.dataset && document.body.dataset.productId) || '';
}

function collectListingProductIdsFromDom() {
    const max = cfgRt().batchPollMaxIds || 60;
    const seen = new Set();
    const out = [];
    document.querySelectorAll('[data-product-id]:not(body)').forEach((el) => {
        const v = el.getAttribute('data-product-id');
        if (!v || seen.has(v)) {
            return;
        }
        seen.add(v);
        out.push(v);
    });
    return out.slice(0, max);
}

function pollUrl() {
    const tpl = cfgRt().pollUrlTemplate || '';
    const id = productIdFromDom();
    if (!tpl || !id || tpl.indexOf('__ID__') === -1) {
        return '';
    }
    return tpl.replace('__ID__', encodeURIComponent(String(id)));
}

function batchPollUrlWithIds(ids) {
    const base = cfgRt().batchPollUrl || '';
    if (!base || !ids.length) {
        return '';
    }
    const sep = base.indexOf('?') === -1 ? '?' : '&';
    return base + sep + 'ids=' + encodeURIComponent(ids.join(','));
}

function applyPollPayload(payload) {
    if (!payload || typeof payload !== 'object' || !payload.product) {
        return;
    }
    const fp = fingerprint(payload);
    if (fp === lastPayloadFingerprint) {
        return;
    }
    lastPayloadFingerprint = fp;
    dispatchProductUpdate(payload);
}

function applyBatchResponse(data) {
    if (!data || !Array.isArray(data.products)) {
        return;
    }
    const curSym = listingCfg().curSym || '';
    data.products.forEach((row) => {
        if (!row || row.id == null) {
            return;
        }
        const payload = batchRowToPayload(row, curSym);
        const fp = fingerprint(payload);
        const idKey = String(row.id);
        if (lastBatchFingerprintById[idKey] === fp) {
            return;
        }
        lastBatchFingerprintById[idKey] = fp;
        dispatchProductUpdate(payload);
    });
}

function shouldStartPollFallback() {
    const listingIds = collectListingProductIdsFromDom();
    const hasBatch = !!(cfgRt().batchPollUrl && listingIds.length > 0);
    const hasSingle = !!(pollUrl() && productIdFromDom());
    return hasBatch || hasSingle;
}

function stopPolling() {
    if (pollTimer) {
        clearInterval(pollTimer);
        pollTimer = null;
    }
}

function startPolling() {
    if (!shouldStartPollFallback()) {
        return;
    }
    if (pollTimer) {
        return;
    }
    setStatus('polling');
    const ms = Math.min(Math.max(cfgRt().pollIntervalMs || 12000, 10000), 15000);

    async function tick() {
        const listingIds = collectListingProductIdsFromDom();
        const batchUrl = batchPollUrlWithIds(listingIds);
        const singleU = pollUrl();
        try {
            if (batchUrl) {
                const res = await fetch(batchUrl, {
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json' },
                    cache: 'no-store',
                });
                if (res.ok) {
                    const data = await res.json();
                    applyBatchResponse(data);
                }
            }
            if (singleU && productIdFromDom()) {
                const res = await fetch(singleU, {
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json' },
                    cache: 'no-store',
                });
                if (res.ok) {
                    const data = await res.json();
                    applyPollPayload(data);
                }
            }
        } catch (e) {
            console.warn('[staylbd realtime] poll failed', e);
        }
    }

    tick();
    pollTimer = setInterval(tick, ms);
    ensureWsResumeProbe();
}

function clearWsDeadFallback() {
    if (wsDeadTimer) {
        clearTimeout(wsDeadTimer);
        wsDeadTimer = null;
    }
}

function scheduleWsDeadFallback() {
    const ms = cfgRt().wsDeadBeforePollMs || 15000;
    clearWsDeadFallback();
    wsDeadTimer = setTimeout(() => {
        if (!pusherRef) {
            if (shouldStartPollFallback()) {
                startPolling();
            }
            return;
        }
        const st = pusherRef.connection && pusherRef.connection.state;
        if (st === 'connected') {
            return;
        }
        if (shouldStartPollFallback()) {
            startPolling();
        }
        setStatus('offline');
    }, ms);
}

function ensureWsResumeProbe() {
    if (wsResumeTimer || !pusherRef) {
        return;
    }
    const every = cfgRt().wsResumeProbeMs || 45000;
    wsResumeTimer = setInterval(() => {
        if (!pollTimer || !pusherRef) {
            return;
        }
        const st = pusherRef.connection && pusherRef.connection.state;
        if (st === 'disconnected' || st === 'failed' || st === 'unavailable') {
            try {
                pusherRef.connect();
            } catch (e) {
                /* ignore */
            }
        }
    }, every);
}

function bindEchoChannels() {
    window.Echo.channel('products').listen('.product.updated', (payload) => {
        dispatchProductUpdate(payload);
    });
    const pid = productIdFromDom();
    if (pid) {
        window.Echo.channel('product.' + pid).listen('.product.updated', (payload) => {
            dispatchProductUpdate(payload);
        });
    }
}

function getPusherFromEcho() {
    const c = window.Echo && window.Echo.connector;
    if (c && c.pusher) {
        return c.pusher;
    }
    return null;
}

function bootWebSocket() {
    const cfg = cfgEcho();
    if (!cfg || !cfg.key) {
        return false;
    }

    window.Pusher = Pusher;

    const options = {
        broadcaster: 'pusher',
        key: cfg.key,
        cluster: cfg.cluster || 'mt1',
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    };

    if (cfg.customHost && cfg.wsHost) {
        options.wsHost = cfg.wsHost;
        options.wsPort = cfg.wsPort || (cfg.forceTLS ? 443 : 80);
        options.wssPort = cfg.wssPort || options.wsPort;
        options.forceTLS = !!cfg.forceTLS;
    }

    window.Echo = new Echo(options);
    pusherRef = getPusherFromEcho();
    if (!pusherRef) {
        bindEchoChannels();
        setStatus('reconnecting');
        scheduleWsDeadFallback();
        return true;
    }

    setStatus('connecting');
    scheduleWsDeadFallback();

    function ensurePollingWhileWsDown() {
        scheduleWsDeadFallback();
        if (shouldStartPollFallback()) {
            startPolling();
        }
    }

    pusherRef.connection.bind('connected', () => {
        clearWsDeadFallback();
        setStatus('live');
        stopPolling();
        if (wsResumeTimer) {
            clearInterval(wsResumeTimer);
            wsResumeTimer = null;
        }
        lastPayloadFingerprint = '';
        Object.keys(lastBatchFingerprintById).forEach((k) => {
            delete lastBatchFingerprintById[k];
        });
    });

    pusherRef.connection.bind('state_change', (states) => {
        const cur = states && states.current;
        if (cur === 'connected') {
            return;
        }
        if (cur === 'connecting') {
            setStatus('connecting');
            scheduleWsDeadFallback();
            return;
        }
        if (cur === 'disconnected' || cur === 'unavailable') {
            setStatus('reconnecting');
            ensurePollingWhileWsDown();
            return;
        }
        if (cur === 'failed') {
            setStatus('offline');
            ensurePollingWhileWsDown();
        }
    });

    pusherRef.connection.bind('error', () => {
        setStatus('reconnecting');
        ensurePollingWhileWsDown();
    });

    pusherRef.connection.bind('disconnected', () => {
        setStatus('reconnecting');
        ensurePollingWhileWsDown();
    });

    pusherRef.connection.bind('unavailable', () => {
        setStatus('offline');
        ensurePollingWhileWsDown();
    });

    bindEchoChannels();
    return true;
}

function shouldBootRealtimeUi() {
    if (cfgEcho()) {
        return true;
    }
    if (cfgRt().batchPollUrl && collectListingProductIdsFromDom().length > 0) {
        return true;
    }
    if (pollUrl() && productIdFromDom()) {
        return true;
    }
    return false;
}

function boot() {
    if (!shouldBootRealtimeUi()) {
        setStatus('off');
        return;
    }

    const hasWs = bootWebSocket();

    if (!hasWs && shouldStartPollFallback()) {
        setStatus('polling');
        startPolling();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
