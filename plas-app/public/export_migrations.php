<?php
/**
 * Laravel Migration SQL Exporter
 * 
 * This script generates SQL for all pending migrations without running them.
 * The output can be copied and run manually in phpMyAdmin.
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

// Bootstrap Laravel
require_once '/home/plaschem/laravel/vendor/autoload.php';
$app = require_once '/home/plaschem/laravel/bootstrap/app.php';

// Get the kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Create output buffer to capture migration SQL
ob_start();

// Get the migration repository
$repository = $app->make('migration.repository');

// Ensure the migration table exists
if (!$repository->repositoryExists()) {
    // Generate SQL to create the migrations table
    echo "-- Create migrations table if it doesn't exist\n";
    echo "CREATE TABLE IF NOT EXISTS `migrations` (\n";
    echo "  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,\n";
    echo "  `migration` varchar(255) NOT NULL,\n";
    echo "  `batch` int(11) NOT NULL,\n";
    echo "  PRIMARY KEY (`id`)\n";
    echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";
}

// Get migration files
$migrator = $app->make('migrator');
$migrator->setConnection(config('database.default'));

// Get pending migrations
$files = $migrator->getMigrationFiles(database_path('migrations'));
$ran = $repository->getRan();
$pending = array_diff(array_keys($files), $ran);

if (empty($pending)) {
    echo "-- No pending migrations found.\n";
} else {
    // Get the SQL for each pending migration
    foreach ($pending as $migration) {
        $file = $files[$migration];
        $instance = $migrator->resolve($file);
        
        echo "-- Migration: {$migration}\n";
        
        // Up migrations
        echo "-- Up\n";
        foreach ($instance->up() ?? [] as $statement) {
            if (is_string($statement)) {
                echo $statement . ";\n";
            }
        }
        
        // Record that the migration ran
        echo "INSERT INTO `migrations` (`migration`, `batch`) VALUES ('{$migration}', " . ($repository->getNextBatchNumber()) . ");\n\n";
    }
}

$sql = ob_get_clean();

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Migration SQL Exporter</title>
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
            margin-top: 20px;
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
        .copy-button {
            background: #4299e1;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .copy-button:hover {
            background: #3182ce;
        }
    </style>
</head>
<body>
    <h1>Laravel Migration SQL Exporter</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> Delete this file immediately after use!
    </div>
    
    <div class="card">
        <h2>Migration SQL</h2>
        <p>Copy the SQL below and run it in phpMyAdmin or another SQL client:</p>
        <button class="copy-button" onclick="copySQL()">Copy SQL to Clipboard</button>
        <pre id="sql-content"><?php echo htmlspecialchars($sql); ?></pre>
    </div>
    
    <div class="card">
        <h2>Instructions</h2>
        <ol>
            <li>Copy the SQL above</li>
            <li>Log into phpMyAdmin in your hosting control panel</li>
            <li>Select your database (the one specified in your .env file)</li>
            <li>Go to the SQL tab</li>
            <li>Paste the SQL and click "Go" to execute</li>
            <li>Delete this file immediately for security</li>
        </ol>
        <a href="/" class="btn">Go to Homepage</a>
    </div>
    
    <script>
        function copySQL() {
            const sqlContent = document.getElementById('sql-content');
            const textArea = document.createElement('textarea');
            textArea.value = sqlContent.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('SQL copied to clipboard!');
        }
    </script>
    
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