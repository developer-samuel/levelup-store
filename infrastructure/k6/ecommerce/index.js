import { healthCheck } from './scenarios/health.js';
import { search }      from './scenarios/search.js';
import { login, refresh, logout } from './scenarios/auth.js';
import { storeCookies } from './scenarios/cookies.js';
import { session }      from './scenarios/assistant.js';

export const options = {
  stages: [
    { duration: '30s', target: 20  },
    { duration: '1m',  target: 20  },
    { duration: '30s', target: 100 },
    { duration: '1m',  target: 100 },
    { duration: '30s', target: 0   },
  ],
  thresholds: {
    http_req_failed:      ['rate<0.01'],
    http_req_duration:    ['p(95)<1000', 'p(99)<2000'],
    search_latency_ms:    ['p(95)<1500', 'p(99)<3000'],
    auth_latency_ms:      ['p(95)<500',  'p(99)<1000'],
    assistant_latency_ms: ['p(95)<3000', 'p(99)<5000'],
    errors:               ['rate<0.01'],
  },
};

export default function () {
  healthCheck();
  search();
  login();
  refresh();
  logout();
  storeCookies();
  session();
}
