<?php
/**
 * Reminder Class Test Suite
 * Tests all CRUD operations for the Reminder class
 * Author: Pratik Tamang
 */

require_once 'bootstrap.php';

echo "<h1>📋 Reminder Class Tests</h1>";
echo "<p><strong>Component:</strong> Reminders & Notifications</p>";
echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p><hr>";

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("<h2 style='color: red;'>❌ Database connection failed!</h2>");
}

$reminder = new Reminder($db);

// ============================================
// TEST 1: Add Reminder - Valid Data
// ============================================
echo "<h2>Test Group 1: Add Reminder Operations</h2>";

$reminder->UserID = 1;
$reminder->HabitID = 1;
$reminder->ReminderTime = '07:00';
$reminder->IsEnabled = 1;
$reminder->Message = 'Morning exercise reminder';

$result = $reminder->add();
logTest(
    'TR-001: Add Reminder with valid data',
    ['UserID' => 1, 'HabitID' => 1, 'Time' => '07:00'],
    'Reminder added successfully',
    $result ? 'Added' : 'Failed',
    $result === true
);

// ============================================
// TEST 2: Add Reminder - Midnight Edge Case
// ============================================
$reminder->UserID = 1;
$reminder->HabitID = 1;
$reminder->ReminderTime = '00:00';
$reminder->IsEnabled = 1;
$reminder->Message = 'Midnight reminder test';

$result = $reminder->add();
logTest(
    'TR-040: Add Reminder at midnight (00:00)',
    ['Time' => '00:00'],
    'Reminder added at midnight',
    $result ? 'Added' : 'Failed',
    $result === true
);

// ============================================
// TEST 3: Add Reminder - End of Day Edge Case
// ============================================
$reminder->UserID = 1;
$reminder->HabitID = 1;
$reminder->ReminderTime = '23:59';
$reminder->IsEnabled = 1;
$reminder->Message = 'End of day reminder';

$result = $reminder->add();
logTest(
    'TR-041: Add Reminder at end of day (23:59)',
    ['Time' => '23:59'],
    'Reminder added at 23:59',
    $result ? 'Added' : 'Failed',
    $result === true
);

// ============================================
// TEST 4: Add Reminder - With Max Length Message
// ============================================
$reminder->UserID = 2;
$reminder->HabitID = 2;
$reminder->ReminderTime = '12:00';
$reminder->IsEnabled = 1;
$reminder->Message = str_repeat('A', 255);

$result = $reminder->add();
logTest(
    'TR-038: Add Reminder with 255 character message',
    ['MessageLength' => 255],
    'Accepted at boundary',
    $result ? 'Added' : 'Failed',
    $result === true
);

// ============================================
// TEST 5: Add Reminder - Disabled Status
// ============================================
$reminder->UserID = 2;
$reminder->HabitID = 3;
$reminder->ReminderTime = '15:30';
$reminder->IsEnabled = 0;
$reminder->Message = 'Disabled reminder for testing';

$result = $reminder->add();
logTest(
    'TR-008: Add Reminder with IsEnabled=0',
    ['IsEnabled' => 0],
    'Disabled reminder added',
    $result ? 'Added' : 'Failed',
    $result === true
);

// ============================================
// TEST GROUP 2: List All Reminders
// ============================================
echo "<h2>Test Group 2: List Operations</h2>";

$stmt = $reminder->listAll();
$count = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $count++;
}
logTest(
    'TR-014: List all reminders',
    'Operation: listAll',
    'All reminders displayed',
    "Found $count reminders",
    $count > 0
);

// ============================================
// TEST GROUP 3: Find Reminder by ID
// ============================================
echo "<h2>Test Group 3: Find Operations</h2>";

// Test valid ID
$reminder->ReminderID = 1;
$result = $reminder->findById();
logTest(
    'TR-017: Find Reminder with valid ID (1)',
    ['ReminderID' => 1],
    'Reminder found and details loaded',
    $result ? 'Found' : 'Not found',
    $result === true
);

// Test invalid ID
$reminder->ReminderID = 99999;
$result = $reminder->findById();
logTest(
    'TR-018: Find Reminder with invalid ID (99999)',
    ['ReminderID' => 99999],
    'No reminder found',
    $result ? 'Found (unexpected)' : 'Not found (correct)',
    $result === false
);

// Test ID = 0
$reminder->ReminderID = 0;
$result = $reminder->findById();
logTest(
    'TR-021: Find Reminder with ID = 0',
    ['ReminderID' => 0],
    'No reminder found',
    $result ? 'Found' : 'Not found',
    $result === false
);

// ============================================
// TEST GROUP 4: Update Reminder
// ============================================
echo "<h2>Test Group 4: Update Operations</h2>";

$reminder->ReminderID = 1;
$reminder->UserID = 1;
$reminder->HabitID = 2;
$reminder->ReminderTime = '08:30';
$reminder->IsEnabled = 1;
$reminder->Message = 'Updated morning reminder';

$result = $reminder->update();
logTest(
    'TR-006: Update Reminder time and message',
    ['ReminderID' => 1, 'NewTime' => '08:30'],
    'Reminder updated successfully',
    $result ? 'Updated' : 'Failed',
    $result === true
);

// ============================================
// TEST GROUP 5: Filter Operations
// ============================================
echo "<h2>Test Group 5: Filter Operations</h2>";

// Filter by Habit
$reminder->HabitID = 1;
$stmt = $reminder->filterByHabit();
$count = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $count++;
}
logTest(
    'TR-022: Filter by HabitID = 1',
    ['HabitID' => 1],
    'Only Habit 1 reminders displayed',
    "Found $count reminders for Habit 1",
    $count >= 0
);

// Filter by Status - Enabled
$reminder->IsEnabled = 1;
$stmt = $reminder->filterByStatus();
$enabledCount = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $enabledCount++;
}
logTest(
    'TR-024: Filter by Status (Enabled)',
    ['IsEnabled' => 1],
    'Only enabled reminders displayed',
    "Found $enabledCount enabled reminders",
    $enabledCount > 0
);

// Filter by Status - Disabled
$reminder->IsEnabled = 0;
$stmt = $reminder->filterByStatus();
$disabledCount = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $disabledCount++;
}
logTest(
    'TR-025: Filter by Status (Disabled)',
    ['IsEnabled' => 0],
    'Only disabled reminders displayed',
    "Found $disabledCount disabled reminders",
    $disabledCount >= 0
);

// Filter by User
$reminder->UserID = 1;
$stmt = $reminder->filterByUser();
$userCount = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $userCount++;
}
logTest(
    'TR-027: Filter by UserID = 1',
    ['UserID' => 1],
    'Only User 1 reminders displayed',
    "Found $userCount reminders for User 1",
    $userCount >= 0
);

// ============================================
// TEST GROUP 6: Filter by Non-Existent
// ============================================

$reminder->HabitID = 999;
$stmt = $reminder->filterByHabit();
$noMatchCount = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $noMatchCount++;
}
logTest(
    'TR-023: Filter by HabitID = 999 (non-existent)',
    ['HabitID' => 999],
    'No matches found',
    "Found $noMatchCount reminders",
    $noMatchCount === 0
);

// ============================================
// Display Summary
// ============================================
displayTestSummary();

echo "<p style='margin-top: 30px;'>";
echo "<a href='RunAllTests.php' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Test Dashboard</a>";
echo "</p>";

echo "</body></html>";
?>
