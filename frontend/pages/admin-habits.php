<?php
// admin-habits.php
// Developer: Sashi Khatri
// Admin page for Habit Management

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['UserID']) || !$_SESSION['IsAdmin']) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../backend/config/db-connect.php';
require_once __DIR__ . '/../../backend/classes/Habit.php';

$habit = new Habit($conn);

// Get all habits for all users
$sql    = "SELECT h.*, u.Username FROM tblHabits h
           JOIN tblUsers u ON h.UserID = u.UserID
           ORDER BY h.CreatedDate DESC";
$result = $conn->query($sql);

$deleted = isset($_GET['deleted']) && $_GET['deleted'] == 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Habit Management</title>
    <style>
    <?php echo file_get_contents('C:/xampp/htdocs/consistency/frontend/css/style.css'); ?>
    </style>
</head>
<body>

<nav class="navbar">
    <a href="habits.php" class="logo">CON<span>SIST</span>ENCY</a>
    <div class="nav-links">
        <a href="habits.php">My Habits</a>
        <a href="admin-habits.php" style="background:rgba(255,255,255,0.2);">Admin</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="page-header">
        <h1>Admin — All Habits</h1>
        <span style="color:#888; font-size:14px;">Viewing all habits across all users</span>
    </div>

    <?php if ($deleted): ?>
        <div class="success">Habit deleted successfully.</div>
    <?php endif; ?>

    <!-- FILTER BY USER -->
    <form method="GET" action="admin-habits.php">
        <div class="search-bar">
            <input type="text" name="search"
                   placeholder="Search by habit name..."
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <select name="status">
                <option value="">All Status</option>
                <option value="1" <?php echo (isset($_GET['status']) && $_GET['status']=='1') ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo (isset($_GET['status']) && $_GET['status']=='0') ? 'selected' : ''; ?>>Inactive</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="admin-habits.php" class="btn btn-secondary">Clear</a>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Habit Name</th>
                    <th>Description</th>
                    <th>Target/Week</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    // Apply search filter
                    if (isset($_GET['search']) && $_GET['search'] !== '') {
                        if (stripos($row['HabitName'], $_GET['search']) === false) continue;
                    }
                    // Apply status filter
                    if (isset($_GET['status']) && $_GET['status'] !== '') {
                        if ($row['IsActive'] != $_GET['status']) continue;
                    }
            ?>
                <tr>
                    <td><?php echo $row['HabitID']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['Username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['HabitName']); ?></td>
                    <td><?php echo htmlspecialchars($row['Description'] ?? '—'); ?></td>
                    <td><?php echo $row['TargetPerWeek']; ?>x</td>
                    <td>
                        <span class="badge <?php echo $row['IsActive'] ? 'badge-active' : 'badge-inactive'; ?>">
                            <?php echo $row['IsActive'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td><?php echo $row['CreatedDate']; ?></td>
                    <td class="action-links">
                        <a href="edit-habit.php?id=<?php echo $row['HabitID']; ?>"
                           class="edit-link">Edit</a>
                        <a href="delete-habit.php?id=<?php echo $row['HabitID']; ?>"
                           class="delete-link"
                           onclick="return confirm('Delete this habit?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <p>No habits found in the system.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>