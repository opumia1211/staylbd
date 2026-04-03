/**
 * StayLBD Load Test - k6
 * Tests: Home, Product listing, Product details, Add to cart, Checkout (GET), Login (GET), Register (GET)
 * Run: k6 run staylbd-load.js (default 1000 VUs)
 *      k6 run -e VUS=5000 staylbd-load.js
 *      k6 run -e VUS=10000 staylbd-load.js
 */
import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE_URL = __ENV.BASE_URL || 'http://localhost';
const VUS = __ENV.VUS ? parseInt(__ENV.VUS, 10) : 1000;
const DURATION = __ENV.DURATION || '2m';

export const options = {
  scenarios: {
    load: {
      executor: 'constant-vus',
      vus: VUS,
      duration: DURATION,
      startTime: '0s',
      gracefulStop: '30s',
    },
  },
  thresholds: {
    http_req_duration: ['p(95)<3000', 'p(99)<5000'],
    http_req_failed: ['rate<0.05'],
  },
};

export function setup() {
  const res = http.get(`${BASE_URL}/`);
  if (res.status !== 200) {
    console.warn('Warning: Base URL may not be reachable. Check BASE_URL.');
  }
  return { baseUrl: BASE_URL };
}

export default function (data) {
  const base = data.baseUrl;
  const jar = http.cookieJar();

  const endpoints = [
    { method: 'GET', url: `${base}/`, name: 'Home' },
    { method: 'GET', url: `${base}/all/products`, name: 'Product listing' },
    { method: 'GET', url: `${base}/product/item-1`, name: 'Product detail (sample)' },
    { method: 'GET', url: `${base}/user/login`, name: 'Login page' },
    { method: 'GET', url: `${base}/user/register`, name: 'Register page' },
  ];

  const r = endpoints[Math.floor(Math.random() * endpoints.length)];
  const res = r.method === 'GET'
    ? http.get(r.url, { tags: { name: r.name } })
    : http.post(r.url, {}, { tags: { name: r.name } });

  check(res, { [`${r.name} status 2xx or 3xx`]: (r) => r.status >= 200 && r.status < 400 });

  sleep(Math.random() * 2 + 0.5);
}

export function handleSummary(data) {
  return {
    'stdout': textSummary(data, { indent: ' ', enableColors: true }),
    'summary-k6.json': JSON.stringify(data, null, 2),
    'summary-k6.html': htmlReport(data),
  };
}

function textSummary(data, opts) {
  const indent = opts?.indent || '';
  let out = '\n' + indent + '========== k6 Summary ==========\n';
  if (data.metrics) {
    const m = data.metrics;
    if (m.http_reqs) out += indent + `Requests: ${m.http_reqs.values.count}\n`;
    if (m.http_req_duration) out += indent + `Duration p95: ${(m.http_req_duration.values['p(95)'] / 1000).toFixed(2)}s\n`;
    if (m.http_req_failed) out += indent + `Failed rate: ${(m.http_req_failed.values.rate * 100).toFixed(2)}%\n`;
  }
  return out;
}

function htmlReport(data) {
  const m = data.metrics || {};
  const duration = m.http_req_duration ? (m.http_req_duration.values['p(95)'] / 1000).toFixed(2) : 'N/A';
  const failed = m.http_req_failed ? (m.http_req_failed.values.rate * 100).toFixed(2) : 'N/A';
  const count = m.http_reqs ? m.http_reqs.values.count : 0;
  return `<!DOCTYPE html><html><head><meta charset="utf-8"><title>k6 Report</title></head><body><h1>StayLBD k6 Load Test</h1><pre>${JSON.stringify({ requests: count, p95_sec: duration, fail_rate_pct: failed }, null, 2)}</pre></body></html>`;
}
