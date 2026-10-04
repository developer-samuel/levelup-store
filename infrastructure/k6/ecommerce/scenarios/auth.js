import http from 'k6/http';
import { check, sleep } from 'k6';
import { Trend } from 'k6/metrics';

import { BASE_URL, headers, errorRate } from '../config.js';

export const authTime = new Trend('auth_latency_ms', true);

export function login() {
  const payload = JSON.stringify({
    email:    __ENV.TEST_EMAIL    || 'test@example.com',
    password: __ENV.TEST_PASSWORD || 'password',
  });

  const res = http.post(`${BASE_URL}/api/auth/login`, payload, { headers: headers({ json: true }) });
  authTime.add(res.timings.duration);

  errorRate.add(!check(res, { 'login status 200': (r) => r.status === 200 }));
  sleep(1);
}

export function refresh() {
  const res = http.post(`${BASE_URL}/api/auth/refresh`, null, { headers: headers() });
  authTime.add(res.timings.duration);

  errorRate.add(!check(res, { 'refresh status 200 or 401': (r) => r.status === 200 || r.status === 401 }));
  sleep(1);
}

export function logout() {
  const res = http.post(`${BASE_URL}/api/auth/logout`, null, { headers: headers() });
  authTime.add(res.timings.duration);

  errorRate.add(!check(res, { 'logout status 200': (r) => r.status === 200 }));
  sleep(1);
}
