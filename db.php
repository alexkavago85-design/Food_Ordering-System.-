<?php
// Start session for auth/cart use
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials - update these for your environment
define('DB_HOST', 'localhost');
define('DB_NAME', 'food_ordering');
define('DB_USER', 'root');
define('DB_PASS', 'Andrea2004@');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // Do not reveal details in production
    die('Database connection failed: ' . $e->getMessage());
}

// Helper: escape output
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
