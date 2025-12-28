<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Sekarang pintu API udah aktif
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Cukup satu blok middleware buat semua settingan
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Karena pake api.php, kita GAK BUTUH lagi validateCsrfTokens(except: ...)
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create(); // create() SELALU di paling bawah