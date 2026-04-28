<?php
// ============================================
// File:      progress.php
// Author:    Juna Bhujel
// Component: Progress & Statistics
// Sprint 1 - Thursday 24th April 2026
// ============================================

session_start();
require_once '../../backend/config/db.php';
require_once '../../backend/classes/Streak.php';

// Restrict to logged in users only
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['UserID'];
$streak = new Streak($conn);
$streaks = $streak->getStreakByUser($userID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Progress — Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>My Progress & Statistics</h1>

<table border="1">
    <thead>
        <tr>
            <th>Habit</th>
            <th>Current Streak</th>
            <th>Longest Streak</th>
            <th>Status</th>
            <th>Last Updated</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $streaks->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['HabitName']) ?></td>
            <td><?= $row['CurrentStreak'] ?> days</td>
            <td><?= $row['LongestStreak'] ?> days</td>
            <td><?= $row['IsActive'] ? 'Active' : 'Inactive' ?></td>
            <td><?= $row['LastUpdated'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="menu.php">Back to Menu</a>

</body>
</html>