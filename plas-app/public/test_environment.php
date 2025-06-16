<?php
/**
 * Laravel Environment Tester
 * 
 * This script tests if the Laravel environment is working correctly.
 * 
 * SECURITY WARNING: Delete this file immediately after use!
 */

// Basic security check - restrict by IP
$allowed_ips = [
    // Add your IP address here
    '102.91.104.42'
];

// Uncomment this section to enable IP restriction
// if (!empty($allowed_ips) && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
//     die("Access denied. Your IP ({$_SERVER['REMOTE_ADDR']}) is not allowed to access this file.");
// }

// Set maximum execution time
set_time_limit(300);

// Define paths
$laravel_root = '/home/plaschem/laravel';
$storage_path = $laravel_root . '/storage';

// Fix path issues by setting environment variables
putenv("STORAGE_PATH={$storage_path}");
putenv("LOG_PATH={$storage_path}/logs");

// Initialize results array
$results = [];

// Test PHP version
$results[] = "PHP Version: " . PHP_VERSION;
$php_version_ok = version_compare(PHP_VERSION, '8.2.0', '>=');
$results[] = "PHP Version Check: " . ($php_version_ok ? "✅ OK" : "❌ Needs PHP 8.2.0 or higher");

// Check if Laravel files exist
$results[] = "\nChecking Laravel Files:";
$files_to_check = [
    'Autoloader' => $laravel_root . '/vendor/autoload.php',
    'Bootstrap App' => $laravel_root . '/bootstrap/app.php',
    'Environment File' => $laravel_root . '/.env',
];

foreach ($files_to_check as $name => $file) {
    $exists = file_exists($file);
    $results[] = "$name: " . ($exists ? "✅ Exists" : "❌ Missing");
}

// Check directory permissions
$results[] = "\nChecking Directory Permissions:";
$dirs_to_check = [
    'Storage' => $storage_path,
    'Storage/Framework' => $storage_path . '/framework',
    'Storage/Framework/Cache' => $storage_path . '/framework/cache',
    'Storage/Framework/Sessions' => $storage_path . '/framework/sessions',
    'Storage/Framework/Views' => $storage_path . '/framework/views',
    'Storage/Logs' => $storage_path . '/logs',
    'Bootstrap/Cache' => $laravel_root . '/bootstrap/cache',
];

foreach ($dirs_to_check as $name => $dir) {
    $exists = file_exists($dir);
    $writable = $exists && is_writable($dir);
    $results[] = "$name: " . ($exists ? "✅ Exists" : "❌ Missing") . ", " . ($writable ? "✅ Writable" : "❌ Not Writable");
    
    // Create directory if it doesn't exist
    if (!$exists) {
        if (mkdir($dir, 0755, true)) {
            $results[] = "  - Created directory: $dir";
        } else {
            $results[] = "  - Failed to create directory: $dir";
        }
    }
}

// Try to bootstrap Laravel
$results[] = "\nBootstrapping Laravel:";
try {
    require_once $laravel_root . '/vendor/autoload.php';
    $app = require_once $laravel_root . '/bootstrap/app.php';
    
    // Override the storage path
    $app->useStoragePath($storage_path);
    
    // Disable logging to prevent path issues
    $app->make('config')->set('logging.channels.single.path', '/dev/null');
    $app->make('config')->set('logging.default', 'null');
    $app->make('config')->set('logging.channels.null', [
        'driver' => 'monolog',
        'handler' => Monolog\Handler\NullHandler::class,
    ]);
    
    // Get the kernel and bootstrap
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $results[] = "✅ Laravel bootstrapped successfully";
    
    // Check environment
    $results[] = "\nEnvironment: " . app()->environment();
    
    // Check database connection
    try {
        DB::connection()->getPdo();
        $results[] = "Database Connection: ✅ Connected successfully to " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        $results[] = "Database Connection: ❌ Failed - " . $e->getMessage();
    }
    
    // Check cache configuration
    $results[] = "Cache Driver: " . config('cache.default');
    $results[] = "Session Driver: " . config('session.driver');
    $results[] = "Queue Connection: " . config('queue.default');
    
    // Check storage configuration
    $results[] = "\nStorage Configuration:";
    $results[] = "Storage Path: " . storage_path();
    $results[] = "Public Path: " . public_path();
    
} catch (\Exception $e) {
    $results[] = "❌ Laravel bootstrap failed: " . $e->getMessage();
    $results[] = "Stack trace: " . $e->getTraceAsString();
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Environment Tester</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
            border-left: 4px solid #3182ce;
        }
        pre {
            background: #f7fafc;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 14px;
            white-space: pre-wrap;
        }
        .btn {
            display: inline-block;
            background: #4299e1;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 20px;
        }
        .btn:hover {
            background: #3182ce;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Laravel Environment Tester</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> Delete this file immediately after use!
    </div>
    
    <div class="card">
        <h2>Environment Test Results</h2>
        <pre><?php echo implode("\n", $results); ?></pre>
    </div>
    
    <div class="card">
        <h2>PHP Information</h2>
        <p>Here's a summary of your PHP configuration:</p>
        <pre><?php
            ob_start();
            phpinfo(INFO_MODULES);
            $phpinfo = ob_get_clean();
            
            // Extract just the basic information
            if (preg_match('/<body>(.*?)<\/body>/s', $phpinfo, $matches)) {
                $phpinfo = $matches[1];
                $phpinfo = preg_replace('/<table[^>]*>/', '<table>', $phpinfo);
                $phpinfo = preg_replace('/<tr[^>]*>/', '<tr>', $phpinfo);
                $phpinfo = preg_replace('/<td[^>]*>/', '<td>', $phpinfo);
                $phpinfo = preg_replace('/<h2[^>]*>/', '<h3>', $phpinfo);
                $phpinfo = preg_replace('/<\/h2>/', '</h3>', $phpinfo);
                
                // Extract just the core info
                if (preg_match('/<h3>Core<\/h3>(.*?)<h3>/s', $phpinfo, $matches)) {
                    echo strip_tags($matches[1], '<table><tr><td><th><br>');
                } else {
                    echo "PHP version: " . PHP_VERSION;
                }
            } else {
                echo "PHP version: " . PHP_VERSION;
            }
        ?></pre>
    </div>
    
    <a href="/" class="btn">Go to Homepage</a>
    
    <?php
    // Self-delete option
    if (isset($_GET['delete']) && $_GET['delete'] === 'true') {
        @unlink(__FILE__);
        echo "<script>alert('This file has been deleted for security purposes.'); window.location='/';</script>";
    }
    ?>
    
    <p><a href="?delete=true" onclick="return confirm('Are you sure you want to delete this file?')">Delete this file</a></p>
</body>
</html> 