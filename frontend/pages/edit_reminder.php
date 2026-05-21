<?php
/**
 * Edit Reminder Page - Allows users to update existing reminders
 * Pre-populates form with current reminder data
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
    
    // Fetch reminder details to populate form
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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $reminder->UserID = htmlspecialchars(strip_tags($_POST['UserID']));
    $reminder->HabitID = htmlspecialchars(strip_tags($_POST['HabitID']));
    $reminder->ReminderTime = htmlspecialchars(strip_tags($_POST['ReminderTime']));
    $reminder->IsEnabled = isset($_POST['IsEnabled']) ? 1 : 0;
    $reminder->Message = htmlspecialchars(strip_tags($_POST['Message']));
    
    // Validate inputs
    $errors = [];
    if (!is_numeric($reminder->UserID)) {
        $errors[] = "User ID must be a valid number.";
    }
    if (!is_numeric($reminder->HabitID)) {
        $errors[] = "Habit ID must be a valid number.";
    }
    if (strlen($reminder->Message) > 255) {
        $errors[] = "Message must be 255 characters or less.";
    }
    
    // Update if validation passes
    if (empty($errors)) {
        if ($reminder->update()) {
            $message = "<p style='color: green;'>✓ Reminder updated successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error: Unable to update reminder.</p>";
        }
    } else {
        $message = "<p style='color: red;'>" . implode("<br>", $errors) . "</p>";
    }
}

// Fetch users and habits for dropdowns
$userQuery = "SELECT UserID, Username FROM tblUsers WHERE IsActive = 1 ORDER BY Username";
$userStmt = $db->query($userQuery);

$habitQuery = "SELECT HabitID, HabitName FROM tblHabits WHERE IsActive = 1 ORDER BY HabitName";
$habitStmt = $db->query($habitQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reminder - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Edit Reminder</h1>
    
    <?php echo $message; ?>
    
    <form method="POST" action="">
        <!-- Hidden field to preserve ReminderID -->
        <input type="hidden" name="ReminderID" value="<?php echo $reminder->ReminderID; ?>">
        
        <!-- User dropdown with current selection pre-selected -->
        <label for="UserID">Select User: *</label><br>
        <select id="UserID" name="UserID" required>
            <option value="">-- Select User --</option>
            <?php while ($user = $userStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $user['UserID']; ?>" 
                        <?php echo ($user['UserID'] == $reminder->UserID) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user['Username']); ?>
                </option>
            <?php } ?>
        </select><br><br>
        
        <!-- Habit dropdown with current selection pre-selected -->
        <label for="HabitID">Select Habit: *</label><br>
        <select id="HabitID" name="HabitID" required>
            <option value="">-- Select Habit --</option>
            <?php 
            $habitStmt->execute(); // Reset cursor for second loop
            while ($habit = $habitStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $habit['HabitID']; ?>"
                        <?php echo ($habit['HabitID'] == $reminder->HabitID) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($habit['HabitName']); ?>
                </option>
            <?php } ?>
        </select><br><br>
        
        <label for="ReminderTime">Reminder Time (HH:MM): *</label><br>
        <input type="time" id="ReminderTime" name="ReminderTime" required 
               value="<?php echo $reminder->ReminderTime; ?>"><br><br>
        
        <label for="IsEnabled">Enabled:</label>
        <input type="checkbox" id="IsEnabled" name="IsEnabled" 
               <?php echo ($reminder->IsEnabled == 1) ? 'checked' : ''; ?>><br><br>
        
        <label for="Message">Message (optional, max 255 characters):</label><br>
        <textarea id="Message" name="Message" rows="3" cols="40" maxlength="255"><?php echo htmlspecialchars($reminder->Message); ?></textarea><br><br>
        
        <!-- Display CreatedDate as read-only -->
        <label>Created Date:</label><br>
        <input type="text" value="<?php echo $reminder->CreatedDate; ?>" disabled><br><br>
        
        <button type="submit">Update Reminder</button>
        <a href="list_reminders.php">Cancel</a>
    </form>
    
    <br>
    <a href="../index.html">Back to Main Menu</a>
</body>
</html>