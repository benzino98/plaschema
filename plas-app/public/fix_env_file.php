<?php
/**
 * Laravel .env File Creator
 * 
 * This script creates the .env file for Laravel on shared hosting if it's missing.
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
$home_dir = dirname($_SERVER['DOCUMENT_ROOT']);
$laravel_root = $home_dir; // Laravel files are in the root directory
$env_file = $laravel_root . '/.env';

// Collect results
$results = [];
$results[] = "Server home directory: " . $home_dir;
$results[] = "Laravel directory path (root): " . $laravel_root;

// Check if we're running in the correct environment
$results[] = "Current directory: " . getcwd();
$results[] = "Document root: " . $_SERVER['DOCUMENT_ROOT'];
$results[] = "Looking for Laravel at: " . $laravel_root;

// Check if Laravel directory exists
if (!file_exists($laravel_root)) {
    $results[] = "❌ Laravel directory not found at: " . $laravel_root;
    $results[] = "Please run create_laravel_structure.php first to create the directory structure.";
} else {
    $results[] = "✅ Laravel directory exists at: " . $laravel_root;
    
    // Check if .env file exists
    if (file_exists($env_file)) {
        $results[] = "✅ .env file already exists at: " . $env_file;
        
        // Check if we can read it
        if (is_readable($env_file)) {
            $results[] = "✅ .env file is readable";
            
            // Read the first few lines to check if it's valid
            $env_content = file_get_contents($env_file);
            $has_app_key = strpos($env_content, 'APP_KEY=') !== false;
            
            if ($has_app_key) {
                $results[] = "✅ .env file appears to be valid (contains APP_KEY)";
            } else {
                $results[] = "⚠️ .env file might be incomplete (no APP_KEY found)";
                $results[] = "Consider creating a new .env file.";
            }
        } else {
            $results[] = "❌ .env file exists but is not readable";
            $results[] = "Try fixing permissions: chmod 644 " . $env_file;
        }
    } else {
        $results[] = "❌ .env file not found at: " . $env_file;
        $results[] = "Creating new .env file...";
        
        // Create a basic .env file
        $env_content = <<<EOT
APP_NAME=Plaschema
APP_ENV=production
APP_KEY=base64:
APP_DEBUG=false
APP_URL=https://plaschema.pl.gov.ng

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plaschem_db
DB_USERNAME=plaschem_user
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
EOT;

        // Try to generate a key
        $key = '';
        if (function_exists('random_bytes')) {
            try {
                $key = 'base64:' . base64_encode(random_bytes(32));
                $results[] = "✅ Generated new APP_KEY";
            } catch (Exception $e) {
                $results[] = "⚠️ Could not generate secure APP_KEY: " . $e->getMessage();
            }
        } else {
            $results[] = "⚠️ random_bytes function not available, cannot generate secure APP_KEY";
        }
        
        // Replace the APP_KEY placeholder if we generated a key
        if (!empty($key)) {
            $env_content = str_replace('APP_KEY=base64:', 'APP_KEY=' . $key, $env_content);
        }
        
        // Try to write the .env file
        if (@file_put_contents($env_file, $env_content)) {
            @chmod($env_file, 0644);
            $results[] = "✅ Successfully created .env file at: " . $env_file;
        } else {
            $results[] = "❌ Failed to create .env file. Check directory permissions.";
        }
    }
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel .env File Creator</title>
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
    <h1>Laravel .env File Creator</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> Delete this file immediately after use!
    </div>
    
    <div class="card">
        <h2>Results</h2>
        <pre><?php echo implode("\n", $results); ?></pre>
    </div>
    
    <div class="card">
        <h2>Next Steps</h2>
        <p>After creating or verifying the .env file:</p>
        <ol>
            <li>Delete this file immediately for security</li>
            <li>Visit the initialization script to complete the setup: <a href="initialize_deployment.php">initialize_deployment.php</a></li>
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