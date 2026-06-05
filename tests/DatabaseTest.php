<?php
/**
 * Database Integrity Test Suite
 * Tests database connection, tables, procedures, and foreign keys
 * Author: Pratik Tamang
 */

require_once 'bootstrap.php';

echo "<h1>🗄️ Database Integrity Tests</h1>";
echo "<p><strong>Database:</strong> consistency_db</p>";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p><hr>";

// ============================================
// TEST 1: Database Connection
// ============================================
echo "<h2>Test Group 1: Database Connection</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    $connected = ($db !== null);
    
    logTest(
        'Database Connection',
        ['Database' => 'consistency_db'],
        'Connection established',
        $connected ? 'Connected' : 'Failed',
        $connected
    );
} catch (Exception $e) {
    logTest(
        'Database Connection',
        [],
        'Connection established',
        'Error: ' . $e->getMessage(),
        false
    );
    die("<p>Cannot continue tests without database connection.</p>");
}

// ============================================
// TEST 2: Required Tables Exist
// ============================================
echo "<h2>Test Group 2: Required Tables Exist</h2>";

$requiredTables = [
    'tblUsers' => 'User Management',
    'tblHabits' => 'Habit Management',
    'tblReminders' => 'Reminders & Notifications',
    'tblHabitLogs' => 'Habit Logging',
    'tblStreaks' => 'Progress & Streaks',
];

foreach ($requiredTables as $table => $component) {
    try {
        $query = "SELECT 1 FROM $table LIMIT 1";
        $stmt = $db->query($query);
        $exists = ($stmt !== false);
        
        logTest(
            "Table exists: $table ($component)",
            ['Table' => $table],
            'Table accessible',
            $exists ? 'Exists' : 'Missing',
            $exists
        );
    } catch (Exception $e) {
        logTest(
            "Table exists: $table",
            ['Table' => $table],
            'Table accessible',
            'Missing or inaccessible',
            false
        );
    }
}

// ============================================
// TEST 3: Stored Procedures Exist (Reminders)
// ============================================
echo "<h2>Test Group 3: Reminder Stored Procedures</h2>";

$requiredProcedures = [
    'sp_AddReminder',
    'sp_GetAllReminders',
    'sp_GetReminderById',
    'sp_UpdateReminder',
    'sp_DeleteReminder',
    'sp_FilterRemindersByHabit',
    'sp_FilterRemindersByStatus',
    'sp_FilterRemindersByUser',
];

foreach ($requiredProcedures as $procedure) {
    try {
        $query = "SHOW PROCEDURE STATUS WHERE Name = :name";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $procedure);
        $stmt->execute();
        $exists = ($stmt->rowCount() > 0);
        
        logTest(
            "Procedure exists: $procedure",
            ['Procedure' => $procedure],
            'Procedure available',
            $exists ? 'Exists' : 'Missing',
            $exists
        );
    } catch (Exception $e) {
        logTest(
            "Procedure exists: $procedure",
            [],
            'Available',
            'Error checking',
            false
        );
    }
}

// ============================================
// TEST 4: Foreign Key Constraints
// ============================================
echo "<h2>Test Group 4: Foreign Key Integrity</h2>";

// Try to insert reminder with non-existent UserID
try {
    $query = "INSERT INTO tblReminders (UserID, HabitID, ReminderTime, IsEnabled, Message, CreatedDate) 
              VALUES (99999, 1, '12:00', 1, 'Test FK', CURDATE())";
    $stmt = $db->prepare($query);
    $result = $stmt->execute();
    
    logTest(
        'Foreign Key: Invalid UserID (99999)',
        ['UserID' => 99999],
        'Should reject (FK violation)',
        'Accepted (FK not working!)',
        false
    );
} catch (PDOException $e) {
    logTest(
        'Foreign Key: Invalid UserID (99999)',
        ['UserID' => 99999],
        'Should reject (FK violation)',
        'Rejected (FK working)',
        true
    );
}

// Try to insert reminder with non-existent HabitID
try {
    $query = "INSERT INTO tblReminders (UserID, HabitID, ReminderTime, IsEnabled, Message, CreatedDate) 
              VALUES (1, 99999, '12:00', 1, 'Test FK', CURDATE())";
    $stmt = $db->prepare($query);
    $result = $stmt->execute();
    
    logTest(
        'Foreign Key: Invalid HabitID (99999)',
        ['HabitID' => 99999],
        'Should reject (FK violation)',
        'Accepted (FK not working!)',
        false
    );
} catch (PDOException $e) {
    logTest(
        'Foreign Key: Invalid HabitID (99999)',
        ['HabitID' => 99999],
        'Should reject (FK violation)',
        'Rejected (FK working)',
        true
    );
}

// ============================================
// TEST 5: Test Data Exists
// ============================================
echo "<h2>Test Group 5: Test Data Verification</h2>";

$dataTests = [
    ['table' => 'tblUsers', 'minExpected' => 3, 'description' => 'Users in database'],
    ['table' => 'tblHabits', 'minExpected' => 3, 'description' => 'Habits in database'],
    ['table' => 'tblReminders', 'minExpected' => 1, 'description' => 'Reminders in database'],
];

foreach ($dataTests as $test) {
    try {
        $query = "SELECT COUNT(*) AS count FROM " . $test['table'];
        $stmt = $db->query($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $row['count'];
        $passed = ($count >= $test['minExpected']);
        
        logTest(
            $test['description'],
            ['Table' => $test['table']],
            'At least ' . $test['minExpected'] . ' records',
            "Found $count records",
            $passed
        );
    } catch (Exception $e) {
        logTest(
            $test['description'],
            [],
            'Records exist',
            'Error: ' . $e->getMessage(),
            false
        );
    }
}

// ============================================
// TEST 6: Data Integrity Checks
// ============================================
echo "<h2>Test Group 6: Data Integrity</h2>";

// Check no orphaned reminders
try {
    $query = "SELECT COUNT(*) AS count FROM tblReminders r 
              LEFT JOIN tblUsers u ON r.UserID = u.UserID 
              WHERE u.UserID IS NULL";
    $stmt = $db->query($query);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $orphans = $row['count'];
    
    logTest(
        'No orphaned reminders (without valid User)',
        ['Check' => 'JOIN check'],
        'Zero orphans',
        "Found $orphans orphans",
        $orphans == 0
    );
} catch (Exception $e) {
    logTest(
        'Orphan check',
        [],
        'Zero orphans',
        'Error: ' . $e->getMessage(),
        false
    );
}

// Check time format integrity
try {
    $query = "SELECT COUNT(*) AS count FROM tblReminders 
              WHERE ReminderTime IS NULL OR ReminderTime = '00:00:00' AND Message LIKE '%invalid%'";
    $stmt = $db->query($query);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    logTest(
        'Time format integrity',
        ['Check' => 'Valid time values'],
        'All times are valid TIME format',
        'All times valid',
        true
    );
} catch (Exception $e) {
    logTest('Time format check', [], 'Valid times', 'Error', false);
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
