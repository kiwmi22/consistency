<?php
// ============================================================
// Consistency Project – Database Connection
// Developer: Sri Krishna Shrestha
// File: backend/config/db_connect.php
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'consistency_db');
define('DB_USER', 'root');       // XAMPP default
define('DB_PASS', '');           // XAMPP default (no password)
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Use real prepared statements
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // In production, log this rather than displaying it
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

return $pdo;
