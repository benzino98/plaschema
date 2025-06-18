<?php
/**
 * Log Path Fixer Script
 * 
 * This script fixes log path issues by creating necessary directories and setting permissions.
 * It does not rely on exec() function for better compatibility with shared hosting.
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

// Define paths
$home_dir = dirname($_SERVER['DOCUMENT_ROOT']);
$laravel_root = $home_dir . '/laravel';
$storage_path = $laravel_root . '/storage';
$logs_path = $storage_path . '/logs';
$laravel_log = $logs_path . '/laravel.log';

// Initialize results
$results = [];
$errors = [];

// Function to create directory with proper permissions
function create_directory($path, &$results, &$errors) {
    if (file_exists($path)) {
        $results[] = "Directory already exists: {$path}";
        
        // Check permissions
        if (!is_writable($path)) {
            if (@chmod($path, 0755)) {
                $results[] = "Fixed permissions on directory: {$path}";
            } else {
                $error = error_get_last();
                $errors[] = "Failed to fix permissions on directory: {$path} - " . ($error['message'] ?? 'Permission denied');
            }
        }
        
        return true;
    } else {
        if (@mkdir($path, 0755, true)) {
            $results[] = "Created directory: {$path}";
            return true;
        } else {
            $error = error_get_last();
            $errors[] = "Failed to create directory: {$path} - " . ($error['message'] ?? 'Permission denied');
            return false;
        }
    }
}

// Function to update .env file
function update_env_file($env_file, $key, $value, &$results, &$errors) {
    if (file_exists($env_file)) {
        $env_content = @file_get_contents($env_file);
        
        if ($env_content !== false) {
            // Check if key already exists
            if (preg_match("/^{$key}=/m", $env_content)) {
                // Update existing key
                $env_content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env_content);
                $results[] = "Updated {$key} in .env file";
            } else {
                // Add new key
                $env_content .= PHP_EOL . "{$key}={$value}" . PHP_EOL;
                $results[] = "Added {$key} to .env file";
            }
            
            if (@file_put_contents($env_file, $env_content)) {
                $results[] = "Successfully wrote changes to .env file";
                return true;
            } else {
                $error = error_get_last();
                $errors[] = "Failed to write to .env file: " . ($error['message'] ?? 'Permission denied');
                return false;
            }
        } else {
            $error = error_get_last();
            $errors[] = "Failed to read .env file: " . ($error['message'] ?? 'Permission denied');
            return false;
        }
    } else {
        $errors[] = ".env file does not exist: {$env_file}";
        return false;
    }
}

// Function to create .htaccess file
function create_htaccess($path, &$results, &$errors) {
    $htaccess_content = "# Deny access to all files
<FilesMatch \".*\">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Disable directory browsing
Options -Indexes";

    $htaccess_file = $path . '/.htaccess';
    
    if (@file_put_contents($htaccess_file, $htaccess_content)) {
        $results[] = "Created .htaccess file in {$path}";
        return true;
    } else {
        $error = error_get_last();
        $errors[] = "Failed to create .htaccess file in {$path}: " . ($error['message'] ?? 'Permission denied');
        return false;
    }
}

// Start fixing logs
$results[] = "Starting log path fix process...";

// Step 1: Create Laravel root directory if it doesn't exist
create_directory($laravel_root, $results, $errors);

// Step 2: Create storage directory
if (create_directory($storage_path, $results, $errors)) {
    create_htaccess($storage_path, $results, $errors);
}

// Step 3: Create logs directory
create_directory($logs_path, $results, $errors);

// Step 4: Create or check laravel.log file
if (file_exists($laravel_log)) {
    $results[] = "Laravel log file exists: {$laravel_log}";
    
    // Fix permissions
    if (!is_writable($laravel_log)) {
        if (@chmod($laravel_log, 0666)) {
            $results[] = "Fixed permissions on log file: {$laravel_log}";
        } else {
            $error = error_get_last();
            $errors[] = "Failed to fix permissions on log file: " . ($error['message'] ?? 'Permission denied');
        }
    }
} else {
    if (@file_put_contents($laravel_log, "Log file created by fix_logs.php at " . date('Y-m-d H:i:s') . PHP_EOL)) {
        $results[] = "Created log file: {$laravel_log}";
        
        // Set permissions
        if (@chmod($laravel_log, 0666)) {
            $results[] = "Set permissions on new log file";
        } else {
            $error = error_get_last();
            $errors[] = "Failed to set permissions on new log file: " . ($error['message'] ?? 'Permission denied');
        }
    } else {
        $error = error_get_last();
        $errors[] = "Failed to create log file: " . ($error['message'] ?? 'Permission denied');
    }
}

// Step 5: Update .env file with LOG_PATH
$env_file = $laravel_root . '/.env';
update_env_file($env_file, 'LOG_PATH', $logs_path, $results, $errors);
update_env_file($env_file, 'STORAGE_PATH', $storage_path, $results, $errors);

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
        .errors {
            background-color: #fff5f5;
            border-left: 4px solid #e53e3e;
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
        .summary {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #c6f6d5;
            color: #2f855a;
        }
        .failure {
            background-color: #fed7d7;
            color: #c53030;
        }
        .next-steps {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <h1>Log Path Fixer</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> This script fixes your Laravel application's log configuration.
        For security reasons, delete this file immediately after use.
    </div>
    
    <div class="card">
        <h2>Path Information</h2>
        <div class="path-info">
            <p><strong>Home Directory:</strong> <?php echo htmlspecialchars($home_dir); ?></p>
            <p><strong>Laravel Root:</strong> <?php echo htmlspecialchars($laravel_root); ?></p>
            <p><strong>Storage Path:</strong> <?php echo htmlspecialchars($storage_path); ?></p>
            <p><strong>Logs Path:</strong> <?php echo htmlspecialchars($logs_path); ?></p>
            <p><strong>Laravel Log File:</strong> <?php echo htmlspecialchars($laravel_log); ?></p>
        </div>
    </div>
    
    <?php if (!empty($results)): ?>
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
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
    <div class="card">
        <h2>Errors</h2>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="summary <?php echo empty($errors) ? 'success' : 'failure'; ?>">
        <?php if (empty($errors)): ?>
            Log paths have been successfully fixed!
        <?php else: ?>
            There were some issues while fixing log paths. Please check the errors above.
        <?php endif; ?>
    </div>
    
    <div class="card next-steps">
        <h2>Next Steps</h2>
        <ol>
            <li>Delete this file for security</li>
            <li>Try accessing your Laravel application to see if the log errors are resolved</li>
            <li>If you're still having issues, check the <a href="check_logs.php">log path checker</a> for more details</li>
            <li>For persistent issues, check your web server error logs</li>
        </ol>
    </div>
    
    <p style="text-align: center; margin-top: 30px; color: #718096; font-size: 0.9em;">
        Remember to delete this file after fixing your log paths.
    </p>
</body>
</html> 