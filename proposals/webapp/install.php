<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Warming Room Proposal System</title>
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
            <h2>Installation Guide</h2>
            <p>Follow these steps to install and set up the Warming Room Proposal System.</p>
        </div>

        <div class="card">
            <h3>Prerequisites</h3>
            <p>Before you begin, ensure you have the following installed:</p>
            <ul>
                <li>Web server (Apache or Nginx)</li>
                <li>PHP 7.4 or higher with PDO MySQL extension</li>
                <li>MySQL 5.7 or higher (or MariaDB 10.2+)</li>
                <li>Basic command line access</li>
            </ul>
        </div>

        <div class="card setup-steps">
            <h3>Installation Steps</h3>
            
            <div class="setup-step">
                <h4>Step 1: Database Setup</h4>
                <p>Create the database and import the schema:</p>
                <pre><code>mysql -u root -p &lt; database_schema.sql</code></pre>
                <p>Or manually in MySQL:</p>
                <pre><code>mysql -u root -p
CREATE DATABASE warming_room_db;
USE warming_room_db;
SOURCE database_schema.sql;</code></pre>
            </div>

            <div class="setup-step">
                <h4>Step 2: Configure Database Connection</h4>
                <p>Copy the example configuration file and edit it with your database credentials:</p>
                <pre><code>cd config/
cp database.example.php database.php
nano database.php  # or use your preferred editor</code></pre>
                <p>Update the following values:</p>
                <pre><code>define('DB_HOST', 'localhost');
define('DB_NAME', 'warming_room_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');</code></pre>
            </div>

            <div class="setup-step">
                <h4>Step 3: Set File Permissions</h4>
                <p>Ensure proper permissions for the web server:</p>
                <pre><code>chmod 644 config/database.php
chmod 755 api/
chmod 755 includes/</code></pre>
                <p><strong>Security Note:</strong> Make sure <code>config/database.php</code> is not accessible directly via web browser. Use <code>.htaccess</code> or server configuration to deny access.</p>
            </div>

            <div class="setup-step">
                <h4>Step 4: Test Installation</h4>
                <p>Navigate to the installation test page:</p>
                <pre><code>http://your-domain.com/proposals/webapp/install.php</code></pre>
                <p>Or click the button below to test the database connection:</p>
                <button onclick="testDatabaseConnection()" class="btn btn-primary">Test Database Connection</button>
                <div id="test-result" style="margin-top: 1rem;"></div>
            </div>

            <div class="setup-step">
                <h4>Step 5: Access the Application</h4>
                <p>Once the database connection is successful, you can access the application:</p>
                <ul>
                    <li><a href="index.php">Main Dashboard</a> - View all proposals</li>
                    <li><a href="submit_proposal.php">Submit Proposal</a> - Create a new proposal</li>
                    <li><a href="configure.php">Configuration</a> - Adjust system settings</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h3>Apache Configuration (Optional)</h3>
            <p>If using Apache, create an <code>.htaccess</code> file in the webapp root:</p>
            <pre><code># Enable rewrite engine
RewriteEngine On

# Deny access to config directory
&lt;Files "config/*"&gt;
    Order deny,allow
    Deny from all
&lt;/Files&gt;

# Deny access to database.php
&lt;Files "database.php"&gt;
    Order deny,allow
    Deny from all
&lt;/Files&gt;

# Set default timezone
php_value date.timezone "America/Toronto"</code></pre>
        </div>

        <div class="card">
            <h3>Nginx Configuration (Optional)</h3>
            <p>If using Nginx, add this to your server block:</p>
            <pre><code>location /proposals/webapp/config/ {
    deny all;
    return 403;
}

location ~ /database\.php$ {
    deny all;
    return 403;
}</code></pre>
        </div>

        <div class="card">
            <h3>Troubleshooting</h3>
            <p>Having issues? Check the <a href="troubleshoot.php">Troubleshooting Guide</a> for common problems and solutions.</p>
        </div>

        <div class="card">
            <h3>Next Steps</h3>
            <div class="grid grid-2">
                <a href="configure.php" class="btn btn-primary">Configure System</a>
                <a href="index.php" class="btn btn-success">Go to Dashboard</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Northumberland Warming Room Proposal System | <a href="https://github.com/acesonder/NWRP" style="color: white;">GitHub</a></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
