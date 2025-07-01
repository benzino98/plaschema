<?php
/**
 * Test Storage Link
 * 
 * This file tests if images from storage can be accessed correctly.
 */

// Get image paths from database
$db_host = 'localhost';
$db_name = 'plaschem_db'; // Adjust if your database name is different
$db_user = 'plaschem_user'; // Adjust if your database user is different
$db_pass = ''; // Leave blank for security, you'll need to fill this in manually

$images = [];
$error = '';

try {
    if (extension_loaded('mysqli')) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        if (!$mysqli->connect_error) {
            $result = $mysqli->query("SELECT id, title, image_path, image_path_large, image_path_medium, image_path_small FROM news LIMIT 5");
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $images[] = $row;
                }
            } else {
                $error = "No news images found in database";
            }
            
            $mysqli->close();
        } else {
            $error = "Database connection error: " . $mysqli->connect_error;
        }
    } else {
        $error = "MySQLi extension not loaded";
    }
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}

// Test image path
$test_image = '/storage/test/test.png';

// Function to check if a file exists
function file_exists_remote($url) {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200') !== false;
}

// Get server info
$server_name = $_SERVER['SERVER_NAME'];
$is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$protocol = $is_https ? 'https' : 'http';
$base_url = $protocol . '://' . $server_name;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Link Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; }
        .test-section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .image-card { border: 1px solid #eee; padding: 15px; border-radius: 5px; }
        .image-card img { max-width: 100%; height: auto; }
        .success { color: green; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .path-test { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Storage Link Test</h1>
    
    <div class="test-section">
        <h2>Test Image</h2>
        <p>This is a test image created by the fix_storage_link.php script:</p>
        <img src="<?php echo $test_image; ?>" alt="Test Image" style="border: 1px solid #ddd;">
        <p>
            <?php 
            $test_url = $base_url . $test_image;
            if (file_exists_remote($test_url)) {
                echo '<span class="success">✓ Test image is accessible</span>';
            } else {
                echo '<span class="error">✗ Test image is not accessible</span>';
            }
            ?>
        </p>
        <p>Image URL: <?php echo $test_url; ?></p>
    </div>

    <div class="test-section">
        <h2>Path Tests</h2>
        <div class="path-test">
            <p>Testing different ways to reference the same image:</p>
            <?php
            $image_paths = [
                '/storage/test/test.png',
                'storage/test/test.png',
                '../storage/test/test.png',
                '../../storage/test/test.png',
                $base_url . '/storage/test/test.png'
            ];
            
            echo '<table>';
            echo '<tr><th>Path</th><th>Image</th><th>Status</th></tr>';
            
            foreach ($image_paths as $path) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($path) . '</td>';
                echo '<td><img src="' . $path . '" alt="Test" style="max-width: 50px; max-height: 50px;"></td>';
                
                $check_url = strpos($path, 'http') === 0 ? $path : $base_url . '/' . ltrim($path, '/');
                if (file_exists_remote($check_url)) {
                    echo '<td><span class="success">✓ Accessible</span></td>';
                } else {
                    echo '<td><span class="error">✗ Not accessible</span></td>';
                }
                
                echo '</tr>';
            }
            
            echo '</table>';
            ?>
        </div>
    </div>
    
    <?php if (!empty($images)): ?>
    <div class="test-section">
        <h2>News Images from Database</h2>
        <div class="image-grid">
            <?php foreach ($images as $image): ?>
            <div class="image-card">
                <h3><?php echo htmlspecialchars($image['title'] ?? 'News Item ' . $image['id']); ?></h3>
                
                <h4>Original Image</h4>
                <img src="/storage/<?php echo htmlspecialchars($image['image_path']); ?>" alt="Original Image">
                
                <h4>Large Image</h4>
                <img src="/storage/<?php echo htmlspecialchars($image['image_path_large']); ?>" alt="Large Image">
                
                <h4>Medium Image</h4>
                <img src="/storage/<?php echo htmlspecialchars($image['image_path_medium']); ?>" alt="Medium Image">
                
                <h4>Small Image</h4>
                <img src="/storage/<?php echo htmlspecialchars($image['image_path_small']); ?>" alt="Small Image">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="test-section">
        <h2>News Images from Database</h2>
        <p class="error"><?php echo $error ?: 'No images found'; ?></p>
    </div>
    <?php endif; ?>

    <div class="test-section">
        <h2>Debug Information</h2>
        <ul>
            <li>Server Name: <?php echo $_SERVER['SERVER_NAME']; ?></li>
            <li>Document Root: <?php echo $_SERVER['DOCUMENT_ROOT']; ?></li>
            <li>Script Path: <?php echo __FILE__; ?></li>
            <li>Base URL: <?php echo $base_url; ?></li>
            <li>PHP Version: <?php echo PHP_VERSION; ?></li>
        </ul>
    </div>
</body>
</html> 