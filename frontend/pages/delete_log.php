<?php
// Delete Log — confirmation then delete
// Presentation Layer — Abin Rai
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); exit;
}
require_once '../../backend/config/db_connect.php';
require_once '../../backend/classes/HabitLog.php';

$habitLog = new HabitLog($pdo);
$logID    = (int)($_GET['id'] ?? 0);
$log      = $habitLog->getLogByID($logID);

if (!$log) { header('Location: view_logs.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $habitLog->deleteLog($logID);
    header('Location: view_logs.php?deleted=1'); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Log | Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>Delete Log Entry</h1>
    <p>Are you sure you want to delete this log?</p>
    <table>
        <tr><th>Log ID</th><td><?= $log['LogID'] ?></td></tr>
        <tr><th>Date</th><td><?= $log['LogDate'] ?></td></tr>
        <tr><th>Completed</th>
            <td><?= $log['IsCompleted'] ? 'Yes':'No' ?></td></tr>
        <tr><th>Notes</th>
            <td><?= htmlspecialchars($log['Notes'] ?? '') ?></td>
        </tr>
        <tr><th>Duration</th>
            <td><?= $log['DurationMins'] ?> mins</td></tr>
    </table>
    <form method="POST" action="">
        <button type="submit" class="btn-danger">
            Yes Delete This Log
        </button>
    </form>
    <div class="nav-links">
        <a href="view_logs.php">Cancel</a>
    </div>
</div>
</body>
</html>