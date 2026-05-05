<?php
// Daily Check-In page — Add a new log entry
// Presentation layer — calls HabitLog middle layer
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}

require_once '../../backend/config/db_connect.php';
require_once '../../backend/classes/HabitLog.php';

$habitLog = new HabitLog($pdo);
$today    = date('Y-m-d');
$userID   = $_SESSION['UserID'];
$errors   = [];
$success  = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $habitID     = (int) ($_POST['habit_id'] ?? 0);
    $notes       = htmlspecialchars(trim($_POST['notes'] ?? ''));
    $duration    = (int) ($_POST['duration'] ?? 0);

    // Validate inputs
    if ($habitID <= 0) {
        $errors[] = "Please enter a valid Habit ID.";
    }
    if ($duration < 0) {
        $errors[] = "Duration cannot be negative.";
    }

    // Only submit if no errors
    if (empty($errors)) {
        $result = $habitLog->addLog(
            $habitID, $userID, $today, true, $notes, $duration
        );
        if ($result) {
            $success = "Habit logged successfully for today!";
        } else {
            $errors[] = "Could not log habit. You may have already logged
                         this habit today.";
        }
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
        <p>Date: <?= $today ?></p>

        <!-- Success message -->
        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>

        <!-- Error messages -->
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Check-in form -->
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
            <button type="submit">Mark as Done</button>
        </form>

        <a href="menu.php">Back to Menu</a>
    </div>
</body>
</html>
