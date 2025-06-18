<?php
/**
 * Log Path Checker Script
 * 
 * This script checks if the log path issues have been fixed by attempting to write to the log file.
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

// Function to check if a directory exists and is writable
function check_directory($path, &$results, &$errors) {
    if (file_exists($path)) {
        $results[] = "✅ Directory exists: {$path}";
        
        if (is_writable($path)) {
            $results[] = "✅ Directory is writable: {$path}";
            return true;
        } else {
            $errors[] = "❌ Directory is not writable: {$path}";
            return false;
        }
    } else {
        $errors[] = "❌ Directory does not exist: {$path}";
        return false;
    }
}

// Check if we can write to the Laravel directory
if (!is_writable($laravel_root)) {
    $errors[] = "❌ Laravel directory is not writable: {$laravel_root}";
} else {
    $results[] = "✅ Laravel directory is writable: {$laravel_root}";
}

// Check storage directory
check_directory($storage_path, $results, $errors);

// Check logs directory
$logs_writable = check_directory($logs_path, $results, $errors);

// Check laravel.log file
if ($logs_writable) {
    if (file_exists($laravel_log)) {
        $results[] = "✅ Laravel log file exists: {$laravel_log}";
        
        if (is_writable($laravel_log)) {
            $results[] = "✅ Laravel log file is writable";
            
            // Try to write to the log file
            $test_message = "Test log entry from check_logs.php at " . date('Y-m-d H:i:s');
            if (@file_put_contents($laravel_log, $test_message . PHP_EOL, FILE_APPEND)) {
                $results[] = "✅ Successfully wrote to log file";
            } else {
                $error = error_get_last();
                $errors[] = "❌ Failed to write to log file: " . ($error['message'] ?? 'Unknown error');
            }
        } else {
            $errors[] = "❌ Laravel log file is not writable";
            
            // Try to fix permissions
            if (@chmod($laravel_log, 0666)) {
                $results[] = "✅ Fixed permissions on log file";
                
                // Try to write to the log file again
                $test_message = "Test log entry from check_logs.php at " . date('Y-m-d H:i:s');
                if (@file_put_contents($laravel_log, $test_message . PHP_EOL, FILE_APPEND)) {
                    $results[] = "✅ Successfully wrote to log file after fixing permissions";
                } else {
                    $error = error_get_last();
                    $errors[] = "❌ Failed to write to log file even after fixing permissions: " . ($error['message'] ?? 'Unknown error');
                }
            } else {
                $error = error_get_last();
                $errors[] = "❌ Failed to fix permissions on log file: " . ($error['message'] ?? 'Permission denied');
            }
        }
    } else {
        $errors[] = "❌ Laravel log file does not exist: {$laravel_log}";
        
        // Try to create the log file
        if (@file_put_contents($laravel_log, "Log file created by check_logs.php at " . date('Y-m-d H:i:s') . PHP_EOL)) {
            $results[] = "✅ Created log file";
            
            // Set permissions
            if (@chmod($laravel_log, 0666)) {
                $results[] = "✅ Set permissions on new log file";
            } else {
                $error = error_get_last();
                $errors[] = "❌ Failed to set permissions on new log file: " . ($error['message'] ?? 'Permission denied');
            }
        } else {
            $error = error_get_last();
            $errors[] = "❌ Failed to create log file: " . ($error['message'] ?? 'Permission denied');
        }
    }
}

// Check environment variables
$env_storage_path = getenv('STORAGE_PATH');
$env_log_path = getenv('LOG_PATH');

if ($env_storage_path) {
    $results[] = "✅ STORAGE_PATH environment variable is set: {$env_storage_path}";
} else {
    $errors[] = "❌ STORAGE_PATH environment variable is not set";
}

if ($env_log_path) {
    $results[] = "✅ LOG_PATH environment variable is set: {$env_log_path}";
} else {
    $errors[] = "❌ LOG_PATH environment variable is not set";
}

// Check .env file
$env_file = $laravel_root . '/.env';
if (file_exists($env_file)) {
    $env_content = @file_get_contents($env_file);
    
    if ($env_content !== false) {
        if (strpos($env_content, 'LOG_PATH=') !== false) {
            $results[] = "✅ LOG_PATH is defined in .env file";
        } else {
            $errors[] = "❌ LOG_PATH is not defined in .env file";
        }
    } else {
        $errors[] = "❌ Could not read .env file";
    }
} else {
    $errors[] = "❌ .env file does not exist";
}

// Check bootstrap/app.php
$bootstrap_file = $laravel_root . '/bootstrap/app.php';
if (file_exists($bootstrap_file)) {
    $bootstrap_content = @file_get_contents($bootstrap_file);
    
    if ($bootstrap_content !== false) {
        if (strpos($bootstrap_content, 'useStoragePath') !== false) {
            $results[] = "✅ useStoragePath is defined in bootstrap/app.php";
        } else {
            $errors[] = "❌ useStoragePath is not defined in bootstrap/app.php";
        }
        
        if (strpos($bootstrap_content, 'putenv("LOG_PATH=') !== false) {
            $results[] = "✅ LOG_PATH environment variable is set in bootstrap/app.php";
        } else {
            $errors[] = "❌ LOG_PATH environment variable is not set in bootstrap/app.php";
        }
    } else {
        $errors[] = "❌ Could not read bootstrap/app.php";
    }
} else {
    $errors[] = "❌ bootstrap/app.php does not exist";
}

// Output results
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Path Checker</title>
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
    </style>
</head>
<body>
    <h1>Log Path Checker</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> This script checks your Laravel application's log configuration.
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
        <h2>Success Results</h2>
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
            All checks passed! Your log configuration appears to be working correctly.
        <?php else: ?>
            There are still some issues with your log configuration. Please fix the errors above.
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Next Steps</h2>
        <p>After verifying your log configuration:</p>
        <ol>
            <li>Delete this file for security</li>
            <li>Try accessing your Laravel application to see if the log errors are resolved</li>
            <li>If issues persist, check the web server error logs for additional information</li>
        </ol>
    </div>
    
    <p style="text-align: center; margin-top: 30px; color: #718096; font-size: 0.9em;">
        Remember to delete this file after checking your log paths.
    </p>
</body>
</html> 