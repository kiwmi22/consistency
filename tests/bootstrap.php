<?php
/**
 * Test Bootstrap File
 * Initializes the testing environment for Consistency Habit Tracker
 * Author: Pratik Tamang - Reminders & Notifications Component
 */

// Display all errors during testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up paths
define('PROJECT_ROOT', dirname(__DIR__));
define('TESTS_DIR', __DIR__);
define('LOGS_DIR', TESTS_DIR . '/logs');

// Create logs directory if it doesn't exist
if (!file_exists(LOGS_DIR)) {
    mkdir(LOGS_DIR, 0777, true);
}

// Include required classes
require_once PROJECT_ROOT . '/backend/config/database.php';
require_once PROJECT_ROOT . '/backend/classes/Reminder.php';

// Test counter
$GLOBALS['test_passed'] = 0;
$GLOBALS['test_failed'] = 0;
$GLOBALS['test_total'] = 0;

/**
 * Log test results to file and display on screen
 */
function logTest($testName, $testData, $expected, $actual, $passed) {
    $GLOBALS['test_total']++;
    if ($passed) {
        $GLOBALS['test_passed']++;
    } else {
        $GLOBALS['test_failed']++;
    }
    
    $logFile = LOGS_DIR . '/test_results_' . date('Y-m-d') . '.log';
    $status = $passed ? 'PASS' : 'FAIL';
    $log = sprintf(
        "[%s] [%s] %s | Data: %s | Expected: %s | Actual: %s\n",
        date('Y-m-d H:i:s'),
        $status,
        $testName,
        is_array($testData) ? json_encode($testData) : $testData,
        is_array($expected) ? json_encode($expected) : $expected,
        is_array($actual) ? json_encode($actual) : $actual
    );
    file_put_contents($logFile, $log, FILE_APPEND);
    
    // Display on screen
    $color = $passed ? '#27ae60' : '#e74c3c';
    $icon = $passed ? '✓' : '✗';
    echo "<div style='padding: 8px 12px; margin: 5px 0; background: #f8f9fa; border-left: 4px solid $color; font-family: monospace;'>";
    echo "<strong style='color: $color;'>$icon [$status]</strong> $testName";
    echo "</div>";
}

/**
 * Display test summary
 */
function displayTestSummary() {
    $total = $GLOBALS['test_total'];
    $passed = $GLOBALS['test_passed'];
    $failed = $GLOBALS['test_failed'];
    $passRate = $total > 0 ? round(($passed / $total) * 100, 2) : 0;
    
    echo "<div style='margin-top: 30px; padding: 20px; background: #ecf0f1; border-radius: 8px;'>";
    echo "<h2 style='margin-top: 0;'>📊 Test Summary</h2>";
    echo "<p><strong>Total Tests:</strong> $total</p>";
    echo "<p style='color: #27ae60;'><strong>✓ Passed:</strong> $passed</p>";
    echo "<p style='color: #e74c3c;'><strong>✗ Failed:</strong> $failed</p>";
    echo "<p><strong>Pass Rate:</strong> $passRate%</p>";
    echo "</div>";
}

// Display header
echo "<!DOCTYPE html>
<html><head>
<title>Test Suite - Consistency</title>
<style>
body { font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; max-width: 1000px; margin: auto; }
h1 { color: #2c3e50; }
h2 { color: #3498db; margin-top: 30px; }
h3 { color: #34495e; }
hr { margin: 20px 0; }
</style>
</head><body>";
?>
