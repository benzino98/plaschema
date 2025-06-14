<?php
/**
 * Laravel Deployment Initialization Script
 * 
 * This script performs necessary post-deployment tasks for a Laravel application
 * on shared hosting without SSH access.
 * 
 * IMPORTANT: Access this file once via browser after deployment, then it will self-delete.
 * URL: https://plaschema.pl.gov.ng/initialize_deployment.php
 */

// Basic security check - you can customize this with a secret token if needed
$allowed_ips = [
    // Add your IP address here for additional security
    // '123.456.789.0',
    '102.91.104.42'
];

// Comment out this section if you don't want IP restriction
if (!empty($allowed_ips) && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    if (isset($_GET['override']) && $_GET['override'] === 'true') {
        // Allow override with query parameter
        echo "<p>IP restriction overridden. Proceeding with initialization.</p>";
    } else {
        die("Access denied. Your IP ({$_SERVER['REMOTE_ADDR']}) is not allowed to access this file.<br>
            If you are the administrator, you can override this by adding ?override=true to the URL.");
    }
}

// Set maximum execution time to 5 minutes to allow for longer operations
set_time_limit(300);

// Start output buffering for cleaner output
ob_start();

// Determine the Laravel root directory
// Default for our deployment structure
$laravel_root = '/home/plaschem/laravel';

// Check if we're in a shared hosting environment with separated directories
if (!file_exists($laravel_root . '/artisan')) {
    // Try common shared hosting paths
    $possible_paths = [
        '../../laravel',
        '../laravel',
        '/home/plaschem/laravel',
        '../',
        '../../',
        '/home/plaschem',
    ];
    
    foreach ($possible_paths as $path) {
        if (file_exists($path . '/artisan')) {
            $laravel_root = $path;
            break;
        }
    }
}

// If we still can't find Laravel, show an error
if (!file_exists($laravel_root . '/artisan')) {
    echo "ERROR: Cannot find Laravel installation. Tried the following paths:<br>";
    echo "<ul>";
    echo "<li>/home/plaschem/laravel</li>";
    foreach ($possible_paths as $path) {
        echo "<li>" . htmlspecialchars($path) . "</li>";
    }
    echo "</ul>";
    echo "Current directory: " . getcwd() . "<br>";
    echo "Please check your deployment structure and try again.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Deployment Initialization</title>
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
        .success {
            border-left-color: #48bb78;
        }
        .error {
            border-left-color: #e53e3e;
        }
        .warning {
            border-left-color: #ecc94b;
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
    </style>
</head>
<body>
    <h1>Laravel Deployment Initialization</h1>
    
    <div class="card">
        <h2>Environment Check</h2>
        <?php
        // Check PHP version
        echo "<p>PHP Version: " . phpversion() . "</p>";
        
        // Check if required PHP extensions are loaded
        $required_extensions = ['pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo'];
        $missing_extensions = [];
        
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $missing_extensions[] = $ext;
            }
        }
        
        if (empty($missing_extensions)) {
            echo "<p>✅ All required PHP extensions are loaded.</p>";
        } else {
            echo "<p>⚠️ Missing PHP extensions: " . implode(', ', $missing_extensions) . "</p>";
        }
        
        // Check Laravel root directory
        echo "<p>Laravel root directory: " . realpath($laravel_root) . "</p>";
        
        // Check if .env file exists
        if (file_exists($laravel_root . '/.env')) {
            echo "<p>✅ .env file exists</p>";
        } else {
            echo "<p>❌ .env file not found! Application may not function correctly.</p>";
        }
        ?>
    </div>
    
    <div class="card">
        <h2>Storage Directory Permissions</h2>
        <?php
        // Function to check directory permissions
        function check_dir_permissions($dir) {
            $result = [];
            if (file_exists($dir)) {
                $result['exists'] = true;
                $result['writable'] = is_writable($dir);
            } else {
                $result['exists'] = false;
            }
            return $result;
        }
        
        $storage_dirs = [
            $laravel_root . '/storage',
            $laravel_root . '/storage/app',
            $laravel_root . '/storage/framework',
            $laravel_root . '/storage/framework/cache',
            $laravel_root . '/storage/framework/sessions',
            $laravel_root . '/storage/framework/views',
            $laravel_root . '/storage/logs',
            $laravel_root . '/bootstrap/cache'
        ];
        
        $permissions_issues = false;
        
        foreach ($storage_dirs as $dir) {
            $perms = check_dir_permissions($dir);
            if (!$perms['exists']) {
                echo "<p>❌ Directory does not exist: $dir</p>";
                $permissions_issues = true;
            } elseif (!$perms['writable']) {
                echo "<p>❌ Directory not writable: $dir</p>";
                $permissions_issues = true;
            }
        }
        
        if (!$permissions_issues) {
            echo "<p>✅ All storage directories exist and are writable.</p>";
        } else {
            echo "<p>⚠️ Some directories have permission issues. You may need to fix these manually.</p>";
            echo "<p>Typical fix: <code>chmod -R 775 " . $laravel_root . "/storage " . $laravel_root . "/bootstrap/cache</code></p>";
        }
        ?>
    </div>
    
    <div class="card">
        <h2>Database Migrations</h2>
        <?php
        // Check if artisan exists
        if (!file_exists($laravel_root . '/artisan')) {
            echo "<p>❌ Artisan command not found!</p>";
        } else {
            echo "<p>Running migrations...</p>";
            
            // Capture output from command
            $output = [];
            $return_var = 0;
            
            // Check if exec is available
            if (function_exists('exec')) {
                // Change to the Laravel root directory
                $current_dir = getcwd();
                chdir($laravel_root);
                exec('php artisan migrate --force 2>&1', $output, $return_var);
                chdir($current_dir);
                
                echo "<pre>" . implode("\n", $output) . "</pre>";
                
                if ($return_var !== 0) {
                    echo "<p>⚠️ Migration may have encountered issues. Check the output above.</p>";
                } else {
                    echo "<p>✅ Migrations completed successfully.</p>";
                }
            } else {
                echo "<p>❌ The 'exec' function is disabled on this server. Cannot run migrations automatically.</p>";
                echo "<p>You may need to run migrations manually via your hosting panel.</p>";
            }
        }
        ?>
    </div>
    
    <div class="card">
        <h2>Cache Management</h2>
        <?php
        if (!file_exists($laravel_root . '/artisan')) {
            echo "<p>❌ Artisan command not found!</p>";
        } else {
            echo "<p>Clearing application cache...</p>";
            
            // Capture output from command
            $output = [];
            $return_var = 0;
            
            // Check if exec is available
            if (function_exists('exec')) {
                // Change to the Laravel root directory
                $current_dir = getcwd();
                chdir($laravel_root);
                
                // Clear various caches
                $cache_commands = [
                    'php artisan cache:clear',
                    'php artisan config:clear',
                    'php artisan route:clear',
                    'php artisan view:clear'
                ];
                
                foreach ($cache_commands as $command) {
                    exec($command . ' 2>&1', $output, $return_var);
                }
                
                chdir($current_dir);
                
                echo "<pre>" . implode("\n", $output) . "</pre>";
                
                if ($return_var !== 0) {
                    echo "<p>⚠️ Cache clearing may have encountered issues.</p>";
                } else {
                    echo "<p>✅ All caches cleared successfully.</p>";
                }
                
                // Try to regenerate caches
                echo "<p>Regenerating caches for production...</p>";
                $output = [];
                chdir($laravel_root);
                
                $cache_commands = [
                    'php artisan config:cache',
                    'php artisan route:cache',
                    'php artisan view:cache'
                ];
                
                foreach ($cache_commands as $command) {
                    exec($command . ' 2>&1', $output, $return_var);
                }
                
                chdir($current_dir);
                
                echo "<pre>" . implode("\n", $output) . "</pre>";
                
                if ($return_var !== 0) {
                    echo "<p>⚠️ Cache generation may have encountered issues.</p>";
                } else {
                    echo "<p>✅ Production caches generated successfully.</p>";
                }
            } else {
                echo "<p>❌ The 'exec' function is disabled on this server. Cannot manage caches automatically.</p>";
            }
        }
        ?>
    </div>
    
    <div class="card">
        <h2>Symbolic Links</h2>
        <?php
        // Check if storage link exists
        if (is_link('storage')) {
            echo "<p>✅ Storage symbolic link already exists.</p>";
        } else {
            echo "<p>Creating storage symbolic link...</p>";
            
            if (function_exists('exec')) {
                $current_dir = getcwd();
                chdir($laravel_root);
                exec('php artisan storage:link 2>&1', $output, $return_var);
                chdir($current_dir);
                
                echo "<pre>" . implode("\n", $output) . "</pre>";
                
                if (is_link('storage')) {
                    echo "<p>✅ Storage symbolic link created successfully.</p>";
                } else {
                    echo "<p>❌ Failed to create storage symbolic link.</p>";
                    echo "<p>Your hosting provider may not support symbolic links. You may need to manually copy the files from " . $laravel_root . "/storage/app/public to public/storage.</p>";
                    
                    // Try to create a directory and copy files as fallback
                    echo "<p>Attempting to create storage directory and copy files as fallback...</p>";
                    if (!file_exists('storage')) {
                        mkdir('storage', 0755, true);
                    }
                    
                    if (is_dir($laravel_root . '/storage/app/public')) {
                        // Simple recursive copy function
                        function copy_dir($src, $dst) {
                            $dir = opendir($src);
                            @mkdir($dst);
                            while (($file = readdir($dir)) !== false) {
                                if ($file != '.' && $file != '..') {
                                    if (is_dir($src . '/' . $file)) {
                                        copy_dir($src . '/' . $file, $dst . '/' . $file);
                                    } else {
                                        copy($src . '/' . $file, $dst . '/' . $file);
                                    }
                                }
                            }
                            closedir($dir);
                        }
                        
                        copy_dir($laravel_root . '/storage/app/public', 'storage');
                        echo "<p>✅ Files copied from storage/app/public to public/storage.</p>";
                    } else {
                        echo "<p>❌ Source directory " . $laravel_root . "/storage/app/public does not exist.</p>";
                    }
                }
            } else {
                echo "<p>❌ The 'exec' function is disabled. Cannot create symbolic link automatically.</p>";
            }
        }
        ?>
    </div>
    
    <div class="card success">
        <h2>Deployment Complete</h2>
        <p>The initialization process has completed. Please review any warnings or errors above.</p>
        <p>For security reasons, this file will be deleted automatically after you navigate away.</p>
        <p>If you need to run this script again, you'll need to redeploy your application.</p>
        <a href="/" class="btn">Go to Homepage</a>
    </div>
    
    <?php
    // Schedule this file for deletion
    register_shutdown_function(function() {
        // Wait a few seconds to ensure the output is sent
        sleep(2);
        // Delete this file
        @unlink(__FILE__);
    });
    ?>
</body>
</html>
<?php
// End output buffering and send content
ob_end_flush();
?> 