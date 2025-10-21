<?php
/**
 * Activations API Endpoint
 * Handles warming room activation management
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));
$activationId = isset($pathParts[3]) ? intval($pathParts[3]) : null;

switch ($method) {
    case 'GET':
        if ($activationId) {
            getActivation($activationId);
        } else {
            getActivations();
        }
        break;
        
    case 'POST':
        createActivation();
        break;
        
    case 'PUT':
        if ($activationId) {
            updateActivation($activationId);
        } else {
            sendResponse(['error' => 'Activation ID required for update'], 400);
        }
        break;
        
    case 'DELETE':
        if ($activationId) {
            deleteActivation($activationId);
        } else {
            sendResponse(['error' => 'Activation ID required for deletion'], 400);
        }
        break;
        
    default:
        sendResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get all activations
 */
function getActivations() {
    global $db;
    
    $status = $_GET['status'] ?? null;
    $current = $_GET['current'] ?? null;
    
    $sql = "SELECT a.*, 
                   CONCAT(v.first_name, ' ', v.last_name) as coordinator_name,
                   COUNT(s.id) as total_shifts,
                   COUNT(CASE WHEN s.status = 'completed' THEN 1 END) as completed_shifts
            FROM activations a
            LEFT JOIN volunteers v ON a.coordinator_id = v.id
            LEFT JOIN shifts s ON a.id = s.activation_id";
    
    $params = [];
    $conditions = [];
    
    if ($status) {
        $conditions[] = "a.status = :status";
        $params['status'] = $status;
    }
    
    if ($current) {
        $conditions[] = "CURDATE() BETWEEN a.start_date AND a.end_date";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " GROUP BY a.id ORDER BY a.start_date DESC";
    
    $activations = $db->fetchAll($sql, $params);
    
    sendResponse($activations);
}

/**
 * Get single activation with details
 */
function getActivation($id) {
    global $db;
    
    $activation = $db->fetch("
        SELECT a.*, 
               CONCAT(v.first_name, ' ', v.last_name) as coordinator_name,
               v.email as coordinator_email,
               v.phone as coordinator_phone
        FROM activations a
        LEFT JOIN volunteers v ON a.coordinator_id = v.id
        WHERE a.id = :id", ['id' => $id]);
    
    if (!$activation) {
        sendResponse(['error' => 'Activation not found'], 404);
    }
    
    // Get shifts for this activation
    $shifts = $db->fetchAll("
        SELECT s.*, st.name as template_name,
               COUNT(sa.id) as assigned_volunteers,
               COUNT(CASE WHEN sa.status = 'confirmed' THEN 1 END) as confirmed_volunteers
        FROM shifts s
        LEFT JOIN shift_templates st ON s.template_id = st.id
        LEFT JOIN shift_assignments sa ON s.id = sa.shift_id AND sa.status != 'cancelled'
        WHERE s.activation_id = :id
        GROUP BY s.id
        ORDER BY s.shift_date, s.start_time", ['id' => $id]);
    
    $activation['shifts'] = $shifts;
    
    sendResponse($activation);
}

/**
 * Create new activation
 */
function createActivation() {
    global $db;
    
    $input = getJsonInput();
    sanitizeInput($input);
    
    validateRequired($input, ['name', 'start_date', 'end_date', 'location']);
    
    $activationData = [
        'name' => $input['name'],
        'start_date' => $input['start_date'],
        'end_date' => $input['end_date'],
        'location' => $input['location'],
        'coordinator_id' => $input['coordinator_id'] ?? null,
        'notes' => $input['notes'] ?? null
    ];
    
    try {
        $activationId = $db->insert('activations', $activationData);
        sendResponse(['id' => $activationId, 'message' => 'Activation created successfully'], 201);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to create activation: ' . $e->getMessage()], 500);
    }
}

/**
 * Update existing activation
 */
function updateActivation($id) {
    global $db;
    
    $input = getJsonInput();
    sanitizeInput($input);
    
    // Check if activation exists
    $existing = $db->fetch("SELECT id FROM activations WHERE id = :id", ['id' => $id]);
    if (!$existing) {
        sendResponse(['error' => 'Activation not found'], 404);
    }
    
    $allowedFields = ['name', 'start_date', 'end_date', 'location', 'coordinator_id', 'status', 'notes'];
    
    $updateData = [];
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateData[$field] = $input[$field];
        }
    }
    
    if (empty($updateData)) {
        sendResponse(['error' => 'No valid fields to update'], 400);
    }
    
    try {
        $db->update('activations', $updateData, 'id = :id', ['id' => $id]);
        sendResponse(['message' => 'Activation updated successfully']);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to update activation: ' . $e->getMessage()], 500);
    }
}

/**
 * Delete activation
 */
function deleteActivation($id) {
    global $db;
    
    // Check if activation exists
    $existing = $db->fetch("SELECT id FROM activations WHERE id = :id", ['id' => $id]);
    if (!$existing) {
        sendResponse(['error' => 'Activation not found'], 404);
    }
    
    try {
        $db->update('activations', ['status' => 'cancelled'], 'id = :id', ['id' => $id]);
        sendResponse(['message' => 'Activation cancelled successfully']);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to cancel activation: ' . $e->getMessage()], 500);
    }
}