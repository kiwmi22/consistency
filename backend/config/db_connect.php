<?php
// db_connect.php - provides $pdo (PDO connection) for User and HabitLog classes
require_once __DIR__ . '/database.php';
$db  = new Database();
$pdo = $db->getConnection();
