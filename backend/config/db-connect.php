<?php
// db-connect.php - provides $conn (mysqli) for Habit class
require_once __DIR__ . '/database.php';
$conn = getMysqliConnection();
