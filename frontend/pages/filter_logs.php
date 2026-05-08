<?php
// Filter Logs — filter by habit, status, or user
// Presentation Layer — Abin Rai — Sprint 3
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); exit;
}
require_once '../../backend/config/db_connect.php';
require_once '../../backend/classes/HabitLog.php';

$habitLog  = new HabitLog($pdo);
$logs      = [];
$errors    = [];
$searched  = false;

// Get filter values
$filterType = $_GET['filter_type'] ?? '';
$habitID    = (int)($_GET['habit_id'] ?? 0);
$status     = $_GET['status'] ?? '';
$filterUser = (int)($_GET['filter_user'] ?? 0);
$startDate  = $_GET['start_date'] ?? '';
$endDate    = $_GET['end_date']   ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $filterType) {
    $searched = true;

    if ($filterType === 'habit') {
        // Filter by HabitID
        if ($habitID <= 0) {
            $errors[] = "Please enter a valid Habit ID.";
        } else {
            $logs = $habitLog->filterByHabit($habitID);
        }

    } elseif ($filterType === 'status') {
        // Filter by completion status
        if ($status === '') {
            $errors[] = "Please select a status.";
        } else {
            $logs = $habitLog->filterByStatus((bool)$status);
        }

    } elseif ($filterType === 'user') {
        // Filter by UserID (admin)
        if ($filterUser <= 0) {
            $errors[] = "Please enter a valid User ID.";
        } else {
            $logs = $habitLog->filterByUser($filterUser);
        }

    } elseif ($filterType === 'date') {
        // Filter by date range
        if (!$startDate || !$endDate) {
            $errors[] = "Please select both start and end dates.";
        } elseif ($startDate > $endDate) {
            $errors[] = "Start date cannot be after end date.";
        } else {
            $logs = $habitLog->getLogsByUser(
                $_SESSION['UserID'], $startDate, $endDate
            );
        }
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
    <h1>Filter Logs</h1>

    <form method="GET" action="">

        <label>Filter Type:
            <select name="filter_type">
                <option value="">-- Select Filter --</option>
                <option value="habit"
                    <?= $filterType==='habit' ? 'selected':'' ?>>
                    By Habit ID
                </option>
                <option value="status"
                    <?= $filterType==='status' ? 'selected':'' ?>>
                    By Completion Status
                </option>
                <option value="date"
                    <?= $filterType==='date' ? 'selected':'' ?>>
                    By Date Range
                </option>
                <option value="user"
                    <?= $filterType==='user' ? 'selected':'' ?>>
                    By User ID (Admin)
                </option>
            </select>
        </label>

        <label>Habit ID:
            <input type="number" name="habit_id"
                value="<?= $habitID ?: '' ?>" min="1">
        </label>

        <label>Status:
            <select name="status">
                <option value="">-- All --</option>
                <option value="1"
                    <?= $status==='1' ? 'selected':'' ?>>
                    Completed
                </option>
                <option value="0"
                    <?= $status==='0' ? 'selected':'' ?>>
                    Incomplete
                </option>
            </select>
        </label>

        <label>Start Date:
            <input type="date" name="start_date"
                value="<?= $startDate ?>">
        </label>

        <label>End Date:
            <input type="date" name="end_date"
                value="<?= $endDate ?>">
        </label>

        <label>User ID (Admin only):
            <input type="number" name="filter_user"
                value="<?= $filterUser ?: '' ?>" min="1">
        </label>

        <button type="submit">Filter</button>
        <a href="filter_logs.php">Reset Filters</a>
    </form>

    <?php foreach ($errors as $e): ?>
        <div class="error"><?= $e ?></div>
    <?php endforeach; ?>

    <?php if ($searched && empty($errors)): ?>
        <p>Results: <strong><?= count($logs) ?></strong></p>
        <?php if (empty($logs)): ?>
            <p>No logs found matching your filter.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Log ID</th><th>Habit ID</th>
                    <th>Date</th><th>Completed</th>
                    <th>Notes</th><th>Duration</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log['LogID'] ?></td>
                    <td><?= $log['HabitID'] ?></td>
                    <td><?= $log['LogDate'] ?></td>
                    <td>
                        <?= $log['IsCompleted'] ? 'Yes':'No' ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($log['Notes'] ?? '') ?>
                    </td>
                    <td><?= $log['DurationMins'] ?> mins</td>
                    <td>
                        <a href="edit_log.php?id=
                            <?= $log['LogID'] ?>">Edit</a>
                        <a href="delete_log.php?id=
                            <?= $log['LogID'] ?>"
                           class="btn-danger"
                           onclick="return confirm('Delete?')">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <div class="nav-links">
        <a href="find_log.php">Find by ID</a>
        <a href="view_logs.php">View Today's Logs</a>
        <a href="../index.html">Main Menu</a>
    </div>
</div>
</body>
</html>