<?php
// delete-habit.php
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


$habitID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$habit   = new Habit($conn);

if ($habitID > 0) {
    $habitData = $habit->getHabitByID($habitID);
}

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $habit->deleteHabit($habitID);
    header("Location: habits.php?deleted=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Habit - Consistency</title>
    <style>
<?php echo file_get_contents('C:/xampp/htdocs/consistency/frontend/css/style.css'); ?>
</style>
</head>
<body>

<nav class="navbar">
    <a href="habits.php" class="logo">CON<span>SIST</span>ENCY</a>
    <div class="nav-links">
        <a href="habits.php">My Habits</a>
        <a href="add-habit.php">+ Add Habit</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="card" style="max-width:500px; margin:60px auto; text-align:center;">
        <h2 style="color:#e74c3c; margin-bottom:15px;">⚠️ Delete Habit</h2>
        <p style="color:#666; margin-bottom:25px;">
            Are you sure you want to delete
            <strong><?php echo htmlspecialchars($habitData['HabitName'] ?? 'this habit'); ?></strong>?
            <br><br>
            This action cannot be undone.
        </p>
        <form method="POST" action="delete-habit.php?id=<?php echo $habitID; ?>">
            <button type="submit" class="btn btn-danger" style="margin-right:10px;">
                Yes, Delete
            </button>
            <a href="habits.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>