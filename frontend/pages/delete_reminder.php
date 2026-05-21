<?php
/**
 * Delete Reminder Page - Allows users to delete reminders with confirmation
 * Shows all reminder details before deletion to prevent accidents
 * Author: Pratik Tamang
 */

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);
$message = "";

// Validate ReminderID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $reminder->ReminderID = $_GET['id'];
    
    // Fetch reminder details to display for confirmation
    if (!$reminder->findById()) {
        $message = "<p style='color: red;'>Error: Reminder not found.</p>";
        header("refresh:2;url=list_reminders.php");
        exit();
    }
} else {
    $message = "<p style='color: red;'>Error: Invalid Reminder ID.</p>";
    header("refresh:2;url=list_reminders.php");
    exit();
}

// Process deletion confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ReminderID']) && is_numeric($_POST['ReminderID'])) {
        $reminder->ReminderID = $_POST['ReminderID'];
        
        // Call delete method which executes sp_DeleteReminder
        if ($reminder->delete()) {
            // Redirect to list page with success parameter
            header("Location: list_reminders.php?deleted=1");
            exit();
        } else {
            $message = "<p style='color: red;'>Error: Unable to delete reminder.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Reminder - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Delete Reminder</h1>
    
    <?php echo $message; ?>
    
    <h2 style="color: red;">⚠️ Are you sure you want to delete this reminder?</h2>
    
    <!-- Display all reminder details for user confirmation -->
    <table border="1" cellpadding="10">
        <tr>
            <th>Reminder ID</th>
            <td><?php echo $reminder->ReminderID; ?></td>
        </tr>
        <tr>
            <th>User ID</th>
            <td><?php echo $reminder->UserID; ?></td>
        </tr>
        <tr>
            <th>Habit ID</th>
            <td><?php echo $reminder->HabitID; ?></td>
        </tr>
        <tr>
            <th>Reminder Time</th>
            <td><?php echo $reminder->ReminderTime; ?></td>
        </tr>
        <tr>
            <th>Enabled</th>
            <td><?php echo ($reminder->IsEnabled == 1) ? 'Yes' : 'No'; ?></td>
        </tr>
        <tr>
            <th>Message</th>
            <td><?php echo htmlspecialchars($reminder->Message); ?></td>
        </tr>
        <tr>
            <th>Created Date</th>
            <td><?php echo $reminder->CreatedDate; ?></td>
        </tr>
    </table>
    
    <br>
    
    <!-- Confirmation form with red warning button -->
    <form method="POST" action="">
        <input type="hidden" name="ReminderID" value="<?php echo $reminder->ReminderID; ?>">
        <button type="submit" style="background-color: red; color: white; padding: 10px 20px;">
            Yes, Delete This Reminder
        </button>
    </form>
    
    <br>
    <a href="list_reminders.php">Cancel</a>
    
    <br><br>
    <a href="../index.html">Back to Main Menu</a>
</body>
</html>