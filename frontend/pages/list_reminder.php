<?php
/**
 * List Reminders Page - Displays all reminders in a table
 * Provides Edit and Delete links for each reminder
 * Author: Pratik Tamang
 */

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

// Get all reminders using listAll method
$stmt = $reminder->listAll();

// Check for success messages from redirects
$message = "";
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $message = "<p style='color: green; background-color: #d4edda; padding: 10px; border: 1px solid green;'>✓ Reminder deleted successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Reminders - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>All Reminders</h1>
    
    <?php echo $message; ?>
    
    <p><a href="add_reminder.php">Add New Reminder</a></p>
    
    <!-- Table displays all reminder records -->
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
            // Iterate through all reminders
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
                // Action links pass ReminderID as URL parameter
                echo "<td>";
                echo "<a href='edit_reminder.php?id=" . $row['ReminderID'] . "'>Edit</a> | ";
                echo "<a href='delete_reminder.php?id=" . $row['ReminderID'] . "' style='color: red;'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
            
            // Display message if no reminders found
            if ($count == 0) {
                echo "<tr><td colspan='8' style='text-align: center;'>No reminders found. <a href='add_reminder.php'>Add your first reminder</a></td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <p>Total Reminders: <?php echo $count; ?></p>
    
    <br>
    <a href="find_reminder.php">Find Reminder by ID</a> | 
    <a href="filter_reminders.php">Filter Reminders</a>
    
    <br><br>
    <a href="../index.html">Back to Main Menu</a>
</body>
</html>