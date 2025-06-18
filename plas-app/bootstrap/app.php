<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Only set custom public path in production server
// Using a direct path check instead of app()->environment() to avoid bootstrapping issues
if (file_exists('/home/plaschem/public_html')) {
    $app->usePublicPath('/home/plaschem/public_html');
}

return $app;
