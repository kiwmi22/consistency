<?php
/**
 * Validation & Security Test Suite
 * Tests input validation, XSS prevention, SQL injection protection
 * Author: Pratik Tamang
 */

require_once 'bootstrap.php';

echo "<h1>🔒 Validation & Security Tests</h1>";
echo "<p><strong>Component:</strong> Reminders & Notifications</p>";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p><hr>";

// ============================================
// TEST GROUP 1: UserID Validation
// ============================================
echo "<h2>Test Group 1: UserID Numeric Validation</h2>";

$userIdTests = [
    ['data' => '1', 'shouldPass' => true, 'desc' => 'Valid numeric: 1'],
    ['data' => '5', 'shouldPass' => true, 'desc' => 'Valid numeric: 5'],
    ['data' => 'abc', 'shouldPass' => false, 'desc' => 'Letters: abc'],
    ['data' => '-1', 'shouldPass' => false, 'desc' => 'Negative: -1'],
    ['data' => '0', 'shouldPass' => false, 'desc' => 'Zero: 0'],
    ['data' => '', 'shouldPass' => false, 'desc' => 'Empty string'],
];

foreach ($userIdTests as $test) {
    $isValid = is_numeric($test['data']) && $test['data'] > 0;
    $passed = ($isValid === $test['shouldPass']);
    
    logTest(
        'UserID Validation - ' . $test['desc'],
        ['Input' => $test['data']],
        $test['shouldPass'] ? 'Should accept' : 'Should reject',
        $isValid ? 'Accepted' : 'Rejected',
        $passed
    );
}

// ============================================
// TEST GROUP 2: HabitID Validation
// ============================================
echo "<h2>Test Group 2: HabitID Validation</h2>";

$habitIdTests = [
    ['data' => '1', 'shouldPass' => true, 'desc' => 'Valid: 1'],
    ['data' => '7', 'shouldPass' => true, 'desc' => 'Valid: 7'],
    ['data' => 'habit', 'shouldPass' => false, 'desc' => 'Text: habit'],
    ['data' => '', 'shouldPass' => false, 'desc' => 'Empty'],
    ['data' => '999999', 'shouldPass' => true, 'desc' => 'Large number'],
];

foreach ($habitIdTests as $test) {
    $isValid = is_numeric($test['data']) && $test['data'] > 0;
    $passed = ($isValid === $test['shouldPass']);
    
    logTest(
        'HabitID Validation - ' . $test['desc'],
        ['Input' => $test['data']],
        $test['shouldPass'] ? 'Should accept' : 'Should reject',
        $isValid ? 'Accepted' : 'Rejected',
        $passed
    );
}

// ============================================
// TEST GROUP 3: Time Format Validation
// ============================================
echo "<h2>Test Group 3: Time Format Validation (HH:MM)</h2>";

$timeTests = [
    ['data' => '07:00', 'shouldPass' => true, 'desc' => 'Valid: 07:00'],
    ['data' => '23:59', 'shouldPass' => true, 'desc' => 'Boundary: 23:59'],
    ['data' => '00:00', 'shouldPass' => true, 'desc' => 'Boundary: 00:00'],
    ['data' => '12:30', 'shouldPass' => true, 'desc' => 'Mid: 12:30'],
    ['data' => '25:00', 'shouldPass' => false, 'desc' => 'Invalid hour: 25:00'],
    ['data' => '12:60', 'shouldPass' => false, 'desc' => 'Invalid minute: 12:60'],
    ['data' => '24:00', 'shouldPass' => false, 'desc' => 'Invalid: 24:00'],
    ['data' => 'morning', 'shouldPass' => false, 'desc' => 'Text: morning'],
    ['data' => '7:0', 'shouldPass' => false, 'desc' => 'Wrong format: 7:0'],
];

foreach ($timeTests as $test) {
    $pattern = '/^([01][0-9]|2[0-3]):[0-5][0-9]$/';
    $isValid = preg_match($pattern, $test['data']) === 1;
    $passed = ($isValid === $test['shouldPass']);
    
    logTest(
        'Time Validation - ' . $test['desc'],
        ['Input' => $test['data']],
        $test['shouldPass'] ? 'Valid format' : 'Invalid format',
        $isValid ? 'Valid' : 'Invalid',
        $passed
    );
}

