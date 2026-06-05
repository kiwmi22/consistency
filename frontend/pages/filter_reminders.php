<?php
require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$habitStmt = $db->query("SELECT HabitID, HabitName FROM tblHabits WHERE IsActive = 1 ORDER BY HabitName");
$userStmt = $db->query("SELECT UserID, Username FROM tblUsers WHERE IsActive = 1 ORDER BY Username");

$filterType = "All Reminders";
$stmt = null;

if (isset($_GET['HabitID']) && !empty($_GET['HabitID'])) {
    $reminder->HabitID = $_GET['HabitID'];
    $stmt = $reminder->filterByHabit();
    $filterType = "Habit ID: " . $_GET['HabitID'];
} elseif (isset($_GET['Status']) && !empty($_GET['Status'])) {
    $reminder->IsEnabled = $_GET['Status'] == 'enabled' ? 1 : 0;
    $stmt = $reminder->filterByStatus();
    $filterType = "Status: " . ucfirst($_GET['Status']);
} elseif (isset($_GET['UserID']) && !empty($_GET['UserID'])) {
    $reminder->UserID = $_GET['UserID'];
    $stmt = $reminder->filterByUser();
    $filterType = "User ID: " . $_GET['UserID'];
} else {
    $stmt = $reminder->listAll();
}
?>
<!DOCTYPE html>
<html><head><title>Filter Reminders</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <h1>Filter Reminders</h1>
    
    <form method="GET">
        <fieldset>
            <legend>Filter Options</legend>
            
            <label>By Habit:</label><br>
            <select name="HabitID">
                <option value="">-- All Habits --</option>
                <?php while ($h = $habitStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?php echo $h['HabitID']; ?>"><?php echo htmlspecialchars($h['HabitName']); ?></option>
                <?php } ?>
            </select><br>
            
            <label>By Status:</label><br>
            <select name="Status">
                <option value="">-- All --</option>
                <option value="enabled">Enabled</option>
                <option value="disabled">Disabled</option>
            </select><br>
            
            <label>By User:</label><br>
            <select name="UserID">
                <option value="">-- All Users --</option>
                <?php while ($u = $userStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?php echo $u['UserID']; ?>"><?php echo htmlspecialchars($u['Username']); ?></option>
                <?php } ?>
            </select><br>
            
            <button type="submit">Apply Filter</button>
            <a href="filter_reminders.php">Reset</a>
        </fieldset>
    </form>
    
    <h2>Results: <?php echo $filterType; ?></h2>
    <table>
        <tr><th>ID</th><th>User</th><th>Habit</th><th>Time</th><th>Enabled</th><th>Message</th><th>Created</th><th>Actions</th></tr>
        <?php
        $count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            echo "<tr>";
            echo "<td>" . $row['ReminderID'] . "</td>";
            echo "<td>" . $row['UserID'] . "</td>";
            echo "<td>" . $row['HabitID'] . "</td>";
            echo "<td>" . $row['ReminderTime'] . "</td>";
            echo "<td>" . ($row['IsEnabled'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . htmlspecialchars($row['Message']) . "</td>";
            echo "<td>" . $row['CreatedDate'] . "</td>";
            echo "<td><a href='edit_reminder.php?id=" . $row['ReminderID'] . "'>Edit</a> | <a href='delete_reminder.php?id=" . $row['ReminderID'] . "' style='color:red;'>Delete</a></td>";
            echo "</tr>";
        }
        if ($count == 0) echo "<tr><td colspan='8'>No reminders found.</td></tr>";
        ?>
    </table>
    <p>Total: <?php echo $count; ?></p>
    
    <br><a href="list_reminders.php">All Reminders</a> | <a href="../index.html">← Main Menu</a>
</body></html>
