<?php
/**
 * Laravel Storage Link Creator
 * 
 * This script manually creates a storage link for Laravel applications
 * on shared hosting where exec() is disabled.
 * 
 * SECURITY WARNING: Delete this file immediately after use!
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

// Set maximum execution time
set_time_limit(300);

// Define paths
$laravel_root = dirname(__DIR__) . '/../../laravel';
$public_path = dirname(__DIR__);
$storage_app_public = $laravel_root . '/storage/app/public';
$public_storage = $public_path . '/storage';

// Initialize results array
$results = [];

// Check if we have a specific action to perform
$action = $_GET['action'] ?? 'status';

// Perform the requested action
switch ($action) {
    case 'create_link':
        $results[] = "Attempting to create storage link...";
        
        // First check if symlinks are supported
        $test_file = tempnam(sys_get_temp_dir(), 'symlink_test');
        $test_link = sys_get_temp_dir() . '/symlink_test_link';
        
        $symlinks_supported = false;
        if (@symlink($test_file, $test_link)) {
            $symlinks_supported = true;
            @unlink($test_link);
            $results[] = "Symlinks are supported on this server.";
        } else {
            $results[] = "Symlinks are NOT supported on this server. Will use directory copy method instead.";
        }
        @unlink($test_file);
        
        // Create the storage directory if it doesn't exist
        if (!file_exists($public_storage)) {
            if (mkdir($public_storage, 0755, true)) {
                $results[] = "Created storage directory: $public_storage";
            } else {
                $results[] = "Failed to create storage directory: $public_storage";
                break;
            }
        } else {
            $results[] = "Storage directory already exists: $public_storage";
        }
        
        // If symlinks are supported, try to create a symlink
        if ($symlinks_supported) {
            // Remove existing directory if it's not a symlink
            if (is_dir($public_storage) && !is_link($public_storage)) {
                // Remove directory contents
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($public_storage, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                
                // Remove the directory itself
                rmdir($public_storage);
                $results[] = "Removed existing storage directory to create symlink.";
            }
            
            // Create the symlink
            if (@symlink($storage_app_public, $public_storage)) {
                $results[] = "Successfully created symlink from $storage_app_public to $public_storage";
            } else {
                $results[] = "Failed to create symlink. Error: " . error_get_last()['message'];
                $results[] = "Will fall back to directory copy method.";
                $symlinks_supported = false;
            }
        }
        
        // If symlinks are not supported or failed, use directory copy method
        if (!$symlinks_supported) {
            $results[] = "Using directory copy method...";
            
            // Ensure storage/app/public directory exists
            if (!file_exists($storage_app_public)) {
                if (mkdir($storage_app_public, 0755, true)) {
                    $results[] = "Created storage/app/public directory: $storage_app_public";
                } else {
                    $results[] = "Failed to create storage/app/public directory: $storage_app_public";
                    break;
                }
            }
            
            // Copy files from storage/app/public to public/storage
            $results[] = "Copying files from $storage_app_public to $public_storage";
            
            // Simple recursive copy function
            function copy_dir($src, $dst) {
                $results = [];
                $dir = opendir($src);
                @mkdir($dst);
                while (($file = readdir($dir)) !== false) {
                    if ($file != '.' && $file != '..') {
                        if (is_dir($src . '/' . $file)) {
                            $sub_results = copy_dir($src . '/' . $file, $dst . '/' . $file);
                            $results = array_merge($results, $sub_results);
                        } else {
                            if (copy($src . '/' . $file, $dst . '/' . $file)) {
                                $results[] = "Copied: $file";
                            } else {
                                $results[] = "Failed to copy: $file";
                            }
                        }
                    }
                }
                closedir($dir);
                return $results;
            }
            
            $copy_results = copy_dir($storage_app_public, $public_storage);
            $results[] = "Copied " . count(array_filter($copy_results, function($item) {
                return strpos($item, 'Copied:') === 0;
            })) . " files.";
            
            // Create a README file explaining this is not a real symlink
            $readme_content = "This directory contains copies of files from storage/app/public.\n";
            $readme_content .= "It is not a real symbolic link because your hosting does not support symlinks.\n";
            $readme_content .= "You will need to manually sync these files when updating content in storage/app/public.\n";
            $readme_content .= "Generated on: " . date('Y-m-d H:i:s');
            
            file_put_contents($public_storage . '/README_NOT_A_SYMLINK.txt', $readme_content);
            $results[] = "Created README file explaining this is not a real symlink.";
        }
        break;
        
    case 'sync':
        $results[] = "Syncing storage/app/public to public/storage...";
        
        // Check if directories exist
        if (!file_exists($storage_app_public)) {
            $results[] = "Error: Source directory does not exist: $storage_app_public";
            break;
        }
        
        if (!file_exists($public_storage)) {
            if (mkdir($public_storage, 0755, true)) {
                $results[] = "Created storage directory: $public_storage";
            } else {
                $results[] = "Failed to create storage directory: $public_storage";
                break;
            }
        }
        
        // Simple recursive copy function
        function sync_dir($src, $dst) {
            $results = [];
            if (!is_dir($src)) {
                return ["Error: Source is not a directory: $src"];
            }
            
            if (!file_exists($dst)) {
                mkdir($dst, 0755, true);
                $results[] = "Created directory: $dst";
            }
            
            $dir = opendir($src);
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..' && $file != 'README_NOT_A_SYMLINK.txt') {
                    $src_file = $src . '/' . $file;
                    $dst_file = $dst . '/' . $file;
                    
                    if (is_dir($src_file)) {
                        $sub_results = sync_dir($src_file, $dst_file);
                        $results = array_merge($results, $sub_results);
                    } else {
                        if (!file_exists($dst_file) || filemtime($src_file) > filemtime($dst_file)) {
                            if (copy($src_file, $dst_file)) {
                                $results[] = "Copied: $file";
                            } else {
                                $results[] = "Failed to copy: $file";
                            }
                        }
                    }
                }
            }
            closedir($dir);
            return $results;
        }
        
        $sync_results = sync_dir($storage_app_public, $public_storage);
        $results = array_merge($results, $sync_results);
        
        // Update the README file
        $readme_content = "This directory contains copies of files from storage/app/public.\n";
        $readme_content .= "It is not a real symbolic link because your hosting does not support symlinks.\n";
        $readme_content .= "You will need to manually sync these files when updating content in storage/app/public.\n";
        $readme_content .= "Last synced on: " . date('Y-m-d H:i:s');
        
        file_put_contents($public_storage . '/README_NOT_A_SYMLINK.txt', $readme_content);
        $results[] = "Updated README file with sync timestamp.";
        break;
        
    default:
        // Just show status
        $results[] = "Checking storage link status...";
        
        $results[] = "Laravel storage/app/public path: $storage_app_public";
        $results[] = "Public storage path: $public_storage";
        
        if (file_exists($storage_app_public)) {
            $results[] = "✅ storage/app/public directory exists";
        } else {
            $results[] = "❌ storage/app/public directory does not exist";
        }
        
        if (file_exists($public_storage)) {
            if (is_link($public_storage)) {
                $target = readlink($public_storage);
                $results[] = "✅ public/storage is a symbolic link pointing to: $target";
                
                if ($target == $storage_app_public || realpath($target) == realpath($storage_app_public)) {
                    $results[] = "✅ Symbolic link is correctly configured";
                } else {
                    $results[] = "❌ Symbolic link is pointing to the wrong location";
                }
            } else {
                $results[] = "ℹ️ public/storage is a regular directory (not a symbolic link)";
                
                if (file_exists($public_storage . '/README_NOT_A_SYMLINK.txt')) {
                    $readme_content = file_get_contents($public_storage . '/README_NOT_A_SYMLINK.txt');
                    $results[] = "ℹ️ This appears to be a manual copy of storage/app/public";
                    if (preg_match('/Last synced on: (.+)/', $readme_content, $matches)) {
                        $results[] = "ℹ️ Last synced on: " . $matches[1];
                    }
                }
            }
        } else {
            $results[] = "❌ public/storage directory does not exist";
        }
        
        // Test if symlinks are supported
        $test_file = tempnam(sys_get_temp_dir(), 'symlink_test');
        $test_link = sys_get_temp_dir() . '/symlink_test_link';
        
        if (@symlink($test_file, $test_link)) {
            $results[] = "✅ Symlinks are supported on this server";
            @unlink($test_link);
        } else {
            $results[] = "❌ Symlinks are NOT supported on this server";
        }
        @unlink($test_file);
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Storage Link Creator</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
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
            white-space: pre-wrap;
        }
        .btn {
            display: inline-block;
            background: #4299e1;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            margin: 5px;
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
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Laravel Storage Link Creator</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> Delete this file immediately after use!
    </div>
    
    <div class="card">
        <h2>Storage Link Actions</h2>
        <div class="actions">
            <a href="?action=status" class="btn">Check Status</a>
            <a href="?action=create_link" class="btn">Create Storage Link</a>
            <a href="?action=sync" class="btn">Sync Storage Files</a>
        </div>
    </div>
    
    <div class="card">
        <h2>Results</h2>
        <pre><?php echo implode("\n", $results); ?></pre>
    </div>
    
    <div class="card">
        <h2>Instructions</h2>
        <p>Use the buttons above to manage your Laravel storage link. After you're done, delete this file for security.</p>
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