import http from 'k6/http';
import { check } from 'k6';
import { Counter } from 'k6/metrics';

const BASE = __ENV.BASE_URL || 'http://127.0.0.1:8000/api/v1';
const LABEL = __ENV.LABEL || 'run';
const VUS = Number(__ENV.VUS || 5);

const sc = (exec, start) => ({
  executor: 'constant-vus', vus: VUS, duration: '15s',
  exec, startTime: start, gracefulStop: '5s',
});

const etag304 = new Counter('etag_304');

export const options = {
  scenarios: {
    products:   sc('products',   '0s'),
    categories: sc('categories', '20s'),
    etag:       { executor: 'constant-vus', vus: 1, duration: '15s', exec: 'etag', startTime: '40s', gracefulStop: '5s' },
  },
  summaryTrendStats: ['med', 'p(95)', 'p(99)'],
  thresholds: {
    'http_req_duration{scenario:products}':   ['max>=0'],
    'http_req_duration{scenario:categories}': ['max>=0'],
    'http_reqs{scenario:products}':   ['count>=0'],
    'http_reqs{scenario:categories}': ['count>=0'],
  },
};

export function setup() {
  // تسخين الكاش: نفس الصفحات اللي رح نقيس عليها
  for (let p = 1; p <= 5; p++) http.get(`${BASE}/products?page=${p}`);
  http.get(`${BASE}/categories`);
  const r = http.get(`${BASE}/products?page=1`);
  return { etag: r.headers['Etag'] };
}

const ok = (r) => check(r, { 'status 200': (x) => x.status === 200 });

export function products()   { ok(http.get(`${BASE}/products?page=${1 + Math.floor(Math.random() * 5)}`)); }
export function categories() { ok(http.get(`${BASE}/categories`)); }

export function etag(d) {
  const url = `${BASE}/products?page=1`;
  http.get(url);
  const b = http.get(url, { headers: { 'If-None-Match': d.etag } });
  if (check(b, { '304': (r) => r.status === 304 })) etag304.add(1);
}

export function handleSummary(data) {
  const m = (name, key) => data.metrics[name] && data.metrics[name].values[key];
  const out = {
    label: LABEL,
    products: {
      p50: m('http_req_duration{scenario:products}', 'med'),
      p95: m('http_req_duration{scenario:products}', 'p(95)'),
      p99: m('http_req_duration{scenario:products}', 'p(99)'),
      count: m('http_reqs{scenario:products}', 'count'),
    },
    categories: {
      p50: m('http_req_duration{scenario:categories}', 'med'),
      p95: m('http_req_duration{scenario:categories}', 'p(95)'),
      p99: m('http_req_duration{scenario:categories}', 'p(99)'),
      count: m('http_reqs{scenario:categories}', 'count'),
    },
    etag_304: m('etag_304', 'count') || 0,
  };
  return { [`perf/result_${LABEL}.json`]: JSON.stringify(out, null, 2) };
}