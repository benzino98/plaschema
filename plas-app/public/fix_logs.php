<?php
/**
 * Log Path Fixer Script for Server
 * 
 * This script fixes the log path issues by creating necessary directories
 * and setting the correct permissions on the server.
 * 
 * SECURITY NOTICE: Delete this file after use!
 */

// Basic security - restrict by IP
$allowed_ips = [
    '102.91.104.42',
    '102.91.103.139',
    '135.129.124.105',
    '127.0.0.1',
    $_SERVER['SERVER_ADDR'] ?? '',
];

if (!empty($allowed_ips) && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    http_response_code(404);
    die("<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>");
}

// Set execution time limit
set_time_limit(300);

// Define paths
$home_dir = dirname($_SERVER['DOCUMENT_ROOT']);
$laravel_root = $home_dir . '/laravel';
$storage_path = $laravel_root . '/storage';
$logs_path = $storage_path . '/logs';
$ci_cd_logs_path = '/home/runner/work/plaschema/plaschema/plas-app/storage/logs';

// Initialize results
$results = [];

// Function to create directory
function create_directory($path, &$results) {
    if (!file_exists($path)) {
        if (mkdir($path, 0755, true)) {
            $results[] = "✅ Created directory: {$path}";
        } else {
            $results[] = "❌ Failed to create directory: {$path}";
        }
    } else {
        $results[] = "ℹ️ Directory already exists: {$path}";
        
        // Make sure it's writable
        if (is_writable($path)) {
            $results[] = "✅ Directory is writable: {$path}";
        } else {
            if (chmod($path, 0755)) {
                $results[] = "✅ Made directory writable: {$path}";
            } else {
                $results[] = "❌ Failed to make directory writable: {$path}";
            }
        }
    }
}

// Function to create .htaccess file
function create_htaccess($path, &$results) {
    $htaccess_content = <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^ - [F,L]
</IfModule>
<IfModule !mod_rewrite.c>
    Order Allow,Deny
    Deny from all
</IfModule>
EOT;

    $htaccess_file = $path . '/.htaccess';
    if (!file_exists($htaccess_file)) {
        if (file_put_contents($htaccess_file, $htaccess_content)) {
            $results[] = "✅ Created .htaccess file in: {$path}";
        } else {
            $results[] = "❌ Failed to create .htaccess file in: {$path}";
        }
    } else {
        $results[] = "ℹ️ .htaccess file already exists in: {$path}";
    }
}

// Function to create .gitignore file
function create_gitignore($path, &$results) {
    $gitignore_content = <<<EOT
*
!.gitignore
!.htaccess
EOT;

    $gitignore_file = $path . '/.gitignore';
    if (!file_exists($gitignore_file)) {
        if (file_put_contents($gitignore_file, $gitignore_content)) {
            $results[] = "✅ Created .gitignore file in: {$path}";
        } else {
            $results[] = "❌ Failed to create .gitignore file in: {$path}";
        }
    } else {
        $results[] = "ℹ️ .gitignore file already exists in: {$path}";
    }
}

// Create logs directory
create_directory($logs_path, $results);

// Create .htaccess file
create_htaccess($logs_path, $results);

// Create .gitignore file
create_gitignore($logs_path, $results);

// Create an empty laravel.log file if it doesn't exist
$laravel_log = $logs_path . '/laravel.log';
if (!file_exists($laravel_log)) {
    if (file_put_contents($laravel_log, '')) {
        $results[] = "✅ Created empty laravel.log file";
    } else {
        $results[] = "❌ Failed to create laravel.log file";
    }
} else {
    $results[] = "ℹ️ laravel.log file already exists";
}

// Make sure the log file is writable
if (file_exists($laravel_log)) {
    if (is_writable($laravel_log)) {
        $results[] = "✅ laravel.log is writable";
    } else {
        if (chmod($laravel_log, 0666)) {
            $results[] = "✅ Made laravel.log writable";
        } else {
            $results[] = "❌ Failed to make laravel.log writable";
        }
    }
}

// Fix CI/CD path issue by creating a symlink or directory
if (!file_exists(dirname($ci_cd_logs_path))) {
    if (@mkdir(dirname($ci_cd_logs_path), 0755, true)) {
        $results[] = "✅ Created CI/CD storage directory structure";
    } else {
        $results[] = "❌ Failed to create CI/CD storage directory structure";
    }
}

if (!file_exists($ci_cd_logs_path)) {
    // Try to create a symlink first
    if (function_exists('symlink') && !in_array('symlink', explode(',', ini_get('disable_functions')))) {
        if (@symlink($logs_path, $ci_cd_logs_path)) {
            $results[] = "✅ Created symlink from CI/CD logs path to actual logs path";
        } else {
            // If symlink fails, create the directory
            if (@mkdir($ci_cd_logs_path, 0755, true)) {
                $results[] = "✅ Created CI/CD logs directory";
            } else {
                $results[] = "❌ Failed to create CI/CD logs directory";
            }
        }
    } else {
        // If symlink function is not available, create the directory
        if (@mkdir($ci_cd_logs_path, 0755, true)) {
            $results[] = "✅ Created CI/CD logs directory";
        } else {
            $results[] = "❌ Failed to create CI/CD logs directory";
        }
    }
} else {
    $results[] = "ℹ️ CI/CD logs path already exists";
}

