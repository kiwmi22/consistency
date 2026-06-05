<?php
// find-habit.php
// Developer: Sashi Khatri
// Find a habit by HabitID

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$_SESSION['UserID'] = 1; // TEMPORARY - remove when Krishna login ready

require_once __DIR__ . '/../../backend/config/db-connect.php';
require_once __DIR__ . '/../../backend/classes/Habit.php';

$habit = new Habit($conn);
$result = null;
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $habitID = (int) $_POST['habitID'];

    if ($habitID <= 0) {
        $error = "Please enter a valid Habit ID.";
    } else {
        $result = $habit->findHabitByID($habitID);
        if (!$result) {
            $error = "No habit found with ID " . $habitID;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Habit - Consistency</title>
    <style>
<?php echo file_get_contents('C:/xampp/htdocs/consistency/frontend/css/style.css'); ?>
</style>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Find Habit by ID</h1>
<a href="habits.php">Back to My Habits</a>

<?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="find-habit.php">
    <label>Enter Habit ID</label>
    <input type="number" name="habitID" min="1"
           value="<?php echo isset($_POST['habitID']) ? (int)$_POST['habitID'] : ''; ?>">
    <button type="submit">Find Habit</button>
</form>

<?php if ($result): ?>
<h2>Result</h2>
<table>
    <thead>
        <tr>
            <th>Habit ID</th>
            <th>Habit Name</th>
            <th>Description</th>
            <th>Target Per Week</th>
            <th>Status</th>
            <th>Created Date</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo $result['HabitID']; ?></td>
            <td><?php echo htmlspecialchars($result['HabitName']); ?></td>
            <td><?php echo htmlspecialchars($result['Description']); ?></td>
            <td><?php echo $result['TargetPerWeek']; ?></td>
            <td><?php echo $result['IsActive'] ? 'Active' : 'Inactive'; ?></td>
            <td><?php echo $result['CreatedDate']; ?></td>
        </tr>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>