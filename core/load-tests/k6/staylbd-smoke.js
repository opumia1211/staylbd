/**
 * StayLBD k6 Smoke Test - low load to verify endpoints respond
 * Run: k6 run staylbd-smoke.js
 */
import http from 'k6/http';
import { check } from 'k6';

const BASE_URL = __ENV.BASE_URL || 'http://localhost';

export const options = {
  vus: 10,
  duration: '30s',
  thresholds: {
    http_req_duration: ['p(95)<2000'],
    http_req_failed: ['rate<0.01'],
  },
};

export default function () {
  const tests = [
    { name: 'Home', url: `${BASE_URL}/` },
    { name: 'Products', url: `${BASE_URL}/all/products` },
    { name: 'Login', url: `${BASE_URL}/user/login` },
    { name: 'Register', url: `${BASE_URL}/user/register` },
  ];
  for (const t of tests) {
    const res = http.get(t.url);
    check(res, { [`${t.name} ok`]: (r) => r.status === 200 });
  }
}
