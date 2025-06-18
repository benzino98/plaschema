<?php
/**
 * Laravel Utility Script
 * 
 * This script consolidates multiple utility functions for Laravel on shared hosting
 * where direct command-line access might be limited.
 * 
 * SECURITY NOTICE:
 * This file contains sensitive operations. Consider implementing:
 * 1. IP restrictions
 * 2. Password protection
 * 3. Removing this file when not in use
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

// Define common paths
$home_dir = dirname($_SERVER['DOCUMENT_ROOT']);
$laravel_root = $home_dir . '/laravel';
$storage_path = $laravel_root . '/storage';
$bootstrap_cache = $laravel_root . '/bootstrap/cache';
$cache_path = $storage_path . '/framework/cache';
$cache_data_path = $cache_path . '/data';
$views_path = $storage_path . '/framework/views';
$sessions_path = $storage_path . '/framework/sessions';
$logs_path = $storage_path . '/logs';

// Fix path issues by setting environment variables
putenv("STORAGE_PATH={$storage_path}");
putenv("LOG_PATH={$logs_path}");

// Get utility type and action
$utility = $_GET['utility'] ?? 'dashboard';
$action = $_GET['action'] ?? 'status';

// Initialize results array
$results = [];
$results[] = "Server home directory: " . $home_dir;
$results[] = "Laravel directory path: " . $laravel_root;

// Check if we're in API mode
$api_mode = isset($_GET['api']) && $_GET['api'] == '1';
if ($api_mode) {
    header('Content-Type: application/json');
}

// Common utility functions
// ===============================================================

// Function to delete directory contents
function delete_directory_contents($dir) {
    $files = glob($dir . '/*');
    $results = [];
    
    foreach ($files as $file) {
        if (is_dir($file) && !in_array(basename($file), ['.', '..'])) {
            $results = array_merge($results, delete_directory_contents($file));
            $results[] = "Processed directory: " . $file;
        } else if (is_file($file) && !in_array(basename($file), ['.gitignore', '.gitkeep', '.htaccess'])) {
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
        if (mkdir($dir, 0755, true)) {
            return "Created directory: $dir";
        } else {
            return "Failed to create directory: $dir";
        }
    }
    return "Directory already exists: $dir";
}

// Function to create directory and set permissions
function create_directory($path, &$results) {
    if (!file_exists($path)) {
        if (mkdir($path, 0775, true)) {
            $results[] = "✅ Created directory: " . $path;
        } else {
            $results[] = "❌ Failed to create directory: " . $path;
            return false;
        }
    } else {
        $results[] = "ℹ️ Directory already exists: " . $path;
    }
    
    // Set permissions
    if (@chmod($path, 0775)) {
        $results[] = "✅ Set permissions (0775) on: " . $path;
    } else {
        $results[] = "❌ Failed to set permissions on: " . $path;
        return false;
    }
    
    return true;
}

// Create .htaccess file to prevent direct access
function create_htaccess($path, &$results) {
    $htaccess_file = $path . '/.htaccess';
    if (!file_exists($htaccess_file)) {
        $htaccess_content = "Require all denied\nOptions -Indexes\nOrder deny,allow\nDeny from all";
        if (file_put_contents($htaccess_file, $htaccess_content)) {
            $results[] = "✅ Created .htaccess file in: " . $path;
        } else {
            $results[] = "❌ Failed to create .htaccess file in: " . $path;
            return false;
        }
    }
    return true;
}

// Create a .gitignore file to prevent committing cache files
function create_gitignore($path, &$results) {
    $gitignore_file = $path . '/.gitignore';
    if (!file_exists($gitignore_file)) {
        $gitignore_content = "*\n!.gitignore";
        if (file_put_contents($gitignore_file, $gitignore_content)) {
            $results[] = "✅ Created .gitignore file in: " . $path;
        } else {
            $results[] = "❌ Failed to create .gitignore file in: " . $path;
            return false;
        }
    }
    return true;
}

// Function to bootstrap Laravel (for certain operations)
function bootstrap_laravel() {
    global $laravel_root, $storage_path;
    
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
    $app['config']->set('logging.channels.null', [
        'driver' => 'monolog',
        'handler' => Monolog\Handler\NullHandler::class,
    ]);
    
    // Ensure database configuration is set correctly
    if ($app['config']->get('database.default') == 'sqlite' && !file_exists($app['config']->get('database.connections.sqlite.database'))) {
        // Default to MySQL if SQLite database file doesn't exist
        $app['config']->set('database.default', 'mysql');
        
        // Set default MySQL credentials if they're not already set
        if (empty($app['config']->get('database.connections.mysql.host'))) {
            $app['config']->set('database.connections.mysql.host', '127.0.0.1');
        }
        if (empty($app['config']->get('database.connections.mysql.database'))) {
            $app['config']->set('database.connections.mysql.database', 'plaschem_db');
        }
        if (empty($app['config']->get('database.connections.mysql.username'))) {
            $app['config']->set('database.connections.mysql.username', 'plaschem_user');
        }
        
        // Reset the database connection
        $app['db']->purge();
    }
    
    return [$app, $kernel];
}

// Utility implementations
// ===============================================================

// Migration utility
function handle_migrations($action, &$results) {
    global $laravel_root;
    
    switch ($action) {
        case 'status':
            $results[] = "Checking migration status...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                $output = [];
                $kernel->call('migrate:status', [], $output);
                $results = array_merge($results, $output);
            } catch (Exception $e) {
                $results[] = "Error checking migration status: " . $e->getMessage();
            }
            break;
            
        case 'run':
            $results[] = "Running migrations...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                $output = [];
                $kernel->call('migrate', ['--force' => true], $output);
                $results = array_merge($results, $output);
            } catch (Exception $e) {
                $results[] = "Error running migrations: " . $e->getMessage();
            }
            break;
            
        case 'rollback':
            $results[] = "Rolling back last migration batch...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                $output = [];
                $kernel->call('migrate:rollback', ['--force' => true], $output);
                $results = array_merge($results, $output);
            } catch (Exception $e) {
                $results[] = "Error rolling back migrations: " . $e->getMessage();
            }
            break;
            
        case 'fresh':
            $results[] = "WARNING: Dropping all tables and re-running all migrations...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                $output = [];
                $kernel->call('migrate:fresh', ['--force' => true], $output);
                $results = array_merge($results, $output);
            } catch (Exception $e) {
                $results[] = "Error refreshing migrations: " . $e->getMessage();
            }
            break;
            
        default:
            $results[] = "Available migration actions:";
            $results[] = "- status: Check migration status";
            $results[] = "- run: Run pending migrations";
            $results[] = "- rollback: Rollback the last migration batch";
            $results[] = "- fresh: Drop all tables and re-run all migrations (use with caution!)";
            break;
    }
}

// Cache management utility
function handle_cache_management($action, &$results) {
    global $bootstrap_cache, $storage_path, $laravel_root;
    
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
                list($app, $kernel) = bootstrap_laravel();
                $kernel->call('config:cache');
                $results[] = "Config cache generated successfully.";
            } catch (Exception $e) {
                $results[] = "Error generating config cache: " . $e->getMessage();
            }
            break;
            
        case 'route':
            $results[] = "Generating route cache...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                $kernel->call('route:cache');
                $results[] = "Route cache generated successfully.";
            } catch (Exception $e) {
                $results[] = "Error generating route cache: " . $e->getMessage();
            }
            break;
            
        case 'view':
            $results[] = "Generating view cache...";
            try {
                list($app, $kernel) = bootstrap_laravel();
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
                    $results[] = "$name: Enabled (cached)";
                } else {
                    $results[] = "$name: Disabled (not cached)";
                }
            }
            
            // Check directory permissions
            $dirs = [
                $bootstrap_cache,
                $storage_path . '/framework/cache',
                $storage_path . '/framework/sessions',
                $storage_path . '/framework/views',
                $storage_path . '/logs',
            ];
            
            $results[] = "Directory Permissions:";
            foreach ($dirs as $dir) {
                if (file_exists($dir)) {
                    $results[] = "$dir: Exists (Permissions: " . substr(sprintf('%o', fileperms($dir)), -4) . ")";
                } else {
                    $results[] = "$dir: Does not exist";
                }
            }
            break;
    }
}

// Fix cache paths utility
function handle_fix_cache_paths(&$results) {
    global $cache_path, $cache_data_path, $views_path, $sessions_path, $logs_path, $laravel_root;
    
    // Create cache directories
    create_directory($cache_path, $results);
    create_directory($cache_data_path, $results);
    create_directory($views_path, $results);
    create_directory($sessions_path, $results);
    create_directory($logs_path, $results);
    
    // Create .htaccess files
    create_htaccess($cache_path, $results);
    create_htaccess($cache_data_path, $results);
    create_htaccess($views_path, $results);
    create_htaccess($sessions_path, $results);
    create_htaccess($logs_path, $results);
    
    // Create .gitignore files
    $gitignore_paths = [$cache_path, $views_path, $sessions_path, $logs_path];
    foreach ($gitignore_paths as $path) {
        create_gitignore($path, $results);
    }
    
    // Update .env file with CACHE_DRIVER if it exists
    $env_file = $laravel_root . '/.env';
    if (file_exists($env_file)) {
        $env_content = file_get_contents($env_file);
        
        // Check if CACHE_DRIVER already exists in .env
        if (strpos($env_content, 'CACHE_DRIVER=') === false) {
            // Add CACHE_DRIVER after LOG_CHANNEL
            $env_content = preg_replace(
                '/(LOG_CHANNEL=.*?)(\r?\n)/i',
                "$1$2CACHE_DRIVER=file$2",
                $env_content
            );
            
            if (file_put_contents($env_file, $env_content)) {
                $results[] = "✅ Added CACHE_DRIVER to .env file";
            } else {
                $results[] = "❌ Failed to update .env file";
            }
        } else {
            $results[] = "ℹ️ CACHE_DRIVER already exists in .env file";
        }
    } else {
        $results[] = "⚠️ .env file does not exist";
    }
    
    // Clear existing cache files (optional based on query parameter)
    if (isset($_GET['clear_cache']) && $_GET['clear_cache'] == '1') {
        $cache_files = glob($cache_data_path . '/*');
        $deleted_count = 0;
        
        foreach ($cache_files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore' && basename($file) !== '.htaccess') {
                if (unlink($file)) {
                    $deleted_count++;
                }
            }
        }
        
        $results[] = "🧹 Cleared {$deleted_count} cache files";
    }
}

// Fix log path utility
function handle_fix_log_path(&$results) {
    global $logs_path, $laravel_root;
    
    // Create the logs directory
    create_directory($logs_path, $results);
    
    // Create .htaccess file to prevent direct access
    create_htaccess($logs_path, $results);
    
    // Create .gitignore file
    create_gitignore($logs_path, $results);
    
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
}

// Create storage link utility
function handle_create_storage_link(&$results) {
    global $laravel_root, $storage_path;
    
    $public_storage_path = $laravel_root . '/public/storage';
    $storage_public_path = $storage_path . '/app/public';
    
    // Make sure the public directory in storage exists
    create_directory($storage_public_path, $results);
    
    // Remove existing symlink if it exists
    if (is_link($public_storage_path)) {
        if (unlink($public_storage_path)) {
            $results[] = "✅ Removed existing storage symlink";
        } else {
            $results[] = "❌ Failed to remove existing storage symlink";
            return;
        }
    } else if (file_exists($public_storage_path)) {
        // If it's a directory, not a symlink, remove it
        if (is_dir($public_storage_path)) {
            // We'll create a backup of any files that might be there
            $backup_dir = $laravel_root . '/public/storage_backup_' . date('Ymd_His');
            if (rename($public_storage_path, $backup_dir)) {
                $results[] = "✅ Moved existing storage directory to {$backup_dir}";
            } else {
                $results[] = "❌ Failed to backup existing storage directory";
                return;
            }
        } else {
            // It's a regular file, just delete it
            if (unlink($public_storage_path)) {
                $results[] = "✅ Removed existing storage file";
            } else {
                $results[] = "❌ Failed to remove existing storage file";
                return;
            }
        }
    }
    
    // Create the symlink
    if (symlink($storage_public_path, $public_storage_path)) {
        $results[] = "✅ Created storage symlink: {$public_storage_path} → {$storage_public_path}";
    } else {
        $results[] = "❌ Failed to create storage symlink";
        
        // Try alternative method for Windows or if symlink function is disabled
        $results[] = "Trying alternative method for creating symlink...";
        
        if (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
            // Try using exec for command-line symlink creation
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                exec("mklink /D \"{$public_storage_path}\" \"{$storage_public_path}\"", $output, $return_var);
            } else {
                // Unix/Linux
                exec("ln -s \"{$storage_public_path}\" \"{$public_storage_path}\"", $output, $return_var);
            }
            
            if ($return_var === 0) {
                $results[] = "✅ Created storage symlink using exec command";
            } else {
                $results[] = "❌ Failed to create symlink using exec: " . implode("\n", $output);
            }
        } else {
            // Last resort: create a PHP file that does a redirect
            $redirect_file = $public_storage_path . '/index.php';
            $redirect_content = <<<EOT
<?php
// This is a fallback since symlinks couldn't be created
\$request_uri = \$_SERVER['REQUEST_URI'];
\$storage_path = '/storage/app/public';
\$file_path = preg_replace('#^/storage/#', \$storage_path . '/', \$request_uri);

if (file_exists(\$file_path)) {
    \$mime = mime_content_type(\$file_path);
    header('Content-Type: ' . \$mime);
    readfile(\$file_path);
    exit;
} else {
    header('HTTP/1.0 404 Not Found');
    echo '404 Not Found';
}
EOT;
            
            if (mkdir($public_storage_path, 0755, true) && file_put_contents($redirect_file, $redirect_content)) {
                $results[] = "✅ Created PHP redirect file as symlink alternative";
            } else {
                $results[] = "❌ Failed to create PHP redirect file";
            }
        }
    }
}

// Test environment utility
function handle_test_environment(&$results) {
    global $laravel_root;
    
    // PHP Version
    $results[] = "PHP Version: " . PHP_VERSION;
    
    // Check required PHP extensions
    $required_extensions = [
        'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype',
        'json', 'bcmath', 'fileinfo', 'curl', 'zip', 'gd'
    ];
    
    $results[] = "PHP Extensions:";
    foreach ($required_extensions as $ext) {
        if (extension_loaded($ext)) {
            $results[] = "✅ {$ext}: Loaded";
        } else {
            $results[] = "❌ {$ext}: Not Loaded";
        }
    }
    
    // Check PHP functions
    $important_functions = [
        'symlink' => 'Creating symbolic links',
        'exec' => 'Executing shell commands',
        'shell_exec' => 'Executing shell commands',
        'passthru' => 'Executing shell commands',
        'system' => 'Executing shell commands'
    ];
    
    $results[] = "PHP Functions:";
    foreach ($important_functions as $func => $purpose) {
        if (function_exists($func) && !in_array($func, explode(',', ini_get('disable_functions')))) {
            $results[] = "✅ {$func}: Available ({$purpose})";
        } else {
            $results[] = "❌ {$func}: Not Available ({$purpose})";
        }
    }
    
    // Check permissions on important directories
    $important_dirs = [
        $laravel_root . '/storage',
        $laravel_root . '/bootstrap/cache',
        $laravel_root . '/public'
    ];
    
    $results[] = "Directory Permissions:";
    foreach ($important_dirs as $dir) {
        if (file_exists($dir)) {
            $perms = substr(sprintf('%o', fileperms($dir)), -4);
            $writable = is_writable($dir) ? 'Writable' : 'Not Writable';
            $results[] = "✅ {$dir}: Exists (Permissions: {$perms}, {$writable})";
        } else {
            $results[] = "❌ {$dir}: Does not exist";
        }
    }
}

// Database configuration utility
function handle_database_config(&$results) {
    global $laravel_root;
    
    // Get the current .env file
    $env_file = $laravel_root . '/.env';
    
    if (!file_exists($env_file)) {
        $results[] = "❌ .env file does not exist at {$env_file}";
        return;
    }
    
    $env_content = file_get_contents($env_file);
    
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['db_action']) && $_POST['db_action'] === 'update') {
        // Get form data
        $db_connection = $_POST['db_connection'] ?? 'mysql';
        $db_host = $_POST['db_host'] ?? '127.0.0.1';
        $db_port = $_POST['db_port'] ?? '3306';
        $db_database = $_POST['db_database'] ?? '';
        $db_username = $_POST['db_username'] ?? '';
        $db_password = $_POST['db_password'] ?? '';
        
        // Update database configuration in .env
        $patterns = [
            '/DB_CONNECTION=.*/' => "DB_CONNECTION={$db_connection}",
            '/DB_HOST=.*/' => "DB_HOST={$db_host}",
            '/DB_PORT=.*/' => "DB_PORT={$db_port}",
            '/DB_DATABASE=.*/' => "DB_DATABASE={$db_database}",
            '/DB_USERNAME=.*/' => "DB_USERNAME={$db_username}",
            '/DB_PASSWORD=.*/' => "DB_PASSWORD={$db_password}",
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $env_content)) {
                $env_content = preg_replace($pattern, $replacement, $env_content);
            } else {
                // If the variable doesn't exist in .env, append it
                $env_content .= "\n" . $replacement;
            }
        }
        
        // Save the updated .env file
        if (file_put_contents($env_file, $env_content)) {
            $results[] = "✅ Database configuration updated successfully";
            
            // Test the connection
            try {
                list($app, $kernel) = bootstrap_laravel();
                $app['db']->connection()->getPdo();
                $results[] = "✅ Database connection successful";
            } catch (Exception $e) {
                $results[] = "❌ Database connection failed: " . $e->getMessage();
            }
        } else {
            $results[] = "❌ Failed to update .env file";
        }
    } else {
        // Extract current database settings
        $current_settings = [
            'connection' => preg_match('/DB_CONNECTION=(.*)/', $env_content, $matches) ? trim($matches[1]) : 'mysql',
            'host' => preg_match('/DB_HOST=(.*)/', $env_content, $matches) ? trim($matches[1]) : '127.0.0.1',
            'port' => preg_match('/DB_PORT=(.*)/', $env_content, $matches) ? trim($matches[1]) : '3306',
            'database' => preg_match('/DB_DATABASE=(.*)/', $env_content, $matches) ? trim($matches[1]) : '',
            'username' => preg_match('/DB_USERNAME=(.*)/', $env_content, $matches) ? trim($matches[1]) : '',
            'password' => preg_match('/DB_PASSWORD=(.*)/', $env_content, $matches) ? trim($matches[1]) : '',
        ];
        
        // Display current settings and form
        $results[] = "Current Database Settings:";
        $results[] = "- Connection: {$current_settings['connection']}";
        $results[] = "- Host: {$current_settings['host']}";
        $results[] = "- Port: {$current_settings['port']}";
        $results[] = "- Database: {$current_settings['database']}";
        $results[] = "- Username: {$current_settings['username']}";
        $results[] = "- Password: " . (empty($current_settings['password']) ? "(not set)" : "********");
        
        // Add form HTML
        $results[] = "<form method='post' action='?utility=db_config' class='db-config-form' style='margin-top: 20px; background: #f8fafc; padding: 15px; border-radius: 5px;'>";
        $results[] = "<h4 style='margin-top: 0;'>Update Database Configuration</h4>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='db_connection' style='display: block; margin-bottom: 5px;'>Connection:</label>";
        $results[] = "<select name='db_connection' id='db_connection' style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "<option value='mysql'" . ($current_settings['connection'] == 'mysql' ? " selected" : "") . ">MySQL</option>";
        $results[] = "<option value='pgsql'" . ($current_settings['connection'] == 'pgsql' ? " selected" : "") . ">PostgreSQL</option>";
        $results[] = "<option value='sqlite'" . ($current_settings['connection'] == 'sqlite' ? " selected" : "") . ">SQLite</option>";
        $results[] = "<option value='sqlsrv'" . ($current_settings['connection'] == 'sqlsrv' ? " selected" : "") . ">SQL Server</option>";
        $results[] = "</select>";
        $results[] = "</div>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='db_host' style='display: block; margin-bottom: 5px;'>Host:</label>";
        $results[] = "<input type='text' name='db_host' id='db_host' value='{$current_settings['host']}' style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "</div>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='db_port' style='display: block; margin-bottom: 5px;'>Port:</label>";
        $results[] = "<input type='text' name='db_port' id='db_port' value='{$current_settings['port']}' style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "</div>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='db_database' style='display: block; margin-bottom: 5px;'>Database:</label>";
        $results[] = "<input type='text' name='db_database' id='db_database' value='{$current_settings['database']}' style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "</div>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='db_username' style='display: block; margin-bottom: 5px;'>Username:</label>";
        $results[] = "<input type='text' name='db_username' id='db_username' value='{$current_settings['username']}' style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "</div>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='db_password' style='display: block; margin-bottom: 5px;'>Password:</label>";
        $results[] = "<input type='password' name='db_password' id='db_password' value='{$current_settings['password']}' style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "</div>";
        
        $results[] = "<input type='hidden' name='db_action' value='update'>";
        $results[] = "<button type='submit' class='btn' style='margin-top: 10px;'>Update Database Configuration</button>";
        $results[] = "</form>";
        
        // Test current connection
        $results[] = "<div style='margin-top: 20px;'>";
        $results[] = "<h4>Test Current Connection</h4>";
        try {
            list($app, $kernel) = bootstrap_laravel();
            $app['db']->connection()->getPdo();
            $results[] = "✅ Database connection successful";
        } catch (Exception $e) {
            $results[] = "❌ Database connection failed: " . $e->getMessage();
        }
        $results[] = "</div>";
    }
}

