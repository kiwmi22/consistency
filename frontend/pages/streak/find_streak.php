<?php
session_start();


$_SESSION['UserID']  = 1;
$_SESSION['IsAdmin'] = true;

// Connect to database
require_once '../../../backend/config/db.php';

// Load Streak class
require_once '../../../backend/classes/Streak.php';

// Restrict to admin only
if (!isset($_SESSION['IsAdmin']) || !$_SESSION['IsAdmin']) {
    header("Location: ../../login.php");
    exit();
}

// Create Streak object
$streak = new Streak($conn);

// Set empty variables
$record = null;
$error  = "";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check StreakID is not empty
    if (empty($_POST['StreakID'])) {
        $error = "Please enter a Streak ID";

    // Check StreakID is a number
    } elseif (!is_numeric($_POST['StreakID'])) {
        $error = "Streak ID must be a number";

    } else {
        // Convert to integer for safety
        $streakID = (int)$_POST['StreakID'];

        // Call findStreakById method
        $result = $streak->findStreakById($streakID);

        // Get the record
        $record = $result->fetch_assoc();

        // If nothing found show error
        if (!$record) {
            $error = "No streak found with ID: " . $streakID;
        }
    }
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

<!-- Navigation -->
<nav>
    <a href="../admin_streaks.php">📋 All Streaks</a> |
    <a href="add_streak.php">➕ Add</a> |
    <a href="filter_streak.php">🔍 Filter</a> |
    <a href="../../../index.html">🏠 Menu</a>
</nav>

<h1>Find Streak by ID</h1>

<!-- Search Form -->
<form method="POST">
    <label>Enter Streak ID:</label><br>
    <input type="number"
           name="StreakID"
           min="1"
           placeholder="e.g. 1"
           required>
    <br><br>
    <button type="submit">🔎 Find Streak</button>
</form>

<!-- Show error if any -->
<?php if ($error): ?>
    <div class="error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Show result if found -->
<?php if ($record): ?>
<br>
<h2>Streak Found</h2>
<table>
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
        <tr>
            <td><?= $record['StreakID'] ?></td>
            <td>
                <?= htmlspecialchars($record['Username']) ?>
            </td>
            <td>
                <?= htmlspecialchars($record['HabitName']) ?>
            </td>
            <td><?= $record['CurrentStreak'] ?> days</td>
            <td><?= $record['LongestStreak'] ?> days</td>
            <td>
                <?= $record['IsActive']
                    ? '✅ Active'
                    : '❌ Inactive' ?>
            </td>
            <td><?= $record['LastUpdated'] ?></td>
            <td>
                <a class="edit-link"
                   href="edit_streak.php?id=
                   <?= $record['StreakID'] ?>">
                   Edit
                </a> |
                <a class="delete-link"
                   href="delete_streak.php?id=
                   <?= $record['StreakID'] ?>">
                   Delete
                </a>
            </td>
        </tr>
    </tbody>
</table>
<?php endif; ?>

<br>
<a class="back-link" href="../admin_streaks.php">
    ← Back to Admin
</a>

</body>
</html>