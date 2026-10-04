import { Rate } from 'k6/metrics';

export const BASE_URL  = __ENV.BASE_URL  || 'http://localhost:8000';
export const JWT_TOKEN = __ENV.JWT_TOKEN || '';

export const errorRate = new Rate('errors');

export function headers({ json = false, auth = false } = {}) {
  const h = {};
  if (json) h['Content-Type'] = 'application/json';
  if (auth && JWT_TOKEN) h['Authorization'] = `Bearer ${JWT_TOKEN}`;
  return h;
}
