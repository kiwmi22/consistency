<?php
// ============================================================
// Consistency Project – User Registration Page
// Developer: Sri Krishna Shrestha
// File: frontend/pages/register.php
// ============================================================

session_start();

// Redirect if already logged in
if (isset($_SESSION['userID'])) {
    header('Location: Dashboard.php');
    exit;
}

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/classes/User.php';

$userObj = new User($pdo);
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = trim($_POST['username']         ?? '');
    $email           = trim($_POST['email']            ?? '');
    $password        =      $_POST['password']         ?? '';
    $confirmPassword =      $_POST['confirm_password'] ?? '';

    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $result = $userObj->register($username, $email, $password);
        if ($result['success']) {
            $success = 'Account created! <a href="login.php">Log in here</a>.';
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – Consistency</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,.1);
            width: 100%;
            max-width: 420px;
        }
        .card h1 { margin: 0 0 .25rem; font-size: 1.6rem; color: #1a1a2e; }
        .card p.subtitle { margin: 0 0 1.5rem; color: #666; font-size: .9rem; }
        .form-group { margin-bottom: 1rem; }
        label {
            display: block;
            margin-bottom: .35rem;
            font-size: .875rem;
            font-weight: 600;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: .65rem .9rem;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: .95rem;
            transition: border-color .2s;
        }
        input:focus { outline: none; border-color: #4f46e5; }
        .btn-primary {
            width: 100%;
            padding: .75rem;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: .5rem;
        }
        .btn-primary:hover { background: #4338ca; }
        .alert-error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #86efac; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }
        .login-link { text-align: center; margin-top: 1.25rem; font-size: .875rem; color: #666; }
        .login-link a { color: #4f46e5; text-decoration: none; font-weight: 600; }
        .password-hint { font-size: .78rem; color: #999; margin-top: .25rem; }
    </style>
</head>
<body>

<div class="card">
    <h1>Create account</h1>
    <p class="subtitle">Start building your habits today.</p>

    <?php if ($error):   ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" action="register.php" id="registerForm" novalidate>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   placeholder="e.g. sri_krishna" required maxlength="50">
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="you@example.com" required maxlength="100">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Min. 8 characters" required minlength="8">
            <p class="password-hint">Must be at least 8 characters.</p>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password"
                   placeholder="Repeat your password" required>
            <p class="password-hint" id="matchMsg" style="color:red;display:none;">Passwords do not match.</p>
        </div>

        <button type="submit" class="btn-primary">Create account</button>
    </form>

    <p class="login-link">Already have an account? <a href="login.php">Log in</a></p>
</div>

<script>
const form = document.getElementById('registerForm');
const pass  = document.getElementById('password');
const conf  = document.getElementById('confirm_password');
const msg   = document.getElementById('matchMsg');

conf.addEventListener('input', () => {
    msg.style.display = (conf.value && pass.value !== conf.value) ? 'block' : 'none';
});

form.addEventListener('submit', (e) => {
    if (pass.value !== conf.value) {
        e.preventDefault();
        msg.style.display = 'block';
        conf.focus();
        return;
    }
    if (pass.value.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters.');
    }
});
</script>

</body>
</html>
