<?php
/**
 * Storage Link Fix Script
 * 
 * This script creates a symbolic link from public/storage to storage/app/public
 * and ensures proper permissions for file uploads.
 */

// Basic security - restrict by IP
$allowed_ips = [
    '102.91.104.42',
    '102.91.102.193',
    '102.91.102.195',
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

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'details' => []
];

// Define paths for both local and production environments
if (file_exists('/home/plaschem/laravel')) {
    // Production environment
    $storage_path = '/home/plaschem/laravel/storage/app/public';
    $public_path = '/home/plaschem/public_html/storage';
    
    // Add debug info
    $response['environment'] = 'Production';
    $response['server_root'] = $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown';
    
    // Try to load Laravel environment
    if (file_exists('/home/plaschem/laravel/.env')) {
        $env_file = file_get_contents('/home/plaschem/laravel/.env');
        preg_match('/DB_DATABASE=([^\s]+)/', $env_file, $db_matches);
        preg_match('/DB_USERNAME=([^\s]+)/', $env_file, $user_matches);
        preg_match('/DB_PASSWORD=([^\s]+)/', $env_file, $pass_matches);
        
        $db_name = $db_matches[1] ?? null;
        $db_user = $user_matches[1] ?? null;
        $db_pass = $pass_matches[1] ?? null;
        
        if ($db_name && $db_user) {
            $response['db_config_found'] = true;
        }
    }
} else {
    // Local environment
    $storage_path = __DIR__ . '/storage/app/public';
    $public_path = __DIR__ . '/public/storage';
    
    // Add debug info
    $response['environment'] = 'Local';
    $response['script_path'] = __DIR__;
}

// Add PHP info for debugging
$response['php_version'] = PHP_VERSION;
$response['symlink_function_exists'] = function_exists('symlink') ? 'Yes' : 'No';
$response['is_callable_symlink'] = is_callable('symlink') ? 'Yes' : 'No';

// Function to check if a path exists and is writable
function check_path_writable($path) {
    if (!file_exists($path)) {
        return [
            'exists' => false,
            'writable' => false,
            'message' => "Path does not exist: $path"
        ];
    }
    
    if (!is_writable($path)) {
        return [
            'exists' => true,
            'writable' => false,
            'message' => "Path exists but is not writable: $path"
        ];
    }
    
    return [
        'exists' => true,
        'writable' => true,
        'message' => "Path exists and is writable: $path"
    ];
}

// Check if storage path exists and is writable
$storage_check = check_path_writable(dirname($storage_path));
$response['details']['storage_check'] = $storage_check;

// Check if public path parent exists and is writable
$public_check = check_path_writable(dirname($public_path));
$response['details']['public_check'] = $public_check;

// If the storage directory doesn't exist, create it
if (!file_exists($storage_path)) {
    if (mkdir($storage_path, 0755, true)) {
        $response['details'][] = "Created storage directory: $storage_path";
    } else {
        $response['details'][] = "Failed to create storage directory: $storage_path";
    }
}

// If the public/storage link already exists, remove it
if (file_exists($public_path)) {
    if (is_link($public_path)) {
        if (unlink($public_path)) {
            $response['details'][] = "Removed existing symbolic link: $public_path";
        } else {
            $response['details'][] = "Failed to remove existing symbolic link: $public_path";
        }
    } else {
        // If it's a directory, try to remove it
        if (is_dir($public_path)) {
            if (rmdir($public_path)) {
                $response['details'][] = "Removed existing directory: $public_path";
            } else {
                $response['details'][] = "Failed to remove existing directory: $public_path";
            }
        } else {
            // It's a regular file, remove it
            if (unlink($public_path)) {
                $response['details'][] = "Removed existing file: $public_path";
            } else {
                $response['details'][] = "Failed to remove existing file: $public_path";
            }
        }
    }
}

