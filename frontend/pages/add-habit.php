<?php
session_start();
require_once '../../backend/config/db-connect.php';
require_once '../../backend/classes/Habit.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Server-side validation
    $habitName     = trim($_POST['habitName']);
    $description   = trim($_POST['description']);
    $targetPerWeek = (int) $_POST['targetPerWeek'];

    if (empty($habitName)) {
        $error = "Habit name is required.";
    } elseif ($targetPerWeek < 1 || $targetPerWeek > 7) {
        $error = "Target per week must be between 1 and 7.";
    } else {
        $habit = new Habit($conn);
        $habit->setUserID($_SESSION['UserID']);
        $habit->setHabitName($habitName);
        $habit->setDescription($description);
        $habit->setTargetPerWeek($targetPerWeek);
        $habit->setCreatedDate(date('Y-m-d'));

        if ($habit->addHabit()) {
            $success = "Habit added successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Habit - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Add New Habit</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" action="add-habit.php" onsubmit="return validateForm()">

    <label>Habit Name *</label>
    <input type="text" name="habitName" id="habitName" maxlength="50">

    <label>Description</label>
    <textarea name="description" id="description" maxlength="255"></textarea>

    <label>Target Per Week (1-7) *</label>
    <input type="number" name="targetPerWeek" id="targetPerWeek" min="1" max="7">

    <button type="submit">Add Habit</button>
    <a href="habits.php">Cancel</a>

</form>

<script src="../js/validate-habit.js"></script>
</body>
</html>