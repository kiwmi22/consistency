<?php
// ============================================================
// Consistency Project - Change Password Page
// Developer: Sri Krishna Shrestha
// File: frontend/pages/change_password.php
// Sprint 2 - Presentation layer
// ============================================================

session_start();

// Redirect if not logged in
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/class/User.php';

$userObj   = new User($pdo);
$userID    = (int)$_SESSION['userID'];
$error     = '';
$success   = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $userObj->changePassword(
        $userID,
        $_POST['current_password'] ?? '',
        $_POST['new_password']     ?? ''
    );
    if ($result['success']) {
        $success = 'Password changed successfully.';
    } else {
        $error = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password – Consistency</title>
    <style>
        * { box-sizing:border-box; }
        body {
            font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
            background:#f0f4f8; min-height:100vh;
            display:flex; align-items:center; justify-content:center;
        }
        .card {
            background:#fff; padding:2.5rem 2rem; border-radius:14px;
            box-shadow:0 4px 24px rgba(0,0,0,.1); width:100%; max-width:420px;
        }
        .card h1 { font-size:1.5rem; color:#1a1a2e; margin-bottom:.3rem; }
        .card p.sub { color:#666; font-size:.875rem; margin-bottom:1.5rem; }
        .form-group { margin-bottom:1rem; }
        label { display:block; font-size:.825rem; font-weight:600; color:#374151; margin-bottom:.35rem; }
        input[type="password"] {
            width:100%; padding:.6rem .9rem; border:1.5px solid #ddd;
            border-radius:8px; font-size:.9rem;
        }
        input:focus { outline:none; border-color:#4f46e5; }
        .btn-primary {
            width:100%; padding:.7rem; background:#4f46e5; color:#fff;
            border:none; border-radius:8px; font-size:.95rem; font-weight:600;
            cursor:pointer; margin-top:.25rem;
        }
        .btn-primary:hover { background:#4338ca; }
        .alert-error   { background:#fef2f2; color:#b91c1c; border:1px solid #fca5a5; padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.875rem; }
        .alert-success { background:#f0fdf4; color:#166534; border:1px solid #86efac; padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.875rem; }
        .back-link { display:block; text-align:center; margin-top:1rem; font-size:.85rem; color:#4f46e5; text-decoration:none; }
        .hint { font-size:.75rem; color:#9ca3af; margin-top:.25rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>🔒 Change Password</h1>
    <p class="sub">Enter your current password then choose a new one.</p>

    <?php if ($error):   ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" action="change_password.php" id="pwForm">

        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>

        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password"
                   placeholder="Min 8 characters" required minlength="8">
            <p class="hint">Must be at least 8 characters.</p>
        </div>

        <div class="form-group">
            <label for="confirm_new">Confirm New Password</label>
            <input type="password" id="confirm_new" name="confirm_new" required>
            <p class="hint" id="matchMsg" style="color:#ef4444;display:none;">Passwords do not match.</p>
        </div>

        <button type="submit" class="btn-primary">Update Password</button>
    </form>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<script>
// Client-side validation - check passwords match before submit
const form    = document.getElementById('pwForm');
const newPass = document.getElementById('new_password');
const confPass= document.getElementById('confirm_new');
const matchMsg= document.getElementById('matchMsg');

confPass.addEventListener('input', () => {
    matchMsg.style.display = (confPass.value && newPass.value !== confPass.value) ? 'block' : 'none';
});

form.addEventListener('submit', (e) => {
    if (newPass.value !== confPass.value) {
        e.preventDefault();
        matchMsg.style.display = 'block';
        confPass.focus();
    }
});
</script>
</body>
</html>