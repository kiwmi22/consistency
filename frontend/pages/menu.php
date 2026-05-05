<?php
// Main menu — presentation layer
// All navigation for the Habit Logging component
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main Menu | Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Consistency</h1>
        <h2>Welcome, <?= htmlspecialchars($_SESSION['Username']) ?></h2>

        <h3>Habit Logging</h3>
        <nav>
            <a href="checkin.php">Daily Check-In</a>
            <a href="view_logs.php">View My Logs</a>
            <a href="filter_logs.php">Filter Logs</a>
        </nav>

        <?php if (isset($_SESSION['IsAdmin']) && $_SESSION['IsAdmin']): ?>
        <h3>Admin Panel</h3>
        <nav>
            <a href="admin_logs.php">Manage All Logs</a>
        </nav>
        <?php endif; ?>

        <a href="logout.php">Log Out</a>
    </div>
</body>
</html>
