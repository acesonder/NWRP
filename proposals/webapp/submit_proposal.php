<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Proposal - Warming Room Proposal System</title>
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
            <h2>Submit a New Proposal</h2>
            <p>Share your ideas for improving the warming room initiative. All required fields are marked with *</p>
        </div>

        <div id="alert-container"></div>

        <div class="card">
            <form id="proposal-form" onsubmit="return submitProposal('proposal-form')">
                <h3>Basic Information</h3>
                
                <div class="form-group">
                    <label for="title">Proposal Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required
                           placeholder="e.g., Volunteer Coordination System">
                    <small>A clear, descriptive title for your proposal</small>
                </div>

                <div class="form-group">
                    <label for="author">Your Name *</label>
                    <input type="text" id="author" name="author" class="form-control" required
                           placeholder="e.g., John Doe">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Draft">Draft</option>
                        <option value="Under Review">Under Review</option>
                        <option value="Approved">Approved</option>
                        <option value="Implemented">Implemented</option>
                    </select>
                    <small>Most new proposals should start as "Draft"</small>
                </div>

                <h3>Proposal Details</h3>

                <div class="form-group">
                    <label for="summary">Summary *</label>
                    <textarea id="summary" name="summary" class="form-control" required
                              placeholder="Brief overview of your proposal (2-3 sentences)"></textarea>
                    <small>A concise summary that explains the core idea</small>
                </div>

                <div class="form-group">
                    <label for="problem_statement">Problem Statement *</label>
                    <textarea id="problem_statement" name="problem_statement" class="form-control" required
                              placeholder="What problem does this address? What need are we filling?"></textarea>
                    <small>Describe the problem or challenge this proposal aims to solve</small>
                </div>

                <div class="form-group">
                    <label for="proposed_solution">Proposed Solution *</label>
                    <textarea id="proposed_solution" name="proposed_solution" class="form-control" required
                              placeholder="Detailed description of your idea. Include how it works, who it serves, and what resources it requires."></textarea>
                    <small>Explain your solution in detail</small>
                </div>

                <h3>Additional Details (Optional)</h3>

                <div class="form-group">
                    <label for="benefits">Benefits</label>
                    <textarea id="benefits" name="benefits" class="form-control"
                              placeholder="What are the key benefits of this proposal?"></textarea>
                    <small>List the main advantages and positive outcomes</small>
                </div>

                <div class="form-group">
                    <label for="challenges">Challenges & Risks</label>
                    <textarea id="challenges" name="challenges" class="form-control"
                              placeholder="What challenges might we face? How can we mitigate risks?"></textarea>
                    <small>Identify potential obstacles and mitigation strategies</small>
                </div>

                <div class="form-group">
                    <label for="resources_required">Resources Required</label>
                    <textarea id="resources_required" name="resources_required" class="form-control"
                              placeholder="Personnel, materials, equipment, and financial resources needed"></textarea>
                    <small>Detail what resources will be needed to implement this proposal</small>
                </div>

                <div class="form-group">
                    <label for="timeline">Implementation Timeline</label>
                    <textarea id="timeline" name="timeline" class="form-control"
                              placeholder="Phases and estimated timeframes for implementation"></textarea>
                    <small>Outline the key phases and timeline for rolling out this proposal</small>
                </div>

                <div class="form-group">
                    <label for="success_metrics">Success Metrics</label>
                    <textarea id="success_metrics" name="success_metrics" class="form-control"
                              placeholder="How will we measure if this is working?"></textarea>
                    <small>Define measurable indicators of success</small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit Proposal</button>
                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="card">
            <h3>Tips for Writing a Good Proposal</h3>
            <ul>
                <li><strong>Be specific:</strong> Clearly define the problem and your proposed solution</li>
                <li><strong>Consider feasibility:</strong> Think about what resources are actually available</li>
                <li><strong>Include details:</strong> The more information you provide, the easier it is for others to understand and support your idea</li>
                <li><strong>Think about impact:</strong> How will this benefit the warming room guests and operations?</li>
                <li><strong>Be realistic:</strong> Consider both benefits and challenges honestly</li>
                <li><strong>Collaborate:</strong> Your proposal can be refined through community feedback</li>
            </ul>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Northumberland Warming Room Proposal System | <a href="https://github.com/acesonder/NWRP" style="color: white;">GitHub</a></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
