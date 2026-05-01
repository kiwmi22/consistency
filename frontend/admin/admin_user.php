<?php
// ============================================================
// Consistency Project - Admin User Management
// Developer: Sri Krishna Shrestha
// File: frontend/admin/admin_users.php
// Sprint 2 - Presentation layer
// Functions: Add, Edit, List, Find, Filter, Delete (soft)
// ============================================================

session_start();

// Protect page - must be logged in AND admin
if (!isset($_SESSION['userID']) || !$_SESSION['isAdmin']) {
    header('Location: ../pages/login.php');
    exit;
}

require_once __DIR__ . '/../../backend/config/db_connect.php';
require_once __DIR__ . '/../../backend/classes/User.php';

$userObj = new User($pdo);
$message = '';
$msgType = '';

// ── Handle all POST actions ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = $_POST['action']         ?? '';
    $targetID = (int)($_POST['target_id'] ?? 0);

    // Prevent admin acting on their own account
    if ($targetID === (int)$_SESSION['userID'] && $action !== 'add') {
        $message = 'You cannot perform this action on your own account.';
        $msgType = 'error';

    } elseif ($action === 'add') {
        // ADD: Admin creates a new user
        $result  = $userObj->adminAddUser(
            $_POST['new_username'] ?? '',
            $_POST['new_email']    ?? '',
            $_POST['new_password'] ?? '',
            isset($_POST['new_isadmin'])
        );
        $message = $result['success'] ? 'User created successfully.' : $result['error'];
        $msgType = $result['success'] ? 'success' : 'error';

    } elseif ($action === 'edit') {
        // EDIT: Update username and email
        $result  = $userObj->updateProfile(
            $targetID,
            $_POST['edit_username'] ?? '',
            $_POST['edit_email']    ?? ''
        );
        $message = $result['success'] ? 'User updated successfully.' : $result['error'];
        $msgType = $result['success'] ? 'success' : 'error';

    } elseif ($action === 'deactivate') {
        // DELETE (soft): Set IsActive = FALSE
        $result  = $userObj->deactivateUser($targetID);
        $message = $result['success'] ? 'User deactivated successfully.' : $result['error'];
        $msgType = $result['success'] ? 'success' : 'error';

    } elseif ($action === 'toggle_admin') {
        // Toggle IsAdmin flag
        $result  = $userObj->toggleAdmin($targetID);
        $message = $result['success'] ? 'Admin privilege updated.' : $result['error'];
        $msgType = $result['success'] ? 'success' : 'error';
    }
}

// ── Handle GET: search and filter ────────────────────────────
$filter     = isset($_GET['filter'])  ? (int)$_GET['filter']   : -1;
$searchTerm = isset($_GET['search'])  ? trim($_GET['search'])   : '';
$showAdd    = isset($_GET['add'])     ? true                    : false;
$editUser   = null;
$editID     = isset($_GET['edit_id']) ? (int)$_GET['edit_id']  : 0;

// FIND or LIST users
if (!empty($searchTerm)) {
    $users = $userObj->findUsers($searchTerm);  // FIND
} else {
    $users = $userObj->getAllUsers($filter);     // LIST + FILTER
}

