<?php
session_start();
require_once '../../../backend/config/db.php';
require_once '../../../backend/classes/Streak.php';

if (!isset($_SESSION['IsAdmin']) || !$_SESSION['IsAdmin']) {
    header("Location: ../../login.php");
    exit();
}

$streak   = new Streak($conn);
$streakID = $_GET['id'] ?? 0;
$record   = $streak->findStreakById($streakID)->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $streak->deleteStreak($streakID);
    header("Location: ../admin_streaks.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Streak</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<h1>Delete Streak</h1>

<p>Are you sure you want to delete this record?</p>

<table border="1">
    <thead>
        <tr>
            <th>Username</th>
            <th>Habit</th>
            <th>Current Streak</th>
            <th>Longest Streak</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= htmlspecialchars($record['Username']) ?></td>
            <td><?= htmlspecialchars($record['HabitName']) ?></td>
            <td><?= $record['CurrentStreak'] ?></td>
            <td><?= $record['LongestStreak'] ?></td>
        </tr>
    </tbody>
</table>
<br>
<form method="POST">
    <button type="submit" style="color:red">
        Yes Delete
    </button>
</form>
<a href="../admin_streaks.php">Cancel — Back to Admin</a>
</body>
</html>