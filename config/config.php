<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'mentalhealthsystemdatabase'); // Make sure this matches your phpMyAdmin database name
define('DB_USER', 'root');
define('DB_PASS', ''); 

// App Settings
define('APP_NAME', 'SU Care Tracker');
// Your actual folder name in htdocs
define('BASE_URL', 'http://localhost/mental-health-system/'); 

// Start Session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- DATABASE CONNECTION ---
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed. Please check config.php settings. Error: " . $e->getMessage());
}
?>