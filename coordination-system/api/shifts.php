<?php
/**
 * Shifts and Scheduling API Endpoint
 * Handles shift creation, assignment, and scheduling
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));
$shiftId = isset($pathParts[3]) ? intval($pathParts[3]) : null;

switch ($method) {
    case 'GET':
        if ($shiftId) {
            getShift($shiftId);
        } else {
            getShifts();
        }
        break;
        
    case 'POST':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'assign':
                    assignVolunteer();
                    break;
                case 'generate':
                    generateShifts();
                    break;
                default:
                    createShift();
            }
        } else {
            createShift();
        }
        break;
        
    case 'PUT':
        if ($shiftId) {
            updateShift($shiftId);
        } else {
            sendResponse(['error' => 'Shift ID required for update'], 400);
        }
        break;
        
    case 'DELETE':
        if ($shiftId) {
            deleteShift($shiftId);
        } else {
            sendResponse(['error' => 'Shift ID required for deletion'], 400);
        }
        break;
        
    default:
        sendResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get shifts with optional filtering
 */
function getShifts() {
    global $db;
    
    $activationId = $_GET['activation_id'] ?? null;
    $date = $_GET['date'] ?? null;
    $status = $_GET['status'] ?? null;
    $upcoming = $_GET['upcoming'] ?? null;
    
    $sql = "SELECT s.*, 
                   a.name as activation_name,
                   st.name as template_name,
                   COUNT(sa.id) as assigned_volunteers,
                   COUNT(CASE WHEN sa.status = 'confirmed' THEN 1 END) as confirmed_volunteers
            FROM shifts s
            JOIN activations a ON s.activation_id = a.id
            LEFT JOIN shift_templates st ON s.template_id = st.id
            LEFT JOIN shift_assignments sa ON s.id = sa.shift_id AND sa.status != 'cancelled'";
    
    $params = [];
    $conditions = [];
    
    if ($activationId) {
        $conditions[] = "s.activation_id = :activation_id";
        $params['activation_id'] = $activationId;
    }
    
    if ($date) {
        $conditions[] = "s.shift_date = :date";
        $params['date'] = $date;
    }
    
    if ($status) {
        $conditions[] = "s.status = :status";
        $params['status'] = $status;
    }
    
    if ($upcoming) {
        $conditions[] = "CONCAT(s.shift_date, ' ', s.start_time) >= NOW()";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " GROUP BY s.id ORDER BY s.shift_date, s.start_time";
    
    $shifts = $db->fetchAll($sql, $params);
    
    sendResponse($shifts);
}

/**
 * Get single shift with assignments
 */
function getShift($id) {
    global $db;
    
    $shift = $db->fetch("
        SELECT s.*, 
               a.name as activation_name,
               st.name as template_name
        FROM shifts s
        JOIN activations a ON s.activation_id = a.id
        LEFT JOIN shift_templates st ON s.template_id = st.id
        WHERE s.id = :id", ['id' => $id]);
    
    if (!$shift) {
        sendResponse(['error' => 'Shift not found'], 404);
    }
    
    // Get assignments
    $assignments = $db->fetchAll("
        SELECT sa.*, 
               CONCAT(v.first_name, ' ', v.last_name) as volunteer_name,
               v.phone, v.email, v.preferred_contact_method
        FROM shift_assignments sa
        JOIN volunteers v ON sa.volunteer_id = v.id
        WHERE sa.shift_id = :id
        ORDER BY sa.role DESC, v.last_name", ['id' => $id]);
    
    $shift['assignments'] = $assignments;
    
    sendResponse($shift);
}

/**
 * Create new shift
 */
function createShift() {
    global $db;
    
    $input = getJsonInput();
    sanitizeInput($input);
    
    validateRequired($input, ['activation_id', 'template_id', 'shift_date']);
    
    // Get template details
    $template = $db->fetch("SELECT * FROM shift_templates WHERE id = :id", ['id' => $input['template_id']]);
    if (!$template) {
        sendResponse(['error' => 'Shift template not found'], 404);
    }
    
    $shiftData = [
        'activation_id' => $input['activation_id'],
        'template_id' => $input['template_id'],
        'shift_date' => $input['shift_date'],
        'start_time' => $template['start_time'],
        'end_time' => $template['end_time'],
        'min_volunteers' => $template['min_volunteers'],
        'notes' => $input['notes'] ?? null
    ];
    
    try {
        $shiftId = $db->insert('shifts', $shiftData);
        sendResponse(['id' => $shiftId, 'message' => 'Shift created successfully'], 201);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to create shift: ' . $e->getMessage()], 500);
    }
}

/**
 * Generate shifts for an activation based on templates
 */
function generateShifts() {
    global $db;
    
    $input = getJsonInput();
    sanitizeInput($input);
    
    validateRequired($input, ['activation_id', 'start_date', 'end_date', 'template_ids']);
    
    // Get activation details
    $activation = $db->fetch("SELECT * FROM activations WHERE id = :id", ['id' => $input['activation_id']]);
    if (!$activation) {
        sendResponse(['error' => 'Activation not found'], 404);
    }
    
    $startDate = new DateTime($input['start_date']);
    $endDate = new DateTime($input['end_date']);
    $templateIds = $input['template_ids'];
    
    $createdShifts = 0;
    
    try {
        // Generate shifts for each day in the range
        while ($startDate <= $endDate) {
            $dateStr = $startDate->format('Y-m-d');
            
            foreach ($templateIds as $templateId) {
                $template = $db->fetch("SELECT * FROM shift_templates WHERE id = :id", ['id' => $templateId]);
                if ($template) {
                    // Check if shift already exists
                    $existing = $db->fetch("
                        SELECT id FROM shifts 
                        WHERE activation_id = :activation_id 
                        AND template_id = :template_id 
                        AND shift_date = :shift_date", [
                        'activation_id' => $input['activation_id'],
                        'template_id' => $templateId,
                        'shift_date' => $dateStr
                    ]);
                    
                    if (!$existing) {
                        $shiftData = [
                            'activation_id' => $input['activation_id'],
                            'template_id' => $templateId,
                            'shift_date' => $dateStr,
                            'start_time' => $template['start_time'],
                            'end_time' => $template['end_time'],
                            'min_volunteers' => $template['min_volunteers']
                        ];
                        
                        $db->insert('shifts', $shiftData);
                        $createdShifts++;
                    }
                }
            }
            
            $startDate->add(new DateInterval('P1D'));
        }
        
        sendResponse(['message' => "Generated {$createdShifts} shifts successfully"]);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to generate shifts: ' . $e->getMessage()], 500);
    }
}

/**
 * Assign volunteer to shift
 */
function assignVolunteer() {
    global $db;
    
    $input = getJsonInput();
    sanitizeInput($input);
    
    validateRequired($input, ['shift_id', 'volunteer_id']);
    
    $role = $input['role'] ?? 'volunteer';
    
    // Check if shift and volunteer exist
    $shift = $db->fetch("SELECT * FROM shifts WHERE id = :id", ['id' => $input['shift_id']]);
    if (!$shift) {
        sendResponse(['error' => 'Shift not found'], 404);
    }
    
    $volunteer = $db->fetch("SELECT * FROM volunteers WHERE id = :id AND status = 'active'", ['id' => $input['volunteer_id']]);
    if (!$volunteer) {
        sendResponse(['error' => 'Volunteer not found or inactive'], 404);
    }
    
    // Check if already assigned
    $existing = $db->fetch("
        SELECT id FROM shift_assignments 
        WHERE shift_id = :shift_id AND volunteer_id = :volunteer_id", [
        'shift_id' => $input['shift_id'],
        'volunteer_id' => $input['volunteer_id']
    ]);
    
    if ($existing) {
        sendResponse(['error' => 'Volunteer already assigned to this shift'], 409);
    }
    
    try {
        $assignmentData = [
            'shift_id' => $input['shift_id'],
            'volunteer_id' => $input['volunteer_id'],
            'role' => $role,
            'notes' => $input['notes'] ?? null
        ];
        
        $assignmentId = $db->insert('shift_assignments', $assignmentData);
        
        // TODO: Send notification to volunteer
        
        sendResponse(['id' => $assignmentId, 'message' => 'Volunteer assigned successfully'], 201);
        
    } catch (Exception $e) {
        sendResponse(['error' => 'Failed to assign volunteer: ' . $e->getMessage()], 500);
    }
}