<?php
// ============================================================
// Consistency Project - Test Script
// Developer: Sri Krishna Shrestha
// File: database/test-data/test_users.php
// Sprint 2 - Runs all 20 test cases for the User class
// HOW TO USE: Visit http://localhost/Consistency/database/test-data/test_users.php
// ============================================================

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/classes/User.php';

$userObj = new User($pdo);

// Track results
$passed = 0;
$failed = 0;
$results = [];

// Helper function to record each test result
function runTest(string $testNum, string $method, string $description, $actual, bool $pass): array {
    return [
        'num'         => $testNum,
        'method'      => $method,
        'description' => $description,
        'actual'      => is_array($actual) ? json_encode($actual) : (string)$actual,
        'pass'        => $pass
    ];
}

// ── TEST 1: register() valid input ───────────────────────────
$r = $userObj->register('testrun_user', 'testrun@example.com', 'Password1!');
$results[] = runTest('1', 'register()', 'Register with valid inputs (VARCHAR test)',
    $r, $r['success'] === true);

// ── TEST 2: register() duplicate email ───────────────────────
$r = $userObj->register('another_user', 'testrun@example.com', 'Password1!');
$results[] = runTest('2', 'register()', 'Register with duplicate email (error expected)',
    $r, $r['success'] === false && !empty($r['error']));

// ── TEST 3: register() password too short ────────────────────
$r = $userObj->register('shortpass', 'shortpass@example.com', 'abc');
$results[] = runTest('3', 'register()', 'Register with password < 8 chars (VARCHAR validation)',
    $r, $r['success'] === false);

// ── TEST 4: register() invalid email format ──────────────────
$r = $userObj->register('bademail', 'notanemail', 'Password1!');
$results[] = runTest('4', 'register()', 'Register with invalid email format (validation)',
    $r, $r['success'] === false);

// ── TEST 5: login() valid credentials ────────────────────────
$r = $userObj->login('testrun@example.com', 'Password1!');
$results[] = runTest('5', 'login()', 'Login with correct email and password (VARCHAR)',
    $r, $r['success'] === true && isset($r['user']));

// Store new UserID for later tests
$newUserID = $r['success'] ? (int)$r['user']['UserID'] : 0;

// ── TEST 6: login() wrong password ───────────────────────────
$r = $userObj->login('testrun@example.com', 'WrongPass!');
$results[] = runTest('6', 'login()', 'Login with incorrect password (error expected)',
    $r, $r['success'] === false);

// ── TEST 7: login() non-existent email ───────────────────────
$r = $userObj->login('nobody@nowhere.com', 'Password1!');
$results[] = runTest('7', 'login()', 'Login with email that does not exist',
    $r, $r['success'] === false);

// ── TEST 8: getUserByID() valid INT ───────────────────────────
$r = $userObj->getUserByID(1);
$results[] = runTest('8', 'getUserByID()', 'Get user by valid UserID=1 (INT test)',
    $r, $r !== null && isset($r['UserID']));

// ── TEST 9: getUserByID() non-existent ID ────────────────────
$r = $userObj->getUserByID(9999);
$results[] = runTest('9', 'getUserByID()', 'Get user by non-existent UserID=9999 (INT edge case)',
    $r, $r === null);

// ── TEST 10: getAllUsers() filter all ────────────────────────
$r = $userObj->getAllUsers(-1);
$results[] = runTest('10', 'getAllUsers()', 'Get all users (filter=-1, INT test)',
    $r, is_array($r) && count($r) > 0);

// ── TEST 11: getAllUsers() active only ────────────────────────
$r = $userObj->getAllUsers(1);
$allActive = is_array($r) && !empty($r);
if ($allActive) {
    foreach ($r as $u) {
        if (!$u['IsActive']) { $allActive = false; break; }
    }
}
$results[] = runTest('11', 'getAllUsers()', 'Filter active users only (BOOLEAN IsActive=TRUE)',
    $r, $allActive);

// ── TEST 12: getAllUsers() inactive only ──────────────────────
$r = $userObj->getAllUsers(0);
$results[] = runTest('12', 'getAllUsers()', 'Filter inactive users only (BOOLEAN IsActive=FALSE)',
    $r, is_array($r));

// ── TEST 13: findUsers() matching term ───────────────────────
$r = $userObj->findUsers('testrun');
$results[] = runTest('13', 'findUsers()', 'Find user by username search term (VARCHAR)',
    $r, is_array($r) && count($r) > 0);

// ── TEST 14: findUsers() no match ────────────────────────────
$r = $userObj->findUsers('xyzabc99999');
$results[] = runTest('14', 'findUsers()', 'Find with term that matches nothing',
    $r, is_array($r) && count($r) === 0);

// ── TEST 15: updateProfile() valid ───────────────────────────
if ($newUserID > 0) {
    $r = $userObj->updateProfile($newUserID, 'testrun_updated', 'testrun_updated@example.com');
    $results[] = runTest('15', 'updateProfile()', 'Update username and email (VARCHAR, INT)',
        $r, $r['success'] === true);
} else {
    $results[] = runTest('15', 'updateProfile()', 'Skipped - no userID from test 1', 'skipped', false);
}

