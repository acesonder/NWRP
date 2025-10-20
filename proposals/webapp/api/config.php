<?php
/**
 * Configuration API
 * Handles system configuration
 */

require_once '../includes/database.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'get';

try {
    switch ($action) {
        case 'get':
            getConfig();
            break;
        case 'update':
            updateConfig();
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}

function getConfig() {
    $sql = "SELECT config_key, config_value, description FROM system_config";
    $rows = Database::fetchAll($sql);
    
    $config = [];
    foreach ($rows as $row) {
        $config[$row['config_key']] = $row['config_value'];
    }
    
    json_response([
        'success' => true,
        'data' => $config
    ]);
}

function updateConfig() {
    $updates = 0;
    
    foreach ($_POST as $key => $value) {
        if ($key === 'action') continue;
        
        $sql = "UPDATE system_config SET config_value = ? WHERE config_key = ?";
        $affected = Database::execute($sql, [$value, $key]);
        $updates += $affected;
    }
    
    Database::logActivity('config_updated', "System configuration updated");
    
    json_response([
        'success' => true,
        'message' => "Updated $updates configuration values"
    ]);
}
?>
