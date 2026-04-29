<?php
// Edit Reminder Page - Presentation layer
// Allows users to edit an existing reminder

require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

$message = "";

// Get reminder ID from URL
if (isset($_GET['id'])) {
    $reminder->ReminderID = $_GET['id'];
    if (!$reminder->findById()) {
        $message = "<p style='color: red;'>Reminder not found.</p>";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    if (empty($_POST['UserID']) || !is_numeric($_POST['UserID'])) {
        $errors[] = "User ID must be a valid number.";
    }
    if (empty($_POST['HabitID']) || !is_numeric($_POST['HabitID'])) {
        $errors[] = "Habit ID must be a valid number.";
    }
    if (empty($_POST['ReminderTime']) || !preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $_POST['ReminderTime'])) {
        $errors[] = "Reminder time must be in HH:MM 24-hour format.";
    }
    if (strlen($_POST['Message']) > 255) {
        $errors[] = "Message must be 255 characters or less.";
    }

    if (empty($errors)) {
        $reminder->ReminderID = $_POST['ReminderID'];
        $reminder->UserID = $_POST['UserID'];
        $reminder->HabitID = $_POST['HabitID'];
        $reminder->ReminderTime = $_POST['ReminderTime'];
        $reminder->IsEnabled = isset($_POST['IsEnabled']) ? 1 : 0;
        $reminder->Message = $_POST['Message'];

        if ($reminder->update()) {
            $message = "<p style='color: green;'>Reminder updated successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error: Unable to update reminder.</p>";
        }
    } else {
        $message = "<p style='color: red;'>" . implode("<br>", $errors) . "</p>";
    }
}
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
    <nav>
        <a href="../../frontend/index.html">Home</a>
        <a href="add_reminder.php">Add Reminder</a>
        <a href="list_reminders.php">List Reminders</a>
    </nav>

    <h1>Edit Reminder</h1>

    <?php echo $message; ?>

    <form method="POST" action="">
        <input type="hidden" name="ReminderID" value="<?php echo htmlspecialchars($reminder->ReminderID); ?>">

        <label for="UserID">User ID:</label><br>
        <input type="number" id="UserID" name="UserID" required min="1" value="<?php echo htmlspecialchars($reminder->UserID); ?>"><br><br>

        <label for="HabitID">Habit ID:</label><br>
        <input type="number" id="HabitID" name="HabitID" required min="1" value="<?php echo htmlspecialchars($reminder->HabitID); ?>"><br><br>

        <label for="ReminderTime">Reminder Time (HH:MM):</label><br>
        <input type="text" id="ReminderTime" name="ReminderTime" required placeholder="e.g. 07:30" maxlength="5" value="<?php echo htmlspecialchars($reminder->ReminderTime); ?>"><br><br>

        <label for="IsEnabled">Enabled:</label>
        <input type="checkbox" id="IsEnabled" name="IsEnabled" <?php echo $reminder->IsEnabled ? 'checked' : ''; ?>><br><br>

        <label for="Message">Message (optional):</label><br>
        <textarea id="Message" name="Message" rows="3" cols="40" maxlength="255"><?php echo htmlspecialchars($reminder->Message); ?></textarea><br><br>

        <button type="submit">Update Reminder</button>
        <a href="list_reminders.php">Cancel</a>
    </form>
</body>
</html>