<?php
/**
 * Test Database Connection
 */

require_once '../includes/database.php';

header('Content-Type: application/json');

try {
    $connected = Database::testConnection();
    
    if ($connected) {
        // Try a simple query
        $result = Database::fetchOne("SELECT COUNT(*) as count FROM proposals");
        
        json_response([
            'success' => true,
            'message' => 'Database connection successful! Found ' . $result['count'] . ' proposals.',
            'data' => [
                'connected' => true,
                'proposal_count' => $result['count']
            ]
        ]);
    } else {
        json_response([
            'success' => false,
            'message' => 'Failed to connect to database',
            'data' => ['connected' => false]
        ], 500);
    }
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => ['connected' => false]
    ], 500);
}
?>
