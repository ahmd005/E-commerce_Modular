import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  scenarios: {
    // المرحلة الأولى: إنشاء السلال بشكل متسلسل وبسيط لضمان نجاحهما 100%
    create_carts: {
      executor: 'per-vu-iterations',
      vus: 2,
      iterations: 1,
      maxDuration: '5s',
    },
  },
};

const productUuid = '83205f21-8437-4bdd-a106-ad21fdf390dc';
const quantity = 1;

const tokens = [
  'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL0VfY29tbWVyY2UvdjEvYXV0aC9sb2dpbiIsImlhdCI6MTc5MzgyMTIxNiwiZXhwIjoxNzkzODI0ODE2LCJuYmYiOjE3OTM4MjEyMTYsImp0aSI6ImxwbDBoRW9ZV3lzYWFQMkoiLCJzdWIiOiI0IiwicHJ2IjoiMzRjZWM3ZWMyOTY1NmY1NGQyMzg4MDM3MDBjYjNmYTYxYTYzMjk4YSIsImVtYWlsIjoiYWhtZWQxMjM0NUBnbWFpbC5jb20iLCJyb2xlcyI6ImFkbWluIiwidHlwZSI6InJlZnJlc2gifQ.o8ARPMzomyEmNBmLMOM7bWnFo9KKdfhF0K4pg8aPXG0',
  'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL0VfY29tbWVyY2UvdjEvYXV0aC9sb2dpbiIsImlhdCI6MTc5MzgyMTE5MSwiZXhwIjoxNzkzODI0NzkxLCJuYmYiOjE3OTM4MjExOTEsImp0aSI6IkxMVldFYXZlZGxHM0JLTWIiLCJzdWIiOiIzIiwicHJ2IjoiMzRjZWM3ZWMyOTY1NmY1NGQyMzg4MDM3MDBjYjNmYTYxYTYzMjk4YSIsImVtYWlsIjoiYWhtZWRfdW5pcXVlXzEyMzQ1QGdtYWlsLmNvbSIsInJvbGVzIjoiYWRtaW4iLCJ0eXBlIjoicmVmcmVzaCJ9.6ZFdNDrI37bMq3Z6Q4SlFvRmUMapehM2NIsYzZOec8s'
];

export default function () {
  const currentToken = tokens[__VU - 1];
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': `Bearer ${currentToken}`,
  };

  // ==========================================
  // Step 1: Add to Cart (Staggered by VU ID to prevent concurrent DB race on cart)
  // ==========================================


  const cartUrl = `http://localhost:8000/api/cart/v1?quantity=${quantity}&product_uuid=${productUuid}`;
  const cartRes = http.post(cartUrl, null, { headers: headers });

  console.log(`[VU ${__VU}] 🛒 Cart Status: ${cartRes.status}`);

  const cartSuccess = check(cartRes, {
    'cart added successfully': (r) => r.status === 200 || r.status === 201,
  });

  if (!cartSuccess) {
    console.log(`[VU ${__VU}] ❌ Failed to add item to cart: ${cartRes.body}`);
    return;
  }

  // ==========================================
  // Step 2: Synchronization Pause
  // ==========================================
  // ننتظر قليلاً حتى تكتمل سلة المستخدم الثاني تماماً ويستقر السيرفر
  sleep(1.5);

  // ==========================================
  // Step 3: THE ORDER RACE CONDITION (Checkout)
  // ==========================================
  // هنا ينطلق الاثنان معاً في نفس الملي ثانية تماماً لطلب الشراء وحجز المخزون
  const orderUrl = 'http://localhost:8000/api/orders/v1';
  const orderRes = http.post(orderUrl, null, { headers: headers });

  console.log(`[VU ${__VU}] 📦 Order Status: ${orderRes.status} | Body: ${orderRes.body}`);

  check(orderRes, {
    'order is success (200/201) or out of stock (422)': (r) => 
      r.status === 200 || r.status === 201 || r.status === 422,
  });
}

export function handleSummary(data) {
  const totalRequests = data.metrics.http_reqs ? data.metrics.http_reqs.values.count : 0;
  
  return {
    'stdout': `
==================================================
   RACE CONDITION TEST REPORT (Order Checkout)
==================================================
> Total Requests Sent: ${totalRequests}
--------------------------------------------------
> Expected Results:
  - Both carts created successfully (201).
  - One user gets SUCCESS (200/201) and locks the stock.
  - The other user gets OUT OF STOCK (422).
==================================================
    `,
  };
}