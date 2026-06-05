<?php
/**
 * Add Reminder Page
 * Author: Pratik Tamang
 */

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reminder->UserID = htmlspecialchars(strip_tags($_POST['UserID']));
    $reminder->HabitID = htmlspecialchars(strip_tags($_POST['HabitID']));
    $reminder->ReminderTime = htmlspecialchars(strip_tags($_POST['ReminderTime']));
    $reminder->IsEnabled = isset($_POST['IsEnabled']) ? 1 : 0;
    $reminder->Message = htmlspecialchars(strip_tags($_POST['Message']));
    
    $errors = [];
    if (!is_numeric($reminder->UserID)) $errors[] = "User ID must be valid.";
    if (!is_numeric($reminder->HabitID)) $errors[] = "Habit ID must be valid.";
    if (strlen($reminder->Message) > 255) $errors[] = "Message must be 255 chars or less.";
    
    if (empty($errors)) {
        if ($reminder->add()) {
            $message = "<p style='color: green;'>✓ Reminder added successfully!</p>";
            $_POST = array();
        } else {
            $message = "<p style='color: red;'>Error adding reminder.</p>";
        }
    } else {
        $message = "<p style='color: red;'>" . implode("<br>", $errors) . "</p>";
    }
}

$userStmt = $db->query("SELECT UserID, Username FROM tblUsers WHERE IsActive = 1 ORDER BY Username");
$habitStmt = $db->query("SELECT HabitID, HabitName FROM tblHabits WHERE IsActive = 1 ORDER BY HabitName");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Reminder - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Add Reminder</h1>
    <?php echo $message; ?>
    
    <form method="POST" action="">
        <label for="UserID">Select User:</label><br>
        <select id="UserID" name="UserID" required>
            <option value="">-- Select User --</option>
            <?php while ($user = $userStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $user['UserID']; ?>"><?php echo htmlspecialchars($user['Username']); ?></option>
            <?php } ?>
        </select><br>
        
        <label for="HabitID">Select Habit:</label><br>
        <select id="HabitID" name="HabitID" required>
            <option value="">-- Select Habit --</option>
            <?php while ($habit = $habitStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $habit['HabitID']; ?>"><?php echo htmlspecialchars($habit['HabitName']); ?></option>
            <?php } ?>
        </select><br>
        
        <label for="ReminderTime">Reminder Time:</label><br>
        <input type="time" id="ReminderTime" name="ReminderTime" required><br>
        
        <label for="IsEnabled">Enabled:</label>
        <input type="checkbox" id="IsEnabled" name="IsEnabled" checked><br><br>
        
        <label for="Message">Message (optional):</label><br>
        <textarea id="Message" name="Message" rows="3" maxlength="255"></textarea><br>
        
        <button type="submit">Add Reminder</button>
        <a href="list_reminders.php">Cancel</a>
    </form>
    
    <br>
    <a href="../index.html">← Back to Main Menu</a>
</body>
</html>
