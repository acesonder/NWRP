<?php
require_once 'includes/database.php';

$proposalId = $_GET['id'] ?? null;

if (!$proposalId) {
    header('Location: index.php');
    exit;
}

// Fetch proposal details
$sql = "SELECT * FROM proposals WHERE id = ?";
$proposal = Database::fetchOne($sql, [$proposalId]);

if (!$proposal) {
    die('Proposal not found');
}

// Fetch comments
$commentsSql = "SELECT * FROM comments WHERE proposal_id = ? ORDER BY created_at DESC";
$comments = Database::fetchAll($commentsSql, [$proposalId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($proposal['title']); ?> - Warming Room Proposal System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🏠 Warming Room Proposal System</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="install.php">Install</a></li>
                    <li><a href="configure.php">Configure</a></li>
                    <li><a href="troubleshoot.php">Troubleshoot</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h2><?php echo escape($proposal['title']); ?></h2>
                    <p>
                        <strong>Author:</strong> <?php echo escape($proposal['author']); ?> | 
                        <strong>Created:</strong> <?php echo date('F j, Y', strtotime($proposal['created_at'])); ?>
                    </p>
                </div>
                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $proposal['status'])); ?>">
                    <?php echo escape($proposal['status']); ?>
                </span>
            </div>
        </div>

        <div class="card">
            <h3>Summary</h3>
            <p><?php echo nl2br(escape($proposal['summary'])); ?></p>
        </div>

        <div class="card">
            <h3>Problem Statement</h3>
            <p><?php echo nl2br(escape($proposal['problem_statement'])); ?></p>
        </div>

        <div class="card">
            <h3>Proposed Solution</h3>
            <p><?php echo nl2br(escape($proposal['proposed_solution'])); ?></p>
        </div>

        <?php if ($proposal['benefits']): ?>
        <div class="card">
            <h3>Benefits</h3>
            <p><?php echo nl2br(escape($proposal['benefits'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($proposal['challenges']): ?>
        <div class="card">
            <h3>Challenges & Risks</h3>
            <p><?php echo nl2br(escape($proposal['challenges'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($proposal['resources_required']): ?>
        <div class="card">
            <h3>Resources Required</h3>
            <p><?php echo nl2br(escape($proposal['resources_required'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($proposal['timeline']): ?>
        <div class="card">
            <h3>Implementation Timeline</h3>
            <p><?php echo nl2br(escape($proposal['timeline'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($proposal['success_metrics']): ?>
        <div class="card">
            <h3>Success Metrics</h3>
            <p><?php echo nl2br(escape($proposal['success_metrics'])); ?></p>
        </div>
        <?php endif; ?>

        <div class="card">
            <h3>Comments (<?php echo count($comments); ?>)</h3>
            
            <div id="alert-container"></div>

            <?php if (count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                <div class="card" style="margin-bottom: 1rem; background-color: #f9f9f9;">
                    <p style="margin-bottom: 0.5rem;">
                        <strong><?php echo escape($comment['author']); ?></strong> - 
                        <small><?php echo date('F j, Y g:i A', strtotime($comment['created_at'])); ?></small>
                    </p>
                    <p style="margin: 0;"><?php echo nl2br(escape($comment['comment_text'])); ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>

            <h4>Add a Comment</h4>
            <form id="comment-form" onsubmit="submitComment(<?php echo $proposalId; ?>); return false;">
                <div class="form-group">
                    <label for="comment-author">Your Name</label>
                    <input type="text" id="comment-author" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="comment-text">Comment</label>
                    <textarea id="comment-text" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Comment</button>
            </form>
        </div>

        <div class="card">
            <a href="index.php" class="btn btn-secondary">← Back to All Proposals</a>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Northumberland Warming Room Proposal System | <a href="https://github.com/acesonder/NWRP" style="color: white;">GitHub</a></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
