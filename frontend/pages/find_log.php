<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); exit;
}

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/classes/HabitLog.php';

$habitLog = new HabitLog($pdo);
$log      = null;
$error    = '';
$searched = false;

if (isset($_GET['log_id'])) {
    $logID    = (int)$_GET['log_id'];
    $searched = true;

    if ($logID <= 0) {
        $error = "Please enter a valid Log ID.";
    } else {
        $log = $habitLog->getLogByID($logID);
        if (!$log) {
            $error = "No log entry found with ID " .
                     $logID . ".";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Log | Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>Find Log Entry</h1>

    <form method="GET" action="">
        <label>Enter Log ID:
            <input type="number" name="log_id" min="1"
                value="<?= isset($_GET['log_id'])
                    ? (int)$_GET['log_id'] : '' ?>">
        </label>
        <button type="submit">Find Log</button>
    </form>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($log): ?>
        <h2>Log Entry Found</h2>
        <table>
            <tr>
                <th>Log ID</th>
                <td><?= $log['LogID'] ?></td>
            </tr>
            <tr>
                <th>Habit ID</th>
                <td><?= $log['HabitID'] ?></td>
            </tr>
            <tr>
                <th>User ID</th>
                <td><?= $log['UserID'] ?></td>
            </tr>
            <tr>
                <th>Log Date</th>
                <td><?= $log['LogDate'] ?></td>
            </tr>
            <tr>
                <th>Completed</th>
                <td>
                    <?= $log['IsCompleted'] ? 'Yes' : 'No' ?>
                </td>
            </tr>
            <tr>
                <th>Notes</th>
                <td>
                    <?= htmlspecialchars($log['Notes'] ?? '') ?>
                </td>
            </tr>
            <tr>
                <th>Duration</th>
                <td><?= $log['DurationMins'] ?> mins</td>
            </tr>
        </table>
        <div class="nav-links">
            <a href="edit_log.php?id=<?= $log['LogID'] ?>">
                Edit This Log
            </a>
        </div>
    <?php endif; ?>

    <div class="nav-links">
        <a href="view_logs.php">View My Logs</a>
        <a href="Dashboard.php">Dashboard</a>
    </div>
</div>
</body>
</html>