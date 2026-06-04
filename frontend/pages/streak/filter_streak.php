<?php
// ============================================
// File:      filter_streak.php
// Author:    Juna Bhujel
// Component: Progress & Statistics
// Stories:   2, 3, 4 — Filter Streaks
// ============================================

// Start session
session_start();

// TEMPORARY — remove when login connected
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
$streak  = new Streak($conn);
$results = null;
$error   = "";
$filter  = "";
$count   = 0;

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get which filter type was selected
    $filter = $_POST['filter_type'] ?? '';

    // -----------------------------------------------
    // FILTER BY HABIT — Story 2
    // -----------------------------------------------
    if ($filter == 'habit') {

        // Validate HabitID
        if (empty($_POST['HabitID'])) {
            $error = "Please enter a Habit ID";
        } elseif (!is_numeric($_POST['HabitID'])) {
            $error = "Habit ID must be a number";
        } else {
            // Call filterByHabit method
            $results = $streak->filterByHabit(
                (int)$_POST['HabitID']
            );
            $count = $results->num_rows;
        }

    // -----------------------------------------------
    // FILTER BY STATUS — Story 3
    // -----------------------------------------------
    } elseif ($filter == 'status') {

        // Get checkbox value
        $isActive = isset($_POST['IsActive']) ? 1 : 0;

        // Call filterByStatus method
        $results = $streak->filterByStatus($isActive);
        $count   = $results->num_rows;

    // -----------------------------------------------
    // FILTER BY LENGTH — Story 4
    // -----------------------------------------------
    } elseif ($filter == 'length') {

        // Validate MinLength
        if (empty($_POST['MinLength'])) {
            $error = "Please enter a minimum length";
        } elseif (!is_numeric($_POST['MinLength'])) {
            $error = "Minimum length must be a number";
        } else {
            // Call filterByLength method
            $results = $streak->filterByLength(
                (int)$_POST['MinLength']
            );
            $count = $results->num_rows;
        }
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

<!-- Navigation -->
<nav>
    <a href="../admin_streaks.php">📋 All Streaks</a> |
    <a href="add_streak.php">➕ Add</a> |
    <a href="find_streak.php">🔎 Find</a> |
    <a href="../../../index.html">🏠 Menu</a>
</nav>

<h1>Filter Streaks</h1>

<!-- Filter Form -->
<form method="POST">

    <!-- Filter type dropdown -->
    <label>Filter By:</label><br>
    <select name="filter_type">
        <option value="habit">
            Habit ID
        </option>
        <option value="status">
            Active Status
        </option>
        <option value="length">
            Minimum Streak Length
        </option>
    </select>

    <br><br>

    <!-- Story 2 — Filter by Habit input -->
    <label>Habit ID:</label><br>
    <input type="number"
           name="HabitID"
           min="1"
           placeholder="e.g. 1">

    <br><br>

    <!-- Story 3 — Filter by Status checkbox -->
    <label>Active Streaks Only:</label>
    <input type="checkbox"
           name="IsActive"
           checked>

    <br><br>

    <!-- Story 4 — Filter by Length input -->
    <label>Minimum Streak Length (days):</label><br>
    <input type="number"
           name="MinLength"
           min="0"
           placeholder="e.g. 5">

    <br><br>
    <button type="submit">🔍 Apply Filter</button>
</form>

<!-- Show error if any -->
<?php if ($error): ?>
    <div class="error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Show results -->
<?php if ($results): ?>
<br>
<h2>Filter Results</h2>

    <?php if ($count == 0): ?>
        <div class="error">
            No streaks found matching your filter
        </div>

    <?php else: ?>
        <p>
            Found <strong><?= $count ?></strong> records
        </p>

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
                <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['StreakID'] ?></td>
                    <td>
                        <?= htmlspecialchars($row['Username']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['HabitName']) ?>
                    </td>
                    <td>
                        <?= $row['CurrentStreak'] ?> days
                    </td>
                    <td>
                        <?= $row['LongestStreak'] ?> days
                    </td>
                    <td>
                        <?= $row['IsActive']
                            ? '✅ Active'
                            : '❌ Inactive' ?>
                    </td>
                    <td><?= $row['LastUpdated'] ?></td>
                    <td>
                        <a class="edit-link"
                           href="edit_streak.php?id=
                           <?= $row['StreakID'] ?>">
                           Edit
                        </a> |
                        <a class="delete-link"
                           href="delete_streak.php?id=
                           <?= $row['StreakID'] ?>">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<br>
<a class="back-link" href="../admin_streaks.php">
    ← Back to Admin
</a>

</body>
</html>