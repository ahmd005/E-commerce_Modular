import http from 'k6/http';
import { check } from 'k6';
import { Counter } from 'k6/metrics';

const BASE = __ENV.BASE_URL || 'http://127.0.0.1:8000/api/v2';
const VUS = Number(__ENV.VUS || 1);

const notModified = new Counter('etag_304');
const fullBody = new Counter('etag_200');

export const options = {
  vus: VUS,
  duration: '15s',
  summaryTrendStats: ['avg', 'med', 'p(95)', 'p(99)', 'max'],
};

export function setup() {
  const r = http.get(`${BASE}/products?page=1`);
  return { etag: r.headers['Etag'] };
}

export default function (data) {
  const url = `${BASE}/products?page=1`;

  // طلب بدون ETag: لازم 200 مع body
  const a = http.get(url);
  check(a, { 'no etag -> 200': (r) => r.status === 200 });
  fullBody.add(1);

  // طلب مع ETag: لازم 304 بدون body
  const b = http.get(url, { headers: { 'If-None-Match': data.etag } });
  const ok = check(b, {
    'with etag -> 304': (r) => r.status === 304,
    'empty body': (r) => !r.body || r.body.length === 0,
  });
  if (ok) notModified.add(1);
}