<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration - Warming Room Proposal System</title>
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
            <h2>System Configuration</h2>
            <p>Manage system settings and preferences.</p>
        </div>

        <div id="alert-container"></div>

        <div class="card">
            <h3>General Settings</h3>
            <form id="config-form" onsubmit="saveConfig(); return false;">
                <div class="form-group">
                    <label for="config-site_name">Site Name</label>
                    <input type="text" id="config-site_name" name="site_name" class="form-control" 
                           placeholder="Northumberland Warming Room Proposal System">
                </div>

                <div class="form-group">
                    <label for="config-admin_email">Administrator Email</label>
                    <input type="email" id="config-admin_email" name="admin_email" class="form-control" 
                           placeholder="admin@example.com">
                </div>

                <div class="form-group">
                    <label for="config-items_per_page">Items Per Page</label>
                    <input type="number" id="config-items_per_page" name="items_per_page" 
                           class="form-control" min="5" max="100" placeholder="10">
                    <small>Number of proposals to display per page (5-100)</small>
                </div>

                <h3>Proposal Submission Settings</h3>

                <div class="form-group">
                    <label for="config-allow_public_submissions">Allow Public Submissions</label>
                    <select id="config-allow_public_submissions" name="allow_public_submissions" class="form-control">
                        <option value="1">Yes - Anyone can submit proposals</option>
                        <option value="0">No - Restricted access only</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="config-require_approval">Require Approval</label>
                    <select id="config-require_approval" name="require_approval" class="form-control">
                        <option value="1">Yes - Proposals need admin approval</option>
                        <option value="0">No - Proposals appear immediately</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save Configuration</button>
                    <button type="button" onclick="loadConfig()" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h3>Database Information</h3>
            <p>Current database configuration (edit in <code>config/database.php</code>):</p>
            <table class="table">
                <tr>
                    <td><strong>Database Host:</strong></td>
                    <td><?php 
                        if (file_exists('config/database.php')) {
                            require_once 'config/database.php';
                            echo defined('DB_HOST') ? DB_HOST : 'Not configured';
                        } else {
                            echo '<span style="color: red;">Configuration file not found</span>';
                        }
                    ?></td>
                </tr>
                <tr>
                    <td><strong>Database Name:</strong></td>
                    <td><?php echo defined('DB_NAME') ? DB_NAME : 'Not configured'; ?></td>
                </tr>
                <tr>
                    <td><strong>Database User:</strong></td>
                    <td><?php echo defined('DB_USER') ? DB_USER : 'Not configured'; ?></td>
                </tr>
                <tr>
                    <td><strong>Debug Mode:</strong></td>
                    <td><?php echo defined('DEBUG_MODE') && DEBUG_MODE ? 'Enabled' : 'Disabled'; ?></td>
                </tr>
            </table>
            <p><strong>Note:</strong> To change database settings, edit <code>config/database.php</code> directly.</p>
        </div>

        <div class="card">
            <h3>System Status</h3>
            <button onclick="testDatabaseConnection()" class="btn btn-primary">Test Database Connection</button>
            <div id="test-result" style="margin-top: 1rem;"></div>
        </div>

        <div class="card">
            <h3>Security Recommendations</h3>
            <ul>
                <li>Keep your database credentials secure in <code>config/database.php</code></li>
                <li>Set <code>DEBUG_MODE</code> to <code>false</code> in production</li>
                <li>Restrict access to the config directory via <code>.htaccess</code> or server configuration</li>
                <li>Regularly backup your database</li>
                <li>Use strong passwords for database access</li>
                <li>Keep PHP and MySQL updated to the latest stable versions</li>
            </ul>
        </div>

        <div class="card">
            <h3>Backup & Maintenance</h3>
            <p>Regular backups are essential. To backup your database:</p>
            <pre><code>mysqldump -u [username] -p warming_room_db > backup_$(date +%Y%m%d).sql</code></pre>
            <p>To restore from a backup:</p>
            <pre><code>mysql -u [username] -p warming_room_db < backup_20250120.sql</code></pre>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Northumberland Warming Room Proposal System | <a href="https://github.com/acesonder/NWRP" style="color: white;">GitHub</a></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
