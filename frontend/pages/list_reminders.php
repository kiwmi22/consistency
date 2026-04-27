<?php
// List Reminders Page - Presentation layer
// Displays all reminders in a table

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$stmt = $reminder->listAll();
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
    <nav>
        <a href="../index.html">Home</a>
        <a href="add_reminder.php">Add Reminder</a>
        <a href="list_reminders.php">List Reminders</a>
    </nav>

    <h1>All Reminders</h1>

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
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No reminders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>