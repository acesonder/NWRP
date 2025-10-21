<?php
/**
 * Installation Script for Volunteer Coordination System
 * Run this file once to set up the application
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if already installed
if (file_exists('config/database.php')) {
    $config = require 'config/database.php';
    try {
        $pdo = new PDO(
            "mysql:host={$config['host']};dbname={$config['database']}", 
            $config['username'], 
            $config['password']
        );
        $stmt = $pdo->query("SELECT COUNT(*) FROM volunteers");
        if ($stmt) {
            header('Location: index.html');
            exit('Application is already installed. <a href="index.html">Go to application</a>');
        }
    } catch (Exception $e) {
        // Continue with installation
    }
}

$step = $_GET['step'] ?? 1;
$errors = [];
$success = [];

// Handle form submissions
if ($_POST) {
    switch ($step) {
        case 2:
            $result = testDatabaseConnection($_POST);
            if ($result['success']) {
                $success[] = $result['message'];
                $step = 3;
            } else {
                $errors[] = $result['message'];
            }
            break;
            
        case 3:
            $result = createDatabase($_POST);
            if ($result['success']) {
                $success[] = $result['message'];
                $step = 4;
            } else {
                $errors[] = $result['message'];
            }
            break;
            
        case 4:
            $result = setupAdmin($_POST);
            if ($result['success']) {
                $success[] = $result['message'];
                $step = 5;
            } else {
                $errors[] = $result['message'];
            }
            break;
    }
}

function testDatabaseConnection($data) {
    try {
        $pdo = new PDO(
            "mysql:host={$data['host']}", 
            $data['username'], 
            $data['password']
        );
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$data['database']}`");
        
        // Create config file
        $config = "<?php\nreturn [\n";
        $config .= "    'host' => '{$data['host']}',\n";
        $config .= "    'database' => '{$data['database']}',\n";
        $config .= "    'username' => '{$data['username']}',\n";
        $config .= "    'password' => '{$data['password']}',\n";
        $config .= "    'charset' => 'utf8mb4',\n";
        $config .= "    'options' => [\n";
        $config .= "        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n";
        $config .= "        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n";
        $config .= "        PDO::ATTR_EMULATE_PREPARES => false,\n";
        $config .= "    ]\n];";
        
        file_put_contents('config/database.php', $config);
        
        return ['success' => true, 'message' => 'Database connection successful!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()];
    }
}

function createDatabase($data) {
    try {
        $config = require 'config/database.php';
        $pdo = new PDO(
            "mysql:host={$config['host']};dbname={$config['database']}", 
            $config['username'], 
            $config['password']
        );
        
        // Read and execute schema
        $schema = file_get_contents('database/schema.sql');
        $pdo->exec($schema);
        
        return ['success' => true, 'message' => 'Database tables created successfully!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Failed to create database: ' . $e->getMessage()];
    }
}

function setupAdmin($data) {
    try {
        require_once 'includes/database.php';
        $db = new Database();
        
        // Create admin volunteer
        $adminData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => 'active',
            'training_completed' => true,
            'background_check_completed' => true
        ];
        
        $adminId = $db->insert('volunteers', $adminData);
        
        // Add coordinator skill
        $skillData = [
            'volunteer_id' => $adminId,
            'skill_type' => 'leadership',
            'skill_description' => 'System Administrator/Coordinator'
        ];
        
        $db->insert('volunteer_skills', $skillData);
        
        return ['success' => true, 'message' => 'Administrator account created successfully!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Failed to create admin: ' . $e->getMessage()];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Volunteer Coordination System</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #2563eb; }
        .step { background: #eff6ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #1e40af; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .success { background: #dcfce7; color: #166534; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .progress { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .progress-step { padding: 5px 10px; background: #e5e7eb; border-radius: 15px; font-size: 12px; }
        .progress-step.active { background: #2563eb; color: white; }
        .progress-step.completed { background: #059669; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏠 Volunteer Coordination System</h1>
        <p>Welcome! Let's set up your volunteer coordination system.</p>
        
        <div class="progress">
            <span class="progress-step <?= $step >= 1 ? ($step == 1 ? 'active' : 'completed') : '' ?>">1. Welcome</span>
            <span class="progress-step <?= $step >= 2 ? ($step == 2 ? 'active' : 'completed') : '' ?>">2. Database</span>
            <span class="progress-step <?= $step >= 3 ? ($step == 3 ? 'active' : 'completed') : '' ?>">3. Install</span>
            <span class="progress-step <?= $step >= 4 ? ($step == 4 ? 'active' : 'completed') : '' ?>">4. Admin</span>
            <span class="progress-step <?= $step >= 5 ? 'active' : '' ?>">5. Complete</span>
        </div>
        
        <?php foreach ($errors as $error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
        
        <?php foreach ($success as $msg): ?>
            <div class="success"><?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>
        
        <?php if ($step == 1): ?>
            <div class="step">
                <h2>Step 1: Welcome</h2>
                <p>This installer will help you set up your volunteer coordination system. You'll need:</p>
                <ul>
                    <li>MySQL database server</li>
                    <li>Database credentials with create/drop privileges</li>
                    <li>PHP 7.4 or higher</li>
                    <li>Web server (Apache/Nginx)</li>
                </ul>
                <p>The system will help you manage volunteers, schedule shifts, and coordinate communications for warming room operations.</p>
                <a href="?step=2" class="button" style="background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Continue</a>
            </div>
            
        <?php elseif ($step == 2): ?>
            <div class="step">
                <h2>Step 2: Database Configuration</h2>
                <p>Enter your database connection details:</p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="host">Database Host:</label>
                    <input type="text" id="host" name="host" value="<?= $_POST['host'] ?? 'localhost' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="database">Database Name:</label>
                    <input type="text" id="database" name="database" value="<?= $_POST['database'] ?? 'volunteer_coordination' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?= $_POST['username'] ?? '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="<?= $_POST['password'] ?? '' ?>">
                </div>
                
                <button type="submit">Test Connection</button>
            </form>
            
        <?php elseif ($step == 3): ?>
            <div class="step">
                <h2>Step 3: Database Installation</h2>
                <p>Ready to create the database tables and initial data.</p>
            </div>
            
            <form method="POST">
                <button type="submit">Create Database Tables</button>
            </form>
            
        <?php elseif ($step == 4): ?>
            <div class="step">
                <h2>Step 4: Administrator Account</h2>
                <p>Create your administrator account:</p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?= $_POST['first_name'] ?? '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?= $_POST['last_name'] ?? '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= $_POST['email'] ?? '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" value="<?= $_POST['phone'] ?? '' ?>" required>
                </div>
                
                <button type="submit">Create Administrator</button>
            </form>
            
        <?php elseif ($step == 5): ?>
            <div class="step">
                <h2>🎉 Installation Complete!</h2>
                <p>Your Volunteer Coordination System has been successfully installed.</p>
                
                <h3>Next Steps:</h3>
                <ul>
                    <li>Delete or secure this install.php file</li>
                    <li>Configure your web server SSL certificate</li>
                    <li>Add volunteers and create shift templates</li>
                    <li>Set up your first warming room activation</li>
                </ul>
                
                <p><strong>Important Security Notes:</strong></p>
                <ul>
                    <li>Ensure your database credentials are secure</li>
                    <li>Use HTTPS for all communications</li>
                    <li>Regularly backup your database</li>
                    <li>Keep the system updated</li>
                </ul>
                
                <a href="index.html" style="background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Launch Application</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>