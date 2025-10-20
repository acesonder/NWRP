<?php
/**
 * Proposals API
 * Handles CRUD operations for proposals
 */

require_once '../includes/database.php';

header('Content-Type: application/json');

// Get action from request
$action = $_REQUEST['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            listProposals();
            break;
        case 'get':
            getProposal();
            break;
        case 'create':
            createProposal();
            break;
        case 'update':
            updateProposal();
            break;
        case 'delete':
            deleteProposal();
            break;
        case 'comments':
            getComments();
            break;
        case 'add_comment':
            addComment();
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}

/**
 * List all proposals with optional filtering
 */
function listProposals() {
    $status = $_GET['status'] ?? null;
    $limit = $_GET['limit'] ?? 100;
    $offset = $_GET['offset'] ?? 0;
    
    $sql = "SELECT id, title, author, status, summary, created_at, updated_at 
            FROM proposals";
    $params = [];
    
    if ($status) {
        $sql .= " WHERE status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = (int)$limit;
    $params[] = (int)$offset;
    
    $proposals = Database::fetchAll($sql, $params);
    
    json_response([
        'success' => true,
        'data' => $proposals,
        'count' => count($proposals)
    ]);
}

/**
 * Get a single proposal by ID
 */
function getProposal() {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        json_response(['success' => false, 'message' => 'Proposal ID required'], 400);
    }
    
    $sql = "SELECT * FROM proposals WHERE id = ?";
    $proposal = Database::fetchOne($sql, [$id]);
    
    if (!$proposal) {
        json_response(['success' => false, 'message' => 'Proposal not found'], 404);
    }
    
    json_response([
        'success' => true,
        'data' => $proposal
    ]);
}

/**
 * Create a new proposal
 */
function createProposal() {
    // Required fields
    $required = ['title', 'author', 'summary', 'problem_statement', 'proposed_solution'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            json_response(['success' => false, 'message' => "Field '$field' is required"], 400);
        }
    }
    
    $sql = "INSERT INTO proposals (
                title, author, status, summary, problem_statement, 
                proposed_solution, benefits, challenges, resources_required, 
                timeline, success_metrics
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $_POST['title'],
        $_POST['author'],
        $_POST['status'] ?? 'Draft',
        $_POST['summary'],
        $_POST['problem_statement'],
        $_POST['proposed_solution'],
        $_POST['benefits'] ?? null,
        $_POST['challenges'] ?? null,
        $_POST['resources_required'] ?? null,
        $_POST['timeline'] ?? null,
        $_POST['success_metrics'] ?? null
    ];
    
    Database::execute($sql, $params);
    $id = Database::lastInsertId();
    
    Database::logActivity('proposal_created', "Proposal #{$id} created: " . $_POST['title']);
    
    json_response([
        'success' => true,
        'message' => 'Proposal created successfully',
        'data' => ['id' => $id]
    ], 201);
}

/**
 * Update an existing proposal
 */
function updateProposal() {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        json_response(['success' => false, 'message' => 'Proposal ID required'], 400);
    }
    
    // Check if proposal exists
    $existing = Database::fetchOne("SELECT id FROM proposals WHERE id = ?", [$id]);
    if (!$existing) {
        json_response(['success' => false, 'message' => 'Proposal not found'], 404);
    }
    
    $sql = "UPDATE proposals SET 
                title = ?, author = ?, status = ?, summary = ?, 
                problem_statement = ?, proposed_solution = ?, benefits = ?, 
                challenges = ?, resources_required = ?, timeline = ?, 
                success_metrics = ?
            WHERE id = ?";
    
    $params = [
        $_POST['title'],
        $_POST['author'],
        $_POST['status'],
        $_POST['summary'],
        $_POST['problem_statement'],
        $_POST['proposed_solution'],
        $_POST['benefits'] ?? null,
        $_POST['challenges'] ?? null,
        $_POST['resources_required'] ?? null,
        $_POST['timeline'] ?? null,
        $_POST['success_metrics'] ?? null,
        $id
    ];
    
    Database::execute($sql, $params);
    Database::logActivity('proposal_updated', "Proposal #{$id} updated");
    
    json_response([
        'success' => true,
        'message' => 'Proposal updated successfully'
    ]);
}

/**
 * Delete a proposal
 */
function deleteProposal() {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        json_response(['success' => false, 'message' => 'Proposal ID required'], 400);
    }
    
    $sql = "DELETE FROM proposals WHERE id = ?";
    $affected = Database::execute($sql, [$id]);
    
    if ($affected === 0) {
        json_response(['success' => false, 'message' => 'Proposal not found'], 404);
    }
    
    Database::logActivity('proposal_deleted', "Proposal #{$id} deleted");
    
    json_response([
        'success' => true,
        'message' => 'Proposal deleted successfully'
    ]);
}

/**
 * Get comments for a proposal
 */
function getComments() {
    $proposalId = $_GET['proposal_id'] ?? null;
    
    if (!$proposalId) {
        json_response(['success' => false, 'message' => 'Proposal ID required'], 400);
    }
    
    $sql = "SELECT * FROM comments WHERE proposal_id = ? ORDER BY created_at DESC";
    $comments = Database::fetchAll($sql, [$proposalId]);
    
    json_response([
        'success' => true,
        'data' => $comments
    ]);
}

/**
 * Add a comment to a proposal
 */
function addComment() {
    $proposalId = $_POST['proposal_id'] ?? null;
    $author = $_POST['author'] ?? null;
    $commentText = $_POST['comment_text'] ?? null;
    
    if (!$proposalId || !$author || !$commentText) {
        json_response(['success' => false, 'message' => 'All fields are required'], 400);
    }
    
    $sql = "INSERT INTO comments (proposal_id, author, comment_text) VALUES (?, ?, ?)";
    Database::execute($sql, [$proposalId, $author, $commentText]);
    
    $id = Database::lastInsertId();
    Database::logActivity('comment_added', "Comment added to proposal #{$proposalId}");
    
    json_response([
        'success' => true,
        'message' => 'Comment added successfully',
        'data' => ['id' => $id]
    ], 201);
}
?>
