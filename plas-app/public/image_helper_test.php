<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Helpers\ImageHelper;

// Test paths
$testPaths = [
    'original' => 'images/news/sample.jpg',
    'with_storage' => 'storage/images/news/sample.jpg',
    'with_slash' => '/images/news/sample.jpg',
    'with_storage_slash' => '/storage/images/news/sample.jpg',
    'null' => null,
    'empty' => '',
    'full_url' => 'https://example.com/image.jpg',
    'with_spaces' => ' images/news/sample.jpg ',
];

// Function to display image tag
function displayImage($path, $label) {
    if ($path) {
        echo "<div style='margin-bottom: 20px;'>";
        echo "<h3>$label</h3>";
        echo "<p>Original: <code>$path</code></p>";
        echo "<p>Formatted: <code>" . App\Helpers\ImageHelper::formatPath($path) . "</code></p>";
        echo "<p>URL: <code>" . App\Helpers\ImageHelper::url($path) . "</code></p>";
        echo "<img src='" . App\Helpers\ImageHelper::url($path) . "' style='max-width: 300px; border: 1px solid #ddd;' onerror=\"this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22300%22%20height%3D%22150%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20width%3D%22300%22%20height%3D%22150%22%20fill%3D%22%23cccccc%22%2F%3E%3Ctext%20x%3D%22150%22%20y%3D%2275%22%20font-size%3D%2220%22%20text-anchor%3D%22middle%22%20alignment-baseline%3D%22middle%22%20fill%3D%22%23333333%22%3EImage%20not%20found%3C%2Ftext%3E%3C%2Fsvg%3E';\">";
        echo "</div>";
    } else {
        echo "<div style='margin-bottom: 20px;'>";
        echo "<h3>$label</h3>";
        echo "<p>Original: <code>null or empty</code></p>";
        echo "<p>Formatted: <code>" . (App\Helpers\ImageHelper::formatPath($path) ?? 'null') . "</code></p>";
        echo "<p>URL: <code>" . (App\Helpers\ImageHelper::url($path) ?? 'null') . "</code></p>";
        echo "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Helper Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        code {
            background-color: #f5f5f5;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .test-container {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <h1>Image Helper Test</h1>
    
    <div class="test-container">
        <h2>Test with different path formats</h2>
        
        <?php foreach ($testPaths as $key => $path): ?>
            <?php displayImage($path, ucfirst(str_replace('_', ' ', $key)) . ' path'); ?>
        <?php endforeach; ?>
    </div>
    
    <div class="test-container">
        <h2>Test with real images from the database</h2>
        
        <?php
        // Get some real images from the database
        $news = \App\Models\News::where('image_path', '!=', null)->take(3)->get();
        
        if ($news->count() > 0) {
            foreach ($news as $item) {
                echo "<div style='margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;'>";
                echo "<h3>{$item->title}</h3>";
                
                echo "<h4>Original Image Path</h4>";
                displayImage($item->image_path, 'Original');
                
                echo "<h4>Small Image Path</h4>";
                displayImage($item->image_path_small, 'Small');
                
                echo "<h4>Medium Image Path</h4>";
                displayImage($item->image_path_medium, 'Medium');
                
                echo "<h4>Large Image Path</h4>";
                displayImage($item->image_path_large, 'Large');
                
                echo "</div>";
            }
        } else {
            echo "<p>No news items with images found in the database.</p>";
        }
        ?>
    </div>
</body>
</html> 