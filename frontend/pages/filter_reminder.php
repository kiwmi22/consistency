<?php
// Filter Reminders Page - Presentation layer
// Allows users to filter reminders by habit, status, or user

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$stmt = null;
$filterApplied = false;
$filterType = "";

// Process filter
if (isset($_GET['filter'])) {
    $filterApplied = true;

    // Filter by Habit
    if (!empty($_GET['HabitID'])) {
        $reminder->HabitID = $_GET['HabitID'];
        $stmt = $reminder->filterByHabit();
        $filterType = "Habit ID: " . htmlspecialchars($_GET['HabitID']);
    }
    // Filter by Status
    elseif (isset($_GET['Status']) && $_GET['Status'] !== 'all') {
        $reminder->IsEnabled = ($_GET['Status'] === 'enabled') ? 1 : 0;
        $stmt = $reminder->filterByStatus();
        $filterType = ($_GET['Status'] === 'enabled') ? "Enabled Reminders" : "Disabled Reminders";
    }
    // Filter by User
    elseif (!empty($_GET['UserID'])) {
        $reminder->UserID = $_GET['UserID'];
        $stmt = $reminder->filterByUser();
        $filterType = "User ID: " . htmlspecialchars($_GET['UserID']);
    }
    // Show all if no filter selected
    else {
        $stmt = $reminder->listAll();
        $filterType = "All Reminders";
    }
} else {
    // Default: show all
    $stmt = $reminder->listAll();
    $filterType = "All Reminders";
    $filterApplied = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Reminders - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav>
        <a href="../index.html">Home</a>
        <a href="add_reminder.php">Add Reminder</a>
        <a href="list_reminder.php">List Reminders</a>
        <a href="find_reminder.php">Find Reminder</a>
        <a href="filter_reminder.php">Filter Reminders</a>
    </nav>

    <h1>Filter Reminders</h1>

    <!-- Filter Form -->
    <form method="GET" action="" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>Select Filter:</h3>

        <label for="HabitID">Filter by Habit ID:</label>
        <input type="number" id="HabitID" name="HabitID" min="1" 
               value="<?php echo isset($_GET['HabitID']) ? htmlspecialchars($_GET['HabitID']) : ''; ?>">
        <br><br>

        <label for="Status">Filter by Status:</label>
        <select id="Status" name="Status">
            <option value="all" <?php echo (!isset($_GET['Status']) || $_GET['Status'] === 'all') ? 'selected' : ''; ?>>All</option>
            <option value="enabled" <?php echo (isset($_GET['Status']) && $_GET['Status'] === 'enabled') ? 'selected' : ''; ?>>Enabled Only</option>
            <option value="disabled" <?php echo (isset($_GET['Status']) && $_GET['Status'] === 'disabled') ? 'selected' : ''; ?>>Disabled Only</option>
        </select>
        <br><br>

        <label for="UserID">Filter by User ID (Admin):</label>
        <input type="number" id="UserID" name="UserID" min="1"
               value="<?php echo isset($_GET['UserID']) ? htmlspecialchars($_GET['UserID']) : ''; ?>">
        <br><br>

        <button type="submit" name="filter">Apply Filter</button>
        <a href="filter_reminders.php" style="margin-left: 10px;">Reset Filters</a>
    </form>

    <!-- Display Results -->
    <?php if ($filterApplied): ?>
        <h2>Results: <?php echo $filterType; ?></h2>

        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Habit ID</th>
                    <th>Time</th>
                    <th>Enabled</th>
                    <th>Message</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ReminderID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['HabitID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ReminderTime']) . "</td>";
                        echo "<td>" . ($row['IsEnabled'] ? 'Yes' : 'No') . "</td>";
                        echo "<td>" . htmlspecialchars($row['Message']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CreatedDate']) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_reminder.php?id=" . $row['ReminderID'] . "'>Edit</a> | ";
                        echo "<a href='delete_reminder.php?id=" . $row['ReminderID'] . "'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No reminders found matching the selected filter.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>