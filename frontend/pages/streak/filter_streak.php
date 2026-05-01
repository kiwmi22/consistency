<?php
session_start();
require_once '../../../backend/config/db.php';
require_once '../../../backend/classes/Streak.php';

if (!isset($_SESSION['IsAdmin']) || !$_SESSION['IsAdmin']) {
    header("Location: ../../login.php");
    exit();
}

$streak  = new Streak($conn);
$results = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['UserID'])) {
        $results = $streak->filterStreakByUser($_POST['UserID']);
    } elseif (!empty($_POST['HabitID'])) {
        $results = $streak->filterStreakByHabit($_POST['HabitID']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Filter Streaks</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<h1>Filter Streaks</h1>

<form method="POST">
    <label>Filter by User ID:</label><br>
    <input type="number" name="UserID">
    <br><br>
    <label>Filter by Habit ID:</label><br>
    <input type="number" name="HabitID">
    <br><br>
    <button type="submit">Filter</button>
</form>

<?php if ($results): ?>
<br>
<table border="1">
    <thead>
        <tr>
            <th>Username</th>
            <th>Habit</th>
            <th>Current Streak</th>
            <th>Longest Streak</th>
            <th>Status</th>
            <th>Last Updated</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $results->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Username']) ?></td>
            <td><?= htmlspecialchars($row['HabitName']) ?></td>
            <td><?= $row['CurrentStreak'] ?></td>
            <td><?= $row['LongestStreak'] ?></td>
            <td><?= $row['IsActive'] ? 'Active' : 'Inactive' ?></td>
            <td><?= $row['LastUpdated'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php endif; ?>
<br>
<a href="../admin_streaks.php">Back to Admin</a>
</body>
</html>