<?php
// add-habit.php
// Developer: Sashi Khatri
// Component: Habit Management

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../backend/config/db-connect.php';
require_once __DIR__ . '/../../backend/classes/Habit.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $habitName     = trim($_POST['habitName']);
    $description   = trim($_POST['description']);
    $targetPerWeek = (int) $_POST['targetPerWeek'];

    // Server-side validation
    if (empty($habitName)) {
        $error = "Habit name is required.";
    } elseif (strlen($habitName) > 50) {
        $error = "Habit name must be 50 characters or less.";
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
    <style>
        <?php echo file_get_contents('C:/xampp/htdocs/consistency/frontend/css/style.css'); ?>
</style>

</head>
<body>

<h1>Add New Habit</h1>
<a href="habits.php">Back to My Habits</a>

<?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="success"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" action="add-habit.php" onsubmit="return validateForm()">

    <label>Habit Name * (max 50 characters)</label>
    <input type="text" name="habitName" id="habitName"
           maxlength="50"
           value="<?php echo isset($_POST['habitName']) ? htmlspecialchars($_POST['habitName']) : ''; ?>">

    <label>Description (optional)</label>
    <textarea name="description" id="description"
              maxlength="255"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>

    <label>Target Per Week * (1-7)</label>
    <input type="number" name="targetPerWeek" id="targetPerWeek"
           min="1" max="7"
           value="<?php echo isset($_POST['targetPerWeek']) ? (int)$_POST['targetPerWeek'] : ''; ?>">

    <button type="submit">Add Habit</button>
    <a href="habits.php">Cancel</a>

</form>

<script src="../js/validate-habit.js"></script>
</body>
</html>