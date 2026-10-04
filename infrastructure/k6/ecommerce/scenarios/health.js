import http from 'k6/http';
import { check, sleep } from 'k6';

import { BASE_URL, errorRate } from '../config.js';

export function healthCheck() {
  const res = http.get(`${BASE_URL}/api/dev/health-check`);

  errorRate.add(!check(res, { 'health status 200': (r) => r.status === 200 }));
  sleep(1);
}
