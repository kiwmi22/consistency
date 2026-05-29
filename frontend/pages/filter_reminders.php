<?php
/**
 * Filter Reminders Page - Filter reminders by habit, status, or user
 * Uses dropdowns for user-friendly filtering
 * Author: Pratik Tamang
 */

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$filterApplied = false;
$filterType = "All Reminders";

// Fetch habits and users for dropdown filters
$habitQuery = "SELECT HabitID, HabitName FROM tblHabits WHERE IsActive = 1 ORDER BY HabitName";
$habitStmt = $db->query($habitQuery);

$userQuery = "SELECT UserID, Username FROM tblUsers WHERE IsActive = 1 ORDER BY Username";
$userStmt = $db->query($userQuery);

// Determine which filter to apply based on GET parameters
// Priority: HabitID > Status > UserID > All
if (isset($_GET['HabitID']) && !empty($_GET['HabitID']) && is_numeric($_GET['HabitID'])) {
    // Filter by Habit
    $reminder->HabitID = $_GET['HabitID'];
    $stmt = $reminder->filterByHabit();
    $filterApplied = true;
    $filterType = "Habit ID: " . $_GET['HabitID'];
    
} elseif (isset($_GET['Status']) && !empty($_GET['Status'])) {
    // Filter by enabled/disabled status
    $status = $_GET['Status'];
    if ($status == 'enabled') {
        $reminder->IsEnabled = 1;
    } elseif ($status == 'disabled') {
        $reminder->IsEnabled = 0;
    }
    $stmt = $reminder->filterByStatus();
    $filterApplied = true;
    $filterType = "Status: " . ucfirst($status);
    
} elseif (isset($_GET['UserID']) && !empty($_GET['UserID']) && is_numeric($_GET['UserID'])) {
    // Filter by User (typically for admin use)
    $reminder->UserID = $_GET['UserID'];
    $stmt = $reminder->filterByUser();
    $filterApplied = true;
    $filterType = "User ID: " . $_GET['UserID'];
    
} else {
    // No filter applied - show all reminders
    $stmt = $reminder->listAll();
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
    <h1>Filter Reminders</h1>
    
    <!-- Filter form with multiple dropdown options -->
    <form method="GET" action="">
        <fieldset>
            <legend>Select Filter Options</legend>
            
            <!-- Habit dropdown filter -->
            <label for="HabitID">Filter by Habit:</label><br>
            <select id="HabitID" name="HabitID">
                <option value="">-- All Habits --</option>
                <?php while ($habit = $habitStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?php echo $habit['HabitID']; ?>"
                            <?php echo (isset($_GET['HabitID']) && $_GET['HabitID'] == $habit['HabitID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($habit['HabitName']); ?>
                    </option>
                <?php } ?>
            </select><br><br>
            
            <!-- Status dropdown filter -->
            <label for="Status">Filter by Status:</label><br>
            <select id="Status" name="Status">
                <option value="">-- All Status --</option>
                <option value="enabled" <?php echo (isset($_GET['Status']) && $_GET['Status'] == 'enabled') ? 'selected' : ''; ?>>Enabled</option>
                <option value="disabled" <?php echo (isset($_GET['Status']) && $_GET['Status'] == 'disabled') ? 'selected' : ''; ?>>Disabled</option>
            </select><br><br>
            
            <!-- User dropdown filter (for admin use) -->
            <label for="UserID">Filter by User (Admin only):</label><br>
            <select id="UserID" name="UserID">
                <option value="">-- All Users --</option>
                <?php while ($user = $userStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?php echo $user['UserID']; ?>"
                            <?php echo (isset($_GET['UserID']) && $_GET['UserID'] == $user['UserID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($user['Username']); ?>
                    </option>
                <?php } ?>
            </select><br><br>
            
            <button type="submit">Apply Filter</button>
            <a href="filter_reminders.php">Reset Filters</a>
        </fieldset>
    </form>
    
    <br>
    
    <?php if ($filterApplied) { ?>
        <h2>Results: <?php echo $filterType; ?></h2>
        
        <!-- Display filtered results in table format -->
        <table border="1" cellpadding="10" cellspacing="0">
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
                $count = 0;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $count++;
                    echo "<tr>";
                    echo "<td>" . $row['ReminderID'] . "</td>";
                    echo "<td>" . $row['UserID'] . "</td>";
                    echo "<td>" . $row['HabitID'] . "</td>";
                    echo "<td>" . $row['ReminderTime'] . "</td>";
                    echo "<td>" . (($row['IsEnabled'] == 1) ? 'Yes' : 'No') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Message']) . "</td>";
                    echo "<td>" . $row['CreatedDate'] . "</td>";
                    echo "<td>";
                    echo "<a href='edit_reminder.php?id=" . $row['ReminderID'] . "'>Edit</a> | ";
                    echo "<a href='delete_reminder.php?id=" . $row['ReminderID'] . "' style='color: red;'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                
                if ($count == 0) {
                    echo "<tr><td colspan='8' style='text-align: center;'>No reminders found matching the selected filter.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <p>Total Results: <?php echo $count; ?></p>
    <?php } ?>
    
    <br>
    <a href="list_reminders.php">View All Reminders</a> | 
    <a href="../index.html">Back to Main Menu</a>
</body>
</html>