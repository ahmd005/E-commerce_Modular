<?php

namespace Modules\Users\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimit
{
    /**
     * عدد المحاولات المسموحة
     */
    protected int $maxAttempts = 5;

    /**
     * مدة الحظر بالثواني (15 دقيقة)
     */
    protected int $decaySeconds = 900;

    public function handle(Request $request, Closure $next): Response
    {
        // إنشاء مفتاح فريد بناءً على الإيميل وعنوان الـ IP
        // إذا حاول المخترق تغيير الإيميل، الـ IP سيبقى نفسه
        $key = 'login:' . strtolower($request->input('email')) . '|' . $request->ip();

        // التحقق من عدد المحاولات
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'error' => 'لقد تجاوزت عدد المحاولات المسموح بها.',
                'retry_after_seconds' => $seconds
            ], 429); // 429 Too Many Requests
        }

        // زيادة عداد المحاولات
        RateLimiter::hit($key, $this->decaySeconds);

        // تمرير الطلب للخطوة التالية (الـ Controller)
        $response = $next($request);

        // إذا نجح تسجيل الدخول (كود 200)، نقوم بمسح العداد
        if ($response->getStatusCode() === 200) {
            RateLimiter::clear($key);
        }

        return $response;
    }
}