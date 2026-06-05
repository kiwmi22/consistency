<?php
require_once '../../backend/config/database.php';
require_once '../../backend/classes/Reminder.php';

$database = new Database();
$db = $database->getConnection();
$reminder = new Reminder($db);
$message = "";

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
    $reminder->UserID = htmlspecialchars(strip_tags($_POST['UserID']));
    $reminder->HabitID = htmlspecialchars(strip_tags($_POST['HabitID']));
    $reminder->ReminderTime = htmlspecialchars(strip_tags($_POST['ReminderTime']));
    $reminder->IsEnabled = isset($_POST['IsEnabled']) ? 1 : 0;
    $reminder->Message = htmlspecialchars(strip_tags($_POST['Message']));
    
    if ($reminder->update()) {
        $message = "<p style='color: green;'>✓ Updated successfully!</p>";
    } else {
        $message = "<p style='color: red;'>Update failed.</p>";
    }
}

$userStmt = $db->query("SELECT UserID, Username FROM tblUsers WHERE IsActive = 1 ORDER BY Username");
$habitStmt = $db->query("SELECT HabitID, HabitName FROM tblHabits WHERE IsActive = 1 ORDER BY HabitName");
?>
<!DOCTYPE html>
<html><head><title>Edit Reminder</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <h1>Edit Reminder</h1>
    <?php echo $message; ?>
    <form method="POST">
        <label>User:</label><br>
        <select name="UserID" required>
            <?php while ($u = $userStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $u['UserID']; ?>" <?php if ($u['UserID']==$reminder->UserID) echo 'selected'; ?>><?php echo htmlspecialchars($u['Username']); ?></option>
            <?php } ?>
        </select><br>
        <label>Habit:</label><br>
        <select name="HabitID" required>
            <?php while ($h = $habitStmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <option value="<?php echo $h['HabitID']; ?>" <?php if ($h['HabitID']==$reminder->HabitID) echo 'selected'; ?>><?php echo htmlspecialchars($h['HabitName']); ?></option>
            <?php } ?>
        </select><br>
        <label>Time:</label><br>
        <input type="time" name="ReminderTime" value="<?php echo $reminder->ReminderTime; ?>" required><br>
        <label>Enabled:</label>
        <input type="checkbox" name="IsEnabled" <?php if ($reminder->IsEnabled) echo 'checked'; ?>><br><br>
        <label>Message:</label><br>
        <textarea name="Message" maxlength="255"><?php echo htmlspecialchars($reminder->Message); ?></textarea><br>
        <button type="submit">Update</button>
        <a href="list_reminders.php">Cancel</a>
    </form>
    <br><a href="../index.html">← Main Menu</a>
</body></html>
