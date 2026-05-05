<?php
// Edit Log page — Find a log by ID and update it
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
$errors   = [];
$success  = '';

// Find the log entry by ID
$log = $habitLog->getLogByID($logID);

// If log not found redirect back
if (!$log) {
    header('Location: view_logs.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isCompleted = isset($_POST['is_completed']) ? 1 : 0;
    $notes       = htmlspecialchars(trim($_POST['notes'] ?? ''));
    $duration    = (int) ($_POST['duration'] ?? 0);

    // Validate inputs
    if ($duration < 0) {
        $errors[] = "Duration cannot be negative.";
    }

    if (empty($errors)) {
        $habitLog->updateLog($logID, $isCompleted, $notes, $duration);
        $success = "Log updated successfully!";
        // Refresh log data after update
        $log = $habitLog->getLogByID($logID);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Log | Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Log Entry #<?= $logID ?></h1>

        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>
                <input type="checkbox" name="is_completed"
                    <?= $log['IsCompleted'] ? 'checked' : '' ?>>
                Mark as Completed
            </label>
            <label>Notes:
                <input type="text" name="notes"
                    value="<?= htmlspecialchars($log['Notes'] ?? '') ?>"
                    maxlength="255">
            </label>
            <label>Duration (mins):
                <input type="number" name="duration"
                    value="<?= $log['DurationMins'] ?>" min="0">
            </label>
            <button type="submit">Save Changes</button>
        </form>

        <a href="view_logs.php">Back to My Logs</a>
        <a href="menu.php">Back to Menu</a>
    </div>
</body>
</html>
