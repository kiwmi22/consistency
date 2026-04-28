<?php
// ============================================
// File:   db.php
// Author: Juna Bhujel
// Shared database connection
// ============================================

$host     = "localhost";
$dbname   = "consistency_db";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>