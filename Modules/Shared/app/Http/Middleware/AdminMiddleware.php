<?php

namespace Modules\Shared\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق أن المستخدم مسجل دخول ولديه صلاحية admin
        if (auth('api')->check() && auth('api')->user()->role === 'admin') {
            return $next($request);
        }

        return response()->json([
            'message' => 'غير مسموح لك بالوصول، هذه العملية مخصصة للأدمن فقط.'
        ], 403);
    }
}