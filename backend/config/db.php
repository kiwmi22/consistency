<?php
if (file_exists(__DIR__ . '/database.php')) {
    require_once __DIR__ . '/database.php';
    $conn = getMysqliConnection();
} else {
    // Fallback direct connection
    $host     = "localhost";
    $dbname   = "consistency_db";
    $username = "root";
    $password = "";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}
?>