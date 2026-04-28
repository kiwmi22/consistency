<?php
// ============================================================
// Consistency Project – Logout
// Developer: Sri Krishna Shrestha
// File: frontend/pages/logout.php
// ============================================================

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
