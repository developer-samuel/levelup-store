import http from 'k6/http';
import { check, sleep } from 'k6';
import { Trend } from 'k6/metrics';

import { BASE_URL, errorRate } from '../config.js';

export const searchTime = new Trend('search_latency_ms', true);

const QUERIES = ['nike', 'adidas', 'shoes', 't-shirt', 'jacket'];

export function search() {
  const query = QUERIES[Math.floor(Math.random() * QUERIES.length)];
  const res   = http.get(`${BASE_URL}/api/search?query=${query}`);
  searchTime.add(res.timings.duration);

  errorRate.add(!check(res, { 'search status 200': (r) => r.status === 200 }));
  sleep(1);
}
