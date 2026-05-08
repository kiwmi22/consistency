<?php
session_start();

$_SESSION['UserID']  = 1;
$_SESSION['IsAdmin'] = true;

require_once '../../../backend/config/db.php';
require_once '../../../backend/classes/Streak.php';

if (!isset($_SESSION['IsAdmin']) || !$_SESSION['IsAdmin']) {
    header("Location: ../../login.php");
    exit();
}

$streak  = new Streak($conn);
$errors  = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID  = $_POST['UserID'];
    $habitID = $_POST['HabitID'];

    $errors = $streak->validateStreak($userID, $habitID, 0, 0);

    if (empty($errors)) {
        $streak->addStreak($userID, $habitID);
        $success = "Streak added successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Streak</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<h1>Add New Streak</h1>

<?php foreach ($errors as $error): ?>
    <p style="color:red"><?= $error ?></p>
<?php endforeach; ?>

<?php if ($success): ?>
    <p style="color:green"><?= $success ?></p>
<?php endif; ?>

<form method="POST">
    <label>User ID:</label><br>
    <input type="number" name="UserID" required>
    <br><br>
    <label>Habit ID:</label><br>
    <input type="number" name="HabitID" required>
    <br><br>
    <button type="submit">Add Streak</button>
</form>
<br>
<a href="../admin_streaks.php">Back to Admin</a>
</body>
</html>