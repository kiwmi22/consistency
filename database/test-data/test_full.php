<?php
require_once '../../backend/config/db.php';
require_once '../../backend/classes/Streak.php';

$streak = new Streak($conn);

// TEST 1 — getStreakByUser
echo "<h3>TEST 1: getStreakByUser(1)</h3>";
$r = $streak->getStreakByUser(1);
while ($row = $r->fetch_assoc()) {
    echo $row['HabitName'] . " | Current: " .
         $row['CurrentStreak'] . " | Longest: " .
         $row['LongestStreak'] . "<br>";
}

// TEST 2 — getLongestStreak
echo "<h3>TEST 2: getLongestStreak(1,1)</h3>";
echo "Longest: " . $streak->getLongestStreak(1, 1);

// TEST 3 — updateStreak
echo "<h3>TEST 3: updateStreak(1,1)</h3>";
$streak->updateStreak(1, 1);
echo "Streak updated successfully";

// TEST 4 — resetStreak
echo "<h3>TEST 4: resetStreak(2,1)</h3>";
$streak->resetStreak(2, 1);
echo "Streak reset to zero";

// TEST 5 — getAllStreaksAdmin
echo "<h3>TEST 5: getAllStreaksAdmin()</h3>";
$all = $streak->getAllStreaksAdmin();
while ($row = $all->fetch_assoc()) {
    echo $row['Username'] . " | " .
         $row['HabitName'] . " | Current: " .
         $row['CurrentStreak'] . " | Longest: " .
         $row['LongestStreak'] . "<br>";
}
?>