<?php
/**
 * API Connection Diagnostic Tool
 * 
 * This script tests the connection to the enrollment statistics API
 * and displays detailed debugging information.
 */

// Set execution time limit to 3 minutes
set_time_limit(180);

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

// Bootstrap Laravel to access configuration
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get configuration values
$apiUrl = config('services.external_api.url', 'https://enrollments.plaschema.app/api');
$timeout = config('services.external_api.timeout', 30);
$connectTimeout = config('services.external_api.connect_timeout', 15);

// Function to test API using different methods
function testApi($url, $timeout, $connectTimeout) {
    $results = [];
    $startTime = microtime(true);
    
    // Test 1: Use curl directly
    $results['curl'] = testCurl($url . '/data-records', $timeout, $connectTimeout);
    
    // Test 2: Use Guzzle if available
    if (class_exists('GuzzleHttp\Client')) {
        $results['guzzle'] = testGuzzle($url . '/data-records', $timeout, $connectTimeout);
    }
    
    // Test 3: Use Laravel's Http client
    $results['laravel_http'] = testLaravelHttp($url . '/data-records', $timeout, $connectTimeout);
    
    // Test 4: Use simple file_get_contents
    $results['file_get_contents'] = testFileGetContents($url . '/data-records', $timeout);
    
    $totalTime = microtime(true) - $startTime;
    $results['total_execution_time'] = round($totalTime, 2) . ' seconds';
    
    return $results;
}

function testCurl($url, $timeout, $connectTimeout) {
    $result = [
        'method' => 'cURL',
        'status' => 'Failed',
        'time' => 0,
        'error' => '',
        'response' => ''
    ];
    
    $startTime = microtime(true);
    
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PLAS-Diagnostic-Tool/1.0');
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        // Create a temporary file for the verbose output
        $verboseLog = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verboseLog);
        
        $response = curl_exec($ch);
        
        // Get verbose information
        rewind($verboseLog);
        $verboseOutput = stream_get_contents($verboseLog);
        fclose($verboseLog);
        
        if ($response !== false) {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            
            $result['status'] = 'Success';
            $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
            $result['response_size'] = strlen($response);
            $result['response_headers'] = $headers;
            $result['response'] = substr($body, 0, 1000) . (strlen($body) > 1000 ? '...' : ''); // Truncate long responses
            $result['verbose_log'] = $verboseOutput;
        } else {
            $result['error'] = curl_error($ch);
            $result['error_code'] = curl_errno($ch);
            $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
            $result['verbose_log'] = $verboseOutput;
        }
        
        curl_close($ch);
    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
        $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
    }
    
    return $result;
}

function testGuzzle($url, $timeout, $connectTimeout) {
    $result = [
        'method' => 'Guzzle',
        'status' => 'Failed',
        'time' => 0,
        'error' => '',
        'response' => ''
    ];
    
    $startTime = microtime(true);
    
    try {
        $client = new GuzzleHttp\Client([
            'timeout' => $timeout,
            'connect_timeout' => $connectTimeout
        ]);
        
        $response = $client->request('GET', $url, [
            'http_errors' => false
        ]);
        
        $result['status'] = 'Success';
        $result['http_code'] = $response->getStatusCode();
        $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
        $body = (string) $response->getBody();
        $result['response_size'] = strlen($body);
        $result['response'] = substr($body, 0, 1000) . (strlen($body) > 1000 ? '...' : ''); // Truncate long responses
        
    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
        $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
    }
    
    return $result;
}

function testLaravelHttp($url, $timeout, $connectTimeout) {
    $result = [
        'method' => 'Laravel Http',
        'status' => 'Failed',
        'time' => 0,
        'error' => '',
        'response' => ''
    ];
    
    $startTime = microtime(true);
    
    try {
        $response = Illuminate\Support\Facades\Http::timeout($timeout)
            ->connectTimeout($connectTimeout)
            ->retry(3, 2000)
            ->get($url);
        
        $result['status'] = $response->successful() ? 'Success' : 'Failed';
        $result['http_code'] = $response->status();
        $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
        
        if ($response->successful()) {
            $body = $response->body();
            $result['response_size'] = strlen($body);
            $result['response'] = substr($body, 0, 1000) . (strlen($body) > 1000 ? '...' : ''); // Truncate long responses
        } else {
            $result['error'] = 'HTTP Error ' . $response->status();
            $result['response'] = $response->body();
        }
        
    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
        $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
    }
    
    return $result;
}

function testFileGetContents($url, $timeout) {
    $result = [
        'method' => 'file_get_contents',
        'status' => 'Failed',
        'time' => 0,
        'error' => '',
        'response' => ''
    ];
    
    $startTime = microtime(true);
    
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'user_agent' => 'PLAS-Diagnostic-Tool/1.0',
                'follow_location' => 1,
                'max_redirects' => 5,
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $result['status'] = 'Success';
            $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
            $result['response_size'] = strlen($response);
            $result['response'] = substr($response, 0, 1000) . (strlen($response) > 1000 ? '...' : ''); // Truncate long responses
            
            if (isset($http_response_header)) {
                foreach ($http_response_header as $header) {
                    if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $header, $matches)) {
                        $result['http_code'] = intval($matches[1]);
                    }
                }
                $result['headers'] = $http_response_header;
            }
        } else {
            $result['error'] = 'file_get_contents failed';
            $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
            
            if (isset($http_response_header)) {
                $result['headers'] = $http_response_header;
                
                foreach ($http_response_header as $header) {
                    if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $header, $matches)) {
                        $result['http_code'] = intval($matches[1]);
                    }
                }
            }
        }
    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
        $result['time'] = round(microtime(true) - $startTime, 2) . ' seconds';
    }
    
    return $result;
}

