<?php
/**
 * Laravel Cache Manager
 * 
 * This script provides manual cache management for Laravel applications
 * on shared hosting where exec() is disabled.
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

// Check PHP version
if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    die("Error: This application requires PHP 8.2.0 or higher. Your PHP version is " . PHP_VERSION);
}

// Set maximum execution time
set_time_limit(300);

// Define paths
$laravel_root = '/home/plaschem/laravel';
$bootstrap_cache = $laravel_root . '/bootstrap/cache';
$storage_path = $laravel_root . '/storage';

// Fix path issues by setting environment variables
putenv("STORAGE_PATH={$storage_path}");
putenv("LOG_PATH={$storage_path}/logs");

// Function to delete directory contents
function delete_directory_contents($dir) {
    $files = glob($dir . '/*');
    $results = [];
    
    foreach ($files as $file) {
        if (is_dir($file) && !in_array(basename($file), ['.', '..'])) {
            $results = array_merge($results, delete_directory_contents($file));
            $results[] = "Processed directory: " . $file;
        } else if (is_file($file) && !in_array(basename($file), ['.gitignore', '.gitkeep'])) {
            if (unlink($file)) {
                $results[] = "Deleted file: " . $file;
            } else {
                $results[] = "Failed to delete file: " . $file;
            }
        }
    }
    
    return $results;
}

// Function to create directory if it doesn't exist
function ensure_directory($dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        return "Created directory: $dir";
    }
    return "Directory already exists: $dir";
}

// Initialize results array
$results = [];

// Check if we have a specific action to perform
$action = $_GET['action'] ?? 'status';

// Bootstrap Laravel for certain actions
if (in_array($action, ['config', 'route', 'view'])) {
    // Bootstrap Laravel
    require_once '/home/plaschem/laravel/vendor/autoload.php';
    
    // Use the standard Laravel bootstrap process
    $app = require_once '/home/plaschem/laravel/bootstrap/app.php';
    
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
}

// Perform the requested action
switch ($action) {
    case 'clear_cache':
        $results[] = "Clearing application cache...";
        $cache_dirs = [
            $bootstrap_cache,
            $storage_path . '/framework/cache',
            $storage_path . '/framework/views',
        ];
        
        foreach ($cache_dirs as $dir) {
            $results = array_merge($results, delete_directory_contents($dir));
        }
        break;
        
    case 'clear_config':
        $results[] = "Clearing config cache...";
        $config_cache = $bootstrap_cache . '/config.php';
        if (file_exists($config_cache)) {
            if (unlink($config_cache)) {
                $results[] = "Deleted config cache file: $config_cache";
            } else {
                $results[] = "Failed to delete config cache file: $config_cache";
            }
        } else {
            $results[] = "Config cache file does not exist: $config_cache";
        }
        break;
        
    case 'clear_route':
        $results[] = "Clearing route cache...";
        $route_cache = $bootstrap_cache . '/routes-v7.php';
        if (file_exists($route_cache)) {
            if (unlink($route_cache)) {
                $results[] = "Deleted route cache file: $route_cache";
            } else {
                $results[] = "Failed to delete route cache file: $route_cache";
            }
        } else {
            $results[] = "Route cache file does not exist: $route_cache";
        }
        break;
        
    case 'clear_view':
        $results[] = "Clearing view cache...";
        $view_cache = $storage_path . '/framework/views';
        $results = array_merge($results, delete_directory_contents($view_cache));
        break;
        
    case 'config':
        $results[] = "Generating config cache...";
        try {
            $kernel->call('config:cache');
            $results[] = "Config cache generated successfully.";
        } catch (Exception $e) {
            $results[] = "Error generating config cache: " . $e->getMessage();
        }
        break;
        
    case 'route':
        $results[] = "Generating route cache...";
        try {
            $kernel->call('route:cache');
            $results[] = "Route cache generated successfully.";
        } catch (Exception $e) {
            $results[] = "Error generating route cache: " . $e->getMessage();
        }
        break;
        
    case 'view':
        $results[] = "Generating view cache...";
        try {
            $kernel->call('view:cache');
            $results[] = "View cache generated successfully.";
        } catch (Exception $e) {
            $results[] = "Error generating view cache: " . $e->getMessage();
        }
        break;
        
    case 'create_dirs':
        $results[] = "Creating necessary directories...";
        $dirs = [
            $storage_path . '/framework/cache',
            $storage_path . '/framework/sessions',
            $storage_path . '/framework/views',
            $storage_path . '/logs',
            $bootstrap_cache,
        ];
        
        foreach ($dirs as $dir) {
            $results[] = ensure_directory($dir);
        }
        break;
        
    default:
        // Just show status
        $results[] = "Checking cache status...";
        $cache_files = [
            'Config Cache' => $bootstrap_cache . '/config.php',
            'Route Cache' => $bootstrap_cache . '/routes-v7.php',
        ];
        
        foreach ($cache_files as $name => $file) {
            if (file_exists($file)) {
                $results[] = "$name: Exists (last modified: " . date('Y-m-d H:i:s', filemtime($file)) . ")";
            } else {
                $results[] = "$name: Does not exist";
            }
        }
        
        // Check view cache
        $view_cache = $storage_path . '/framework/views';
        $view_files = glob($view_cache . '/*.php');
        $results[] = "View Cache: " . count($view_files) . " files";
        
        // Check directory permissions
        $dirs_to_check = [
            $storage_path,
            $storage_path . '/framework',
            $storage_path . '/framework/cache',
            $storage_path . '/framework/sessions',
            $storage_path . '/framework/views',
            $storage_path . '/logs',
            $bootstrap_cache,
        ];
        
        $results[] = "\nDirectory Permissions:";
        foreach ($dirs_to_check as $dir) {
            $exists = file_exists($dir);
            $writable = $exists && is_writable($dir);
            $results[] = "$dir: " . ($exists ? 'Exists' : 'Missing') . ", " . ($writable ? 'Writable' : 'Not Writable');
        }
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Cache Manager</title>
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
            margin: 5px;
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
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Laravel Cache Manager</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> Delete this file immediately after use!
    </div>
    
    <div class="card">
        <h2>Cache Actions</h2>
        <div class="actions">
            <a href="?action=status" class="btn">Check Status</a>
            <a href="?action=clear_cache" class="btn">Clear All Cache</a>
            <a href="?action=clear_config" class="btn">Clear Config Cache</a>
            <a href="?action=clear_route" class="btn">Clear Route Cache</a>
            <a href="?action=clear_view" class="btn">Clear View Cache</a>
            <a href="?action=config" class="btn">Generate Config Cache</a>
            <a href="?action=route" class="btn">Generate Route Cache</a>
            <a href="?action=view" class="btn">Generate View Cache</a>
            <a href="?action=create_dirs" class="btn">Create Directories</a>
        </div>
    </div>
    
    <div class="card">
        <h2>Results</h2>
        <pre><?php echo implode("\n", $results); ?></pre>
    </div>
    
    <div class="card">
        <h2>Instructions</h2>
        <p>Use the buttons above to manage your Laravel cache. After you're done, delete this file for security.</p>
        <a href="/" class="btn">Go to Homepage</a>
    </div>
    
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