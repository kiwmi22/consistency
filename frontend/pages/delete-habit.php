<?php
session_start();
require_once '../../backend/config/db-connect.php';
require_once '../../backend/classes/Habit.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$habitID = (int) $_GET['id'];
$habit = new Habit($conn);
$habit->deleteHabit($habitID);

header("Location: habits.php");
exit();
?>