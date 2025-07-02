<?php
/**
 * Form Debug Script
 * This script helps identify issues with form submissions
 * especially with DELETE requests in production.
 */

// Log the request method and headers
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestHeaders = getallheaders();

// Get POST data
$postData = $_POST;

// Check for JSON data in request body
$jsonData = file_get_contents('php://input');
$jsonDataDecoded = null;

if (!empty($jsonData)) {
    $jsonDataDecoded = json_decode($jsonData, true);
}

// Set content type to JSON
header('Content-Type: application/json');

// Return diagnostic information
echo json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'request_method' => $requestMethod,
    'headers' => $requestHeaders,
    'post_data' => $postData,
    'raw_input' => $jsonData,
    'json_data' => $jsonDataDecoded,
    'server' => [
        'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'unknown',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
        'https' => isset($_SERVER['HTTPS']) ? 'on' : 'off',
    ],
    'php_version' => phpversion(),
]); 