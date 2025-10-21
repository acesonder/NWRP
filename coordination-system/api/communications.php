<?php
/**
 * Communications API Endpoint
 * Handles messaging and notifications
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getCommunications();
        break;
        
    case 'POST':
        sendCommunication();
        break;
        
    default:
        sendResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get communications with filtering
 */
function getCommunications() {
    global $db;
    
    $recent = $_GET['recent'] ?? null;
    $type = $_GET['type'] ?? null;
    $limit = $_GET['limit'] ?? 20;
    
    $sql = "SELECT c.*, 
                   CASE 
                       WHEN c.recipient_id IS NOT NULL THEN CONCAT(v.first_name, ' ', v.last_name)
                       ELSE c.recipient_type
                   END as recipient_name
            FROM communications c
            LEFT JOIN volunteers v ON c.recipient_id = v.id";
    
    $params = [];
    $conditions = [];
    
    if ($type) {
        $conditions[] = "c.type = :type";
        $params['type'] = $type;
    }
    
    if ($recent) {
        $conditions[] = "c.sent_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " ORDER BY c.sent_at DESC LIMIT :limit";
    $params['limit'] = intval($limit);
    
    $communications = $db->fetchAll($sql, $params);
    
    sendResponse($communications);
}

/**
 * Send new communication
 */
function sendCommunication() {
    global $db;
    
    $input = getJsonInput();
    sanitizeInput($input);
    
    validateRequired($input, ['type', 'recipients', 'subject', 'message']);
    
    $commData = [
        'type' => $input['type'],
        'method' => $input['method'] ?? 'email',
        'recipient_type' => $input['recipients'],
        'subject' => $input['subject'],
        'message' => $input['message'],
        'related_shift_id' => $input['shift_id'] ?? null,
        'related_activation_id' => $input['activation_id'] ?? null
    ];
    
    try {
        // Get recipients based on type
        $recipients = getRecipients($input['recipients'], $input['shift_id'] ?? null);
        
        $sentCount = 0;
        foreach ($recipients as $recipient) {
            $commData['recipient_id'] = $recipient['id'];
            $commData['delivery_status'] = 'sent'; // In real implementation, this would be 'pending' until actually sent
            
            $db->insert('communications', $commData);
            
            // Here you would integrate with email/SMS service
            // For now, we'll just mark as sent
            $sentCount++;
        }
        
        sendResponse([
            'message' => "Message sent to {$sentCount} recipients",
            'recipients_count' => $sentCount
        ]);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to send communication: ' . $e->getMessage()], 500);
    }
}

/**
 * Get recipients based on type
 */
function getRecipients($recipientType, $shiftId = null) {
    global $db;
    
    switch ($recipientType) {
        case 'all_volunteers':
            return $db->fetchAll("SELECT id, first_name, last_name, email, phone, preferred_contact_method FROM volunteers WHERE status = 'active'");
            
        case 'shift_volunteers':
            if (!$shiftId) {
                throw new Exception('Shift ID required for shift volunteers');
            }
            return $db->fetchAll("
                SELECT v.id, v.first_name, v.last_name, v.email, v.phone, v.preferred_contact_method
                FROM volunteers v
                JOIN shift_assignments sa ON v.id = sa.volunteer_id
                WHERE sa.shift_id = :shift_id AND sa.status != 'cancelled'", ['shift_id' => $shiftId]);
            
        case 'coordinators':
            return $db->fetchAll("
                SELECT DISTINCT v.id, v.first_name, v.last_name, v.email, v.phone, v.preferred_contact_method
                FROM volunteers v
                JOIN volunteer_skills vs ON v.id = vs.volunteer_id
                WHERE v.status = 'active' AND vs.skill_type = 'leadership'");
            
        default:
            throw new Exception('Invalid recipient type');
    }
}