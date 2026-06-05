<?php
require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$habitStmt = $db->query("SELECT HabitID, HabitName FROM tblHabits WHERE IsActive = 1 ORDER BY HabitName");
$searchPerformed = false;
$selectedHabitName = "";

if (isset($_GET['HabitID']) && !empty($_GET['HabitID']) && is_numeric($_GET['HabitID'])) {
    $reminder->HabitID = $_GET['HabitID'];
    
    $nameStmt = $db->prepare("SELECT HabitName FROM tblHabits WHERE HabitID = :id");
    $nameStmt->bindParam(':id', $reminder->HabitID);
    $nameStmt->execute();
    $habitRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
    $selectedHabitName = $habitRow ? $habitRow['HabitName'] : "";
    
    $resultStmt = $reminder->filterByHabit();
    $searchPerformed = true;
}
?>
<!DOCTYPE html>
<html><head><title>Find Reminder</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <h1>Find Reminder by Habit</h1>
    <form method="GET">
        <label>Select Habit:</label><br>
        <select name="HabitID" required>
            <option value="">-- Select a Habit --</option>
            <?php while ($h = $habitStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $h['HabitID']; ?>" <?php if (isset($_GET['HabitID']) && $_GET['HabitID']==$h['HabitID']) echo 'selected'; ?>><?php echo htmlspecialchars($h['HabitName']); ?></option>
            <?php } ?>
        </select><br>
        <button type="submit">Search</button>
    </form>
    
    <?php if ($searchPerformed) { ?>
        <h2>Reminders for: <?php echo htmlspecialchars($selectedHabitName); ?></h2>
        <table>
            <tr><th>ID</th><th>User</th><th>Habit</th><th>Time</th><th>Enabled</th><th>Message</th><th>Created</th><th>Actions</th></tr>
            <?php
            $count = 0;
            while ($row = $resultStmt->fetch(PDO::FETCH_ASSOC)) {
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
    <?php } ?>
    
    <br><a href="list_reminders.php">All Reminders</a> | <a href="../index.html">← Main Menu</a>
</body></html>
