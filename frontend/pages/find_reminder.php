<?php
// Find Reminder Page - Presentation layer
// Allows users to search for a specific reminder by ID

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$message = "";
$reminderFound = false;

// Process search
if (isset($_GET['search']) && !empty($_GET['ReminderID'])) {
    if (is_numeric($_GET['ReminderID'])) {
        $reminder->ReminderID = $_GET['ReminderID'];
        if ($reminder->findById()) {
            $reminderFound = true;
        } else {
            $message = "<p style='color: red;'>No reminder found with ID: " . htmlspecialchars($_GET['ReminderID']) . "</p>";
        }
    } else {
        $message = "<p style='color: red;'>Please enter a valid numeric Reminder ID.</p>";
    }
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
    <nav>
        <a href="../index.html">Home</a>
        <a href="add_reminder.php">Add Reminder</a>
        <a href="list_reminder.php">List Reminders</a>
        <a href="find_reminder.php">Find Reminder</a>
        <a href="filter_reminder.php">Filter Reminders</a>
    </nav>

    <h1>Find Reminder</h1>

    <!-- Search Form -->
    <form method="GET" action="">
        <label for="ReminderID">Enter Reminder ID:</label><br>
        <input type="number" id="ReminderID" name="ReminderID" min="1" required 
               value="<?php echo isset($_GET['ReminderID']) ? htmlspecialchars($_GET['ReminderID']) : ''; ?>"><br><br>
        <button type="submit" name="search">Search</button>
    </form>

    <?php echo $message; ?>

    <!-- Display Result -->
    <?php if ($reminderFound): ?>
        <h2>Reminder Details</h2>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Reminder ID</td>
                <td><?php echo htmlspecialchars($reminder->ReminderID); ?></td>
            </tr>
            <tr>
                <td>User ID</td>
                <td><?php echo htmlspecialchars($reminder->UserID); ?></td>
            </tr>
            <tr>
                <td>Habit ID</td>
                <td><?php echo htmlspecialchars($reminder->HabitID); ?></td>
            </tr>
            <tr>
                <td>Reminder Time</td>
                <td><?php echo htmlspecialchars($reminder->ReminderTime); ?></td>
            </tr>
            <tr>
                <td>Enabled</td>
                <td><?php echo $reminder->IsEnabled ? 'Yes' : 'No'; ?></td>
            </tr>
            <tr>
                <td>Message</td>
                <td><?php echo htmlspecialchars($reminder->Message); ?></td>
            </tr>
            <tr>
                <td>Created Date</td>
                <td><?php echo htmlspecialchars($reminder->CreatedDate); ?></td>
            </tr>
        </table>

        <p>
            <a href="edit_reminder.php?id=<?php echo $reminder->ReminderID; ?>">Edit this Reminder</a> | 
            <a href="delete_reminder.php?id=<?php echo $reminder->ReminderID; ?>">Delete this Reminder</a>
        </p>
    <?php endif; ?>
</body>
</html>