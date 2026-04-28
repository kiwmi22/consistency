<?php
require_once '../../backend/config/db.php';
require_once '../../backend/classes/Streak.php';

$streak = new Streak($conn);

// TEST 1 — getStreakByUser
echo "<h3>TEST 1: Get streaks for User 1</h3>";
$result = $streak->getStreakByUser(1);
while ($row = $result->fetch_assoc()) {
    echo "Habit: " . $row['HabitName'] .
         " | Current: " . $row['CurrentStreak'] .
         " | Longest: " . $row['LongestStreak'] . "<br>";
}

// TEST 2 — getLongestStreak
echo "<h3>TEST 2: Longest streak for User 1, Habit 1</h3>";
$longest = $streak->getLongestStreak(1, 1);
echo "Longest Streak: " . $longest ;
?>