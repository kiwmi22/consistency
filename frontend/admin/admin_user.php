<?php
// ============================================================
// Consistency Project – Admin User Management
// Developer: Sri Krishna Shrestha
// File: frontend/admin/admin_users.php
// ============================================================

session_start();

// Must be logged in AND admin
if (!isset($_SESSION['userID']) || !$_SESSION['isAdmin']) {
    header('Location: ../pages/login.php');
    exit;
}

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/classes/User.php';

$userObj = new User($pdo);
$message = '';
$msgType = '';

// ── Handle POST actions ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $targetID = (int)($_POST['target_user_id'] ?? 0);

    // Prevent admin from acting on themselves
    if ($targetID === (int)$_SESSION['userID']) {
        $message = 'You cannot perform this action on your own account.';
        $msgType = 'error';
    } elseif ($action === 'deactivate') {
        $result  = $userObj->deactivateUser($targetID);
        $message = $result['success'] ? 'User deactivated.' : $result['error'];
        $msgType = $result['success'] ? 'success' : 'error';
    } elseif ($action === 'toggle_admin') {
        $result  = $userObj->toggleAdmin($targetID);
        $message = $result['success'] ? 'Admin privilege updated.' : $result['error'];
        $msgType = $result['success'] ? 'success' : 'error';
    }
}

// ── Filtering & searching ──
$filter     = isset($_GET['filter'])     ? (int)$_GET['filter']        : -1;
$searchTerm = isset($_GET['search'])     ? trim($_GET['search'])        : '';

if (!empty($searchTerm)) {
    $users = $userObj->findUsers($searchTerm);
} else {
    $users = $userObj->getAllUsers($filter);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – User Management | Consistency</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; margin: 0; padding: 0; }

        /* Sidebar */
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 220px; background: #1a1a2e; color: #ccc; padding: 2rem 1.25rem; flex-shrink: 0; }
        .sidebar h2 { color: #fff; font-size: 1.1rem; margin: 0 0 2rem; }
        .sidebar a { display: block; color: #ccc; text-decoration: none; padding: .5rem .75rem; border-radius: 6px; margin-bottom: .25rem; font-size: .9rem; }
        .sidebar a:hover, .sidebar a.active { background: #4f46e5; color: #fff; }

        /* Main content */
        .main { flex: 1; padding: 2rem; }
        .page-title { font-size: 1.5rem; color: #1a1a2e; margin: 0 0 1.5rem; }

        /* Toolbar */
        .toolbar { display: flex; gap: .75rem; flex-wrap: wrap; align-items: center; margin-bottom: 1.25rem; }
        .toolbar input[type="text"] { padding: .55rem .9rem; border: 1.5px solid #ddd; border-radius: 8px; font-size: .9rem; width: 240px; }
        .toolbar input:focus { outline: none; border-color: #4f46e5; }
        .btn { padding: .5rem 1rem; border: none; border-radius: 8px; font-size: .875rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #4338ca; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background: #d1d5db; }
        .btn-danger  { background: #ef4444; color: #fff; font-size: .8rem; padding: .35rem .75rem; }
        .btn-warning { background: #f59e0b; color: #fff; font-size: .8rem; padding: .35rem .75rem; }
        .filter-group { display: flex; gap: .5rem; }

        /* Table */
        .table-wrapper { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.07); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #1a1a2e; color: #fff; }
        th { padding: .9rem 1rem; text-align: left; font-size: .85rem; font-weight: 600; }
        td { padding: .85rem 1rem; font-size: .875rem; color: #374151; border-bottom: 1px solid #f3f4f6; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f9fafb; }

        /* Badges */
        .badge { display: inline-block; padding: .2rem .6rem; border-radius: 20px; font-size: .75rem; font-weight: 600; }
        .badge-active   { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .badge-admin    { background: #ede9fe; color: #5b21b6; }

        /* Alerts */
        .alert-error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #86efac; padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }

        .no-results { text-align: center; padding: 3rem; color: #9ca3af; }
    </style>
</head>
<body>

<div class="layout">
    <!-- Sidebar -->
    <nav class="sidebar">
        <h2>Consistency Admin</h2>
        <a href="admin_users.php" class="active">👥 Users</a>
        <a href="#">📋 Habits</a>
        <a href="#">📅 Logs</a>
        <a href="#">📈 Streaks</a>
        <a href="#">🔔 Reminders</a>
        <a href="../pages/logout.php" style="margin-top:2rem;">🚪 Logout</a>
    </nav>

    <!-- Main content -->
    <main class="main">
        <h1 class="page-title">User Management</h1>

        <?php if ($message): ?>
            <div class="alert-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Search & filter toolbar -->
        <form method="GET" action="admin_users.php">
            <div class="toolbar">
                <input type="text" name="search" placeholder="Search by username or email…"
                       value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="admin_users.php" class="btn btn-secondary">Clear</a>

                <div class="filter-group">
                    <a href="?filter=-1" class="btn <?= $filter === -1 ? 'btn-primary' : 'btn-secondary' ?>">All</a>
                    <a href="?filter=1"  class="btn <?= $filter === 1  ? 'btn-primary' : 'btn-secondary' ?>">Active</a>
                    <a href="?filter=0"  class="btn <?= $filter === 0  ? 'btn-primary' : 'btn-secondary' ?>">Inactive</a>
                </div>
            </div>
        </form>

        <!-- Users table -->
        <div class="table-wrapper">
            <?php if (empty($users)): ?>
                <p class="no-results">No users found.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['UserID'] ?></td>
                        <td><?= htmlspecialchars($u['Username']) ?></td>
                        <td><?= htmlspecialchars($u['Email']) ?></td>
                        <td>
                            <?php if ($u['IsAdmin']): ?>
                                <span class="badge badge-admin">Admin</span>
                            <?php else: ?>
                                <span class="badge" style="background:#e0f2fe;color:#075985;">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['IsActive']): ?>
                                <span class="badge badge-active">Active</span>
                            <?php else: ?>
                                <span class="badge badge-inactive">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['DateCreated']) ?></td>
                        <td style="display:flex;gap:.4rem;flex-wrap:wrap;">

                            <!-- Toggle admin -->
                            <form method="POST" action="admin_users.php" style="display:inline;">
                                <input type="hidden" name="action"         value="toggle_admin">
                                <input type="hidden" name="target_user_id" value="<?= (int)$u['UserID'] ?>">
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('Toggle admin privilege for this user?')">
                                    <?= $u['IsAdmin'] ? 'Remove Admin' : 'Make Admin' ?>
                                </button>
                            </form>

                            <!-- Deactivate (only if active) -->
                            <?php if ($u['IsActive']): ?>
                            <form method="POST" action="admin_users.php" style="display:inline;">
                                <input type="hidden" name="action"         value="deactivate">
                                <input type="hidden" name="target_user_id" value="<?= (int)$u['UserID'] ?>">
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Deactivate this user? They will no longer be able to log in.')">
                                    Deactivate
                                </button>
                            </form>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <p style="margin-top:.75rem;font-size:.8rem;color:#9ca3af;">
            Showing <?= count($users) ?> user(s).
            Note: deletion is a soft deactivation to preserve data integrity.
        </p>
    </main>
</div>

</body>
</html>
