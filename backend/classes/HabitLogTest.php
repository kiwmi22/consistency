<?php
/**
 * HabitLog Test Script
 * Developer: Abin Rai
 * Sprint 4 — Testing section worth 10%
 */

// Load class file first
require_once __DIR__ . '/HabitLog.php';

// Load database connection
// Change db_connect.php to match your actual config filename
require_once __DIR__ . '/../config/db_connect.php';

// Confirm class loaded correctly
if (!class_exists('HabitLog')) {
    die("ERROR: HabitLog class not found. 
         Check HabitLog.php exists in same folder.\n");
}

// Confirm DB connection exists
if (!isset($pdo)) {
    die("ERROR: PDO connection not found. 
         Check db_connect.php path is correct.\n");
}

$test   = new HabitLog($pdo);
$passed = 0;
$failed = 0;

function check($label, $result) {
    global $passed, $failed;
    if ($result) {
        echo "✅ PASS: $label\n";
        $passed++;
    } else {
        echo "❌ FAIL: $label\n";
        $failed++;
    }
}
echo "=== HabitLog Class Tests ===\n\n";

// --- addLog() tests ---
echo "-- addLog() --\n";

$r = $test->addLog(1, 1, '2026-05-12', true, 'Test', 30);
check("addLog() accepts valid entry", $r === true);

$r = $test->addLog(0, 1, '2026-05-12', true, '', 0);
check("addLog() rejects missing habitID", $r === false);

$r = $test->addLog(1, 0, '2026-05-12', true, '', 0);
check("addLog() rejects missing userID", $r === false);

$r = $test->addLog(1, 1, '', true, '', 0);
check("addLog() rejects missing logDate", $r === false);

$r = $test->addLog(1, 1, '2026-05-13', true, '', -5);
check("addLog() rejects negative duration", $r === false);

// --- getLogsByUser() tests ---
echo "\n-- getLogsByUser() --\n";

$logs = $test->getLogsByUser(1, '2026-04-01', '2026-05-31');
check("getLogsByUser() returns array", is_array($logs));

$logs = $test->getLogsByUser(0, '2026-04-01', '2026-05-31');
check("getLogsByUser() returns empty for missing userID",
    $logs === []);

// --- getLogByID() tests ---
echo "\n-- getLogByID() --\n";

$log = $test->getLogByID(2);
check("getLogByID() returns data for valid ID", !empty($log));

$log = $test->getLogByID(0);
check("getLogByID() returns null for missing ID", $log === null);

$log = $test->getLogByID(9999);
check("getLogByID() returns null for non-existent ID",
    empty($log));

// --- updateLog() tests ---
echo "\n-- updateLog() --\n";

$r = $test->updateLog(2, false, 'Updated note', 45);
check("updateLog() accepts valid update", $r === true);

$r = $test->updateLog(0, true, '', 0);
check("updateLog() rejects missing logID", $r === false);

$r = $test->updateLog(2, true, '', -10);
check("updateLog() rejects negative duration", $r === false);

// --- deleteLog() tests ---
echo "\n-- deleteLog() --\n";

$r = $test->deleteLog(2);
check("deleteLog() accepts valid logID", $r === true);

$r = $test->deleteLog(0);
check("deleteLog() rejects missing logID", $r === false);

// --- getAllLogs() tests ---
echo "\n-- getAllLogs() --\n";

$all = $test->getAllLogs();
check("getAllLogs() returns array", is_array($all));

// --- filterByHabit() tests ---
echo "\n-- filterByHabit() --\n";

$logs = $test->filterByHabit(1);
check("filterByHabit() returns array", is_array($logs));

$logs = $test->filterByHabit(0);
check("filterByHabit() returns empty for missing ID",
    $logs === []);

// --- filterByStatus() tests ---
echo "\n-- filterByStatus() --\n";

$logs = $test->filterByStatus(true);
check("filterByStatus() returns completed logs", is_array($logs));

$logs = $test->filterByStatus(false);
check("filterByStatus() returns incomplete logs", is_array($logs));

// --- filterByUser() tests ---
echo "\n-- filterByUser() --\n";

$logs = $test->filterByUser(1);
check("filterByUser() returns array", is_array($logs));

$logs = $test->filterByUser(0);
check("filterByUser() returns empty for missing ID",
    $logs === []);

// --- Summary ---
echo "\n=== Results: $passed passed, $failed failed ===\n";
?>