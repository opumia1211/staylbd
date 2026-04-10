/**
 * k6 load script: storefront realtime polling + optional burst to HTML home.
 * Install: https://k6.io/docs/get-started/installation/
 * Run: k6 run --vus 50 --duration 2m tools/k6-realtime-stress.js
 * Set env: BASE_URL=https://yourdomain.com API_PREFIX=api
 *
 * Not a substitute for WebSocket load (use Pusher/Soketi bench tools separately).
 */
import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE = __ENV.BASE_URL || 'http://localhost';
const API = (__ENV.API_PREFIX || 'api').replace(/^\/+|\/+$/g, '');

export const options = {
  thresholds: {
    http_req_failed: ['rate<0.05'],
    http_req_duration: ['p(95)<3000'],
  },
};

export default function () {
  const ids = '1,2,3,4,5';
  const url = `${BASE}/${API}/products/realtime?ids=${ids}`;
  const res = http.get(url, {
    headers: { Accept: 'application/json' },
    tags: { name: 'realtime_poll' },
  });
  check(res, { 'realtime 200': (r) => r.status === 200 });
  sleep(1);
}
