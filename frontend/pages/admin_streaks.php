<?php
session_start();

$_SESSION['UserID']  = 1;
$_SESSION['IsAdmin'] = true;

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

<!-- ✅ Navigation Links -->
<nav>
    <a href="streak/add_streak.php">➕ Add New Streak</a> |
    <a href="streak/filter_streak.php">🔍 Filter Streaks</a> |
    <a href="streak/find_streak.php">🔎 Find Streak by ID</a> |
    <a href="progress.php">📊 My Progress</a> |
    <a href="../../index.html">🏠 Main Menu</a>
</nav>

<br>

<!-- ✅ All Streaks Table -->
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
                <!-- ✅ Points to streak/ subfolder -->
                <a href="streak/edit_streak.php?id=<?= $row['StreakID'] ?>">Edit</a> |
                <a href="streak/delete_streak.php?id=<?= $row['StreakID'] ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<br>
<a href="../../index.html">Back to Main Menu</a>

</body>
</html>