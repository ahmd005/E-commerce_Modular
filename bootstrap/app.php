<?php

use App\Http\Middleware\ETagMiddleware;
use App\Http\Middleware\ShowDbConnection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Support\Facades\Broadcast;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
      //  channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',

        then: function () {
            Broadcast::routes(['middleware' => ['auth:api']]);
            
            // تحميل ملف القنوات من موديول الطلبات بشكل آمن
            $channelsPath = base_path('Modules/Order/routes/channels.php');
            if (file_exists($channelsPath)) {
                require $channelsPath;
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // تعريف الـ Guard الخاص بـ JWT
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            // هنا نربط auth:api بحزمة JWT
            'auth:api' => \PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate::class,

            
        ]);
        $middleware->api(append: [
            ShowDbConnection::class,
            ETagMiddleware::class
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
