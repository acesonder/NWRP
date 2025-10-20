<?php
/**
 * Database Connection Helper
 * Provides database connection and common database operations
 */

// Check if config exists
if (!file_exists(__DIR__ . '/../config/database.php')) {
    die('Error: Database configuration file not found. Please copy config/database.example.php to config/database.php and configure it.');
}

require_once __DIR__ . '/../config/database.php';

class Database {
    private static $connection = null;
    
    /**
     * Get database connection (singleton pattern)
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                self::$connection = new PDO($dsn, DB_USER, DB_PASS);
                
                // Set error mode
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                
            } catch (PDOException $e) {
                self::handleError("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
    
    /**
     * Execute a query and return results
     */
    public static function query($sql, $params = []) {
        try {
            $conn = self::getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            self::handleError("Query failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all rows from query
     */
    public static function fetchAll($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    /**
     * Get single row from query
     */
    public static function fetchOne($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt ? $stmt->fetch() : null;
    }
    
    /**
     * Execute insert/update/delete and return affected rows
     */
    public static function execute($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt ? $stmt->rowCount() : 0;
    }
    
    /**
     * Get last inserted ID
     */
    public static function lastInsertId() {
        return self::getConnection()->lastInsertId();
    }
    
    /**
     * Log activity
     */
    public static function logActivity($action, $description, $ip = null) {
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        
        $sql = "INSERT INTO activity_log (action, description, ip_address) VALUES (?, ?, ?)";
        self::execute($sql, [$action, $description, $ip]);
    }
    
    /**
     * Handle database errors
     */
    private static function handleError($message) {
        if (DEBUG_MODE) {
            die("<div style='background: #ffebee; border: 1px solid #c62828; padding: 15px; margin: 10px; border-radius: 4px;'>"
                . "<strong>Database Error:</strong> " . htmlspecialchars($message) . "</div>");
        } else {
            error_log($message);
            die("A database error occurred. Please contact the administrator.");
        }
    }
    
    /**
     * Test database connection
     */
    public static function testConnection() {
        try {
            $conn = self::getConnection();
            return $conn !== null;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Helper functions for common operations
function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
