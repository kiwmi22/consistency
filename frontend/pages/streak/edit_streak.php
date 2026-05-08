<?php
session_start();

//temporary - remove when krishna 
$_SESSION['UserID'] = 1;

require_once '../../../backend/config/db.php';
require_once '../../../backend/classes/Streak.php';

if (!isset($_SESSION['IsAdmin']) || !$_SESSION['IsAdmin']) {
    header("Location: ../../login.php");
    exit();
}

$streak   = new Streak($conn);
$errors   = [];
$success  = "";
$streakID = $_GET['id'] ?? 0;
$record   = $streak->findStreakById($streakID)->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentStreak = $_POST['CurrentStreak'];
    $longestStreak = $_POST['LongestStreak'];
    $isActive      = isset($_POST['IsActive']) ? 1 : 0;

    $errors = $streak->validateStreak(
        $record['UserID'],
        $record['HabitID'],
        $currentStreak,
        $longestStreak
    );

    if (empty($errors)) {
        $streak->editStreak(
            $streakID,
            $currentStreak,
            $longestStreak,
            $isActive
        );
        $success = "Streak updated successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Streak</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<h1>Edit Streak</h1>

<?php foreach ($errors as $error): ?>
    <p style="color:red"><?= $error ?></p>
<?php endforeach; ?>

<?php if ($success): ?>
    <p style="color:green"><?= $success ?></p>
<?php endif; ?>

<form method="POST">
    <label>Current Streak:</label><br>
    <input type="number" name="CurrentStreak"
           value="<?= $record['CurrentStreak'] ?>" required>
    <br><br>
    <label>Longest Streak:</label><br>
    <input type="number" name="LongestStreak"
           value="<?= $record['LongestStreak'] ?>" required>
    <br><br>
    <label>Active:</label>
    <input type="checkbox" name="IsActive"
           <?= $record['IsActive'] ? 'checked' : '' ?>>
    <br><br>
    <button type="submit">Update Streak</button>
</form>
<br>
<a href="../admin_streaks.php">Back to Admin</a>
</body>
</html>