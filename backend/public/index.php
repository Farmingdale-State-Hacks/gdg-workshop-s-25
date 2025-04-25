<?php
require_once '../src/bootstrap.php';

// Set headers for CORS and JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse the request URI to determine the endpoint
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// API endpoints routing
$endpoints = [
    'api' => [
        'crimes' => 'getCrimes',
        'crime-types' => 'getCrimeTypes',
        'crime-heatmap' => 'getCrimeHeatmap',
        'neighborhood-stats' => 'getNeighborhoodStats',
        'health' => 'healthCheck'
    ]
];

// Check if this is an API request
if (isset($uri[1]) && $uri[1] === 'api' && isset($uri[2])) {
    $endpoint = $uri[2];
    
    if (isset($endpoints['api'][$endpoint])) {
        $method = $endpoints['api'][$endpoint];
        $controller = new \DetectiveCase\Controllers\CrimeController();
        
        // Call the appropriate method on the controller
        echo $controller->$method();
        exit;
    }
}

// If no valid endpoint is found, return 404
http_response_code(404);
echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']); 