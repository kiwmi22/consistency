<?php
// Admin Logs — manage all logs across all users
// Presentation Layer — Abin Rai
session_start();
if (!isset($_SESSION['IsAdmin']) || !$_SESSION['IsAdmin']) {
    header('Location: ../index.html'); exit;
}
require_once '../../backend/config/db_connect.php';
require_once '../../backend/classes/HabitLog.php';

$habitLog  = new HabitLog($pdo);
$userID    = $_GET['user_id']    ?? null;
$startDate = $_GET['start_date'] ?? null;
$endDate   = $_GET['end_date']   ?? null;
$deleted   = isset($_GET['deleted']) && $_GET['deleted'] == 1;

$logs = ($userID || $startDate || $endDate)
    ? $habitLog->filterLogs($userID, $startDate, $endDate)
    : $habitLog->getAllLogs();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Logs | Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>Admin — All Habit Logs</h1>

    <?php if ($deleted): ?>
        <div class="success">Log deleted successfully.</div>
    <?php endif; ?>

    <form method="GET" action="">
        <label>User ID:
            <input type="number" name="user_id"
                value="<?= htmlspecialchars($userID ?? '') ?>">
        </label>
        <label>Start Date:
            <input type="date" name="start_date"
                value="<?= htmlspecialchars($startDate ?? '') ?>">
        </label>
        <label>End Date:
            <input type="date" name="end_date"
                value="<?= htmlspecialchars($endDate ?? '') ?>">
        </label>
        <button type="submit">Filter</button>
        <a href="admin_logs.php">Clear</a>
    </form>

    <p>Total: <strong><?= count($logs) ?></strong></p>

    <?php if (empty($logs)): ?>
        <p>No logs found.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Log ID</th><th>User</th><th>Habit</th>
                <th>Date</th><th>Done</th>
                <th>Notes</th><th>Mins</th><th>Actions</th>
            </tr>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= $log['LogID'] ?></td>
                <td><?= htmlspecialchars($log['Username']) ?></td>
                <td><?= htmlspecialchars($log['HabitName']) ?></td>
                <td><?= $log['LogDate'] ?></td>
                <td><?= $log['IsCompleted'] ? 'Yes':'No' ?></td>
                <td>
                    <?= htmlspecialchars($log['Notes'] ?? '') ?>
                </td>
                <td><?= $log['DurationMins'] ?></td>
                <td>
                    <a href="edit_log.php?id=<?= $log['LogID'] ?>">
                        Edit
                    </a>
                    <a href="delete_log.php?id=<?= $log['LogID'] ?>"
                       class="btn-danger"
                       onclick="return confirm('Delete?')">
                        Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="nav-links">
        <a href="../index.html">Main Menu</a>
    </div>
</div>
</body>
</html>