// Load user data for edit form
if ($editID > 0) {
    $editUser = $userObj->getUserByID($editID);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – User Management | Consistency</title>
    <style>
        * { box-sizing: border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#f0f4f8; }

        /* Sidebar layout */
        .layout { display:flex; min-height:100vh; }
        .sidebar {
            width:220px; background:#1a1a2e; color:#ccc;
            padding:1.5rem 1.25rem; flex-shrink:0;
        }
        .sidebar h2 { color:#fff; font-size:1rem; margin-bottom:1.5rem; }
        .sidebar a {
            display:block; color:#ccc; text-decoration:none;
            padding:.5rem .75rem; border-radius:6px;
            margin-bottom:.25rem; font-size:.875rem;
        }
        .sidebar a:hover, .sidebar a.active { background:#4f46e5; color:#fff; }
        .sidebar a.back { margin-top:2rem; background:#374151; }

        /* Main */
        .main { flex:1; padding:2rem; }
        .page-title { font-size:1.4rem; color:#1a1a2e; margin-bottom:1.25rem; }

        /* Alerts */
        .alert { padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.875rem; }
        .alert-success { background:#f0fdf4; color:#166534; border:1px solid #86efac; }
        .alert-error   { background:#fef2f2; color:#b91c1c; border:1px solid #fca5a5; }

        /* Toolbar */
        .toolbar { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; margin-bottom:1.25rem; }
        .toolbar input[type="text"] {
            padding:.5rem .9rem; border:1.5px solid #ddd;
            border-radius:8px; font-size:.875rem; width:220px;
        }
        .toolbar input:focus { outline:none; border-color:#4f46e5; }

        /* Buttons */
        .btn { padding:.45rem .9rem; border:none; border-radius:7px; font-size:.825rem; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; }
        .btn-primary  { background:#4f46e5; color:#fff; }
        .btn-primary:hover  { background:#4338ca; }
        .btn-success  { background:#16a34a; color:#fff; }
        .btn-success:hover  { background:#15803d; }
        .btn-secondary{ background:#e5e7eb; color:#374151; }
        .btn-secondary:hover{ background:#d1d5db; }
        .btn-danger   { background:#ef4444; color:#fff; font-size:.775rem; padding:.3rem .65rem; }
        .btn-danger:hover   { background:#dc2626; }
        .btn-warning  { background:#f59e0b; color:#fff; font-size:.775rem; padding:.3rem .65rem; }
        .btn-info     { background:#0ea5e9; color:#fff; font-size:.775rem; padding:.3rem .65rem; }

        /* Filter buttons */
        .filter-group { display:flex; gap:.4rem; }

        /* Table */
        .table-wrapper { background:#fff; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,.07); overflow:hidden; margin-bottom:1rem; }
        table { width:100%; border-collapse:collapse; }
        thead { background:#1a1a2e; }
        th { padding:.8rem 1rem; text-align:left; font-size:.8rem; font-weight:600; color:#fff; }
        td { padding:.75rem 1rem; font-size:.825rem; color:#374151; border-bottom:1px solid #f3f4f6; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#f9fafb; }
        .action-btns { display:flex; gap:.3rem; flex-wrap:wrap; }

        /* Badges */
        .badge { display:inline-block; padding:.15rem .55rem; border-radius:20px; font-size:.72rem; font-weight:700; }
        .badge-active   { background:#dcfce7; color:#166534; }
        .badge-inactive { background:#fee2e2; color:#991b1b; }
        .badge-admin    { background:#ede9fe; color:#5b21b6; }
        .badge-user     { background:#e0f2fe; color:#075985; }

        /* Modal overlay for Add/Edit forms */
        .modal-overlay {
            display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,.5); z-index:100; align-items:center; justify-content:center;
        }
        .modal-overlay.open { display:flex; }
        .modal {
            background:#fff; border-radius:14px; padding:2rem;
            width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,.2);
        }
        .modal h2 { font-size:1.1rem; margin-bottom:1.25rem; color:#1a1a2e; }
        .form-group { margin-bottom:.9rem; }
        label { display:block; font-size:.825rem; font-weight:600; color:#374151; margin-bottom:.3rem; }
        input[type="text"], input[type="email"], input[type="password"] {
            width:100%; padding:.6rem .85rem; border:1.5px solid #ddd;
            border-radius:7px; font-size:.875rem;
        }
        input:focus { outline:none; border-color:#4f46e5; }
        .checkbox-label { display:flex; align-items:center; gap:.5rem; font-size:.875rem; cursor:pointer; }
        .modal-footer { display:flex; gap:.6rem; justify-content:flex-end; margin-top:1.25rem; }

        .no-results { text-align:center; padding:3rem; color:#9ca3af; font-size:.875rem; }
        .count-note { font-size:.775rem; color:#9ca3af; margin-top:.4rem; }
    </style>
</head>
<body>

<div class="layout">

    <!-- Sidebar navigation -->
    <nav class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_users.php" class="active">👥 Users</a>
        <a href="#">📋 Habits</a>
        <a href="#">📅 Logs</a>
        <a href="#">📈 Streaks</a>
        <a href="#">🔔 Reminders</a>
        <a href="../pages/dashboard.php" class="back">← Back to Dashboard</a>
        <a href="../pages/logout.php" style="margin-top:.5rem;background:#7f1d1d;">🚪 Logout</a>
    </nav>

    <!-- Main content -->
    <main class="main">
        <h1 class="page-title">👥 User Management</h1>

        <!-- Alert messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Search + Filter + Add toolbar -->
        <form method="GET" action="admin_users.php">
            <div class="toolbar">
                <!-- FIND: search box -->
                <input type="text" name="search"
                       placeholder="Search username or email..."
                       value="<?= htmlspecialchars($searchTerm) ?>">
                <button type="submit" class="btn btn-primary">🔍 Search</button>
                <a href="admin_users.php" class="btn btn-secondary">✕ Clear</a>

                <!-- FILTER: active/inactive buttons -->
                <div class="filter-group">
                    <a href="?filter=-1" class="btn <?= $filter===-1?'btn-primary':'btn-secondary' ?>">All</a>
                    <a href="?filter=1"  class="btn <?= $filter===1 ?'btn-primary':'btn-secondary' ?>">Active</a>
                    <a href="?filter=0"  class="btn <?= $filter===0 ?'btn-primary':'btn-secondary' ?>">Inactive</a>
                </div>

                <!-- ADD: open add user modal -->
                <a href="?add=1" class="btn btn-success">+ Add User</a>
            </div>
        </form>

        <!-- LIST: Users table -->
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
                            <span class="badge <?= $u['IsAdmin'] ? 'badge-admin' : 'badge-user' ?>">
                                <?= $u['IsAdmin'] ? 'Admin' : 'User' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $u['IsActive'] ? 'badge-active' : 'badge-inactive' ?>">
                                <?= $u['IsActive'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($u['DateCreated']) ?></td>
                        <td>
                            <div class="action-btns">

                                <!-- EDIT button -->
                                <a href="?edit_id=<?= (int)$u['UserID'] ?>" class="btn btn-info">Edit</a>

                                <!-- TOGGLE ADMIN button -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action"    value="toggle_admin">
                                    <input type="hidden" name="target_id" value="<?= (int)$u['UserID'] ?>">
                                    <button type="submit" class="btn btn-warning"
                                        onclick="return confirm('Toggle admin for this user?')">
                                        <?= $u['IsAdmin'] ? 'Remove Admin' : 'Make Admin' ?>
                                    </button>
                                </form>

                                <!-- DELETE (soft) button - only if active -->
                                <?php if ($u['IsActive']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action"    value="deactivate">
                                    <input type="hidden" name="target_id" value="<?= (int)$u['UserID'] ?>">
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Deactivate this user? They will not be able to log in.')">
                                        Deactivate
                                    </button>
                                </form>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <p class="count-note">Showing <?= count($users) ?> user(s). Deletion is soft — IsActive is set to FALSE to preserve data integrity.</p>

    </main>
</div>

<!-- ADD USER MODAL ──────────────────────────────────────────── -->
<div class="modal-overlay <?= $showAdd ? 'open' : '' ?>" id="addModal">
    <div class="modal">
        <h2>➕ Add New User</h2>
        <form method="POST" action="admin_users.php">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="new_username" placeholder="e.g. john_doe" required maxlength="50">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="new_email" placeholder="user@example.com" required maxlength="100">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="new_password" placeholder="Min 8 characters" required minlength="8">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="new_isadmin">
                    Grant admin privileges
                </label>
            </div>

            <div class="modal-footer">
                <a href="admin_users.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT USER MODAL ─────────────────────────────────────────── -->
<?php if ($editUser): ?>
<div class="modal-overlay open">
    <div class="modal">
        <h2>✏️ Edit User — <?= htmlspecialchars($editUser['Username']) ?></h2>
        <form method="POST" action="admin_users.php">
            <input type="hidden" name="action"    value="edit">
            <input type="hidden" name="target_id" value="<?= (int)$editUser['UserID'] ?>">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="edit_username"
                       value="<?= htmlspecialchars($editUser['Username']) ?>"
                       required maxlength="50">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="edit_email"
                       value="<?= htmlspecialchars($editUser['Email']) ?>"
                       required maxlength="100">
            </div>

            <div class="modal-footer">
                <a href="admin_users.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

</body>
</html>