#!/bin/bash

# NWRP Server Startup Script
# This script starts the built-in PHP server for testing both applications

echo "🏠 NWRP - Northumberland Warming Room Project"
echo "Starting development server..."
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 7.4 or higher."
    exit 1
fi

# Get PHP version
PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
echo "✅ PHP Version: $PHP_VERSION"

# Check if we're in the right directory
if [ ! -f "index.html" ]; then
    echo "❌ Please run this script from the NWRP root directory"
    exit 1
fi

# Create a simple router for the development server
cat > router.php << 'EOF'
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
EOF

# Start the server
PORT=8080
HOST=localhost

echo ""
echo "🚀 Starting server on http://$HOST:$PORT"
echo ""
echo "📁 Available Applications:"
echo "   🌟 Main Portal: http://$HOST:$PORT"  
echo "   👥 Coordination System: http://$HOST:$PORT/coordination-system/"
echo "   💡 Proposals System: http://$HOST:$PORT/proposals/webapp/"
echo ""
echo "⏹️  Press Ctrl+C to stop the server"
echo ""

# Check if port is available
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  Port $PORT is already in use. Trying port 8081..."
    PORT=8081
fi

# Start PHP built-in server
php -S $HOST:$PORT router.php

# Cleanup
rm -f router.php
echo ""
echo "👋 Server stopped. Thank you for using NWRP!"