<?php
// Simple router for PHP built-in server

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle coordination system API routes
if (preg_match('/^\/coordination-system\/api\/(.+)\.php/', $uri, $matches)) {
    $apiFile = __DIR__ . '/coordination-system/api/' . $matches[1] . '.php';
    if (file_exists($apiFile)) {
        require $apiFile;
        exit;
    }
}

// Handle proposals API routes  
if (preg_match('/^\/proposals\/webapp\/api\/(.+)\.php/', $uri, $matches)) {
    $apiFile = __DIR__ . '/proposals/webapp/api/' . $matches[1] . '.php';
    if (file_exists($apiFile)) {
        require $apiFile;
        exit;
    }
}

// Handle static files and directories
$filePath = __DIR__ . $uri;

// If it's a directory, look for index files
if (is_dir($filePath)) {
    $indexFiles = ['index.html', 'index.php'];
    foreach ($indexFiles as $index) {
        if (file_exists($filePath . '/' . $index)) {
            $filePath = $filePath . '/' . $index;
            break;
        }
    }
}

// Serve the file if it exists
if (file_exists($filePath) && !is_dir($filePath)) {
    // Set appropriate content type
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    switch ($ext) {
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        case 'json':
            header('Content-Type: application/json');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        case 'svg':
            header('Content-Type: image/svg+xml');
            break;
        case 'php':
            // Execute PHP files
            require $filePath;
            exit;
        default:
            // Let PHP handle other files
            return false;
    }
    
    if ($ext !== 'php') {
        readfile($filePath);
    }
    exit;
}

// Return false to use PHP's built-in handling
return false;
