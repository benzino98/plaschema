<?php
/**
 * Standalone script to run RoleAndPermissionSeeder
 * This script skips transaction handling and provides detailed output
 */

// Security: Basic IP protection
$allowed_ips = [
    '102.91.104.42',
    '98.97.79.54',
    '102.91.102.195',
    '135.129.124.105',
    '127.0.0.1',
    $_SERVER['SERVER_ADDR'] ?? '',
];

if (!empty($allowed_ips) && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    http_response_code(404);
    die("<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1></body></html>");
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define common paths
$laravel_root = dirname($_SERVER['DOCUMENT_ROOT']) . '/laravel';
$storage_path = $laravel_root . '/storage';

// Output as HTML
header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html>
<html>
<head>
    <title>Run RoleAndPermissionSeeder</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; padding: 20px; line-height: 1.6; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <h1>RoleAndPermissionSeeder Runner</h1>";

try {
    echo "<div>Starting Laravel bootstrap...</div>";
    
    // Bootstrap Laravel
    require_once $laravel_root . '/vendor/autoload.php';
    
    // Use the standard Laravel bootstrap process
    $app = require_once $laravel_root . '/bootstrap/app.php';
    
    // Override the storage path
    $app->useStoragePath($storage_path);
    
    // Get the kernel and bootstrap
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Now we can safely use the config service
    $app['config']->set('logging.default', 'null');
    
    echo "<div class='success'>Laravel bootstrap complete.</div>";
    
    // Check database connection
    try {
        $pdo = $app['db']->connection()->getPdo();
        echo "<div class='success'>Database connection successful.</div>";
        echo "<div>Database name: " . $app['config']->get('database.connections.mysql.database') . "</div>";
        echo "<div>Database user: " . $app['config']->get('database.connections.mysql.username') . "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>Database connection failed: " . $e->getMessage() . "</div>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        throw new Exception("Cannot proceed without database connection");
    }
    
    // Check if the RoleAndPermissionSeeder class exists
    $seederClass = 'Database\\Seeders\\RoleAndPermissionSeeder';
    if (!class_exists($seederClass)) {
        echo "<div class='error'>Error: RoleAndPermissionSeeder class not found.</div>";
        echo "<div>Please make sure the seeder file exists at database/seeders/RoleAndPermissionSeeder.php</div>";
        
        // Show available seeders
        echo "<h3>Available Seeders:</h3><ul>";
        $seederDir = $laravel_root . '/database/seeders';
        if (is_dir($seederDir)) {
            $files = scandir($seederDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    echo "<li>$file</li>";
                }
            }
        } else {
            echo "<li>Seeders directory not found</li>";
        }
        echo "</ul>";
        
        throw new Exception("Seeder class not found");
    }
    
    echo "<div class='success'>Found RoleAndPermissionSeeder class.</div>";
    
    // Create an instance of the seeder
    $seeder = new $seederClass();
    echo "<div>Running seeder without transactions...</div>";
    
    // Run the seeder directly without transaction
    $seeder->run();
    
    echo "<div class='success'>Successfully ran RoleAndPermissionSeeder!</div>";
    
    // Show results
    try {
        $roleCount = $app['db']->table('roles')->count();
        $permissionCount = $app['db']->table('permissions')->count();
        $rolePermissionCount = $app['db']->table('role_permission')->count();
        
        echo "<h3>Summary:</h3>";
        echo "<div>- $roleCount roles created</div>";
        echo "<div>- $permissionCount permissions created</div>";
        echo "<div>- $rolePermissionCount role-permission associations created</div>";
        
    } catch (Exception $e) {
        echo "<div class='warning'>Could not count created items: " . $e->getMessage() . "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>Fatal error: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>"; 