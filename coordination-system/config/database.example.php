<?php
/**
 * Database Configuration
 * Copy this file to database.php and update with your settings
 */

return [
    'host' => 'localhost',
    'database' => 'volunteer_coordination',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];