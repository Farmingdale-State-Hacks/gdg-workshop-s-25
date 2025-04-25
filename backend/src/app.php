<?php
// Global configuration
$config = [
    'app_name' => 'DetectiveCase',
    'version' => '1.0.0',
    'python_api_url' => 'http://localhost:8002/api', // URL to Python server
    'debug' => true
];

// Set configuration as global
$GLOBALS['config'] = $config;

// Helper function for making HTTP requests to the Python server
function makeApiRequest($endpoint, $params = []) {
    global $config;
    
    $url = $config['python_api_url'] . '/' . $endpoint;
    
    // Add query parameters if any
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Execute the request
    $response = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return json_encode([
            'status' => 'error',
            'message' => 'cURL Error: ' . $error
        ]);
    }
    
    // Close cURL
    curl_close($ch);
    
    // Return the response
    return $response;
}

// Log function for debugging
function logMessage($message, $level = 'info') {
    $logFile = BASE_PATH . '/logs/' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[$timestamp] [$level]: $message" . PHP_EOL;
    
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}

// Create controller directory if it doesn't exist
$controllersDir = BASE_PATH . '/src/Controllers';
if (!is_dir($controllersDir)) {
    mkdir($controllersDir, 0777, true);
}

// Create models directory if it doesn't exist
$modelsDir = BASE_PATH . '/src/Models';
if (!is_dir($modelsDir)) {
    mkdir($modelsDir, 0777, true);
} 