// Check if symlink function is available
if (function_exists('symlink')) {
    // Try to create the symbolic link
    try {
        if (symlink($storage_path, $public_path)) {
            $response['success'] = true;
            $response['message'] = "Storage link created successfully";
            $response['details'][] = "Created symbolic link from $public_path to $storage_path";
        } else {
            $response['message'] = "Failed to create storage link";
            $response['details'][] = "Error creating symbolic link: " . error_get_last()['message'] ?? 'Unknown error';
            // Fall back to alternative method
            $use_alternative = true;
        }
    } catch (Exception $e) {
        $response['details'][] = "Exception when creating symlink: " . $e->getMessage();
        $use_alternative = true;
    }
} else {
    $response['details'][] = "Symlink function not available. Using alternative method...";
    $use_alternative = true;
}

// Use alternative method if symlink failed or is not available
if (isset($use_alternative) && $use_alternative) {
    $response['details'][] = "Using alternative method for storage link...";
    
    // Create a PHP file that redirects to the actual file
    $redirect_content = <<<EOT
<?php
// This is a workaround for environments where symlinks are not supported
\$request_uri = \$_SERVER['REQUEST_URI'];
\$storage_prefix = '/storage';

// Debug mode - uncomment to enable detailed logging
// error_log("Storage proxy request: " . \$request_uri);

// Extract the file path from the request URI
if (strpos(\$request_uri, \$storage_prefix) === 0) {
    \$file_path = substr(\$request_uri, strlen(\$storage_prefix));
    \$full_path = '{$storage_path}' . \$file_path;
    
    // Debug mode - uncomment to enable detailed logging
    // error_log("Looking for file: " . \$full_path);
    
    if (file_exists(\$full_path)) {
        // Try to get MIME type
        \$mime = 'application/octet-stream';
        
        if (function_exists('mime_content_type')) {
            \$detected_mime = mime_content_type(\$full_path);
            if (\$detected_mime) {
                \$mime = \$detected_mime;
            }
        } else if (function_exists('finfo_open')) {
            \$finfo = finfo_open(FILEINFO_MIME_TYPE);
            \$detected_mime = finfo_file(\$finfo, \$full_path);
            if (\$detected_mime) {
                \$mime = \$detected_mime;
            }
            finfo_close(\$finfo);
        }
        
        // Set common image MIME types based on extension as fallback
        \$ext = strtolower(pathinfo(\$full_path, PATHINFO_EXTENSION));
        \$image_mimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon'
        ];
        
        if (isset(\$image_mimes[\$ext])) {
            \$mime = \$image_mimes[\$ext];
        }
        
        // Debug mode - uncomment to enable detailed logging
        // error_log("Serving file with MIME: " . \$mime);
        
        header('Content-Type: ' . \$mime);
        header('Content-Length: ' . filesize(\$full_path));
        header('Cache-Control: max-age=86400, public');
        
        // Output file contents
        readfile(\$full_path);
        exit;
    }
}

