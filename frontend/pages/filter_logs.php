<?php
// Filter Logs page — search personal log history by date range
// Presentation layer — calls HabitLog middle layer
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

require_once '../../backend/config/db.php';
require_once '../../backend/classes/HabitLog.php';

$habitLog  = new HabitLog($pdo);
$userID    = $_SESSION['UserID'];
$startDate = $_GET['start_date'] ?? '';
$endDate   = $_GET['end_date']   ?? '';
$logs      = [];
$errors    = [];
$searched  = false;

// Handle filter form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $startDate && $endDate) {
    $searched = true;

    // Validate date range
    if ($startDate > $endDate) {
        $errors[] = "Start date cannot be after end date.";
    } else {
        $logs = $habitLog->getLogsByUser($userID, $startDate, $endDate);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Filter Logs | Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Filter My Logs</h1>

        <!-- Filter form -->
        <form method="GET" action="">
            <label>Start Date:
                <input type="date" name="start_date"
                    value="<?= $startDate ?>">
            </label>
            <label>End Date:
                <input type="date" name="end_date"
                    value="<?= $endDate ?>">
            </label>
            <button type="submit">Search</button>
            <a href="filter_logs.php">Clear</a>
        </form>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($searched && empty($errors)): ?>
            <p>Results found: <?= count($logs) ?></p>
            <?php if (empty($logs)): ?>
                <p>No logs found for this date range.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Log ID</th>
                        <th>Habit ID</th>
                        <th>Date</th>
                        <th>Completed</th>
                        <th>Notes</th>
                        <th>Duration</th>
                    </tr>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['LogID'] ?></td>
                        <td><?= $log['HabitID'] ?></td>
                        <td><?= $log['LogDate'] ?></td>
                        <td><?= $log['IsCompleted'] ? 'Yes' : 'No' ?></td>
                        <td><?= htmlspecialchars($log['Notes'] ?? '') ?></td>
                        <td><?= $log['DurationMins'] ?> mins</td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <a href="menu.php">Back to Menu</a>
    </div>
</body>
</html>