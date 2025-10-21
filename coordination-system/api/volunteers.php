<?php
/**
 * Volunteers API Endpoint
 * Handles volunteer registration, updates, and management
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));
$volunteerId = isset($pathParts[3]) ? intval($pathParts[3]) : null;

switch ($method) {
    case 'GET':
        if ($volunteerId) {
            getVolunteer($volunteerId);
        } else {
            getVolunteers();
        }
        break;
        
    case 'POST':
        createVolunteer();
        break;
        
    case 'PUT':
        if ($volunteerId) {
            updateVolunteer($volunteerId);
        } else {
            sendResponse(['error' => 'Volunteer ID required for update'], 400);
        }
        break;
        
    case 'DELETE':
        if ($volunteerId) {
            deleteVolunteer($volunteerId);
        } else {
            sendResponse(['error' => 'Volunteer ID required for deletion'], 400);
        }
        break;
        
    default:
        sendResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get all volunteers with optional filtering
 */
function getVolunteers() {
    global $db;
    
    $status = $_GET['status'] ?? null;
    $skill = $_GET['skill'] ?? null;
    $available = $_GET['available'] ?? null;
    
    $sql = "SELECT v.*, 
                   GROUP_CONCAT(CONCAT(vs.skill_type, ':', vs.skill_description) SEPARATOR ';') as skills
            FROM volunteers v
            LEFT JOIN volunteer_skills vs ON v.id = vs.volunteer_id";
    
    $params = [];
    $conditions = [];
    
    if ($status) {
        $conditions[] = "v.status = :status";
        $params['status'] = $status;
    }
    
    if ($skill) {
        $conditions[] = "vs.skill_type = :skill";
        $params['skill'] = $skill;
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " GROUP BY v.id ORDER BY v.last_name, v.first_name";
    
    $volunteers = $db->fetchAll($sql, $params);
    
    // Format skills for each volunteer
    foreach ($volunteers as &$volunteer) {
        $volunteer['skills'] = parseSkills($volunteer['skills']);
        unset($volunteer['emergency_contact_name'], $volunteer['emergency_contact_phone']); // Hide sensitive info in list
    }
    
    sendResponse($volunteers);
}

/**
 * Get single volunteer with full details
 */
function getVolunteer($id) {
    global $db;
    
    $volunteer = $db->fetch("SELECT * FROM volunteers WHERE id = :id", ['id' => $id]);
    
    if (!$volunteer) {
        sendResponse(['error' => 'Volunteer not found'], 404);
    }
    
    // Get skills
    $skills = $db->fetchAll("SELECT * FROM volunteer_skills WHERE volunteer_id = :id", ['id' => $id]);
    $volunteer['skills'] = $skills;
    
    // Get availability
    $availability = $db->fetchAll("SELECT * FROM volunteer_availability WHERE volunteer_id = :id ORDER BY day_of_week, start_time", ['id' => $id]);
    $volunteer['availability'] = $availability;
    
    // Get recent shift assignments
    $recentShifts = $db->fetchAll("
        SELECT sa.*, s.shift_date, s.start_time, s.end_time, a.name as activation_name
        FROM shift_assignments sa
        JOIN shifts s ON sa.shift_id = s.id
        JOIN activations a ON s.activation_id = a.id
        WHERE sa.volunteer_id = :id
        ORDER BY s.shift_date DESC, s.start_time DESC
        LIMIT 10", ['id' => $id]);
    $volunteer['recent_shifts'] = $recentShifts;
    
    sendResponse($volunteer);
}

/**
 * Create new volunteer
 */
function createVolunteer() {
    global $db;
    
    $input = getJsonInput();
    sanitizeInput($input);
    
    validateRequired($input, ['first_name', 'last_name', 'email', 'phone']);
    
    // Check if email already exists
    $existing = $db->fetch("SELECT id FROM volunteers WHERE email = :email", ['email' => $input['email']]);
    if ($existing) {
        sendResponse(['error' => 'Email address already registered'], 409);
    }
    
    // Prepare volunteer data
    $volunteerData = [
        'first_name' => $input['first_name'],
        'last_name' => $input['last_name'],
        'email' => $input['email'],
        'phone' => $input['phone'],
        'alt_phone' => $input['alt_phone'] ?? null,
        'address' => $input['address'] ?? null,
        'emergency_contact_name' => $input['emergency_contact_name'] ?? null,
        'emergency_contact_phone' => $input['emergency_contact_phone'] ?? null,
        'preferred_contact_method' => $input['preferred_contact_method'] ?? 'email',
        'availability_notes' => $input['availability_notes'] ?? null
    ];
    
    try {
        $volunteerId = $db->insert('volunteers', $volunteerData);
        
        // Add skills if provided
        if (isset($input['skills']) && is_array($input['skills'])) {
            foreach ($input['skills'] as $skill) {
                $skillData = [
                    'volunteer_id' => $volunteerId,
                    'skill_type' => $skill['type'],
                    'skill_description' => $skill['description'],
                    'certification_date' => $skill['certification_date'] ?? null,
                    'expiration_date' => $skill['expiration_date'] ?? null
                ];
                $db->insert('volunteer_skills', $skillData);
            }
        }
        
        // Add availability if provided
        if (isset($input['availability']) && is_array($input['availability'])) {
            foreach ($input['availability'] as $avail) {
                $availData = [
                    'volunteer_id' => $volunteerId,
                    'day_of_week' => $avail['day_of_week'],
                    'start_time' => $avail['start_time'],
                    'end_time' => $avail['end_time'],
                    'is_preferred' => $avail['is_preferred'] ?? false
                ];
                $db->insert('volunteer_availability', $availData);
            }
        }
        
        sendResponse(['id' => $volunteerId, 'message' => 'Volunteer created successfully'], 201);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to create volunteer: ' . $e->getMessage()], 500);
    }
}

/**
 * Update existing volunteer
 */
function updateVolunteer($id) {
    global $db;
    
    $input = getJsonInput();
    sanitizeInput($input);
    
    // Check if volunteer exists
    $existing = $db->fetch("SELECT id FROM volunteers WHERE id = :id", ['id' => $id]);
    if (!$existing) {
        sendResponse(['error' => 'Volunteer not found'], 404);
    }
    
    // Prepare update data (only include provided fields)
    $allowedFields = ['first_name', 'last_name', 'email', 'phone', 'alt_phone', 'address', 
                     'emergency_contact_name', 'emergency_contact_phone', 'preferred_contact_method',
                     'status', 'training_completed', 'training_date', 'background_check_completed',
                     'background_check_date', 'availability_notes'];
    
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
        $db->update('volunteers', $updateData, 'id = :id', ['id' => $id]);
        sendResponse(['message' => 'Volunteer updated successfully']);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to update volunteer: ' . $e->getMessage()], 500);
    }
}

/**
 * Delete volunteer (soft delete - set status to inactive)
 */
function deleteVolunteer($id) {
    global $db;
    
    // Check if volunteer exists
    $existing = $db->fetch("SELECT id FROM volunteers WHERE id = :id", ['id' => $id]);
    if (!$existing) {
        sendResponse(['error' => 'Volunteer not found'], 404);
    }
    
    try {
        $db->update('volunteers', ['status' => 'inactive'], 'id = :id', ['id' => $id]);
        sendResponse(['message' => 'Volunteer deactivated successfully']);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to deactivate volunteer: ' . $e->getMessage()], 500);
    }
}

/**
 * Parse skills string into array
 */
function parseSkills($skillsString) {
    if (empty($skillsString)) return [];
    
    $skills = [];
    $skillParts = explode(';', $skillsString);
    
    foreach ($skillParts as $skill) {
        if (strpos($skill, ':') !== false) {
            list($type, $description) = explode(':', $skill, 2);
            $skills[] = ['type' => $type, 'description' => $description];
        }
    }
    
    return $skills;
}