<?php
// edit-habit.php
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
$habit   = new Habit($conn);
$error   = '';
$success = '';

// Get HabitID from URL
$habitID   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$habitData = $habit->getHabitByID($habitID);

// If habit not found go back to list
if (!$habitData) {
    header("Location: habits.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $habitID       = (int) $_POST['habitID'];
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
        $habit->setHabitName($habitName);
        $habit->setDescription($description);
        $habit->setTargetPerWeek($targetPerWeek);

        if ($habit->editHabit($habitID)) {
            $success   = "Habit updated successfully!";
            $habitData = $habit->getHabitByID($habitID);
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
    <title>Edit Habit - Consistency</title>
  <style>
<?php echo file_get_contents('C:/xampp/htdocs/consistency/frontend/css/style.css'); ?>
</style>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Edit Habit</h1>
<a href="habits.php">Back to My Habits</a>

<?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="success"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" action="edit-habit.php" onsubmit="return validateForm()">

    <input type="hidden" name="habitID"
           value="<?php echo $habitData['HabitID']; ?>">

    <label>Habit Name * (max 50 characters)</label>
    <input type="text" name="habitName" id="habitName"
           value="<?php echo htmlspecialchars($habitData['HabitName']); ?>"
           maxlength="50">

    <label>Description (optional)</label>
    <textarea name="description" id="description"
              maxlength="255"><?php echo htmlspecialchars($habitData['Description']); ?></textarea>

    <label>Target Per Week * (1-7)</label>
    <input type="number" name="targetPerWeek" id="targetPerWeek"
           value="<?php echo $habitData['TargetPerWeek']; ?>"
           min="1" max="7">

    <button type="submit">Save Changes</button>
    <a href="habits.php">Cancel</a>

</form>

<script src="../js/validate-habit.js"></script>
</body>
</html>