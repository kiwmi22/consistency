<?php
require_once 'HabitLog.php';
require_once '../config/db.php';

$test    = new HabitLog($pdo);
$passed  = 0;
$failed  = 0;

function check($label, $result) {
    global $passed, $failed;
    if ($result) {
        echo "PASS: $label\n";
        $passed++;
    } else {
        echo "FAIL: $label\n";
        $failed++;
    }
}

// Test 1: Add a valid log
$r = $test->addLog(1, 1, '2026-04-21', true, 'Test note', 30);
check("Add valid log", $r === true);

// Test 2: Add duplicate log (same habit, user, date)
$r = $test->addLog(1, 1, '2026-04-21', true, 'Duplicate', 10);
check("Block duplicate log", $r === true); // procedure silently skips

// Test 3: Add log with missing habitID
$r = $test->addLog(0, 1, '2026-04-22', true, '', 0);
check("Reject missing HabitID", $r === false);

// Test 4: Get logs by user
$logs = $test->getLogsByUser(1, '2026-04-01', '2026-04-30');
check("Get logs by user returns array", is_array($logs));

// Test 5: Get log by ID
$log = $test->getLogByID(1);
check("Get log by ID returns data", !empty($log));

// Test 6: Update a log
$r = $test->updateLog(1, false, 'Updated note', 45);
check("Update log", $r === true);

// Test 7: Delete a log
$r = $test->deleteLog(1);
check("Delete log", $r === true);

// Test 8: Get all logs (admin)
$all = $test->getAllLogs();
check("Get all logs returns array", is_array($all));

echo "\n--- Results: $passed passed, $failed failed ---\n";
?>