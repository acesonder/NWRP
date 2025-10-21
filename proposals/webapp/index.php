<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Warming Room Proposal System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- NWRP Navigation -->
    <div class="nwrp-nav-bar" style="background: #2563eb; color: white; padding: 0.75rem 1rem; font-family: Inter, sans-serif;">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.5rem;">🏠</span>
                <span style="font-weight: 600;">NWRP Portal</span>
            </div>
            <nav style="display: flex; gap: 1rem; align-items: center;">
                <a href="../../" style="color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px;">
                    � Main Portal
                </a>
                <a href="../../coordination-system/" style="color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px;">
                    👥 Coordination
                </a>
                <a href="./" style="color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; background: rgba(255,255,255,0.2);">
                    💡 Proposals
                </a>
            </nav>
        </div>
    </div>

    <header>
        <div class="container">
            <h1>💡 Community Proposals System</h1>
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
            <h2>Welcome to the Warming Room Proposal System</h2>
            <p>This system helps manage and organize proposals for the Northumberland Warming Room initiative. Browse existing proposals, submit new ideas, and collaborate with the community.</p>
        </div>

        <div id="alert-container"></div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0;">All Proposals</h3>
                <a href="submit_proposal.php" class="btn btn-success">Submit New Proposal</a>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label for="status-filter">Filter by Status: </label>
                <select id="status-filter" onchange="filterProposals()" style="padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                    <option value="">All Statuses</option>
                    <option value="Draft">Draft</option>
                    <option value="Under Review">Under Review</option>
                    <option value="Approved">Approved</option>
                    <option value="Implemented">Implemented</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>

            <div id="proposals-list">
                <div class="spinner"></div>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="card">
                <h3>Quick Actions</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;">
                        <a href="submit_proposal.php" class="btn btn-primary" style="width: 100%; display: block;">Submit New Proposal</a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="configure.php" class="btn btn-secondary" style="width: 100%; display: block;">System Configuration</a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="troubleshoot.php" class="btn btn-warning" style="width: 100%; display: block;">Troubleshooting Help</a>
                    </li>
                </ul>
            </div>

            <div class="card">
                <h3>About This System</h3>
                <p>The Warming Room Proposal System is designed to facilitate collaboration and organization of ideas for the warming room initiative.</p>
                <p><strong>Features:</strong></p>
                <ul>
                    <li>Submit and manage proposals</li>
                    <li>Comment and discuss ideas</li>
                    <li>Track proposal status</li>
                    <li>Easy installation and configuration</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h3>Getting Started</h3>
            <p>If this is your first time using the system:</p>
            <ol>
                <li>Review existing proposals to see what's already been suggested</li>
                <li>Submit your own proposal using the form</li>
                <li>Engage with others by commenting on proposals</li>
                <li>Check back regularly for updates and status changes</li>
            </ol>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Northumberland Warming Room Proposal System | <a href="https://github.com/acesonder/NWRP" style="color: white;">GitHub</a></p>
    </footer>

    <script src="js/main.js"></script>
    <script>
        function filterProposals() {
            const status = document.getElementById('status-filter').value;
            loadProposals(status);
        }
    </script>
</body>
</html>
