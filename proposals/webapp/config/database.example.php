<?php
/**
 * Database Configuration
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to config/database.php (remove .example extension)
 * 2. Update the values below with your database credentials
 * 3. Make sure this file has appropriate permissions (readable by web server only)
 */

// Database Configuration
define('DB_HOST', 'localhost');          // Database host (usually localhost)
define('DB_NAME', 'warming_room_db');    // Database name
define('DB_USER', 'root');               // Database username
define('DB_PASS', '');                   // Database password
define('DB_CHARSET', 'utf8mb4');         // Character set

// Database connection error handling
define('DB_ERROR_MODE', 'exception');    // Options: exception, warning, silent

// Application settings
define('APP_NAME', 'Warming Room Proposal System');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', false);             // Set to false in production

// Timezone
date_default_timezone_set('America/Toronto');

// Error reporting (disable in production)
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
