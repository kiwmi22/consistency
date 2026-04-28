<?php
// ============================================
// File:      admin_streaks.php
// Author:    Juna Bhujel
// Component: Progress & Statistics — Admin
// Sprint 1 - Thursday 24th April 2026
// ============================================

session_start();
require_once '../../backend/config/db.php';
require_once '../../backend/classes/Streak.php';

// Restrict to admin only
if (!isset($_SESSION['IsAdmin']) || !$_SESSION['IsAdmin']) {
    header("Location: login.php");
    exit();
}

$streak  = new Streak($conn);
$streaks = $streak->getAllStreaksAdmin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin — All Streaks</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Admin — All Streak Records</h1>

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Habit</th>
            <th>Current Streak</th>
            <th>Longest Streak</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $streaks->fetch_assoc()): ?>
        <tr>
            <td><?= $row['StreakID'] ?></td>
            <td><?= htmlspecialchars($row['Username']) ?></td>
            <td><?= htmlspecialchars($row['HabitName']) ?></td>
            <td><?= $row['CurrentStreak'] ?></td>
            <td><?= $row['LongestStreak'] ?></td>
            <td><?= $row['IsActive'] ? 'Active' : 'Inactive' ?></td>
            <td><?= $row['LastUpdated'] ?></td>
            <td>
                <a href="reset_streak.php?id=<?= $row['StreakID'] ?>">Reset</a> |
                <a href="edit_streak.php?id=<?= $row['StreakID'] ?>">Edit</a> |
                <a href="delete_streak.php?id=<?= $row['StreakID'] ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="menu.php">Back to Menu</a>

</body>
</html>