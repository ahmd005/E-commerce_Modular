<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ETagMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // فقط GET/HEAD وفقط الردود الناجحة
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $response;
        }
        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === false || $content === '') {
            return $response;
        }

        // البصمة من محتوى الرد
        $etag = '"' . md5($content) . '"';

        $response->headers->set('ETag', $etag);
        $response->headers->set('Cache-Control', 'private, must-revalidate');

        // هل البصمة التي أرسلها العميل تطابق الحالية؟
        $ifNoneMatch = $request->header('If-None-Match');

        if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
            $response->setNotModified(); // 304 وبدون Body
        }

        return $response;
    }
}