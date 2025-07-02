<?php
/**
 * Enrollment Cache Management Tool
 * 
 * This script allows administrators to clear the enrollment statistics cache
 * and force a fresh fetch from the API.
 */

// Set execution time limit to 2 minutes
set_time_limit(120);

// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Add security check to prevent unauthorized access
$allowedIps = ['127.0.0.1', '::1', '35.183.155.138'];
$currentIp = $_SERVER['REMOTE_ADDR'];

// Basic security - restrict to specific IPs or require admin login
// Remove or modify this in production as needed
if (!in_array($currentIp, $allowedIps) && !isset($_GET['bypass_security'])) {
    echo '<p>Access restricted. Add ?bypass_security=1 to the URL if you are an administrator.</p>';
    exit;
}

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Determine action
$action = $_GET['action'] ?? 'status';
$results = [];

// Get services
$cacheService = app(\App\Services\CacheService::class);
$apiService = app(\App\Services\ApiService::class);

// Define cache key
$cacheKey = 'enrollment_statistics';

// Handle actions
switch ($action) {
    case 'clear':
        // Clear the cache
        $success = $cacheService->forget($cacheKey);
        $results['action'] = 'Cache Cleared';
        $results['success'] = $success;
        $results['message'] = $success ? 'Cache cleared successfully' : 'Failed to clear cache';
        break;
        
    case 'refresh':
        // First clear the cache
        $cacheService->forget($cacheKey);
        
        // Then fetch fresh data from API
        try {
            $startTime = microtime(true);
            $data = $apiService->refreshEnrollmentStatistics();
            $executionTime = microtime(true) - $startTime;
            
            $results['action'] = 'Cache Refreshed';
            $results['success'] = true;
            $results['execution_time'] = round($executionTime, 2) . ' seconds';
            $results['data'] = $data;
            $results['is_fallback'] = isset($data['is_fallback']) && $data['is_fallback'];
            $results['is_expired'] = isset($data['is_expired']) && $data['is_expired'];
            $results['message'] = 'Cache refreshed successfully';
            
            if ($results['is_fallback']) {
                $results['message'] .= ' (using fallback data)';
            } elseif ($results['is_expired']) {
                $results['message'] .= ' (using expired cache)';
            }
        } catch (\Exception $e) {
            $results['action'] = 'Cache Refresh';
            $results['success'] = false;
            $results['message'] = 'Error refreshing cache: ' . $e->getMessage();
        }
        break;
        
    case 'view_config':
        // Show configuration details
        $results['action'] = 'View Configuration';
        $results['api_url'] = config('services.external_api.url');
        $results['timeout'] = config('services.external_api.timeout');
        $results['connect_timeout'] = config('services.external_api.connect_timeout');
        $results['cache_driver'] = config('cache.default');
        break;
        
    case 'status':
    default:
        // Get current cache status
        $cachedData = $cacheService->get($cacheKey);
        $results['action'] = 'Cache Status';
        $results['has_cache'] = !is_null($cachedData);
        $results['data'] = $cachedData;
        $results['is_fallback'] = isset($cachedData['is_fallback']) && $cachedData['is_fallback'];
        $results['is_expired'] = isset($cachedData['is_expired']) && $cachedData['is_expired'];
        
        if ($results['has_cache']) {
            $results['message'] = 'Cache exists';
            if ($results['is_fallback']) {
                $results['message'] .= ' (using fallback data)';
            } elseif ($results['is_expired']) {
                $results['message'] .= ' (using expired cache)';
            }
        } else {
            $results['message'] = 'No cache exists';
        }
        break;
}

