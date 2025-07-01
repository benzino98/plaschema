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
    '98.97.79.54',
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

// Define paths for both local and production environments
if (file_exists('/home/plaschem/laravel')) {
    // Production environment
    $storage_path = '/home/plaschem/laravel/storage/app/public';
    $public_path = '/home/plaschem/public_html/storage';
} else {
    // Local environment
    $storage_path = __DIR__ . '/storage/app/public';
    $public_path = __DIR__ . '/public/storage';
}

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'details' => []
];

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
        }
    }
}

// Create the symbolic link
if (symlink($storage_path, $public_path)) {
    $response['success'] = true;
    $response['message'] = "Storage link created successfully";
    $response['details'][] = "Created symbolic link from $public_path to $storage_path";
} else {
    $response['message'] = "Failed to create storage link";
    $response['details'][] = "Error creating symbolic link: " . error_get_last()['message'] ?? 'Unknown error';
    
    // Try alternative method using file_put_contents for shared hosting that doesn't support symlinks
    if (!function_exists('symlink') || !@symlink($storage_path, $public_path)) {
        $response['details'][] = "Symlink function not available or failed. Trying alternative method...";
        
        // Create a PHP file that redirects to the actual file
        $redirect_content = <<<EOT
<?php
// This is a workaround for environments where symlinks are not supported
\$path = '{$storage_path}' . substr(\$_SERVER['REQUEST_URI'], strlen('/storage'));
if (file_exists(\$path)) {
    \$mime = mime_content_type(\$path);
    header('Content-Type: ' . \$mime);
    readfile(\$path);
    exit;
}
http_response_code(404);
echo "File not found";
EOT;

        if (file_put_contents($public_path, $redirect_content)) {
            $response['success'] = true;
            $response['message'] = "Storage link alternative created successfully";
            $response['details'][] = "Created alternative storage link solution";
        } else {
            $response['details'][] = "Alternative method also failed";
        }
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