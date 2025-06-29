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
    '102.91.104.42',
    '98.97.79.54',
    '102.91.102.195',
    '135.129.124.105',
    '127.0.0.1', // localhost for development
    $_SERVER['SERVER_ADDR'] ?? '', // Server's own IP
];

// IP restriction - only allowed IPs can access this file
if (!empty($allowed_ips) && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    http_response_code(404); // Return 404 Not Found instead of 403 Forbidden to hide the file's existence
    die("<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>");
}

// Password protection
$correct_password = 'c3OtGl9V3Sj4pPl27'; // Change this to your secure password

// Check if we need to show the login form
$show_login = true;

// Check if the password cookie is set and valid
if (isset($_COOKIE['laravel_utils_auth']) && $_COOKIE['laravel_utils_auth'] === md5($correct_password)) {
    $show_login = false;
} 
// Check if the password was just submitted
elseif (isset($_POST['utils_password'])) {
    if ($_POST['utils_password'] === $correct_password) {
        // Set a cookie that expires in 12 hours
        setcookie('laravel_utils_auth', md5($correct_password), time() + 43200, '/', '', true, true);
        $show_login = false;
        
        // Redirect to remove the password from POST data
        if (!isset($_GET['redirected'])) {
            $redirect_url = $_SERVER['REQUEST_URI'];
            $redirect_url .= (strpos($redirect_url, '?') === false) ? '?redirected=1' : '&redirected=1';
            header("Location: $redirect_url");
            exit;
        }
    } else {
        $login_error = "Incorrect password. Please try again.";
    }
}

