<?php
/**
 * Add Reminder Page - Allows users to create new reminders
 * Uses dropdowns for User and Habit selection instead of manual ID entry
 * Author: Pratik Tamang - Reminders & Notifications Component
 */

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

// Initialize database connection and Reminder object
$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);
$message = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs to prevent XSS attacks
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
    
    // Add reminder if validation passes
    if (empty($errors)) {
        if ($reminder->add()) {
            $message = "<p style='color: green;'>✓ Reminder added successfully!</p>";
            $_POST = array(); // Clear form
        } else {
            $message = "<p style='color: red;'>Error: Unable to add reminder.</p>";
        }
    } else {
        $message = "<p style='color: red;'>" . implode("<br>", $errors) . "</p>";
    }
}

// Fetch active users and habits for dropdowns
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
    <title>Add Reminder - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Add Reminder</h1>
    
    <?php echo $message; ?>
    
    <form method="POST" action="">
        <!-- User dropdown - populated from tblUsers -->
        <label for="UserID">Select User: *</label><br>
        <select id="UserID" name="UserID" required>
            <option value="">-- Select User --</option>
            <?php while ($user = $userStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $user['UserID']; ?>" 
                        <?php echo (isset($_POST['UserID']) && $_POST['UserID'] == $user['UserID']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user['Username']); ?>
                </option>
            <?php } ?>
        </select><br><br>
        
        <!-- Habit dropdown - populated from tblHabits -->
        <label for="HabitID">Select Habit: *</label><br>
        <select id="HabitID" name="HabitID" required>
            <option value="">-- Select Habit --</option>
            <?php while ($habit = $habitStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $habit['HabitID']; ?>"
                        <?php echo (isset($_POST['HabitID']) && $_POST['HabitID'] == $habit['HabitID']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($habit['HabitName']); ?>
                </option>
            <?php } ?>
        </select><br><br>
        
        <!-- HTML5 time picker for easy time selection -->
        <label for="ReminderTime">Reminder Time (HH:MM): *</label><br>
        <input type="time" id="ReminderTime" name="ReminderTime" required 
               value="<?php echo isset($_POST['ReminderTime']) ? $_POST['ReminderTime'] : ''; ?>"><br><br>
        
        <!-- Checkbox defaults to checked for better UX -->
        <label for="IsEnabled">Enabled:</label>
        <input type="checkbox" id="IsEnabled" name="IsEnabled" 
               <?php echo (!isset($_POST['UserID']) || (isset($_POST['IsEnabled']))) ? 'checked' : ''; ?>><br><br>
        
        <!-- Optional message field with maxlength for client-side validation -->
        <label for="Message">Message (optional, max 255 characters):</label><br>
        <textarea id="Message" name="Message" rows="3" cols="40" maxlength="255" 
                  placeholder="e.g., Time for your morning run!"><?php echo isset($_POST['Message']) ? htmlspecialchars($_POST['Message']) : ''; ?></textarea><br><br>
        
        <button type="submit">Add Reminder</button>
        <a href="list_reminders.php">Cancel</a>
    </form>
    
    <br>
    <a href="../index.html">Back to Main Menu</a>
</body>
</html>