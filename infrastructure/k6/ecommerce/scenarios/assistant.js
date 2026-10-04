import http from 'k6/http';
import { check, sleep } from 'k6';
import { Trend } from 'k6/metrics';

import { BASE_URL, headers, errorRate } from '../config.js';

export const assistantTime = new Trend('assistant_latency_ms', true);

export function session() {
  const res = http.get(`${BASE_URL}/api/assistant/session`, { headers: headers({ auth: true }) });
  assistantTime.add(res.timings.duration);

  errorRate.add(!check(res, {
    'assistant session status 200': (r) => r.status === 200,
    'assistant session has conversation_id': (r) => {
      try {
        return 'conversation_id' in JSON.parse(r.body);
      } catch {
        return false;
      }
    },
  }));
  sleep(1);
}