// Update .env file with LOG_PATH if it exists
$env_file = $laravel_root . '/.env';
if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    
    // Check if LOG_PATH already exists in .env
    if (strpos($env_content, 'LOG_PATH=') === false) {
        // Add LOG_PATH after LOG_CHANNEL
        $env_content = preg_replace(
            '/(LOG_CHANNEL=.*?)(\r?\n)/i',
            "$1$2LOG_PATH={$logs_path}$2",
            $env_content
        );
        
        if (file_put_contents($env_file, $env_content)) {
            $results[] = "✅ Added LOG_PATH to .env file";
        } else {
            $results[] = "❌ Failed to update .env file";
        }
    } else {
        $results[] = "ℹ️ LOG_PATH already exists in .env file";
    }
} else {
    $results[] = "⚠️ .env file does not exist";
}

// Update bootstrap/app.php to set storage path
$bootstrap_file = $laravel_root . '/bootstrap/app.php';
if (file_exists($bootstrap_file)) {
    $bootstrap_content = file_get_contents($bootstrap_file);
    
    // Check if we need to add storage path setting
    if (strpos($bootstrap_content, 'useStoragePath') === false) {
        // Look for the usePublicPath line
        if (strpos($bootstrap_content, 'usePublicPath') !== false) {
            // Add storage path setting after public path
            $bootstrap_content = preg_replace(
                '/(usePublicPath.*?);/i',
                "$1;\n    \n    // Set storage path for logs and other storage needs\n    \$app->useStoragePath('{$storage_path}');\n    \n    // Set environment variables for paths\n    putenv(\"STORAGE_PATH={$storage_path}\");\n    putenv(\"LOG_PATH={$logs_path}\");",
                $bootstrap_content
            );
            
            if (file_put_contents($bootstrap_file, $bootstrap_content)) {
                $results[] = "✅ Updated bootstrap/app.php to set storage path";
            } else {
                $results[] = "❌ Failed to update bootstrap/app.php";
            }
        } else {
            $results[] = "⚠️ Could not find usePublicPath in bootstrap/app.php";
        }
    } else {
        $results[] = "ℹ️ Storage path setting already exists in bootstrap/app.php";
    }
} else {
    $results[] = "⚠️ bootstrap/app.php file does not exist";
}

// Clear Laravel cache
if (file_exists($laravel_root . '/artisan')) {
    $output = [];
    $return_var = 0;
    
    exec("cd {$laravel_root} && php artisan cache:clear 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        $results[] = "✅ Cleared Laravel cache";
    } else {
        $results[] = "❌ Failed to clear Laravel cache: " . implode("\n", $output);
    }
}

// Output results
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Path Fixer</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7fafc;
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
        }
        .path-info {
            background-color: #ebf8ff;
            border-left: 4px solid #4299e1;
            padding: 12px;
            margin-bottom: 20px;
        }
        .results {
            background-color: #f0fff4;
            border-left: 4px solid #48bb78;
            padding: 12px;
            margin-bottom: 20px;
        }
        .warning {
            background-color: #fefcbf;
            border-left: 4px solid #d69e2e;
            padding: 12px;
            margin-bottom: 20px;
        }
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        li {
            padding: 8px 0;
        }
    </style>
</head>
<body>
    <h1>Log Path Fixer</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> This script makes changes to your Laravel application's directory structure and configuration.
        For security reasons, delete this file immediately after use.
    </div>
    
    <div class="card">
        <h2>Path Information</h2>
        <div class="path-info">
            <p><strong>Home Directory:</strong> <?php echo htmlspecialchars($home_dir); ?></p>
            <p><strong>Laravel Root:</strong> <?php echo htmlspecialchars($laravel_root); ?></p>
            <p><strong>Storage Path:</strong> <?php echo htmlspecialchars($storage_path); ?></p>
            <p><strong>Logs Path:</strong> <?php echo htmlspecialchars($logs_path); ?></p>
            <p><strong>CI/CD Logs Path:</strong> <?php echo htmlspecialchars($ci_cd_logs_path); ?></p>
        </div>
    </div>
    
    <div class="card">
        <h2>Results</h2>
        <div class="results">
            <ul>
                <?php foreach ($results as $result): ?>
                    <li><?php echo htmlspecialchars($result); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <div class="card">
        <h2>Next Steps</h2>
        <p>If you've made changes to your application code, make sure to:</p>
        <ol>
            <li>Run <code>composer dump-autoload</code> to ensure any new helper functions are registered</li>
            <li>Clear the Laravel cache with <code>php artisan cache:clear</code></li>
            <li>Restart your web server if possible</li>
        </ol>
    </div>
    
    <p style="text-align: center; margin-top: 30px; color: #718096; font-size: 0.9em;">
        Remember to delete this file after fixing your log paths.
    </p>
</body>
</html> 