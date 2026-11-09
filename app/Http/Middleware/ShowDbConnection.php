<?php

namespace App\Http\Middleware;

use App\Support\DbUsage;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowDbConnection
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! config('app.debug') || ! $request->hasHeader('X-Debug-Db')) {
            return $response;
        }

        $summary = app(DbUsage::class)->summary();

        // في الـ Headers
        $response->headers->set('X-DB-Read',  implode(',', $summary['read_from'])  ?: '-');
        $response->headers->set('X-DB-Write', implode(',', $summary['write_to'])   ?: '-');

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            if (is_array($data)) {
                $data['_db'] = $summary;
                $response->setData($data);
            }
        }

        return $response;
    }
}
