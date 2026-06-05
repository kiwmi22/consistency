<?php
require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);
$stmt = $reminder->listAll();

$message = "";
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $message = "<p style='color: green;'>✓ Reminder deleted successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Reminders</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>All Reminders</h1>
    <?php echo $message; ?>
    
    <p><a href="add_reminder.php">➕ Add New Reminder</a></p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th><th>User ID</th><th>Habit ID</th><th>Time</th>
                <th>Enabled</th><th>Message</th><th>Created</th><th>Actions</th>
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
                echo "<tr><td colspan='8' style='text-align: center;'>No reminders. <a href='add_reminder.php'>Add one</a></td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <p>Total: <?php echo $count; ?></p>
    
    <br>
    <a href="find_reminder.php">Find Reminder</a> | 
    <a href="filter_reminders.php">Filter Reminders</a> | 
    <a href="../index.html">← Main Menu</a>
</body>
</html>
