<?php
// View Logs page — List all logs for logged-in user today
// Presentation layer — calls HabitLog middle layer
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

require_once '../../backend/config/db_connect.php';
require_once '../../backend/classes/HabitLog.php';

$habitLog = new HabitLog($pdo);
$userID   = $_SESSION['UserID'];
$today    = date('Y-m-d');

// Get all logs for today
$logs = $habitLog->getLogsByUser($userID, $today, $today);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Logs | Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>My Logs Today</h1>
        <p>Date: <?= $today ?></p>

        <?php if (empty($logs)): ?>
            <p>No habits logged yet today.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Log ID</th>
                    <th>Habit ID</th>
                    <th>Completed</th>
                    <th>Notes</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log['LogID'] ?></td>
                    <td><?= $log['HabitID'] ?></td>
                    <td><?= $log['IsCompleted'] ? 'Yes' : 'No' ?></td>
                    <td><?= htmlspecialchars($log['Notes'] ?? '') ?></td>
                    <td><?= $log['DurationMins'] ?> mins</td>
                    <td>
                        <a href="edit_log.php?id=<?= $log['LogID'] ?>">
                            Edit
                        </a> |
                        <a href="delete_log.php?id=<?= $log['LogID'] ?>"
                           onclick="return confirm('Delete this log?')">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <a href="checkin.php">Log Another Habit</a>
        <a href="menu.php">Back to Menu</a>
    </div>
</body>
</html>
