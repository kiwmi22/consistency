<?php
// ============================================================
// Consistency Project – Login Page
// Developer: Sri Krishna Shrestha
// File: frontend/pages/login.php
// ============================================================

session_start();

if (isset($_SESSION['userID'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/class/User.php';

$userObj = new User($pdo);
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';

    $result = $userObj->login($email, $password);

    if ($result['success']) {
        $user = $result['user'];
        $_SESSION['userID']   = $user['UserID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['isAdmin']  = (bool)$user['IsAdmin'];

        // Redirect admins to admin panel, regular users to dashboard
        if ($_SESSION['isAdmin']) {
            header('Location: ../admin/admin_users.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
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
    <title>Login – Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
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
            max-width: 400px;
        }
        .card h1  { margin: 0 0 .25rem; font-size: 1.6rem; color: #1a1a2e; }
        .card p.subtitle { margin: 0 0 1.5rem; color: #666; font-size: .9rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: .35rem; font-size: .875rem; font-weight: 600; color: #333; }
        input[type="email"],
        input[type="password"] {
            width: 100%; padding: .65rem .9rem;
            border: 1.5px solid #ddd; border-radius: 8px;
            font-size: .95rem; box-sizing: border-box; transition: border-color .2s;
        }
        input:focus { outline: none; border-color: #4f46e5; }
        .btn-primary {
            width: 100%; padding: .75rem; background: #4f46e5;
            color: #fff; border: none; border-radius: 8px;
            font-size: 1rem; font-weight: 600; cursor: pointer;
            margin-top: .5rem; transition: background .2s;
        }
        .btn-primary:hover { background: #4338ca; }
        .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }
        .register-link { text-align: center; margin-top: 1.25rem; font-size: .875rem; color: #666; }
        .register-link a { color: #4f46e5; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="card">
    <h1>Welcome back</h1>
    <p class="subtitle">Log in to track your habits.</p>

    <?php if ($error): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="you@example.com" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Your password" required>
        </div>

        <button type="submit" class="btn-primary">Log in</button>
    </form>

    <p class="register-link">Don't have an account? <a href="register.php">Sign up</a></p>
</div>

</body>
</html>
