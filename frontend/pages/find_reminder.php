<?php
/**
 * Find Reminder Page - Search for specific reminder by ID
 * Displays all reminder details if found
 * Author: Pratik Tamang
 */

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$message = "";
$reminderFound = false;

// Process search when ID is provided
if (isset($_GET['id'])) {
    // Validate ID is numeric to prevent SQL injection
    if (!is_numeric($_GET['id'])) {
        $message = "<p style='color: red;'>Error: Please enter a valid numeric Reminder ID.</p>";
    } else {
        $reminder->ReminderID = $_GET['id'];
        
        // Call findById method which executes sp_GetReminderById
        if ($reminder->findById()) {
            $reminderFound = true;
        } else {
            $message = "<p style='color: red;'>No reminder found with ID: " . htmlspecialchars($_GET['id']) . "</p>";
        }
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
    <h1>Find Reminder</h1>
    
    <!-- Search form uses GET method to keep ID in URL -->
    <form method="GET" action="">
        <label for="id">Enter Reminder ID:</label><br>
        <input type="number" id="id" name="id" required min="1" 
               value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>" 
               placeholder="e.g., 1"><br><br>
        <button type="submit">Search</button>
    </form>
    
    <br>
    
    <?php echo $message; ?>
    
    <?php if ($reminderFound) { ?>
        <h2>Reminder Details</h2>
        <!-- Display reminder in vertical table format -->
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
        <!-- Quick action links for found reminder -->
        <a href="edit_reminder.php?id=<?php echo $reminder->ReminderID; ?>">Edit this Reminder</a> | 
        <a href="delete_reminder.php?id=<?php echo $reminder->ReminderID; ?>" style="color: red;">Delete this Reminder</a>
    <?php } ?>
    
    <br><br>
    <a href="list_reminders.php">View All Reminders</a> | 
    <a href="../index.html">Back to Main Menu</a>
</body>
</html>