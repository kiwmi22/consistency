<?php
// Delete Log — removes a log entry and redirects back
// Presentation layer — calls HabitLog middle layer
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

require_once '../../backend/config/db_connect.php';
require_once '../../backend/classes/HabitLog.php';

$habitLog = new HabitLog($pdo);
$logID    = (int) ($_GET['id'] ?? 0);

// Validate and delete
if ($logID > 0) {
    $habitLog->deleteLog($logID);
}

// Redirect back to view logs
header('Location: view_logs.php');
exit;
?>
