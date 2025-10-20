<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Troubleshooting - Warming Room Proposal System</title>
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
            <h2>Troubleshooting Guide</h2>
            <p>Solutions to common problems and issues.</p>
        </div>

        <div class="card">
            <h3>Database Connection Issues</h3>
            
            <h4>Problem: "Connection failed" error</h4>
            <p><strong>Possible causes and solutions:</strong></p>
            <ul>
                <li><strong>Wrong credentials:</strong> Verify database username, password, and database name in <code>config/database.php</code></li>
                <li><strong>MySQL not running:</strong> Check if MySQL service is running:
                    <pre><code>sudo systemctl status mysql</code></pre>
                    Start it if needed:
                    <pre><code>sudo systemctl start mysql</code></pre>
                </li>
                <li><strong>Wrong host:</strong> If MySQL is on a different server, update <code>DB_HOST</code> in config</li>
                <li><strong>User permissions:</strong> Ensure the database user has proper permissions:
                    <pre><code>GRANT ALL PRIVILEGES ON warming_room_db.* TO 'username'@'localhost';
FLUSH PRIVILEGES;</code></pre>
                </li>
            </ul>

            <h4>Problem: "Database not found" error</h4>
            <p><strong>Solution:</strong> The database hasn't been created. Run the schema file:</p>
            <pre><code>mysql -u root -p &lt; database_schema.sql</code></pre>
        </div>

        <div class="card">
            <h3>Configuration File Issues</h3>
            
            <h4>Problem: "Configuration file not found"</h4>
            <p><strong>Solution:</strong> Copy the example config file:</p>
            <pre><code>cd config/
cp database.example.php database.php</code></pre>
            <p>Then edit <code>database.php</code> with your settings.</p>

            <h4>Problem: Blank page or PHP errors</h4>
            <p><strong>Solutions:</strong></p>
            <ul>
                <li>Check PHP error log:
                    <pre><code>tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx</code></pre>
                </li>
                <li>Enable DEBUG_MODE temporarily in <code>config/database.php</code>:
                    <pre><code>define('DEBUG_MODE', true);</code></pre>
                </li>
                <li>Verify PHP version (requires 7.4+):
                    <pre><code>php -v</code></pre>
                </li>
            </ul>
        </div>

        <div class="card">
            <h3>Permission Issues</h3>
            
            <h4>Problem: "Permission denied" errors</h4>
            <p><strong>Solution:</strong> Ensure proper file permissions:</p>
            <pre><code>cd /path/to/webapp/
chmod 644 config/database.php
chmod 755 api/
chmod 755 includes/
chmod 755 .
chown -R www-data:www-data .  # For Apache
chown -R nginx:nginx .        # For Nginx</code></pre>
        </div>

        <div class="card">
            <h3>API Not Working</h3>
            
            <h4>Problem: AJAX requests fail or return errors</h4>
            <p><strong>Solutions:</strong></p>
            <ul>
                <li>Check browser console for JavaScript errors (F12 → Console)</li>
                <li>Verify API files exist in the <code>api/</code> directory</li>
                <li>Test API directly in browser:
                    <pre><code>http://your-domain.com/proposals/webapp/api/test_connection.php</code></pre>
                </li>
                <li>Check that <code>.htaccess</code> isn't blocking PHP files</li>
                <li>Verify <code>mod_rewrite</code> is enabled (Apache):
                    <pre><code>sudo a2enmod rewrite
sudo systemctl restart apache2</code></pre>
                </li>
            </ul>
        </div>

        <div class="card">
            <h3>Data Not Displaying</h3>
            
            <h4>Problem: Proposals list is empty</h4>
            <p><strong>Solutions:</strong></p>
            <ul>
                <li>Check if database has data:
                    <pre><code>mysql -u root -p warming_room_db
SELECT COUNT(*) FROM proposals;</code></pre>
                </li>
                <li>If no data, the sample data might not have imported. Re-run schema:
                    <pre><code>mysql -u root -p warming_room_db &lt; database_schema.sql</code></pre>
                </li>
                <li>Check browser console for AJAX errors</li>
                <li>Test the API endpoint directly</li>
            </ul>
        </div>

        <div class="card">
            <h3>PHP Extension Issues</h3>
            
            <h4>Problem: PDO or MySQL extensions not found</h4>
            <p><strong>Solution:</strong> Install required PHP extensions:</p>
            <pre><code># Ubuntu/Debian
sudo apt-get install php-mysql php-pdo

# CentOS/RHEL
sudo yum install php-mysqlnd php-pdo

# Then restart web server
sudo systemctl restart apache2  # or nginx</code></pre>
            
            <p>Verify extensions are installed:</p>
            <pre><code>php -m | grep -i pdo
php -m | grep -i mysql</code></pre>
        </div>

        <div class="card">
            <h3>Performance Issues</h3>
            
            <h4>Problem: Slow page loads</h4>
            <p><strong>Solutions:</strong></p>
            <ul>
                <li>Add indexes to database (already included in schema)</li>
                <li>Enable PHP OPcache in php.ini</li>
                <li>Limit number of items per page in configuration</li>
                <li>Check database query performance:
                    <pre><code>EXPLAIN SELECT * FROM proposals;</code></pre>
                </li>
            </ul>
        </div>

        <div class="card">
            <h3>Testing Tools</h3>
            <button onclick="testDatabaseConnection()" class="btn btn-primary">Test Database Connection</button>
            <div id="test-result" style="margin-top: 1rem;"></div>
        </div>

        <div class="card">
            <h3>Getting More Help</h3>
            <p>If you're still experiencing issues:</p>
            <ul>
                <li>Check the <a href="https://github.com/acesonder/NWRP" target="_blank">GitHub repository</a> for known issues</li>
                <li>Review server error logs for detailed error messages</li>
                <li>Enable debug mode in configuration for more detailed error information</li>
                <li>Contact the system administrator for assistance</li>
            </ul>
        </div>

        <div class="card">
            <h3>Common Error Messages</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Error Message</th>
                        <th>Meaning</th>
                        <th>Solution</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SQLSTATE[HY000] [1045]</td>
                        <td>Access denied</td>
                        <td>Wrong username or password</td>
                    </tr>
                    <tr>
                        <td>SQLSTATE[HY000] [2002]</td>
                        <td>Can't connect to MySQL</td>
                        <td>MySQL not running or wrong host</td>
                    </tr>
                    <tr>
                        <td>SQLSTATE[42S02]</td>
                        <td>Table doesn't exist</td>
                        <td>Run database_schema.sql</td>
                    </tr>
                    <tr>
                        <td>Class 'PDO' not found</td>
                        <td>PDO extension missing</td>
                        <td>Install php-pdo package</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Northumberland Warming Room Proposal System | <a href="https://github.com/acesonder/NWRP" style="color: white;">GitHub</a></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
