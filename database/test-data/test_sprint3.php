<?php
/**
 * ============================================
 * File:   test_sprint3.php
 * Author: Juna Bhujel
 * Tests:  All Streak class methods
 * Sprint: Sprint 3 - Thursday 8th May 2026
 * ============================================
 */

require_once '../../backend/config/db.php';
require_once '../../backend/classes/Streak.php';

$streak = new Streak($conn);
$passed = 0;
$failed = 0;

echo "<h1>Streak Class — Full Test Results</h1>";

// -----------------------------------------------
// TEST 1 — getStreakByUser
// -----------------------------------------------
echo "<h3>TEST 1: getStreakByUser(1)</h3>";
$result = $streak->getStreakByUser(1);
if ($result && $result->num_rows > 0) {
    echo "<p style='color:green'>✅ PASS — " .
         $result->num_rows . " records found</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL — No records found</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 2 — getLongestStreak
// -----------------------------------------------
echo "<h3>TEST 2: getLongestStreak(1,1)</h3>";
$longest = $streak->getLongestStreak(1, 1);
if (is_numeric($longest)) {
    echo "<p style='color:green'>✅ PASS — Longest: $longest</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 3 — updateStreak
// -----------------------------------------------
echo "<h3>TEST 3: updateStreak(1,1)</h3>";
$update = $streak->updateStreak(1, 1);
if ($update) {
    echo "<p style='color:green'>✅ PASS — Streak updated</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 4 — resetStreak
// -----------------------------------------------
echo "<h3>TEST 4: resetStreak(2,1)</h3>";
$reset = $streak->resetStreak(2, 1);
if ($reset) {
    echo "<p style='color:green'>✅ PASS — Streak reset to 0</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 5 — addStreak
// -----------------------------------------------
echo "<h3>TEST 5: addStreak(3,2)</h3>";
$add = $streak->addStreak(3, 2);
if ($add) {
    echo "<p style='color:green'>✅ PASS — Streak added</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 6 — findStreakById
// -----------------------------------------------
echo "<h3>TEST 6: findStreakById(1)</h3>";
$find = $streak->findStreakById(1)->fetch_assoc();
if ($find) {
    echo "<p style='color:green'>✅ PASS — Found: " .
         $find['Username'] . "</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 7 — filterStreakByUser
// -----------------------------------------------
echo "<h3>TEST 7: filterStreakByUser(1)</h3>";
$filter = $streak->filterStreakByUser(1);
if ($filter && $filter->num_rows > 0) {
    echo "<p style='color:green'>✅ PASS — " .
         $filter->num_rows . " records found</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 8 — filterStreakByHabit
// -----------------------------------------------
echo "<h3>TEST 8: filterStreakByHabit(1)</h3>";
$filter2 = $streak->filterStreakByHabit(1);
if ($filter2 && $filter2->num_rows > 0) {
    echo "<p style='color:green'>✅ PASS — " .
         $filter2->num_rows . " records found</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 9 — validateStreak valid inputs
// -----------------------------------------------
echo "<h3>TEST 9: validateStreak — valid inputs</h3>";
$errors = $streak->validateStreak(1, 1, 5, 10);
if (empty($errors)) {
    echo "<p style='color:green'>✅ PASS — No errors</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// TEST 10 — validateStreak invalid inputs
// -----------------------------------------------
echo "<h3>TEST 10: validateStreak — invalid inputs</h3>";
$errors2 = $streak->validateStreak(0, 0, -1, -1);
if (!empty($errors2)) {
    echo "<p style='color:green'>✅ PASS — Errors caught: " .
         count($errors2) . "</p>";
    $passed++;
} else {
    echo "<p style='color:red'>❌ FAIL</p>";
    $failed++;
}

// -----------------------------------------------
// RESULTS SUMMARY
// -----------------------------------------------
echo "<hr>";
echo "<h2>Test Summary</h2>";
echo "<p style='color:green'>✅ Passed: $passed</p>";
echo "<p style='color:red'>❌ Failed: $failed</p>";
echo "<p>Total: " . ($passed + $failed) . " tests</p>";
?>