// ── TEST 16: updateProfile() duplicate email ─────────────────
if ($newUserID > 0) {
    $r = $userObj->updateProfile($newUserID, 'testrun_updated', 'admin@consistency.com');
    $results[] = runTest('16', 'updateProfile()', 'Update with duplicate email (error expected)',
        $r, $r['success'] === false);
} else {
    $results[] = runTest('16', 'updateProfile()', 'Skipped', 'skipped', false);
}

// ── TEST 17: changePassword() correct current pass ───────────
if ($newUserID > 0) {
    $r = $userObj->changePassword($newUserID, 'Password1!', 'NewPassword2!');
    $results[] = runTest('17', 'changePassword()', 'Change password with correct current password (VARCHAR)',
        $r, $r['success'] === true);
} else {
    $results[] = runTest('17', 'changePassword()', 'Skipped', 'skipped', false);
}

// ── TEST 18: changePassword() wrong current pass ─────────────
if ($newUserID > 0) {
    $r = $userObj->changePassword($newUserID, 'WrongOldPass!', 'AnotherPass3!');
    $results[] = runTest('18', 'changePassword()', 'Change password with wrong current password (error expected)',
        $r, $r['success'] === false);
} else {
    $results[] = runTest('18', 'changePassword()', 'Skipped', 'skipped', false);
}

// ── TEST 19: deactivateUser() valid UserID ────────────────────
if ($newUserID > 0) {
    $r = $userObj->deactivateUser($newUserID);
    $results[] = runTest('19', 'deactivateUser()', 'Deactivate user (soft delete, BOOLEAN IsActive, INT UserID)',
        $r, $r['success'] === true);

    // Verify IsActive is now FALSE
    $check = $userObj->getUserByID($newUserID);
    $results[] = runTest('19b', 'deactivateUser()', 'Verify IsActive=FALSE after deactivation (BOOLEAN)',
        $check, $check !== null && !(bool)$check['IsActive']);
} else {
    $results[] = runTest('19', 'deactivateUser()', 'Skipped', 'skipped', false);
    $results[] = runTest('19b', 'deactivateUser()', 'Skipped', 'skipped', false);
}

// ── TEST 20: login() deactivated account ─────────────────────
if ($newUserID > 0) {
    $r = $userObj->login('testrun_updated@example.com', 'NewPassword2!');
    $results[] = runTest('20', 'login()', 'Login with deactivated account (BOOLEAN IsActive check, error expected)',
        $r, $r['success'] === false);
} else {
    $results[] = runTest('20', 'login()', 'Skipped', 'skipped', false);
}

// ── isAdmin() test ────────────────────────────────────────────
$r = $userObj->isAdmin(1);
$results[] = runTest('21', 'isAdmin()', 'Check IsAdmin for UserID=1 (BOOLEAN return)',
    $r, $r === true);

// Count passes and fails
foreach ($results as $res) {
    if ($res['pass']) $passed++; else $failed++;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Log – User Account Management</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; padding: 2rem; }
        h1   { color: #1a1a2e; margin-bottom: .5rem; }
        .summary { margin-bottom: 1.5rem; font-size: 1rem; }
        .pass-count { color: #16a34a; font-weight: 700; }
        .fail-count { color: #dc2626; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,.08); }
        th { background: #1a1a2e; color: #fff; padding: .75rem 1rem; text-align: left; font-size: .8rem; }
        td { padding: .7rem 1rem; font-size: .8rem; color: #374151; border-bottom: 1px solid #f3f4f6; }
        tr:last-child td { border-bottom: none; }
        .pass { background: #f0fdf4; }
        .fail { background: #fef2f2; }
        .badge-pass { background: #dcfce7; color: #166534; padding: .2rem .6rem; border-radius: 20px; font-weight: 700; font-size: .75rem; }
        .badge-fail { background: #fee2e2; color: #991b1b; padding: .2rem .6rem; border-radius: 20px; font-weight: 700; font-size: .75rem; }
        .note { margin-top: 1rem; font-size: .8rem; color: #6b7280; }
    </style>
</head>
<body>

<h1>🧪 Test Log — User Account Management</h1>
<p>Developer: Sri Krishna Shrestha &nbsp;|&nbsp; Sprint 2 &nbsp;|&nbsp; File: User.php</p>
<div class="summary">
    Results: <span class="pass-count"><?= $passed ?> Passed</span> &nbsp;/&nbsp;
    <span class="fail-count"><?= $failed ?> Failed</span> &nbsp;/&nbsp;
    Total: <?= count($results) ?> tests
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Method</th>
            <th>Test Description</th>
            <th>Actual Result (truncated)</th>
            <th>Pass / Fail</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($results as $r): ?>
        <tr class="<?= $r['pass'] ? 'pass' : 'fail' ?>">
            <td><?= htmlspecialchars($r['num']) ?></td>
            <td><strong><?= htmlspecialchars($r['method']) ?></strong></td>
            <td><?= htmlspecialchars($r['description']) ?></td>
            <td><?= htmlspecialchars(substr($r['actual'], 0, 120)) ?></td>
            <td>
                <?php if ($r['pass']): ?>
                    <span class="badge-pass">✅ PASS</span>
                <?php else: ?>
                    <span class="badge-fail">❌ FAIL</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p class="note">
    Data types tested: VARCHAR (Username, Email, PasswordHash), INT (UserID), BOOLEAN (IsAdmin, IsActive), DATE (DateCreated auto via CURDATE()).<br>
    Test types covered: Valid inputs, invalid inputs, boundary/edge cases, error conditions.
</p>

</body>
</html>