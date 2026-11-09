import http from 'k6/http';
import { check } from 'k6';

const BASE = __ENV.BASE_URL || 'http://127.0.0.1:8000/api/v1';
const VUS = Number(__ENV.VUS || 5);

const sc = (exec, start) => ({
  executor: 'constant-vus', vus: VUS, duration: '15s',
  exec, startTime: start, gracefulStop: '5s',
});

export const options = {
  scenarios: {
    list:     sc('list',    '0s'),
    filter:   sc('filter',  '20s'),
    search:   sc('search',  '40s'),
    cat_list: sc('catList', '60s'),
  },
  summaryTrendStats: ['avg', 'med', 'p(95)', 'p(99)', 'max'],
  thresholds: {
    'http_req_duration{scenario:list}':     ['max>=0'],
    'http_req_duration{scenario:filter}':   ['max>=0'],
    'http_req_duration{scenario:search}':   ['max>=0'],
    'http_req_duration{scenario:cat_list}': ['max>=0'],
    'http_reqs{scenario:list}':     ['count>=0'],
    'http_reqs{scenario:filter}':   ['count>=0'],
    'http_reqs{scenario:search}':   ['count>=0'],
    'http_reqs{scenario:cat_list}': ['count>=0'],
  },
};

const WORDS = ['Suscipit', 'Rerum', 'Voluptas', 'Quisquam', 'Illo', 'Modi', 'Veniam', 'Sit'];

export function setup() {
  const cats = http.get(`${BASE}/categories`).json('data');
  if (!cats || cats.length === 0) throw new Error('No categories returned from setup');
  return { categoryUuids: cats.map((c) => c.uuid) };
}

const pick = (a) => a[Math.floor(Math.random() * a.length)];
const ok = (r) => check(r, { 'status 200': (x) => x.status === 200 });

export function list()    { ok(http.get(`${BASE}/products?page=${1 + Math.floor(Math.random() * 10)}`)); }
export function filter(d) { ok(http.get(`${BASE}/products?category_uuid=${pick(d.categoryUuids)}`)); }
export function search()  { ok(http.get(`${BASE}/products?search=${encodeURIComponent(pick(WORDS))}`)); }
export function catList() { ok(http.get(`${BASE}/categories`)); }



