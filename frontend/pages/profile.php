<?php
// ============================================================
// Consistency Project – Profile & Change Password Page
// Developer: Sri Krishna Shrestha
// File: frontend/pages/profile.php
// ============================================================

session_start();

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/class/User.php';

$userObj   = new User($pdo);
$userID    = (int)$_SESSION['userID'];
$user      = $userObj->getUserByID($userID);
$error     = '';
$success   = '';
$pwError   = '';
$pwSuccess = '';

// ── Handle profile update ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $result = $userObj->updateProfile($userID, $_POST['username'] ?? '', $_POST['email'] ?? '');
    if ($result['success']) {
        $success = 'Profile updated successfully.';
        $_SESSION['username'] = $_POST['username'];
        $user = $userObj->getUserByID($userID); // Refresh
    } else {
        $error = $result['error'];
    }
}

// ── Handle password change ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $result = $userObj->changePassword(
        $userID,
        $_POST['current_password'] ?? '',
        $_POST['new_password']     ?? ''
    );
    if ($result['success']) {
        $pwSuccess = 'Password changed successfully.';
    } else {
        $pwError = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; margin: 0; padding: 2rem; }
        .page-title { font-size: 1.6rem; color: #1a1a2e; margin-bottom: 1.5rem; }
        .card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.08); max-width: 500px; margin-bottom: 1.5rem; }
        .card h2 { margin: 0 0 1.25rem; font-size: 1.1rem; color: #1a1a2e; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: .35rem; font-size: .875rem; font-weight: 600; color: #333; }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%; padding: .65rem .9rem; border: 1.5px solid #ddd;
            border-radius: 8px; font-size: .95rem; box-sizing: border-box; transition: border-color .2s;
        }
        input:focus { outline: none; border-color: #4f46e5; }
        .btn { padding: .65rem 1.5rem; border: none; border-radius: 8px; font-size: .95rem; font-weight: 600; cursor: pointer; transition: background .2s; }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #4338ca; }
        .alert-error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; padding: .7rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #86efac; padding: .7rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }
        .back-link { display: inline-block; margin-bottom: 1rem; font-size: .875rem; color: #4f46e5; text-decoration: none; }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-link">← Back to dashboard</a>
<h1 class="page-title">My Profile</h1>

<!-- Update Profile -->
<div class="card">
    <h2>Account details</h2>

    <?php if ($error):   ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" action="profile.php">
        <input type="hidden" name="update_profile" value="1">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?= htmlspecialchars($user['Username']) ?>" required maxlength="50">
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($user['Email']) ?>" required maxlength="100">
        </div>

        <button type="submit" class="btn btn-primary">Save changes</button>
    </form>
</div>

<!-- Change Password -->
<div class="card">
    <h2>Change password</h2>

    <?php if ($pwError):   ?><div class="alert-error"><?= htmlspecialchars($pwError) ?></div><?php endif; ?>
    <?php if ($pwSuccess): ?><div class="alert-success"><?= htmlspecialchars($pwSuccess) ?></div><?php endif; ?>

    <form method="POST" action="profile.php" id="pwForm">
        <input type="hidden" name="change_password" value="1">

        <div class="form-group">
            <label for="current_password">Current password</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>

        <div class="form-group">
            <label for="new_password">New password</label>
            <input type="password" id="new_password" name="new_password"
                   placeholder="Min. 8 characters" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirm_new">Confirm new password</label>
            <input type="password" id="confirm_new" name="confirm_new" required>
        </div>

        <button type="submit" class="btn btn-primary">Update password</button>
    </form>
</div>

<script>
document.getElementById('pwForm').addEventListener('submit', function(e) {
    const np = document.getElementById('new_password').value;
    const cn = document.getElementById('confirm_new').value;
    if (np !== cn) { e.preventDefault(); alert('New passwords do not match.'); }
});
</script>

</body>
</html>
