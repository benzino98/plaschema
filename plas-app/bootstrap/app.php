<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Resolve production paths BEFORE the application boots so config/logging never
// picks up CI runner paths from a pre-built config cache.
$isProduction = (isset($_SERVER['SERVER_NAME']) && str_contains($_SERVER['SERVER_NAME'], 'plaschema.pl.gov.ng'))
    || is_dir('/home/plaschem/public_html');

$isCI = ! $isProduction && (getenv('GITHUB_ACTIONS') === 'true' || is_dir('/home/runner/work/plaschema'));

if ($isProduction) {
    $storagePath = '/home/plaschem/laravel/storage';
    $logsPath = $storagePath.'/logs';
    $publicPath = '/home/plaschem/public_html';

    $_ENV['STORAGE_PATH'] = $storagePath;
    $_ENV['LOG_PATH'] = $logsPath;
    putenv('STORAGE_PATH='.$storagePath);
    putenv('LOG_PATH='.$logsPath);
} elseif ($isCI) {
    $storagePath = dirname(__DIR__).'/storage';
    $logsPath = $storagePath.'/logs';
    $publicPath = null;

    putenv('STORAGE_PATH='.$storagePath);
    putenv('LOG_PATH='.$logsPath);
} else {
    $storagePath = dirname(__DIR__).'/storage';
    $logsPath = $storagePath.'/logs';
    $publicPath = null;

    putenv('STORAGE_PATH='.$storagePath);
    putenv('LOG_PATH='.$logsPath);
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
            'permission' => CheckPermission::class,
            'localize' => SetLocale::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

if ($isProduction) {
    $app->usePublicPath($publicPath);
    $app->useStoragePath($storagePath);
} elseif ($isCI) {
    $app->useStoragePath($storagePath);
}

return $app;