// Run the API tests
$testResults = testApi($apiUrl, $timeout, $connectTimeout);

// Environment information
$environment = [
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'curl_version' => function_exists('curl_version') ? curl_version() : 'Not available',
    'ssl_support' => extension_loaded('openssl') ? 'Enabled' : 'Disabled',
    'laravel_version' => app()->version(),
    'api_config' => [
        'url' => $apiUrl,
        'timeout' => $timeout,
        'connect_timeout' => $connectTimeout
    ]
];

// Output results
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Connection Diagnostic Tool</title>
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
            max-width: 1200px;
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
        .test-result {
            margin-bottom: 30px;
            border-left: 5px solid #ccc;
            padding-left: 15px;
        }
        .test-result.success {
            border-left-color: green;
        }
        .test-result.failure {
            border-left-color: red;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .collapsible {
            cursor: pointer;
            background-color: #eee;
            padding: 10px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
        }
        .active, .collapsible:hover {
            background-color: #ddd;
        }
        .content {
            padding: 0 18px;
            display: none;
            overflow: hidden;
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>API Connection Diagnostic Tool</h1>
        <p>This tool tests the connection to the enrollment statistics API and displays detailed information.</p>
        
        <div class="card">
            <h2>Environment Information</h2>
            <table>
                <tr>
                    <th>PHP Version</th>
                    <td><?= $environment['php_version'] ?></td>
                </tr>
                <tr>
                    <th>Server Software</th>
                    <td><?= $environment['server_software'] ?></td>
                </tr>
                <tr>
                    <th>cURL Support</th>
                    <td>
                        <?php if (is_array($environment['curl_version'])): ?>
                            Version: <?= $environment['curl_version']['version'] ?><br>
                            SSL Version: <?= $environment['curl_version']['ssl_version'] ?>
                        <?php else: ?>
                            <?= $environment['curl_version'] ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>SSL Support</th>
                    <td><?= $environment['ssl_support'] ?></td>
                </tr>
                <tr>
                    <th>Laravel Version</th>
                    <td><?= $environment['laravel_version'] ?></td>
                </tr>
            </table>
            
            <h3>API Configuration</h3>
            <table>
                <tr>
                    <th>API URL</th>
                    <td><?= $environment['api_config']['url'] ?></td>
                </tr>
                <tr>
                    <th>Timeout</th>
                    <td><?= $environment['api_config']['timeout'] ?> seconds</td>
                </tr>
                <tr>
                    <th>Connect Timeout</th>
                    <td><?= $environment['api_config']['connect_timeout'] ?> seconds</td>
                </tr>
            </table>
        </div>
        
        <h2>Test Results</h2>
        <p>Total execution time: <?= $testResults['total_execution_time'] ?></p>
        
        <?php foreach (['curl', 'laravel_http', 'guzzle', 'file_get_contents'] as $method): ?>
            <?php if (isset($testResults[$method])): ?>
                <?php $result = $testResults[$method]; ?>
                <div class="test-result <?= $result['status'] === 'Success' ? 'success' : 'failure' ?>">
                    <h3><?= $result['method'] ?> Test</h3>
                    <p>
                        Status: 
                        <span class="<?= $result['status'] === 'Success' ? 'success' : 'error' ?>">
                            <?= $result['status'] ?>
                        </span>
                    </p>
                    <p>Execution Time: <?= $result['time'] ?></p>
                    
                    <?php if (isset($result['http_code'])): ?>
                        <p>HTTP Code: <?= $result['http_code'] ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($result['error'])): ?>
                        <p>Error: <span class="error"><?= htmlspecialchars($result['error']) ?></span></p>
                    <?php endif; ?>
                    
                    <?php if (isset($result['response_size'])): ?>
                        <p>Response Size: <?= $result['response_size'] ?> bytes</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($result['response'])): ?>
                        <button type="button" class="collapsible">View Response</button>
                        <div class="content">
                            <pre><?= htmlspecialchars($result['response']) ?></pre>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($result['response_headers'])): ?>
                        <button type="button" class="collapsible">View Response Headers</button>
                        <div class="content">
                            <pre><?= htmlspecialchars($result['response_headers']) ?></pre>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($result['verbose_log'])): ?>
                        <button type="button" class="collapsible">View Verbose Log</button>
                        <div class="content">
                            <pre><?= htmlspecialchars($result['verbose_log']) ?></pre>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <h2>What Next?</h2>
        <div class="card">
            <p>Based on the test results above:</p>
            <ul>
                <li>If all tests <span class="success">succeeded</span>, the API is reachable from your server. Check the API response content to ensure it's returning the expected data format.</li>
                <li>If all tests <span class="error">failed</span>, there may be a network connectivity issue between your server and the API endpoint.</li>
                <li>If some tests succeeded and others failed, review the error messages for clues about what might be causing the issue.</li>
            </ul>
            
            <h3>Common Issues and Solutions:</h3>
            <ol>
                <li><strong>Firewall blocking outbound connections:</strong> Verify that your server's firewall allows outbound connections to the API endpoint.</li>
                <li><strong>SSL/TLS issues:</strong> Check that your server trusts the SSL certificate used by the API endpoint.</li>
                <li><strong>DNS resolution problems:</strong> Ensure that the API hostname resolves correctly from your server.</li>
                <li><strong>Rate limiting:</strong> The API may be rate limiting requests from your server.</li>
                <li><strong>Incorrect API URL or endpoint:</strong> Verify the API URL is correct.</li>
            </ol>
        </div>
    </div>
    
    <script>
    var coll = document.getElementsByClassName("collapsible");
    var i;

    for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    }
    </script>
</body>
</html> 