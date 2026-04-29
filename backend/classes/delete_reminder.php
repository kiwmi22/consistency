<?php
// Delete Reminder Page - Presentation layer
// Allows users to delete a reminder with confirmation

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

// Process deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    $reminder->ReminderID = $_POST['ReminderID'];
    if ($reminder->delete()) {
        header("Location: list_reminders.php?deleted=1");
        exit();
    } else {
        $message = "<p style='color: red;'>Error: Unable to delete reminder.</p>";
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
    <nav>
        <a href="../../frontend/index.html">Home</a>
        <a href="add_reminder.php">Add Reminder</a>
        <a href="list_reminders.php">List Reminders</a>
    </nav>

    <h1>Delete Reminder</h1>

    <?php echo $message; ?>

    <?php if ($reminder->ReminderID): ?>
    <p><strong>Are you sure you want to delete this reminder?</strong></p>
    <p>User ID: <?php echo htmlspecialchars($reminder->UserID); ?></p>
    <p>Habit ID: <?php echo htmlspecialchars($reminder->HabitID); ?></p>
    <p>Time: <?php echo htmlspecialchars($reminder->ReminderTime); ?></p>
    <p>Message: <?php echo htmlspecialchars($reminder->Message); ?></p>

    <form method="POST" action="">
        <input type="hidden" name="ReminderID" value="<?php echo htmlspecialchars($reminder->ReminderID); ?>">
        <button type="submit" name="confirm" style="background-color: red; color: white;">Yes, Delete</button>
        <a href="list_reminders.php">Cancel</a>
    </form>
    <?php endif; ?>
</body>
</html>