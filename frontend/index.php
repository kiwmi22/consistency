<?php
// ================================================
// Consistency Habit Tracker - Main Entry Point
// ================================================

session_start();

// If user is already logged in, send them to Dashboard
if (isset($_SESSION['userID'])) {
    header("Location: pages/Dashboard.php");
    exit;
}

// Otherwise, send them to Login page
header("Location: pages/login.php");
exit;
?>