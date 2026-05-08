<?php
// Daily Check-In — Add a log entry
// Presentation Layer — Abin Rai
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); exit;
}
require_once '../../backend/config/db_connect.php';
require_once '../../backend/classes/HabitLog.php';

$habitLog = new HabitLog($pdo);
$today    = date('Y-m-d');
$userID   = $_SESSION['UserID'];
$errors   = [];
$success  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $habitID  = (int)($_POST['habit_id'] ?? 0);
    $notes    = htmlspecialchars(
                    strip_tags(trim($_POST['notes'] ?? ''))
                );
    $duration = (int)($_POST['duration'] ?? 0);

    if ($habitID <= 0)
        $errors[] = "Please enter a valid Habit ID.";
    if ($duration < 0)
        $errors[] = "Duration cannot be negative.";
    if (strlen($notes) > 255)
        $errors[] = "Notes must be under 255 characters.";

    if (empty($errors)) {
        $result = $habitLog->addLog(
            $habitID, $userID, $today,
            true, $notes, $duration
        );
        $success = $result
            ? "Habit logged successfully for today!"
            : "Already logged this habit today.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Check-In | Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h1>Daily Check-In</h1>
    <p>Date: <strong><?= $today ?></strong></p>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>
    <?php foreach ($errors as $e): ?>
        <div class="error"><?= $e ?></div>
    <?php endforeach; ?>

    <form method="POST" action="">
        <label>Habit ID:
            <input type="number" name="habit_id" min="1" required>
        </label>
        <label>Notes (optional):
            <input type="text" name="notes" maxlength="255">
        </label>
        <label>Duration (mins):
            <input type="number" name="duration" min="0" value="0">
        </label>
        <button type="submit">Mark as Done ✅</button>
    </form>
    <div class="nav-links">
        <a href="view_logs.php">View My Logs</a>
        <a href="../index.html">Main Menu</a>
    </div>
</div>
</body>
</html>