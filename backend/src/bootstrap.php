<?php
// Define the base path
define('BASE_PATH', dirname(__DIR__));

// Autoload function for classes
spl_autoload_register(function ($class) {
    $prefix = 'DetectiveCase\\';
    $base_dir = BASE_PATH . '/src/';
    
    // Check if the class uses the prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace namespace separators with directory separators
    // and add .php suffix
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Load environment configuration
$dotenv = __DIR__ . '/.env';
if (file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Create directories if they don't exist
$dirs = [
    BASE_PATH . '/logs',
    BASE_PATH . '/cache'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Set error reporting in development
if (getenv('APP_ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set default timezone
date_default_timezone_set('America/New_York');

// Initialize the application
require_once BASE_PATH . '/src/app.php'; 