// Show login form if needed
if ($show_login) {
    http_response_code(200);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Authentication Required</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 400px;
                margin: 50px auto;
                padding: 20px;
                background-color: #f7fafc;
            }
            h1 {
                color: #4a5568;
                font-size: 24px;
                margin-bottom: 20px;
            }
            .card {
                background: #fff;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                padding: 20px;
            }
            .form-group {
                margin-bottom: 15px;
            }
            label {
                display: block;
                margin-bottom: 5px;
                font-weight: 500;
            }
            input[type="password"] {
                width: 100%;
                padding: 8px;
                border-radius: 4px;
                border: 1px solid #cbd5e0;
            }
            .btn {
                display: inline-block;
                background: #4299e1;
                color: white;
                padding: 8px 16px;
                border-radius: 4px;
                text-decoration: none;
                border: none;
                cursor: pointer;
            }
            .btn:hover {
                background: #3182ce;
            }
            .error {
                color: #e53e3e;
                margin-bottom: 15px;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>Authentication Required</h1>
            <?php if (isset($login_error)): ?>
                <div class="error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="utils_password">Password:</label>
                    <input type="password" id="utils_password" name="utils_password" required autofocus>
                </div>
                <button type="submit" class="btn">Log In</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

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
                $results[] = "Try using the 'Sequential Migration' option below to resolve dependencies.";
            }
            break;
            
        case 'fix_migration':
            $results[] = "Fixing migration issues and creating all tables...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                
                // Step 1: Run base framework migrations (users, cache, jobs)
                $results[] = "Step 1: Creating base framework tables...";
                $baseFiles = [
                    '0001_01_01_000000_create_users_table.php', // users, password_reset_tokens, sessions
                    '0001_01_01_000001_create_cache_table.php', // cache, cache_locks
                    '0001_01_01_000002_create_jobs_table.php', // jobs, job_batches, failed_jobs
                    '2025_05_05_144205_create_personal_access_tokens_table.php',
                ];
                foreach ($baseFiles as $file) {
                    $migrationPath = $laravel_root . '/database/migrations/' . $file;
                    if (file_exists($migrationPath)) {
                        $results[] = "Running migration: " . $file;
                        $output = [];
                        $kernel->call('migrate', [
                            '--path' => 'database/migrations/' . $file,
                            '--force' => true,
                        ], $output);
                        $results = array_merge($results, $output);
                    } else {
                        $results[] = "⚠️ Migration file not found: " . $file;
                    }
                }
                
                // Step 2: Run permission and role migrations
                $results[] = "Step 2: Creating authentication and permission tables...";
                $authFiles = [
                    '2025_05_02_084409_create_roles_table.php',
                    '2025_05_02_084415_create_permissions_table.php',
                    '2025_05_02_084424_create_role_permission_table.php',
                    '2025_05_02_084430_create_user_role_table.php',
                ];
                foreach ($authFiles as $file) {
                    $migrationPath = $laravel_root . '/database/migrations/' . $file;
                    if (file_exists($migrationPath)) {
                        $results[] = "Running migration: " . $file;
                        $output = [];
                        $kernel->call('migrate', [
                            '--path' => 'database/migrations/' . $file,
                            '--force' => true,
                        ], $output);
                        $results = array_merge($results, $output);
                    } else {
                        $results[] = "⚠️ Migration file not found: " . $file;
                    }
                }
                
                // Step 3: Create the core content tables
                $results[] = "Step 3: Creating core content tables...";
                $contentFiles = [
                    '2023_08_12_174437_create_news_table.php',
                    '2023_08_12_174452_create_faqs_table.php',
                    '2023_08_12_174445_create_healthcare_providers_table.php',
                    '2023_08_15_000000_create_resource_categories_table.php',
                    '2023_08_15_000001_create_resources_table.php',
                    '2025_05_02_151506_create_message_categories_table.php',
                    '2025_05_02_151517_create_contact_messages_table.php',
                    '2025_05_05_121722_create_notifications_table.php',
                    '2025_05_06_120209_create_translations_table.php',
                    '2025_08_01_000000_create_activity_logs_table.php',
                ];
                foreach ($contentFiles as $file) {
                    $migrationPath = $laravel_root . '/database/migrations/' . $file;
                    if (file_exists($migrationPath)) {
                        $results[] = "Running migration: " . $file;
                        $output = [];
                        $kernel->call('migrate', [
                            '--path' => 'database/migrations/' . $file,
                            '--force' => true,
                        ], $output);
                        $results = array_merge($results, $output);
                    } else {
                        $results[] = "⚠️ Migration file not found: " . $file;
                    }
                }
                
                // Step 4: Run the remaining alteration migrations (SKIP data seeding migrations)
                $results[] = "Step 4: Running alteration migrations (skipping data seeds)...";
                $alterFiles = [
                    '2025_05_02_112025_add_type_column_to_healthcare_providers_table.php',
                    '2025_05_02_112031_add_type_column_to_healthcare_providers_table.php',
                    '2025_05_05_121112_add_responsive_images_columns_to_models.php',
                    '2023_09_15_000001_alter_resources_table_searchable_content.php',
                    '2025_05_13_152941_alter_resources_table_increase_searchable_content_size.php',
                    '2025_05_18_112641_add_show_on_plans_page_to_faqs_table.php',
                ];
                foreach ($alterFiles as $file) {
                    $migrationPath = $laravel_root . '/database/migrations/' . $file;
                    if (file_exists($migrationPath)) {
                        $results[] = "Running migration: " . $file;
                        $output = [];
                        $kernel->call('migrate', [
                            '--path' => 'database/migrations/' . $file,
                            '--force' => true,
                        ], $output);
                        $results = array_merge($results, $output);
                    } else {
                        $results[] = "⚠️ Migration file not found: " . $file;
                    }
                }
                
                // Final check for any remaining migrations
                $results[] = "Checking for any remaining migrations...";
                $output = [];
                $kernel->call('migrate:status', [], $output);
                $pending = false;
                foreach ($output as $line) {
                    if (stripos($line, 'No') === false && stripos($line, 'Ran?') === false && stripos($line, '|') !== false) {
                        $pending = true;
                        break;
                    }
                }
                
                if ($pending) {
                    $results[] = "⚠️ There are still pending migrations. Running final migration pass...";
                    $output = [];
                    $kernel->call('migrate', [
                        '--force' => true,
                        '--pretend' => true, // First do a dry run to check for issues
                    ], $output);
                    
                    // Check if there are any issues
                    $hasErrors = false;
                    foreach ($output as $line) {
                        if (stripos($line, 'error') !== false || stripos($line, 'exception') !== false) {
                            $hasErrors = true;
                            break;
                        }
                    }
                    
                    if (!$hasErrors) {
                        $output = [];
                        $kernel->call('migrate', [
                            '--force' => true,
                        ], $output);
                        $results = array_merge($results, $output);
                    } else {
                        $results[] = "⚠️ Detected potential issues with remaining migrations. Skipping automatic migration.";
                        $results[] = "Please check the migration files manually or use more specific migration options.";
                    }
                } else {
                    $results[] = "✅ All migrations have been processed!";
                }
                
                // Count the total tables
                try {
                    $tableCount = count($app['db']->connection()->getDoctrineSchemaManager()->listTableNames());
                    $results[] = "✅ Total tables in database: {$tableCount}";
                } catch (Exception $e) {
                    $results[] = "⚠️ Could not count tables: " . $e->getMessage();
                }
                
            } catch (Exception $e) {
                $results[] = "Error in migration fix: " . $e->getMessage();
            }
            break;
            
        case 'schema_only':
            $results[] = "Running only schema migrations (tables only)...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                
                // Get list of migrations
                $migrationFiles = glob($laravel_root . '/database/migrations/*.php');
                $migrationNames = [];
                
                // Filter to include only migrations that create tables (typically named create_*_table.php)
                foreach ($migrationFiles as $file) {
                    $basename = basename($file);
                    if (strpos($basename, 'create_') !== false && strpos($basename, '_table') !== false) {
                        $parts = explode('_', $basename);
                        $timestamp = $parts[0];
                        $migrationNames[] = $timestamp . '_' . implode('_', array_slice($parts, 1));
                    }
                }
                
                // Run only these migrations first
                if (!empty($migrationNames)) {
                    $results[] = "Found " . count($migrationNames) . " table creation migrations";
                    
                    $output = [];
                    $kernel->call('migrate', [
                        '--force' => true,
                        '--path' => 'database/migrations',
                        '--realpath' => true
                    ], $output);
                    
                    $results = array_merge($results, $output);
                } else {
                    $results[] = "No table creation migrations found";
                }
            } catch (Exception $e) {
                $results[] = "Error running schema migrations: " . $e->getMessage();
            }
            break;
            
        case 'sequential':
            $results[] = "Running migrations in sequential batches...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                
                // Step 1: Run migrations that create tables first
                $results[] = "Step 1: Creating database tables...";
                $output = [];
                $kernel->call('migrate', [
                    '--force' => true,
                    '--path' => 'database/migrations',
                ], $output);
                $results = array_merge($results, $output);
                
                // Step 2: Seed any required base data
                $results[] = "Step 2: Running database seeders...";
                if (file_exists($laravel_root . '/database/seeders/DatabaseSeeder.php')) {
                    $output = [];
                    $kernel->call('db:seed', [
                        '--force' => true,
                        '--class' => 'DatabaseSeeder'
                    ], $output);
                    $results = array_merge($results, $output);
                } else {
                    $results[] = "No database seeders found.";
                }
            } catch (Exception $e) {
                $results[] = "Error in sequential migration: " . $e->getMessage();
            }
            break;
            
        case 'seed':
            $results[] = "Running database seeders...";
            try {
                list($app, $kernel) = bootstrap_laravel();
                $output = [];
                $kernel->call('db:seed', ['--force' => true], $output);
                $results = array_merge($results, $output);
            } catch (Exception $e) {
                $results[] = "Error running seeders: " . $e->getMessage();
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
            $results[] = "- fix_migration: Fix migration issues and create all 24 tables";
            $results[] = "- sequential: Run migrations and seeders in the correct sequence";
            $results[] = "- schema_only: Run only table creation migrations";
            $results[] = "- seed: Run database seeders";
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
    
    // Fix CI/CD path issue by creating a symlink or directory
    $ci_cd_logs_path = '/home/runner/work/plaschema/plaschema/plas-app/storage/logs';
    if (!file_exists($ci_cd_logs_path)) {
        // Create the directory structure
        if (!file_exists(dirname($ci_cd_logs_path))) {
            mkdir(dirname($ci_cd_logs_path), 0755, true);
            $results[] = "✅ Created CI/CD storage directory structure";
        }
        
        // Try to create a symlink first
        if (function_exists('symlink') && !in_array('symlink', explode(',', ini_get('disable_functions')))) {
            if (symlink($logs_path, $ci_cd_logs_path)) {
                $results[] = "✅ Created symlink from CI/CD logs path to actual logs path";
            } else {
                // If symlink fails, create the directory
                if (mkdir($ci_cd_logs_path, 0755, true)) {
                    $results[] = "✅ Created CI/CD logs directory";
                } else {
                    $results[] = "❌ Failed to create CI/CD logs directory";
                }
            }
        } else {
            // If symlink function is not available, create the directory
            if (mkdir($ci_cd_logs_path, 0755, true)) {
                $results[] = "✅ Created CI/CD logs directory";
            } else {
                $results[] = "❌ Failed to create CI/CD logs directory";
            }
        }
    } else {
        $results[] = "ℹ️ CI/CD logs path already exists";
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

// App Key Generation utility
function handle_app_key(&$results) {
    global $laravel_root;
    
    $env_file = $laravel_root . '/.env';
    $env_example_file = $laravel_root . '/.env.example';
    
    // Check if .env file exists, if not try to create it from .env.example
    if (!file_exists($env_file)) {
        $results[] = "⚠️ .env file does not exist at {$env_file}";
        
        // Try to create .env from .env.example if it exists
        if (file_exists($env_example_file)) {
            $results[] = "Attempting to create .env file from .env.example...";
            if (copy($env_example_file, $env_file)) {
                $results[] = "✅ Created .env file from .env.example";
            } else {
                $results[] = "❌ Failed to create .env file. Trying to create a minimal .env file...";
                
                // Create a minimal .env file
                $minimal_env = "APP_NAME=Laravel\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_URL=https://plaschema.pl.gov.ng\n\nLOG_CHANNEL=stack\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=plaschem_db\nDB_USERNAME=plaschem_user\nDB_PASSWORD=\n\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\n";
                
                if (file_put_contents($env_file, $minimal_env)) {
                    $results[] = "✅ Created minimal .env file";
                } else {
                    $results[] = "❌ Failed to create .env file. Please check file permissions.";
                    $results[] = "Create a .env file manually in {$laravel_root} with at least APP_KEY=";
                    return;
                }
            }
        } else {
            // Create a minimal .env file if .env.example doesn't exist
            $results[] = "⚠️ .env.example does not exist. Creating a minimal .env file...";
            $minimal_env = "APP_NAME=Laravel\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_URL=https://plaschema.pl.gov.ng\n\nLOG_CHANNEL=stack\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=plaschem_db\nDB_USERNAME=plaschem_user\nDB_PASSWORD=\n\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\n";
            
            if (file_put_contents($env_file, $minimal_env)) {
                $results[] = "✅ Created minimal .env file";
            } else {
                $results[] = "❌ Failed to create .env file. Please check file permissions.";
                $results[] = "Create a .env file manually in {$laravel_root} with at least APP_KEY=";
                return;
            }
        }
    }
    
    // Make .env writable if it's not
    if (!is_writable($env_file)) {
        $results[] = "⚠️ .env file is not writable. Attempting to fix permissions...";
        if (@chmod($env_file, 0644)) {
            $results[] = "✅ Fixed .env file permissions";
        } else {
            $results[] = "❌ Failed to make .env file writable. Please fix permissions manually.";
            $results[] = "Run: chmod 644 {$env_file}";
            return;
        }
    }
    
    // Now read the .env file content
    $env_content = file_get_contents($env_file);
    if ($env_content === false) {
        $results[] = "❌ Failed to read .env file. Creating a new one...";
        $env_content = "APP_NAME=Laravel\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_URL=https://plaschema.pl.gov.ng\n\nLOG_CHANNEL=stack\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=plaschem_db\nDB_USERNAME=plaschem_user\nDB_PASSWORD=\n\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\n";
    }
    
    // Check if APP_KEY exists and has a value
    if (preg_match('/APP_KEY=(.+)/', $env_content, $matches)) {
        $current_key = trim($matches[1]);
        if (!empty($current_key) && $current_key !== 'base64:') {
            $results[] = "✅ APP_KEY is already set: {$current_key}";
            return;
        }
    }
    
    // If we got here, we need to generate a new key
    $results[] = "⚠️ No valid APP_KEY found. Generating a new application key...";
    
    // Try using the Laravel method first
    $laravel_method_successful = false;
    try {
        // Bootstrap Laravel to use the artisan command
        list($app, $kernel) = bootstrap_laravel();
        
        // Call key:generate
        $output = [];
        $kernel->call('key:generate', ['--force' => true], $output);
        $results = array_merge($results, $output);
        
        // Verify the key was generated
        clearstatcache();
        $new_env_content = file_get_contents($env_file);
        if (preg_match('/APP_KEY=(.+)/', $new_env_content, $matches)) {
            $new_key = trim($matches[1]);
            if (!empty($new_key) && $new_key !== 'base64:') {
                $results[] = "✅ Successfully generated and set new APP_KEY: {$new_key}";
                $laravel_method_successful = true;
            }
        }
        
        if (!$laravel_method_successful) {
            $results[] = "⚠️ Laravel key:generate may have failed to update the .env file.";
        }
    } catch (Exception $e) {
        $results[] = "❌ Error with Laravel key:generate: " . $e->getMessage();
    }
    
    // If Laravel method wasn't successful, use the fallback method
    if (!$laravel_method_successful) {
        $results[] = "Using fallback method to generate APP_KEY...";
        
        // Generate a random key (32 random bytes, base64 encoded)
        $random_key = 'base64:' . base64_encode(random_bytes(32));
        
        // Update or add APP_KEY in .env
        if (preg_match('/APP_KEY=/', $env_content)) {
            $env_content = preg_replace('/APP_KEY=.*/', "APP_KEY={$random_key}", $env_content);
        } else {
            $env_content .= "\nAPP_KEY={$random_key}";
        }
        
        // Save the updated .env file
        if (file_put_contents($env_file, $env_content)) {
            $results[] = "✅ Successfully generated and set new APP_KEY (fallback method): {$random_key}";
            
            // Double-check the key was actually written
            clearstatcache();
            $final_env_content = file_get_contents($env_file);
            if (strpos($final_env_content, $random_key) !== false) {
                $results[] = "✅ Verified APP_KEY was written to .env file";
            } else {
                $results[] = "⚠️ APP_KEY may not have been written to .env file properly.";
                
                // Last resort: Create a completely new .env file
                $new_env = "APP_NAME=Laravel\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_URL=https://plaschema.pl.gov.ng\nAPP_KEY={$random_key}\n\nLOG_CHANNEL=stack\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=plaschem_db\nDB_USERNAME=plaschem_user\nDB_PASSWORD=\n\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\n";
                
                $backup_env = $env_file . '.backup.' . date('YmdHis');
                if (copy($env_file, $backup_env)) {
                    $results[] = "✅ Created backup of original .env file at {$backup_env}";
                }
                
                if (file_put_contents($env_file, $new_env)) {
                    $results[] = "✅ Created new .env file with APP_KEY set to: {$random_key}";
                } else {
                    $results[] = "❌ Failed to create new .env file.";
                    $results[] = "Please manually add the following line to your .env file:";
                    $results[] = "APP_KEY={$random_key}";
                }
            }
        } else {
            $results[] = "❌ Failed to update .env file with new APP_KEY.";
            $results[] = "Please manually add the following line to your .env file:";
            $results[] = "APP_KEY={$random_key}";
        }
    }
    
    // Final instructions
    $results[] = "After setting the APP_KEY, you should:";
    $results[] = "1. Clear your application cache by clicking on 'Clear All Cache' in the Cache Management section";
    $results[] = "2. Restart your application if needed on your hosting environment";
}

// Fix Vite assets utility
function handle_vite_assets(&$results) {
    global $laravel_root;
    
    $manifest_dir = $laravel_root . '/public/build';
    $manifest_file = $manifest_dir . '/manifest.json';
    $dummy_manifest_content = '{
    "resources/css/app.css": {
        "file": "assets/app-4ed993c7.css",
        "isEntry": true,
        "src": "resources/css/app.css"
    },
    "resources/js/app.js": {
        "file": "assets/app-a25c28d9.js",
        "isEntry": true,
        "src": "resources/js/app.js"
    }
}';
    
    $results[] = "Checking Vite assets configuration...";
    
    // Check if the project is using Vite
    $vite_config = $laravel_root . '/vite.config.js';
    $package_json = $laravel_root . '/package.json';
    
    if (!file_exists($vite_config) && !file_exists($package_json)) {
        $results[] = "⚠️ This project doesn't appear to use Vite (vite.config.js and package.json not found).";
        $results[] = "If you're still seeing Vite errors, we'll create a dummy manifest to fix it.";
    } else {
        $results[] = "✅ This project appears to use Vite for asset bundling.";
    }
    
    // Check if the build directory exists
    if (!file_exists($manifest_dir)) {
        $results[] = "⚠️ Build directory doesn't exist at: {$manifest_dir}";
        $results[] = "Creating build directory...";
        
        if (mkdir($manifest_dir, 0755, true)) {
            $results[] = "✅ Created build directory: {$manifest_dir}";
        } else {
            $results[] = "❌ Failed to create build directory. Please check permissions.";
            return;
        }
    } else {
        $results[] = "✅ Build directory exists at: {$manifest_dir}";
    }
    
    // Check if the manifest file exists
    if (!file_exists($manifest_file)) {
        $results[] = "⚠️ Vite manifest file doesn't exist: {$manifest_file}";
        $results[] = "Creating dummy manifest file...";
        
        if (file_put_contents($manifest_file, $dummy_manifest_content)) {
            $results[] = "✅ Created dummy manifest file: {$manifest_file}";
        } else {
            $results[] = "❌ Failed to create manifest file. Please check permissions.";
            return;
        }
    } else {
        $results[] = "✅ Vite manifest file exists: {$manifest_file}";
        
        // Check if the manifest file is valid JSON
        $manifest_content = file_get_contents($manifest_file);
        json_decode($manifest_content);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $results[] = "⚠️ Existing manifest file is not valid JSON. Replacing with dummy manifest...";
            
            // Create a backup of the existing file
            $backup_file = $manifest_file . '.backup.' . date('YmdHis');
            if (copy($manifest_file, $backup_file)) {
                $results[] = "✅ Created backup of original manifest file: {$backup_file}";
            }
            
            if (file_put_contents($manifest_file, $dummy_manifest_content)) {
                $results[] = "✅ Replaced invalid manifest with dummy manifest.";
            } else {
                $results[] = "❌ Failed to replace manifest file. Please check permissions.";
                return;
            }
        } else {
            $results[] = "✅ Existing manifest file is valid JSON.";
        }
    }
    
    // Create assets directory and dummy asset files
    $assets_dir = $manifest_dir . '/assets';
    if (!file_exists($assets_dir)) {
        $results[] = "Creating assets directory...";
        if (mkdir($assets_dir, 0755, true)) {
            $results[] = "✅ Created assets directory: {$assets_dir}";
        } else {
            $results[] = "❌ Failed to create assets directory. Please check permissions.";
            return;
        }
    }
    
    // Create dummy CSS file
    $css_file = $assets_dir . '/app-4ed993c7.css';
    if (!file_exists($css_file)) {
        $dummy_css = "/* Placeholder CSS file to satisfy Vite manifest requirements */";
        if (file_put_contents($css_file, $dummy_css)) {
            $results[] = "✅ Created dummy CSS file: {$css_file}";
        } else {
            $results[] = "❌ Failed to create dummy CSS file.";
        }
    }
    
    // Create dummy JS file
    $js_file = $assets_dir . '/app-a25c28d9.js';
    if (!file_exists($js_file)) {
        $dummy_js = "// Placeholder JS file to satisfy Vite manifest requirements";
        if (file_put_contents($js_file, $dummy_js)) {
            $results[] = "✅ Created dummy JS file: {$js_file}";
        } else {
            $results[] = "❌ Failed to create dummy JS file.";
        }
    }
    
    // Fix app config if needed
    $app_config_file = $laravel_root . '/config/app.php';
    if (file_exists($app_config_file)) {
        $app_config = file_get_contents($app_config_file);
        
        // Check if Vite middleware is disabled
        if (strpos($app_config, "'disable_vite' => true") === false) {
            $results[] = "Adding Vite disable option to app config...";
            
            // Backup original file
            $backup_config = $app_config_file . '.backup.' . date('YmdHis');
            if (copy($app_config_file, $backup_config)) {
                $results[] = "✅ Created backup of app config: {$backup_config}";
            }
            
            // Add disable_vite option to the config array
            if (preg_match('/(return \[\s*)/i', $app_config, $matches)) {
                $replacement = $matches[1] . "    'disable_vite' => env('DISABLE_VITE', false),\n\n    ";
                $app_config = preg_replace('/(return \[\s*)/i', $replacement, $app_config, 1);
                
                if (file_put_contents($app_config_file, $app_config)) {
                    $results[] = "✅ Added disable_vite option to app config.";
                } else {
                    $results[] = "❌ Failed to update app config.";
                }
            } else {
                $results[] = "⚠️ Could not find appropriate location to add disable_vite option in app config.";
            }
        } else {
            $results[] = "✅ Vite disable option already exists in app config.";
        }
    }
    
    // Check and update .env file to disable Vite
    $env_file = $laravel_root . '/.env';
    if (file_exists($env_file)) {
        $env_content = file_get_contents($env_file);
        
        if (strpos($env_content, 'DISABLE_VITE=') === false) {
            $results[] = "Adding DISABLE_VITE option to .env file...";
            
            // Append to the .env file
            $env_content .= "\n# Disable Vite asset processing in production\nDISABLE_VITE=true\n";
            
            if (file_put_contents($env_file, $env_content)) {
                $results[] = "✅ Added DISABLE_VITE=true to .env file.";
            } else {
                $results[] = "❌ Failed to update .env file.";
            }
        } else {
            $results[] = "✅ DISABLE_VITE option already exists in .env file.";
        }
    }
    
    $results[] = "✅ Vite asset fixes have been applied!";
    $results[] = "Please clear your application cache and refresh your page.";
}

