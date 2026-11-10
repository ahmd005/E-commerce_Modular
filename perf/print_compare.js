import fs from 'fs';
const before = JSON.parse(fs.readFileSync('perf/result_v1.json', 'utf8'));
const after  = JSON.parse(fs.readFileSync('perf/result_v2.json', 'utf8'));

const ms = (v) => (v == null ? '-' : v.toFixed(0) + ' ms');
const gain = (b, a) => (b && a ? (((b - a) / b) * 100).toFixed(0) + '%' : '-');
const x = (b, a) => (b && a ? (a / b).toFixed(1) + 'x' : '-');

const rows = [];
for (const [title, key] of [['Products List', 'products'], ['Categories List', 'categories']]) {
  const b = before[key], a = after[key];
  rows.push({ 'Test Case': title + ' p50', 'Before (v1)': ms(b.p50), 'After (v2)': ms(a.p50), 'Improvement': gain(b.p50, a.p50) });
  rows.push({ 'Test Case': title + ' p95', 'Before (v1)': ms(b.p95), 'After (v2)': ms(a.p95), 'Improvement': gain(b.p95, a.p95) });
  rows.push({ 'Test Case': title + ' p99', 'Before (v1)': ms(b.p99), 'After (v2)': ms(a.p99), 'Improvement': gain(b.p99, a.p99) });
  rows.push({ 'Test Case': title + ' (Reqs/15s)', 'Before (v1)': b.count, 'After (v2)': a.count, 'Improvement': x(b.count, a.count) });
}
rows.push({ 'Test Case': '304 Responses (ETag)', 'Before (v1)': before.etag_304, 'After (v2)': after.etag_304, 'Improvement': '-' });

console.log('\n========== Performance Comparison: Before (v1) vs After (v2) ==========\n');
console.table(rows);