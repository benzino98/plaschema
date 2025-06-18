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

// Define common paths based on environment detection
$isProduction = false;
$isCI = false;

// Check for production environment
if (isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'plaschema.pl.gov.ng') !== false) {
    $isProduction = true;
}

// Alternative check for production - check for specific directory
if (!$isProduction && is_dir('/home/plaschem/public_html')) {
    $isProduction = true;
}

// Check for CI/CD environment
if (getenv('GITHUB_ACTIONS') === 'true' || is_dir('/home/runner/work/plaschema')) {
    $isCI = true;
}

// Set paths based on environment
if ($isProduction) {
    // Production environment
    $publicPath = '/home/plaschem/public_html';
    $storagePath = '/home/plaschem/laravel/storage';
    $logsPath = $storagePath . '/logs';
    
    // Set public path for the web server
    $app->usePublicPath($publicPath);
    
    // Set storage path for logs and other storage needs
    $app->useStoragePath($storagePath);
    
    // Set environment variables for paths
    putenv("STORAGE_PATH=" . $storagePath);
    putenv("LOG_PATH=" . $logsPath);
} elseif ($isCI) {
    // CI/CD environment
    $storagePath = dirname(__DIR__) . '/storage';
    $logsPath = $storagePath . '/logs';
    
    // Set storage path
    $app->useStoragePath($storagePath);
    
    // Set environment variables for paths
    putenv("STORAGE_PATH=" . $storagePath);
    putenv("LOG_PATH=" . $logsPath);
}

return $app;
