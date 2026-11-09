import http from 'k6/http';
import { check, sleep } from 'k6';

// 1. إعدادات ضغط عالي وقوي (100 مستخدم متزامن)
export const options = {
  stages: [
    { duration: '15s', target: 20 },  // تصاعد سريع إلى 20 مستخدم
    { duration: '30s', target: 100 }, // ضغط عالي بـ 100 مستخدم متزامن لمدة 30 ثانية
    { duration: '15s', target: 0 },   // خفض تدريجي
  ],
  thresholds: {
    http_req_failed: ['rate<0.01'],    // نسبة الأخطاء أقل من 1%
    http_req_duration: ['p(95)<2000'], // هدف تقريبي
  },
};

const BASE_URL = 'http://127.0.0.1:8000/api/v1';

// مصفوفة كلمات بحث شائعة للمحاكاة الواقعية
const searchKeywords = ['Pro', 'Max', 'Ultra', 'Wireless', 'Smart', 'Phone'];

export default function () {
  // --- السيناريو الأول: جلب قائمة المنتجات مع الصفحة الأولى ---
  let resProducts = http.get(`${BASE_URL}/products?page=1&per_page=10`);
  check(resProducts, {
    'Products status is 200': (r) => r.status === 200,
  });

  sleep(0.3);

  // --- السيناريو الثاني: بحث عشوائي بدون مكتبات خارجية ---
  let randomKeyword = searchKeywords[Math.floor(Math.random() * searchKeywords.length)];
  let resSearch = http.get(`${BASE_URL}/products?search=${randomKeyword}&per_page=10`);
  check(resSearch, {
    'Search status is 200': (r) => r.status === 200,
  });

  sleep(0.3);

  // --- السيناريو الثالث: جلب التصنيفات ---
  let resCategories = http.get(`${BASE_URL}/categories`);
  check(resCategories, {
    'Categories status is 200': (r) => r.status === 200,
  });

  sleep(0.5);
}