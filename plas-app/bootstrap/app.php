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

// Set custom paths for production server
// Using a direct path check instead of app()->environment() to avoid bootstrapping issues
if (file_exists('/home/plaschem/public_html')) {
    // Set public path for the web server
    $app->usePublicPath('/home/plaschem/public_html');
    
    // Set storage path for logs and other storage needs
    $app->useStoragePath('/home/plaschem/laravel/storage');
    
    // Set environment variables for paths
    putenv("STORAGE_PATH=/home/plaschem/laravel/storage");
    putenv("LOG_PATH=/home/plaschem/laravel/storage/logs");
}

// Handle CI/CD environment paths
// This checks if we're in the GitHub Actions environment
if (getenv('GITHUB_ACTIONS') === 'true' || file_exists('/home/runner/work/plaschema')) {
    // Create storage directory structure if it doesn't exist
    $cicd_storage_path = dirname(__DIR__) . '/storage';
    $cicd_logs_path = $cicd_storage_path . '/logs';
    
    if (!file_exists($cicd_logs_path)) {
        @mkdir($cicd_logs_path, 0755, true);
    }
    
    // Set environment variables for paths
    putenv("STORAGE_PATH={$cicd_storage_path}");
    putenv("LOG_PATH={$cicd_logs_path}");
}

return $app;
