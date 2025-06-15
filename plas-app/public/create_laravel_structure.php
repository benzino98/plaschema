<?php
/**
 * Laravel Directory Structure Creator
 * 
 * This script creates the necessary directory structure for Laravel on shared hosting.
 * Use this if your deployment is failing because the Laravel directory doesn't exist.
 * 
 * SECURITY WARNING: Delete this file immediately after use!
 */

// Basic security check - restrict by IP
$allowed_ips = [
    // Add your IP address here
    '102.91.104.42'
];

// Uncomment this section to enable IP restriction
// if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
//     die("Access denied. Your IP ({$_SERVER['REMOTE_ADDR']}) is not allowed to access this file.");
// }

// Set maximum execution time
set_time_limit(300);

// Start output buffering for cleaner output
ob_start();

// Define paths
$laravel_root = '/home/plaschem/laravel';
$public_html = '/home/plaschem/public_html';

// Function to create directory if it doesn't exist
function create_directory($path) {
    if (!file_exists($path)) {
        if (mkdir($path, 0755, true)) {
            return "✅ Created directory: $path";
        } else {
            return "❌ Failed to create directory: $path";
        }
    } else {
        return "ℹ️ Directory already exists: $path";
    }
}

// Function to check if we can write to a path
function check_writable($path) {
    if (is_writable($path)) {
        return "✅ Path is writable: $path";
    } else {
        return "❌ Path is not writable: $path";
    }
}

// Collect results
$results = [];

// Check if we're running in the correct environment
$results[] = "Current directory: " . getcwd();
$results[] = "Document root: " . $_SERVER['DOCUMENT_ROOT'];

// Check if we can access the home directory
$home_dir = dirname($_SERVER['DOCUMENT_ROOT']);
$results[] = "Home directory: " . $home_dir;
$results[] = check_writable($home_dir);

// Create Laravel root directory
$results[] = create_directory($laravel_root);
$results[] = check_writable($laravel_root);

// Create Laravel subdirectories
$laravel_dirs = [
    '/app',
    '/bootstrap',
    '/bootstrap/cache',
    '/config',
    '/database',
    '/public',
    '/resources',
    '/routes',
    '/storage',
    '/storage/app',
    '/storage/app/public',
    '/storage/framework',
    '/storage/framework/cache',
    '/storage/framework/sessions',
    '/storage/framework/views',
    '/storage/logs',
];

foreach ($laravel_dirs as $dir) {
    $results[] = create_directory($laravel_root . $dir);
}

// Set proper permissions
$chmod_dirs = [
    $laravel_root . '/storage',
    $laravel_root . '/bootstrap/cache',
];

foreach ($chmod_dirs as $dir) {
    if (file_exists($dir)) {
        if (@chmod($dir, 0775)) {
            $results[] = "✅ Set permissions (0775) on: $dir";
        } else {
            $results[] = "❌ Failed to set permissions on: $dir";
        }
    }
}

// Create a placeholder .env file
$env_file = $laravel_root . '/.env';
if (!file_exists($env_file)) {
    $env_content = "APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=https://plaschema.pl.gov.ng\n\nLOG_CHANNEL=stack\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=laravel\nDB_USERNAME=root\nDB_PASSWORD=\n\nCACHE_DRIVER=file\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\n";
    
    if (file_put_contents($env_file, $env_content)) {
        $results[] = "✅ Created placeholder .env file";
    } else {
        $results[] = "❌ Failed to create .env file";
    }
}

// Create a placeholder artisan file
$artisan_file = $laravel_root . '/artisan';
if (!file_exists($artisan_file)) {
    $artisan_content = "#!/usr/bin/env php\n<?php\n\necho \"This is a placeholder artisan file created by the setup script.\";\necho \"Replace this with the actual artisan file from your Laravel application.\";\n";
    
    if (file_put_contents($artisan_file, $artisan_content)) {
        chmod($artisan_file, 0755);
        $results[] = "✅ Created placeholder artisan file";
    } else {
        $results[] = "❌ Failed to create artisan file";
    }
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Directory Structure Creator</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
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
    <h1>Laravel Directory Structure Creator</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> Delete this file immediately after use!
    </div>
    
    <div class="card">
        <h2>Directory Structure Creation Results</h2>
        <pre><?php echo implode("\n", $results); ?></pre>
    </div>
    
    <div class="card">
        <h2>Next Steps</h2>
        <p>The directory structure has been created. Now you should:</p>
        <ol>
            <li>Delete this file immediately for security</li>
            <li>Run your GitHub Actions workflow to deploy your Laravel application</li>
            <li>After deployment, visit the initialization script to complete the setup</li>
        </ol>
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