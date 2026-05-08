<?php
session_start();

//temporary - remove when krishna 
$_SESSION['UserID'] = 1;

require_once '../../../backend/config/db.php';
require_once '../../../backend/classes/Streak.php';

if (!isset($_SESSION['IsAdmin']) || !$_SESSION['IsAdmin']) {
    header("Location: ../../login.php");
    exit();
}

$streak = new Streak($conn);
$record = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $streakID = $_POST['StreakID'];
    $result   = $streak->findStreakById($streakID);
    $record   = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Streak</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<h1>Find Streak by ID</h1>

<form method="POST">
    <label>Streak ID:</label><br>
    <input type="number" name="StreakID" required>
    <br><br>
    <button type="submit">Find</button>
</form>

<?php if ($record): ?>
<br>
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
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $record['StreakID'] ?></td>
            <td><?= htmlspecialchars($record['Username']) ?></td>
            <td><?= htmlspecialchars($record['HabitName']) ?></td>
            <td><?= $record['CurrentStreak'] ?></td>
            <td><?= $record['LongestStreak'] ?></td>
            <td><?= $record['IsActive'] ? 'Active' : 'Inactive' ?></td>
            <td><?= $record['LastUpdated'] ?></td>
        </tr>
    </tbody>
</table>
<?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
    <p style="color:red">No streak found with that ID</p>
<?php endif; ?>
<br>
<a href="../admin_streaks.php">Back to Admin</a>
</body>
</html>