// Output results
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Cache Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h1, h2, h3 {
            color: #0066cc;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .warning {
            color: orange;
            font-weight: bold;
        }
        pre {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .button {
            display: inline-block;
            background-color: #0066cc;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .button:hover {
            background-color: #0052a3;
        }
        .button.danger {
            background-color: #cc3300;
        }
        .button.danger:hover {
            background-color: #a32900;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enrollment Cache Management</h1>
        <p>This tool allows administrators to manage the enrollment statistics cache.</p>
        
        <div class="card">
            <h2>Actions</h2>
            <a href="?action=status<?= isset($_GET['bypass_security']) ? '&bypass_security=1' : '' ?>" class="button">View Cache Status</a>
            <a href="?action=clear<?= isset($_GET['bypass_security']) ? '&bypass_security=1' : '' ?>" class="button danger">Clear Cache</a>
            <a href="?action=refresh<?= isset($_GET['bypass_security']) ? '&bypass_security=1' : '' ?>" class="button">Refresh Cache</a>
            <a href="?action=view_config<?= isset($_GET['bypass_security']) ? '&bypass_security=1' : '' ?>" class="button">View Configuration</a>
        </div>
        
        <div class="card">
            <h2>Results: <?= $results['action'] ?? 'Unknown Action' ?></h2>
            
            <?php if (isset($results['message'])): ?>
                <p>
                    <strong>Message:</strong> 
                    <span class="<?= isset($results['success']) && $results['success'] ? 'success' : (isset($results['success']) && !$results['success'] ? 'error' : '') ?>">
                        <?= $results['message'] ?>
                    </span>
                </p>
            <?php endif; ?>
            
            <?php if (isset($results['execution_time'])): ?>
                <p><strong>Execution Time:</strong> <?= $results['execution_time'] ?></p>
            <?php endif; ?>
            
            <?php if ($action === 'view_config'): ?>
                <h3>API Configuration</h3>
                <table>
                    <tr>
                        <th>Setting</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>API URL</td>
                        <td><?= $results['api_url'] ?></td>
                    </tr>
                    <tr>
                        <td>Timeout</td>
                        <td><?= $results['timeout'] ?> seconds</td>
                    </tr>
                    <tr>
                        <td>Connect Timeout</td>
                        <td><?= $results['connect_timeout'] ?> seconds</td>
                    </tr>
                    <tr>
                        <td>Cache Driver</td>
                        <td><?= $results['cache_driver'] ?></td>
                    </tr>
                </table>
            <?php endif; ?>
            
            <?php if (isset($results['data']) && !empty($results['data'])): ?>
                <h3>Cached Data</h3>
                <?php if (isset($results['is_fallback']) && $results['is_fallback']): ?>
                    <p class="warning">⚠️ This is fallback data. The API could not be reached.</p>
                <?php endif; ?>
                
                <?php if (isset($results['is_expired']) && $results['is_expired']): ?>
                    <p class="warning">⚠️ This is expired cache data. The API could not be reached.</p>
                <?php endif; ?>
                
                <table>
                    <tr>
                        <th>Statistic</th>
                        <th>Value</th>
                    </tr>
                    <?php foreach ($results['data'] as $key => $value): ?>
                        <?php if ($key !== 'is_fallback' && $key !== 'is_expired'): ?>
                            <tr>
                                <td><?= str_replace('_', ' ', ucfirst($key)) ?></td>
                                <td>
                                    <?php 
                                        if (is_numeric($value) && strpos($key, 'count') !== false) {
                                            echo number_format($value);
                                        } else {
                                            echo htmlspecialchars($value);
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            <?php elseif ($action === 'status' || $action === 'refresh'): ?>
                <p class="warning">No cached data available.</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Help</h2>
            <ul>
                <li><strong>View Cache Status:</strong> Shows the current enrollment statistics in the cache.</li>
                <li><strong>Clear Cache:</strong> Removes the enrollment statistics from the cache.</li>
                <li><strong>Refresh Cache:</strong> Clears the cache and fetches fresh data from the API.</li>
                <li><strong>View Configuration:</strong> Shows the current API configuration settings.</li>
            </ul>
            
            <h3>Troubleshooting</h3>
            <ul>
                <li>If the refresh operation shows fallback data, the API could not be reached.</li>
                <li>Try the <a href="api_test.php<?= isset($_GET['bypass_security']) ? '?bypass_security=1' : '' ?>">API Test Tool</a> for more detailed diagnostics.</li>
            </ul>
        </div>
    </div>
</body>
</html> 