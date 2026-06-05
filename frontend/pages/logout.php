<?php
// ============================================================
// Consistency Project – Logout
// File: frontend/pages/logout.php
// ============================================================

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
