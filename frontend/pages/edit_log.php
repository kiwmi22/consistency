<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); exit;
}

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/classes/HabitLog.php';

$habitLog = new HabitLog($pdo);
$logID    = (int)($_GET['id'] ?? 0);
$errors   = [];
$success  = '';

$log = $habitLog->getLogByID($logID);
if (!$log) {
    header('Location: view_logs.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isCompleted = isset($_POST['is_completed']) ? 1 : 0;
    $notes       = htmlspecialchars(
                       strip_tags(trim($_POST['notes'] ?? ''))
                   );
    $duration    = (int)($_POST['duration'] ?? 0);

    if ($duration < 0)
        $errors[] = "Duration cannot be negative.";
    if (strlen($notes) > 255)
        $errors[] = "Notes must be under 255 characters.";

    if (empty($errors)) {
        $habitLog->updateLog(
            $logID, $isCompleted, $notes, $duration
        );
        $success = "Log updated successfully!";
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
        <div class="success"><?= $success ?></div>
    <?php endif; ?>
    <?php foreach ($errors as $e): ?>
        <div class="error"><?= $e ?></div>
    <?php endforeach; ?>

    <form method="POST" action="">
        <label>
            <input type="checkbox" name="is_completed"
                <?= $log['IsCompleted'] ? 'checked' : '' ?>>
            Mark as Completed
        </label>
        <label>Notes:
            <input type="text" name="notes"
                value="<?= htmlspecialchars(
                    $log['Notes'] ?? ''
                ) ?>"
                maxlength="255">
        </label>
        <label>Duration (mins):
            <input type="number" name="duration"
                value="<?= $log['DurationMins'] ?>"
                min="0">
        </label>
        <button type="submit">Save Changes</button>
    </form>

    <div class="nav-links">
        <a href="view_logs.php">Back to My Logs</a>
        <a href="Dashboard.php">Dashboard</a>
    </div>
</div>
</body>
</html>