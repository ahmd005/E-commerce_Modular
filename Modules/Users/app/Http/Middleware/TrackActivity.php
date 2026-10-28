<?php

namespace Modules\Users\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. تسجيل وقت بداية الطلب لحساب مدة التنفيذ
        $startTime = microtime(true);

        // 2. تمرير الطلب للتنفيذ (الـ Controller)
        $response = $next($request);

        // 3. حساب الوقت المستغرق بالمللي ثانية
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // 4. تجهيز البيانات للتسجيل
        $logData = [
            'user_id'     => auth('api')->check() ? auth('api')->id() : 'Guest',
            'method'      => $request->method(),
            'url'         => $request->fullUrl(),
            'ip'          => $request->ip(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration . 'ms',
        ];

        // 5. تحديد مستوى التسجيل بناءً على حالة الاستجابة
        $status = $response->getStatusCode();
        
        if ($status >= 500) {
            // خطأ في السيرفر (Critical)
            Log::channel('tracking')->critical('API Request Failed', $logData);
        } elseif ($status >= 400) {
            // خطأ من العميل مثل 401 أو 404 (Warning)
            Log::channel('tracking')->warning('API Request Warning', $logData);
        } else {
            // نجاح (Info)
            Log::channel('tracking')->info('API Request Success', $logData);
        }

        return $response;
    }
}