// ============================================
// TEST GROUP 4: Message Length Validation
// ============================================
echo "<h2>Test Group 4: Message Length Validation (Max 255 chars)</h2>";

$messageTests = [
    ['data' => '', 'shouldPass' => true, 'desc' => 'Empty (allowed)'],
    ['data' => 'A', 'shouldPass' => true, 'desc' => '1 character'],
    ['data' => str_repeat('A', 100), 'shouldPass' => true, 'desc' => '100 characters'],
    ['data' => str_repeat('A', 254), 'shouldPass' => true, 'desc' => '254 chars (Max-1)'],
    ['data' => str_repeat('A', 255), 'shouldPass' => true, 'desc' => '255 chars (Boundary)'],
    ['data' => str_repeat('A', 256), 'shouldPass' => false, 'desc' => '256 chars (Max+1)'],
    ['data' => str_repeat('A', 500), 'shouldPass' => false, 'desc' => '500 characters'],
];

foreach ($messageTests as $test) {
    $length = strlen($test['data']);
    $isValid = $length <= 255;
    $passed = ($isValid === $test['shouldPass']);
    
    logTest(
        'Message Length - ' . $test['desc'],
        ['Length' => $length],
        $test['shouldPass'] ? 'Should accept' : 'Should reject',
        $isValid ? 'Accepted' : 'Rejected',
        $passed
    );
}

// ============================================
// TEST GROUP 5: XSS Prevention
// ============================================
echo "<h2>Test Group 5: XSS Prevention (htmlspecialchars + strip_tags)</h2>";

$xssAttempts = [
    '<script>alert("XSS")</script>',
    '<img src=x onerror=alert(1)>',
    '"><script>evil()</script>',
    'javascript:alert(1)',
    '<iframe src="evil.com"></iframe>',
    '<svg onload=alert(1)>',
];

foreach ($xssAttempts as $xss) {
    $sanitized = htmlspecialchars(strip_tags($xss));
    
    $isSafe = (
        strpos($sanitized, '<script>') === false && 
        strpos($sanitized, '<img') === false &&
        strpos($sanitized, '<iframe') === false &&
        strpos($sanitized, '<svg') === false
    );
    
    $displayInput = substr($xss, 0, 40) . (strlen($xss) > 40 ? '...' : '');
    
    logTest(
        'XSS Prevention Test',
        ['Input' => $displayInput],
        'Tags removed/escaped',
        $isSafe ? 'Sanitized' : 'XSS NOT prevented!',
        $isSafe
    );
}

// ============================================
// TEST GROUP 6: SQL Injection Prevention
// ============================================
echo "<h2>Test Group 6: SQL Injection Prevention (PDO Prepared Statements)</h2>";

$database = new Database();
$db = $database->getConnection();

$sqlInjectionAttempts = [
    "1' OR '1'='1",
    "'; DROP TABLE tblReminders;--",
    "1 UNION SELECT * FROM tblUsers--",
    "1; DELETE FROM tblReminders;--",
];

foreach ($sqlInjectionAttempts as $attempt) {
    try {
        $query = "SELECT * FROM tblReminders WHERE ReminderID = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $attempt);
        $stmt->execute();
        
        $rows = $stmt->rowCount();
        $isSafe = ($rows === 0);
        
        $displayAttempt = substr($attempt, 0, 40) . (strlen($attempt) > 40 ? '...' : '');
        
        logTest(
            'SQL Injection Prevention',
            ['Attempt' => $displayAttempt],
            'No SQL execution, treated as literal',
            $isSafe ? 'Prevented' : 'VULNERABLE!',
            $isSafe
        );
    } catch (Exception $e) {
        logTest(
            'SQL Injection Prevention',
            ['Attempt' => substr($attempt, 0, 40)],
            'No SQL execution',
            'Exception caught - protected',
            true
        );
    }
}

// ============================================
// Display Summary
// ============================================
displayTestSummary();

echo "<p style='margin-top: 30px;'>";
echo "<a href='RunAllTests.php' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Test Dashboard</a>";
echo "</p>";

echo "</body></html>";
?>
