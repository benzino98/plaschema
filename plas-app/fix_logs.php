<?php
/**
 * Log Path Fixer Script
 * 
 * This script fixes the log path issues by creating necessary directories
 * and setting the correct permissions.
 */

// Set execution time limit
set_time_limit(300);

// Define paths
$home_dir = dirname(__FILE__);
$storage_path = $home_dir . '/storage';
$logs_path = $storage_path . '/logs';
$ci_cd_logs_path = '/home/runner/work/plaschema/plaschema/plas-app/storage/logs';

echo "Starting log path fix...\n";
echo "Home directory: {$home_dir}\n";
echo "Storage path: {$storage_path}\n";
echo "Logs path: {$logs_path}\n";
echo "CI/CD logs path: {$ci_cd_logs_path}\n";

// Create storage/logs directory if it doesn't exist
if (!file_exists($logs_path)) {
    if (mkdir($logs_path, 0755, true)) {
        echo "✅ Created logs directory: {$logs_path}\n";
    } else {
        echo "❌ Failed to create logs directory: {$logs_path}\n";
    }
} else {
    echo "ℹ️ Logs directory already exists: {$logs_path}\n";
}

// Set permissions for the logs directory
if (file_exists($logs_path)) {
    if (chmod($logs_path, 0755)) {
        echo "✅ Set permissions for logs directory\n";
    } else {
        echo "❌ Failed to set permissions for logs directory\n";
    }
}

// Create .htaccess file to prevent direct access
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

$htaccess_file = $logs_path . '/.htaccess';
if (!file_exists($htaccess_file)) {
    if (file_put_contents($htaccess_file, $htaccess_content)) {
        echo "✅ Created .htaccess file in logs directory\n";
    } else {
        echo "❌ Failed to create .htaccess file in logs directory\n";
    }
} else {
    echo "ℹ️ .htaccess file already exists in logs directory\n";
}

// Create .gitignore file
$gitignore_content = <<<EOT
*
!.gitignore
!.htaccess
EOT;

$gitignore_file = $logs_path . '/.gitignore';
if (!file_exists($gitignore_file)) {
    if (file_put_contents($gitignore_file, $gitignore_content)) {
        echo "✅ Created .gitignore file in logs directory\n";
    } else {
        echo "❌ Failed to create .gitignore file in logs directory\n";
    }
} else {
    echo "ℹ️ .gitignore file already exists in logs directory\n";
}

// Create CI/CD logs directory if it doesn't exist
if (!file_exists(dirname($ci_cd_logs_path))) {
    if (@mkdir(dirname($ci_cd_logs_path), 0755, true)) {
        echo "✅ Created CI/CD storage directory structure\n";
    } else {
        echo "❌ Failed to create CI/CD storage directory structure\n";
    }
}

if (!file_exists($ci_cd_logs_path)) {
    // Try to create a symlink first
    if (function_exists('symlink') && !in_array('symlink', explode(',', ini_get('disable_functions')))) {
        if (@symlink($logs_path, $ci_cd_logs_path)) {
            echo "✅ Created symlink from CI/CD logs path to actual logs path\n";
        } else {
            // If symlink fails, create the directory
            if (@mkdir($ci_cd_logs_path, 0755, true)) {
                echo "✅ Created CI/CD logs directory\n";
            } else {
                echo "❌ Failed to create CI/CD logs directory\n";
            }
        }
    } else {
        // If symlink function is not available, create the directory
        if (@mkdir($ci_cd_logs_path, 0755, true)) {
            echo "✅ Created CI/CD logs directory\n";
        } else {
            echo "❌ Failed to create CI/CD logs directory\n";
        }
    }
} else {
    echo "ℹ️ CI/CD logs path already exists\n";
}

// Update .env file with LOG_PATH if it exists
$env_file = $home_dir . '/.env';
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
            echo "✅ Added LOG_PATH to .env file\n";
        } else {
            echo "❌ Failed to update .env file\n";
        }
    } else {
        echo "ℹ️ LOG_PATH already exists in .env file\n";
    }
} else {
    echo "⚠️ .env file does not exist\n";
}

echo "\nLog path fix completed!\n";
echo "Please run 'composer dump-autoload' to ensure the helper functions are registered.\n"; 