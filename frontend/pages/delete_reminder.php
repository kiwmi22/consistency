<?php
require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $reminder->ReminderID = $_GET['id'];
    if (!$reminder->findById()) {
        header("Location: list_reminders.php");
        exit();
    }
} else {
    header("Location: list_reminders.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($reminder->delete()) {
        header("Location: list_reminders.php?deleted=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html><head><title>Delete Reminder</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <h1>Delete Reminder</h1>
    <h2 style="color: red;">⚠️ Are you sure?</h2>
    <table>
        <tr><th>ID</th><td><?php echo $reminder->ReminderID; ?></td></tr>
        <tr><th>User ID</th><td><?php echo $reminder->UserID; ?></td></tr>
        <tr><th>Habit ID</th><td><?php echo $reminder->HabitID; ?></td></tr>
        <tr><th>Time</th><td><?php echo $reminder->ReminderTime; ?></td></tr>
        <tr><th>Message</th><td><?php echo htmlspecialchars($reminder->Message); ?></td></tr>
    </table>
    <form method="POST">
        <button type="submit" class="btn-danger">Yes, Delete</button>
        <a href="list_reminders.php">Cancel</a>
    </form>
    <br><a href="../index.html">← Main Menu</a>
</body></html>
