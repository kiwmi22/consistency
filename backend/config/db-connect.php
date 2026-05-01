php<?php
// db-connect.php
// Shared database connection for all components

$host     = "localhost";
$dbname   = "consistency";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>