// Fix htaccess file for routing
function handle_fix_htaccess(&$results) {
    global $laravel_root;
    
    $public_html = '/home/plaschem/public_html';
    $htaccess_file = $public_html . '/.htaccess';
    
    $htaccess_content = <<<'EOT'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Disable directory browsing
Options -Indexes

# PHP settings
<IfModule mod_php.c>
    php_value upload_max_filesize 64M
    php_value post_max_size 64M
    php_value memory_limit 256M
    php_value max_execution_time 600
    php_value max_input_vars 3000
    php_value display_errors Off
    php_value log_errors On
</IfModule>
EOT;

    $results[] = "Checking .htaccess file in public directory...";
    
    if (file_exists($htaccess_file)) {
        // Backup existing file
        $backup_file = $htaccess_file . '.backup.' . date('YmdHis');
        if (copy($htaccess_file, $backup_file)) {
            $results[] = "✅ Created backup of existing .htaccess at: {$backup_file}";
        } else {
            $results[] = "⚠️ Failed to create backup of existing .htaccess";
        }
        
        // Check if the file already has the necessary rewrite rules
        $current_content = file_get_contents($htaccess_file);
        if (strpos($current_content, 'RewriteRule ^ index.php [L]') !== false) {
            $results[] = "ℹ️ .htaccess file already contains Laravel routing rules";
            
            // Ask if they want to overwrite
            $results[] = "<div class='warning'>";
            $results[] = "Existing .htaccess file appears to contain Laravel routing rules.";
            $results[] = "<a href='?utility=fix_htaccess&action=force' class='btn'>Overwrite with default Laravel .htaccess</a>";
            $results[] = "</div>";
            
            if (isset($_GET['action']) && $_GET['action'] === 'force') {
                // Force overwrite
                if (file_put_contents($htaccess_file, $htaccess_content)) {
                    $results[] = "✅ Successfully overwrote .htaccess file with default Laravel configuration";
                } else {
                    $results[] = "❌ Failed to overwrite .htaccess file. Please check permissions.";
                }
            }
            
            return;
        }
    }
    
    // Create or overwrite the file
    if (file_put_contents($htaccess_file, $htaccess_content)) {
        $results[] = "✅ Successfully created/updated .htaccess file with Laravel routing rules";
    } else {
        $results[] = "❌ Failed to create/update .htaccess file. Please check permissions.";
        $results[] = "<div class='warning'>";
        $results[] = "Manual solution: Create a file at {$htaccess_file} with the following content:";
        $results[] = "<pre style='background:#f8f9fa;padding:10px;border-radius:5px;overflow:auto;'>";
        $results[] = htmlspecialchars($htaccess_content);
        $results[] = "</pre>";
        $results[] = "</div>";
    }
    
    // Check if mod_rewrite is enabled
    $results[] = "Checking if mod_rewrite is enabled...";
    
    // Create a simple PHP test file to check mod_rewrite
    $test_file = $public_html . '/rewrite-test.php';
    $test_content = "<?php\necho 'Mod_rewrite check: ' . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? 'Enabled' : 'Unknown (CGI mode)');\nunlink(__FILE__);";
    
    if (file_put_contents($test_file, $test_content)) {
        $results[] = "Created test file to check mod_rewrite. <a href='/rewrite-test.php' target='_blank'>Check mod_rewrite status</a>";
    }
    
    $results[] = "<div class='card' style='margin-top:20px;'>";
    $results[] = "<h3>Troubleshooting 404 Errors</h3>";
    $results[] = "<p>If you still encounter 404 errors after fixing the .htaccess file, try these steps:</p>";
    $results[] = "<ol>";
    $results[] = "<li>Check if mod_rewrite is enabled on your server</li>";
    $results[] = "<li>Make sure AllowOverride is set to All in your Apache configuration</li>";
    $results[] = "<li>Clear browser cache or try in incognito/private browsing mode</li>";
    $results[] = "<li>Check if there are any other .htaccess files in parent directories that might be conflicting</li>";
    $results[] = "</ol>";
    $results[] = "</div>";
}