// Main execution
// ===============================================================

// Execute the requested utility function
switch ($utility) {
    case 'cache':
        handle_cache_management($action, $results);
        break;
        
    case 'fix_cache':
        handle_fix_cache_paths($results);
        break;
        
    case 'fix_log':
        handle_fix_log_path($results);
        break;
        
    case 'storage_link':
        handle_create_storage_link($results);
        break;
        
    case 'test_env':
        handle_test_environment($results);
        break;
        
    case 'migrations':
        handle_migrations($action, $results);
        break;
        
    case 'db_config':
        handle_database_config($results);
        break;
        
    // Add more utilities as needed
        
    default:
        // Show dashboard/menu
        $results[] = "Laravel Utilities Dashboard";
        $results[] = "Available utilities:";
        $results[] = "- cache: Manage Laravel cache";
        $results[] = "- fix_cache: Fix cache paths";
        $results[] = "- fix_log: Fix log paths";
        $results[] = "- storage_link: Create storage symlink";
        $results[] = "- test_env: Test environment";
        $results[] = "- migrations: Manage database migrations";
        $results[] = "- db_config: Configure database connection";
        break;
}

// If in API mode, return JSON response
if ($api_mode) {
    echo json_encode([
        'success' => true,
        'results' => $results
    ]);
    exit;
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Utilities</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7fafc;
        }
        h1, h2 {
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
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn:hover {
            background: #3182ce;
        }
        .actions {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .results {
            background: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .utility-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .utility-card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px;
            flex: 1 1 300px;
            border-top: 4px solid #4299e1;
        }
        .warning {
            background-color: #fff5f5;
            color: #c53030;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c53030;
        }
    </style>
</head>
<body>
    <h1>Laravel Utilities</h1>
    
    <div class="warning">
        <strong>⚠️ Security Warning:</strong> This utility file provides access to sensitive operations. 
        For security reasons, enable IP restrictions or password protection, and consider deleting this file when not in use.
    </div>
    
    <?php if ($utility === 'dashboard'): ?>
        <div class="utility-menu">
            <div class="utility-card">
                <h3>Cache Management</h3>
                <p>Manage Laravel's cache system when you don't have CLI access.</p>
                <div class="actions">
                    <a href="?utility=cache&action=status" class="btn">View Cache Status</a>
                    <a href="?utility=cache&action=clear_cache" class="btn">Clear All Cache</a>
                    <a href="?utility=cache&action=create_dirs" class="btn">Create Cache Directories</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Storage Link</h3>
                <p>Create a symbolic link from public/storage to storage/app/public</p>
                <div class="actions">
                    <a href="?utility=storage_link" class="btn">Create Storage Link</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Fix Paths</h3>
                <p>Fix cache and log paths for Laravel on shared hosting</p>
                <div class="actions">
                    <a href="?utility=fix_cache" class="btn">Fix Cache Paths</a>
                    <a href="?utility=fix_log" class="btn">Fix Log Path</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Environment</h3>
                <p>Test your environment for Laravel compatibility</p>
                <div class="actions">
                    <a href="?utility=test_env" class="btn">Test Environment</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Database Migrations</h3>
                <p>Manage your database migrations</p>
                <div class="actions">
                    <a href="?utility=migrations&action=status" class="btn">Migration Status</a>
                    <a href="?utility=migrations&action=run" class="btn">Run Migrations</a>
                    <a href="?utility=migrations&action=rollback" class="btn">Rollback Migrations</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Database Configuration</h3>
                <p>Configure your database connection settings</p>
                <div class="actions">
                    <a href="?utility=db_config" class="btn">Configure Database</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <h2>Utility: <?= ucfirst(str_replace('_', ' ', $utility)) ?></h2>
            <div class="actions">
                <a href="?utility=dashboard" class="btn">Back to Dashboard</a>
                
                <?php if ($utility === 'cache'): ?>
                    <a href="?utility=cache&action=clear_cache" class="btn">Clear All Cache</a>
                    <a href="?utility=cache&action=clear_config" class="btn">Clear Config Cache</a>
                    <a href="?utility=cache&action=clear_route" class="btn">Clear Route Cache</a>
                    <a href="?utility=cache&action=clear_view" class="btn">Clear View Cache</a>
                    <a href="?utility=cache&action=config" class="btn">Generate Config Cache</a>
                    <a href="?utility=cache&action=route" class="btn">Generate Route Cache</a>
                    <a href="?utility=cache&action=view" class="btn">Generate View Cache</a>
                    <a href="?utility=cache&action=create_dirs" class="btn">Create Cache Directories</a>
                <?php endif; ?>
                
                <?php if ($utility === 'fix_cache'): ?>
                    <a href="?utility=fix_cache&clear_cache=1" class="btn">Fix Paths & Clear Cache</a>
                <?php endif; ?>
                
                <?php if ($utility === 'migrations'): ?>
                    <a href="?utility=migrations&action=status" class="btn">Migration Status</a>
                    <a href="?utility=migrations&action=run" class="btn">Run Migrations</a>
                    <a href="?utility=migrations&action=rollback" class="btn">Rollback Migrations</a>
                    <a href="?utility=migrations&action=fresh" class="btn" onclick="return confirm('WARNING: This will drop all tables and re-run all migrations. All data will be lost. Are you sure?')">Fresh Migrations</a>
                <?php endif; ?>
            </div>
            
            <div class="results">
                <h3>Results:</h3>
                <pre><?= implode("\n", $results); ?></pre>
            </div>
        </div>
    <?php endif; ?>
    
    <p style="text-align: center; margin-top: 30px; color: #718096; font-size: 0.9em;">
        Laravel Utilities &copy; <?= date('Y') ?>
    </p>
</body>
</html>
