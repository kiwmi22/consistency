<?php
/**
 * Find Reminder Page - Search reminders by Habit Name
 * Users select a habit by name instead of remembering IDs
 * Author: Pratik Tamang
 */

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$message = "";
$searchPerformed = false;
$selectedHabitName = "";

// Fetch active habits for the dropdown
$habitQuery = "SELECT HabitID, HabitName FROM tblHabits WHERE IsActive = 1 ORDER BY HabitName";
$habitStmt = $db->query($habitQuery);

// Process search when a habit is selected
if (isset($_GET['HabitID']) && !empty($_GET['HabitID']) && is_numeric($_GET['HabitID'])) {
    $reminder->HabitID = $_GET['HabitID'];
    
    // Get the habit name for display
    $nameQuery = "SELECT HabitName FROM tblHabits WHERE HabitID = :HabitID";
    $nameStmt = $db->prepare($nameQuery);
    $nameStmt->bindParam(':HabitID', $reminder->HabitID);
    $nameStmt->execute();
    $habitRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
    $selectedHabitName = $habitRow ? $habitRow['HabitName'] : "Unknown";
    
    // Search reminders for this habit using existing filterByHabit method
    $resultStmt = $reminder->filterByHabit();
    $searchPerformed = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Reminder - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Find Reminder by Habit</h1>
    
    <!-- Search form with habit dropdown -->
    <form method="GET" action="">
        <label for="HabitID">Select Habit to Search:</label><br>
        <select id="HabitID" name="HabitID" required>
            <option value="">-- Select a Habit --</option>
            <?php while ($habit = $habitStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $habit['HabitID']; ?>"
                        <?php echo (isset($_GET['HabitID']) && $_GET['HabitID'] == $habit['HabitID']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($habit['HabitName']); ?>
                </option>
            <?php } ?>
        </select><br><br>
        <button type="submit">Search</button>
    </form>
    
    <br>
    
    <?php echo $message; ?>
    
    <?php if ($searchPerformed) { ?>
        <h2>Reminders for: <?php echo htmlspecialchars($selectedHabitName); ?></h2>
        
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
                while ($row = $resultStmt->fetch(PDO::FETCH_ASSOC)) {
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
                    echo "<tr><td colspan='8' style='text-align: center;'>No reminders found for this habit.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <p>Total Reminders Found: <?php echo $count; ?></p>
    <?php } ?>
    
    <br>
    <a href="list_reminders.php">View All Reminders</a> | 
    <a href="../index.html">Back to Main Menu</a>
</body>
</html>