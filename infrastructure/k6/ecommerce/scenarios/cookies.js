import http from 'k6/http';
import { check, sleep } from 'k6';

import { BASE_URL, headers, errorRate } from '../config.js';

export function storeCookies() {
  const payload = JSON.stringify({ analytics: true, marketing: false });
  const res     = http.post(`${BASE_URL}/api/cookies/store`, payload, { headers: headers({ json: true }) });

  errorRate.add(!check(res, { 'cookies status 200': (r) => r.status === 200 }));
  sleep(1);
}