// Create Admin User utility
function handle_create_admin(&$results) {
    global $laravel_root;
    
    // Process form submission for creating admin user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action']) && $_POST['admin_action'] === 'create') {
        try {
            // Bootstrap Laravel
            list($app, $kernel) = bootstrap_laravel();
            
            // Get form data
            $name = $_POST['admin_name'] ?? '';
            $email = $_POST['admin_email'] ?? '';
            $password = $_POST['admin_password'] ?? '';
            $role = $_POST['admin_role'] ?? 'admin';
            
            // Validate input
            if (empty($name) || empty($email) || empty($password)) {
                $results[] = "❌ All fields are required.";
                return;
            }
            
            // Check if email is valid
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results[] = "❌ Invalid email format.";
                return;
            }
            
            // Get current timestamp for database
            $now = date('Y-m-d H:i:s');
            
            // Check if user already exists
            try {
                $userExists = $app['db']->table('users')->where('email', $email)->exists();
            } catch (Exception $e) {
                $results[] = "❌ Error checking if user exists: " . $e->getMessage();
                return;
            }
            
            if ($userExists) {
                $results[] = "⚠️ A user with this email already exists.";
                
                // If user exists, we can update their role instead
                try {
                    $user = $app['db']->table('users')->where('email', $email)->first();
                    $userId = $user->id;
                    
                    // Check if user already has the role
                    $hasRole = $app['db']->table('user_role')
                        ->join('roles', 'roles.id', '=', 'user_role.role_id')
                        ->where('user_role.user_id', $userId)
                        ->where('roles.slug', $role)
                        ->exists();
                    
                    if ($hasRole) {
                        $results[] = "✅ User already has the '{$role}' role.";
                    } else {
                        // Get role ID
                        $roleRecord = $app['db']->table('roles')->where('slug', $role)->first();
                        if (!$roleRecord) {
                            $results[] = "❌ Role '{$role}' not found.";
                            return;
                        }
                        
                        // Assign role to user
                        $app['db']->table('user_role')->insert([
                            'user_id' => $userId,
                            'role_id' => $roleRecord->id,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                        
                        $results[] = "✅ Role '{$role}' assigned to existing user.";
                    }
                } catch (Exception $e) {
                    $results[] = "❌ Error updating user role: " . $e->getMessage();
                }
                
                return;
            }
            
            // Create new user with proper error handling
            try {
                // Hash the password correctly
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $userId = $app['db']->table('users')->insertGetId([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                
                $results[] = "✅ User created successfully!";
                
                // Get role ID
                $roleRecord = $app['db']->table('roles')->where('slug', $role)->first();
                if (!$roleRecord) {
                    $results[] = "❌ Role '{$role}' not found. User created without role.";
                    return;
                }
                
                // Assign role to user
                $app['db']->table('user_role')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleRecord->id,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                
                $results[] = "✅ Role '{$role}' assigned to user.";
                
                // Provide login instructions
                $results[] = "✅ Admin user created successfully!";
                $results[] = "You can now log in with the following credentials:";
                $results[] = "Email: {$email}";
                $results[] = "Password: (the password you entered)";
            } catch (Exception $e) {
                $results[] = "❌ Error creating user: " . $e->getMessage();
                // Add more detailed debugging information
                $results[] = "Error details: " . $e->getTraceAsString();
            }
            
        } catch (Exception $e) {
            $results[] = "❌ Error initializing Laravel: " . $e->getMessage();
            $results[] = "Error details: " . $e->getTraceAsString();
        }
    } else {
        // Display the admin creation form
        $results[] = "<form method='post' action='?utility=create_admin' class='admin-form' style='margin-top: 20px; background: #f8fafc; padding: 15px; border-radius: 5px;'>";
        $results[] = "<h4 style='margin-top: 0;'>Create Admin User</h4>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='admin_name' style='display: block; margin-bottom: 5px;'>Name:</label>";
        $results[] = "<input type='text' name='admin_name' id='admin_name' required style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "</div>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='admin_email' style='display: block; margin-bottom: 5px;'>Email:</label>";
        $results[] = "<input type='email' name='admin_email' id='admin_email' required style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "</div>";
        
        $results[] = "<div style='margin-bottom: 10px;'>";
        $results[] = "<label for='admin_password' style='display: block; margin-bottom: 5px;'>Password:</label>";
        $results[] = "<input type='password' name='admin_password' id='admin_password' required style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
        $results[] = "</div>";
        
        // Get available roles
        try {
            list($app, $kernel) = bootstrap_laravel();
            $roles = $app['db']->table('roles')->get();
            
            $results[] = "<div style='margin-bottom: 10px;'>";
            $results[] = "<label for='admin_role' style='display: block; margin-bottom: 5px;'>Role:</label>";
            $results[] = "<select name='admin_role' id='admin_role' style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
            
            foreach ($roles as $role) {
                $selected = $role->slug === 'super-admin' ? 'selected' : '';
                $results[] = "<option value='{$role->slug}' {$selected}>{$role->name}</option>";
            }
            
            $results[] = "</select>";
            $results[] = "</div>";
        } catch (Exception $e) {
            $results[] = "<div style='margin-bottom: 10px; color: #e53e3e;'>";
            $results[] = "Could not fetch roles: " . $e->getMessage();
            $results[] = "Defaulting to built-in roles.";
            $results[] = "</div>";
            
            // Fallback to hardcoded roles
            $results[] = "<div style='margin-bottom: 10px;'>";
            $results[] = "<label for='admin_role' style='display: block; margin-bottom: 5px;'>Role:</label>";
            $results[] = "<select name='admin_role' id='admin_role' style='width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #cbd5e0;'>";
            $results[] = "<option value='super-admin' selected>Super Admin</option>";
            $results[] = "<option value='admin'>Admin</option>";
            $results[] = "<option value='editor'>Editor</option>";
            $results[] = "<option value='viewer'>Viewer</option>";
            $results[] = "</select>";
            $results[] = "</div>";
        }
        
        $results[] = "<input type='hidden' name='admin_action' value='create'>";
        $results[] = "<button type='submit' class='btn' style='margin-top: 10px;'>Create Admin User</button>";
        $results[] = "</form>";
        
        // Display a list of existing users with roles
        try {
            list($app, $kernel) = bootstrap_laravel();
            
            $users = $app['db']->table('users')
                ->leftJoin('user_role', 'users.id', '=', 'user_role.user_id')
                ->leftJoin('roles', 'user_role.role_id', '=', 'roles.id')
                ->select('users.id', 'users.name', 'users.email', 'roles.name as role_name')
                ->get();
            
            if (count($users) > 0) {
                $results[] = "<div style='margin-top: 30px;'>";
                $results[] = "<h4>Existing Users</h4>";
                $results[] = "<table style='width: 100%; border-collapse: collapse;'>";
                $results[] = "<thead>";
                $results[] = "<tr>";
                $results[] = "<th style='text-align: left; padding: 8px; border-bottom: 1px solid #e2e8f0;'>Name</th>";
                $results[] = "<th style='text-align: left; padding: 8px; border-bottom: 1px solid #e2e8f0;'>Email</th>";
                $results[] = "<th style='text-align: left; padding: 8px; border-bottom: 1px solid #e2e8f0;'>Role</th>";
                $results[] = "</tr>";
                $results[] = "</thead>";
                $results[] = "<tbody>";
                
                foreach ($users as $user) {
                    $results[] = "<tr>";
                    $results[] = "<td style='padding: 8px; border-bottom: 1px solid #e2e8f0;'>{$user->name}</td>";
                    $results[] = "<td style='padding: 8px; border-bottom: 1px solid #e2e8f0;'>{$user->email}</td>";
                    $results[] = "<td style='padding: 8px; border-bottom: 1px solid #e2e8f0;'>{$user->role_name}</td>";
                    $results[] = "</tr>";
                }
                
                $results[] = "</tbody>";
                $results[] = "</table>";
                $results[] = "</div>";
            } else {
                $results[] = "<div style='margin-top: 30px;'>";
                $results[] = "<h4>No users found</h4>";
                $results[] = "</div>";
            }
        } catch (Exception $e) {
            $results[] = "<div style='margin-top: 30px; color: #e53e3e;'>";
            $results[] = "Could not fetch existing users: " . $e->getMessage();
            $results[] = "</div>";
        }
    }
}

// Run Role and Permission Seeder utility
function handle_role_permission_seeder(&$results) {
    try {
        // Bootstrap Laravel
        list($app, $kernel) = bootstrap_laravel();
        
        $results[] = "<h3>Running Role and Permission Seeder</h3>";
        
        // Check if the RoleAndPermissionSeeder class exists
        $seederClass = 'Database\\Seeders\\RoleAndPermissionSeeder';
        if (!class_exists($seederClass)) {
            $results[] = "❌ Error: RoleAndPermissionSeeder class not found.";
            $results[] = "Please make sure the seeder file exists at database/seeders/RoleAndPermissionSeeder.php";
            return;
        }
        
        // Create an instance of the seeder
        $seeder = new $seederClass();
        
        // Run the seeder
        $results[] = "🔄 Running RoleAndPermissionSeeder...";
        
        try {
            // Start a database transaction
            $app['db']->beginTransaction();
            
            // Run the seeder
            $seeder->run();
            
            // Commit the transaction
            $app['db']->commit();
            
            $results[] = "✅ Successfully ran RoleAndPermissionSeeder!";
            $results[] = "✅ Default roles and permissions have been created.";
            
            // Count the number of roles and permissions created
            $roleCount = $app['db']->table('roles')->count();
            $permissionCount = $app['db']->table('permissions')->count();
            $rolePermissionCount = $app['db']->table('role_permission')->count();
            
            $results[] = "📊 Summary:";
            $results[] = "- {$roleCount} roles created";
            $results[] = "- {$permissionCount} permissions created";
            $results[] = "- {$rolePermissionCount} role-permission associations created";
            
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $app['db']->rollBack();
            
            $results[] = "❌ Error running seeder: " . $e->getMessage();
            $results[] = "Error details: " . $e->getTraceAsString();
        }
        
    } catch (Exception $e) {
        $results[] = "❌ Error initializing Laravel: " . $e->getMessage();
        $results[] = "Error details: " . $e->getTraceAsString();
    }
}

// File obfuscation utility
function handle_obfuscate_file(&$results) {
    global $laravel_root;
    
    $current_file = __FILE__;
    $current_filename = basename($current_file);
    $current_dir = dirname($current_file);
    
    // Process rename request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['obfuscate_action'])) {
        if ($_POST['obfuscate_action'] === 'rename') {
            // Generate a new random filename
            $random_prefix = substr(md5(uniqid(mt_rand(), true)), 0, 10);
            $new_filename = $random_prefix . '_utils.php';
            $new_filepath = $current_dir . '/' . $new_filename;
            
            // Create a copy of the file with the new name
            if (copy($current_file, $new_filepath)) {
                $results[] = "✅ File copied to: {$new_filename}";
                $results[] = "To access the utilities, use: <a href='{$new_filename}'>{$new_filename}</a>";
                
                // Create a safety note file with the new name
                $note_file = $laravel_root . '/.utils_location';
                file_put_contents($note_file, $new_filename);
                $results[] = "✅ Filename saved to private note at: .utils_location";
                
                $results[] = "<div class='warning' style='margin-top: 20px;'>";
                $results[] = "<strong>IMPORTANT: SAVE THIS INFORMATION</strong><br>";
                $results[] = "Your utility file is now accessible at: <code>{$new_filename}</code><br>";
                $results[] = "Please make note of this name, as you will need it to access the utilities.<br>";
                $results[] = "For security reasons, consider deleting this original file.";
                $results[] = "</div>";
                
                $results[] = "<div style='margin-top: 20px;'>";
                $results[] = "<a href='{$new_filename}' class='btn'>Go to New Utility File</a>";
                $results[] = "<form method='post' style='display: inline-block; margin-left: 10px;'>";
                $results[] = "<input type='hidden' name='obfuscate_action' value='delete_original'>";
                $results[] = "<button type='submit' class='btn' style='background-color: #e53e3e;' onclick='return confirm(\"Are you sure you want to delete this original file? Make sure you have saved the new filename.\")'>Delete Original File</button>";
                $results[] = "</form>";
                $results[] = "</div>";
            } else {
                $results[] = "❌ Failed to create copy with new name. Check file permissions.";
            }
        } elseif ($_POST['obfuscate_action'] === 'delete_original') {
            // Check if a note file exists before deleting
            $note_file = $laravel_root . '/.utils_location';
            if (!file_exists($note_file)) {
                $results[] = "❌ Safety note file not found. Cannot safely delete original.";
                return;
            }
            
            // Get the new filename from the note file
            $new_filename = trim(file_get_contents($note_file));
            $new_filepath = $current_dir . '/' . $new_filename;
            
            // Verify the new file exists before deleting the original
            if (!file_exists($new_filepath)) {
                $results[] = "❌ New utility file not found at: {$new_filename}";
                $results[] = "Cannot safely delete original file.";
                return;
            }
            
            // Delete the original file
            if (unlink($current_file)) {
                $results[] = "✅ Original file deleted successfully.";
                $results[] = "Your utility file is now only accessible at: <code>{$new_filename}</code>";
                $results[] = "<meta http-equiv='refresh' content='5;url={$new_filename}' />";
                $results[] = "Redirecting to new location in 5 seconds...";
            } else {
                $results[] = "❌ Failed to delete original file. You may need to delete it manually.";
            }
        }
    } else {
        // Show the file obfuscation form
        $results[] = "<form method='post' class='admin-form' style='margin-top: 20px; background: #f8fafc; padding: 15px; border-radius: 5px;'>";
        $results[] = "<h4 style='margin-top: 0;'>Hide Utility File</h4>";
        $results[] = "<p>This utility will create a copy of this file with a random name to make it harder to discover.</p>";
        $results[] = "<input type='hidden' name='obfuscate_action' value='rename'>";
        $results[] = "<button type='submit' class='btn' style='margin-top: 10px;'>Create Hidden Copy</button>";
        $results[] = "</form>";
        
        // Check if a renamed version already exists
        $note_file = $laravel_root . '/.utils_location';
        if (file_exists($note_file)) {
            $existing_name = trim(file_get_contents($note_file));
            $existing_path = $current_dir . '/' . $existing_name;
            
            if (file_exists($existing_path)) {
                $results[] = "<div style='margin-top: 20px;'>";
                $results[] = "A hidden copy already exists at: <code>{$existing_name}</code>";
                $results[] = "<a href='{$existing_name}' class='btn' style='margin-top: 10px;'>Go to Hidden Copy</a>";
                $results[] = "</div>";
            }
        }
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
        
    case 'app_key':
        handle_app_key($results);
        break;
    
    case 'vite_assets':
        handle_vite_assets($results);
        break;
    
    case 'fix_htaccess':
        handle_fix_htaccess($results);
        break;
    
    case 'create_admin':
        handle_create_admin($results);
        break;
    
    case 'obfuscate':
        handle_obfuscate_file($results);
        break;
    
    case 'role_permission_seeder':
        handle_role_permission_seeder($results);
        break;
    
    // ... existing code ...
        
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
        $results[] = "- app_key: Generate application encryption key";
        $results[] = "- vite_assets: Fix Vite asset issues";
        $results[] = "- fix_htaccess: Fix routing and .htaccess configuration";
        $results[] = "- create_admin: Create admin user and assign roles";
        $results[] = "- role_permission_seeder: Run Role and Permission Seeder";
        $results[] = "- obfuscate: Hide this utility file with a random name";
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
                    <a href="?utility=fix_htaccess" class="btn">Fix .htaccess & Routing</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Application Key</h3>
                <p>Generate or check Laravel application encryption key</p>
                <div class="actions">
                    <a href="?utility=app_key" class="btn">Generate App Key</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Vite Assets</h3>
                <p>Fix Vite manifest missing errors in production</p>
                <div class="actions">
                    <a href="?utility=vite_assets" class="btn">Fix Vite Assets</a>
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
                    <a href="?utility=migrations&action=fix_migration" class="btn">Fix Migrations (Create All Tables)</a>
                    <a href="?utility=migrations&action=sequential" class="btn">Sequential Migration</a>
                    <a href="?utility=migrations&action=run" class="btn">Run Migrations</a>
                    <a href="?utility=migrations&action=schema_only" class="btn">Schema Only</a>
                    <a href="?utility=migrations&action=seed" class="btn">Run Seeders</a>
                    <a href="?utility=migrations&action=rollback" class="btn">Rollback Migrations</a>
                    <a href="?utility=migrations&action=fresh" class="btn" onclick="return confirm('WARNING: This will drop all tables and re-run all migrations. All data will be lost. Are you sure?')">Fresh Migrations</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Database Configuration</h3>
                <p>Configure your database connection settings</p>
                <div class="actions">
                    <a href="?utility=db_config" class="btn">Configure Database</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>User Management</h3>
                <p>Create admin users and assign roles</p>
                <div class="actions">
                    <a href="?utility=create_admin" class="btn">Create Admin User</a>
                    <a href="?utility=role_permission_seeder" class="btn">Run Role & Permission Seeder</a>
                </div>
            </div>
            
            <div class="utility-card">
                <h3>Security</h3>
                <p>Hide this utility file and manage access</p>
                <div class="actions">
                    <a href="?utility=obfuscate" class="btn">Hide Utility File</a>
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
                    <a href="?utility=migrations&action=fix_migration" class="btn">Fix Migrations (Create All Tables)</a>
                    <a href="?utility=migrations&action=sequential" class="btn">Sequential Migration</a>
                    <a href="?utility=migrations&action=run" class="btn">Run Migrations</a>
                    <a href="?utility=migrations&action=schema_only" class="btn">Schema Only</a>
                    <a href="?utility=migrations&action=seed" class="btn">Run Seeders</a>
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