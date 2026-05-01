<?php
// ============================================================
// Consistency Project - Dashboard / Main Menu
// Developer: Sri Krishna Shrestha
// File: frontend/pages/dashboard.php
// Sprint 2 - Presentation layer
// Required by marking scheme: Login page + Main Menu
// ============================================================

session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

// Get session data
$userID   = (int)$_SESSION['userID'];
$username = htmlspecialchars($_SESSION['username']);
$isAdmin  = (bool)$_SESSION['isAdmin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Consistency</title>
    <style>
        /* ── Reset & base ── */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }

        /* ── Top navigation bar ── */
        .navbar {
            background: #1a1a2e;
            padding: .9rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
        }
        .navbar .nav-links a {
            color: #ccc;
            text-decoration: none;
            margin-left: 1.5rem;
            font-size: .9rem;
            transition: color .2s;
        }
        .navbar .nav-links a:hover { color: #fff; }
        .navbar .nav-links a.logout {
            background: #ef4444;
            color: #fff;
            padding: .35rem .85rem;
            border-radius: 6px;
        }
        .navbar .nav-links a.logout:hover { background: #dc2626; }

        /* ── Main content ── */
        .container {
            max-width: 960px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
        }

        /* ── Welcome banner ── */
        .welcome-card {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            padding: 2rem;
            border-radius: 14px;
            margin-bottom: 2rem;
        }
        .welcome-card h1 { font-size: 1.8rem; margin-bottom: .4rem; }
        .welcome-card p  { opacity: .85; font-size: .95rem; }

        /* ── Menu grid ── */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .menu-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            color: #1a1a2e;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            transition: transform .2s, box-shadow .2s;
            text-align: center;
        }
        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,.12);
        }
        .menu-card .icon { font-size: 2.2rem; margin-bottom: .75rem; }
        .menu-card h3    { font-size: 1rem; margin-bottom: .3rem; }
        .menu-card p     { font-size: .8rem; color: #666; }

        /* ── Admin section ── */
        .admin-section { margin-top: 1rem; }
        .admin-section h2 {
            font-size: 1.1rem;
            color: #374151;
            margin-bottom: 1rem;
            padding-bottom: .5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .menu-card.admin { border: 2px solid #ede9fe; }
        .menu-card.admin:hover { border-color: #7c3aed; }
    </style>
</head>
<body>

<!-- Navigation bar (main menu) -->
<nav class="navbar">
    <a href="dashboard.php" class="logo">✅ Consistency</a>
    <div class="nav-links">
        <a href="dashboard.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="change_password.php">Password</a>
        <?php if ($isAdmin): ?>
            <a href="../admin/admin_users.php">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</nav>

<!-- Main content -->
<div class="container">

    <!-- Welcome banner -->
    <div class="welcome-card">
        <h1>Welcome back, <?= $username ?>! 👋</h1>
        <p>Track your habits and build consistency every day.</p>
    </div>

    <!-- User menu cards -->
    <div class="menu-grid">
        <a href="#" class="menu-card">
            <div class="icon">📋</div>
            <h3>My Habits</h3>
            <p>View and manage your habits</p>
        </a>
        <a href="#" class="menu-card">
            <div class="icon">✅</div>
            <h3>Check In</h3>
            <p>Log today's habit completions</p>
        </a>
        <a href="#" class="menu-card">
            <div class="icon">📈</div>
            <h3>My Progress</h3>
            <p>View streaks and statistics</p>
        </a>
        <a href="#" class="menu-card">
            <div class="icon">🔔</div>
            <h3>Reminders</h3>
            <p>Manage your habit reminders</p>
        </a>
        <a href="profile.php" class="menu-card">
            <div class="icon">👤</div>
            <h3>My Profile</h3>
            <p>Update your account details</p>
        </a>
        <a href="change_password.php" class="menu-card">
            <div class="icon">🔒</div>
            <h3>Change Password</h3>
            <p>Update your password securely</p>
        </a>
    </div>

    <!-- Admin section - only shows for admin users -->
    <?php if ($isAdmin): ?>
    <div class="admin-section">
        <h2>🛡️ Admin Panel</h2>
        <div class="menu-grid">
            <a href="../admin/admin_users.php" class="menu-card admin">
                <div class="icon">👥</div>
                <h3>Manage Users</h3>
                <p>Add, edit, deactivate users</p>
            </a>
            <a href="#" class="menu-card admin">
                <div class="icon">📊</div>
                <h3>System Stats</h3>
                <p>View system-wide statistics</p>
            </a>
        </div>
    </div>
    <?php endif; ?>

</div>

</body>
</html>