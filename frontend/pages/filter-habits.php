<?php
// filter-habits.php
// Developer: Sashi Khatri
// Filter habits by status, target frequency, or search by name

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$_SESSION['UserID'] = 1; // TEMPORARY - remove when Krishna login ready

require_once __DIR__ . '/../../backend/config/db-connect.php';
require_once __DIR__ . '/../../backend/classes/Habit.php';
$habit   = new Habit($conn);
$userID  = $_SESSION['UserID'];
$results = null;

// Get filter values from form
$searchName   = isset($_GET['searchName'])   ? trim($_GET['searchName'])      : '';
$filterTarget = isset($_GET['filterTarget']) ? (int)$_GET['filterTarget']     : 0;
$filterStatus = isset($_GET['filterStatus']) ? $_GET['filterStatus']          : '';

// Apply correct filter based on what was submitted
if ($searchName !== '') {
    $results = $habit->searchHabits($userID, $searchName);
} elseif ($filterTarget > 0) {
    $results = $habit->filterByTarget($userID, $filterTarget);
} elseif ($filterStatus !== '') {
    $isActive = ($filterStatus === 'active') ? 1 : 0;
    $results  = $habit->filterByStatus($userID, $isActive);
} else {
    $results = $habit->listHabits($userID);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Filter Habits - Consistency</title>
    <style>
<?php echo file_get_contents('C:/xampp/htdocs/consistency/frontend/css/style.css'); ?>
</style>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>Search and Filter Habits</h1>
<a href="habits.php">Back to My Habits</a>

<!-- FILTER FORM -->
<form method="GET" action="filter-habits.php">

    <label>Search by Name</label>
    <input type="text" name="searchName"
           placeholder="Type habit name..."
           value="<?php echo htmlspecialchars($searchName); ?>">

    <label>Filter by Target Per Week</label>
    <select name="filterTarget">
        <option value="0">-- Select frequency --</option>
        <?php for ($i = 1; $i <= 7; $i++): ?>
            <option value="<?php echo $i; ?>"
                <?php echo ($filterTarget == $i) ? 'selected' : ''; ?>>
                <?php echo $i; ?> day(s) per week
            </option>
        <?php endfor; ?>
    </select>

    <label>Filter by Status</label>
    <select name="filterStatus">
        <option value="">-- Select status --</option>
        <option value="active"   <?php echo ($filterStatus === 'active')   ? 'selected' : ''; ?>>Active</option>
        <option value="inactive" <?php echo ($filterStatus === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
    </select>

    <button type="submit">Apply Filter</button>
    <a href="filter-habits.php">Clear</a>

</form>

<!-- RESULTS TABLE -->
<h2>Results</h2>

<?php if ($results && $results->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>Habit ID</th>
            <th>Habit Name</th>
            <th>Description</th>
            <th>Target Per Week</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $results->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['HabitID']; ?></td>
            <td><?php echo htmlspecialchars($row['HabitName']); ?></td>
            <td><?php echo htmlspecialchars($row['Description']); ?></td>
            <td><?php echo $row['TargetPerWeek']; ?></td>
            <td><?php echo $row['IsActive'] ? 'Active' : 'Inactive'; ?></td>
            <td>
                <a href="edit-habit.php?id=<?php echo $row['HabitID']; ?>">Edit</a>
                <a href="delete-habit.php?id=<?php echo $row['HabitID']; ?>"
                   onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <p class="error">No habits found matching your search.</p>
<?php endif; ?>

</body>
</html>