// If we get here, file was not found
http_response_code(404);
echo "File not found";
EOT;

    if (file_put_contents($public_path, $redirect_content)) {
        $response['success'] = true;
        $response['message'] = "Storage link alternative created successfully";
        $response['details'][] = "Created alternative storage link solution";
        
        // Create a test image to verify the setup
        $test_dir = $storage_path . '/test';
        if (!file_exists($test_dir)) {
            mkdir($test_dir, 0755, true);
        }
        
        // Create a simple 1x1 pixel test image
        $test_image_path = $test_dir . '/test.png';
        $test_image_content = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        if (file_put_contents($test_image_path, $test_image_content)) {
            $response['test_image'] = '/storage/test/test.png';
            $response['details'][] = "Created test image at $test_image_path";
        } else {
            $response['details'][] = "Failed to create test image";
        }
        
        // Add debug information about the storage directory
        $response['storage_path'] = $storage_path;
        $response['public_path'] = $public_path;
        
        // Check if we can list files in the storage directory
        if (is_readable($storage_path)) {
            $files = scandir($storage_path);
            $response['storage_contents'] = array_slice($files, 0, 10); // Show up to 10 files
            $response['details'][] = "Successfully listed storage directory contents";
        } else {
            $response['details'][] = "Could not read storage directory contents";
        }
        
        // Check for image files in common upload directories
        $image_dirs = [
            $storage_path . '/news',
            $storage_path . '/uploads',
            $storage_path . '/images',
            $storage_path . '/photos',
        ];
        
        $found_images = [];
        foreach ($image_dirs as $dir) {
            if (is_dir($dir)) {
                $dir_files = glob($dir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                if (!empty($dir_files)) {
                    $found_images[] = [
                        'directory' => $dir,
                        'count' => count($dir_files),
                        'samples' => array_slice($dir_files, 0, 3)
                    ];
                }
            }
        }
        
        if (!empty($found_images)) {
            $response['found_images'] = $found_images;
            $response['details'][] = "Found image files in storage directories";
        } else {
            $response['details'][] = "No image files found in common upload directories";
        }
        
        // Check database for image paths if possible
        if (isset($response['db_config_found']) && $response['db_config_found'] && extension_loaded('mysqli')) {
            try {
                $mysqli = new mysqli('localhost', $db_user, $db_pass, $db_name);
                
                if (!$mysqli->connect_error) {
                    $response['details'][] = "Successfully connected to database";
                    
                    // Try to find tables that might contain image paths
                    $tables_result = $mysqli->query("SHOW TABLES");
                    $tables = [];
                    while ($table = $tables_result->fetch_array()) {
                        $tables[] = $table[0];
                    }
                    
                    $image_columns = [];
                    foreach ($tables as $table) {
                        $columns_result = $mysqli->query("SHOW COLUMNS FROM `$table`");
                        while ($column = $columns_result->fetch_assoc()) {
                            $column_name = $column['Field'];
                            if (stripos($column_name, 'image') !== false || 
                                stripos($column_name, 'photo') !== false || 
                                stripos($column_name, 'picture') !== false || 
                                stripos($column_name, 'file') !== false) {
                                
                                $image_columns[] = [
                                    'table' => $table,
                                    'column' => $column_name
                                ];
                            }
                        }
                    }
                    
                    // Sample image paths from the database
                    $sample_images = [];
                    foreach ($image_columns as $column_info) {
                        $table = $column_info['table'];
                        $column = $column_info['column'];
                        
                        $sample_result = $mysqli->query("SELECT `$column` FROM `$table` WHERE `$column` IS NOT NULL AND `$column` != '' LIMIT 5");
                        if ($sample_result && $sample_result->num_rows > 0) {
                            while ($row = $sample_result->fetch_assoc()) {
                                $image_path = $row[$column];
                                $sample_images[] = [
                                    'table' => $table,
                                    'column' => $column,
                                    'path' => $image_path,
                                    'exists_in_storage' => file_exists($storage_path . '/' . ltrim($image_path, '/'))
                                ];
                            }
                        }
                    }
                    
                    if (!empty($sample_images)) {
                        $response['sample_db_images'] = $sample_images;
                        $response['details'][] = "Found image paths in database";
                    } else {
                        $response['details'][] = "No image paths found in database";
                    }
                    
                    $mysqli->close();
                } else {
                    $response['details'][] = "Could not connect to database: " . $mysqli->connect_error;
                }
            } catch (Exception $e) {
                $response['details'][] = "Database error: " . $e->getMessage();
            }
        }
    } else {
        $response['details'][] = "Alternative method also failed";
    }
}

// Set proper permissions for the storage directory
if ($response['success']) {
    // Make sure the storage directory is writable
    if (chmod($storage_path, 0755)) {
        $response['details'][] = "Set permissions on storage directory";
    } else {
        $response['details'][] = "Failed to set permissions on storage directory";
    }
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT); 