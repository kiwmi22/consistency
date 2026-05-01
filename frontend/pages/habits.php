<?php
session_start();
require_once '../../backend/config/db-connect.php';
require_once '../../backend/classes/Habit.php';

// Redirect if not logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$habit = new Habit($conn);
$userID = $_SESSION['UserID'];

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search !== '') {
    $results = $habit->findHabit($userID, $search);
} else {
    $results = $habit->listHabits($userID);
}

// Handle filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Habits - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>My Habits</h1>

<!-- Search and Filter -->
<form method="GET" action="habits.php">
    <input type="text" name="search" placeholder="Search habits..." value="<?php echo htmlspecialchars($search); ?>">
    <select name="filter">
        <option value="">Filter by frequency</option>
        <?php for ($i = 1; $i <= 7; $i++): ?>
            <option value="<?php echo $i; ?>" <?php echo ($filter == $i) ? 'selected' : ''; ?>>
                <?php echo $i; ?> day(s) per week
            </option>
        <?php endfor; ?>
    </select>
    <button type="submit">Search</button>
    <a href="habits.php">Clear</a>
</form>

<a href="add-habit.php">+ Add New Habit</a>

<!-- Habits Table -->
<table>
    <thead>
        <tr>
            <th>Habit Name</th>
            <th>Description</th>
            <th>Target Per Week</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($results && $results->num_rows > 0):
        while ($row = $results->fetch_assoc()):
            // Apply filter if set
            if ($filter !== '' && $row['TargetPerWeek'] != $filter) continue;
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row['HabitName']); ?></td>
            <td><?php echo htmlspecialchars($row['Description']); ?></td>
            <td><?php echo $row['TargetPerWeek']; ?></td>
            <td>
                <a href="edit-habit.php?id=<?php echo $row['HabitID']; ?>">Edit</a>
                <a href="delete-habit.php?id=<?php echo $row['HabitID']; ?>"
                   onclick="return confirm('Are you sure you want to delete this habit?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; else: ?>
        <tr><td colspan="4">No habits found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>