<?php
// ============================================
// File:      Streak.php
// Author:    Juna Bhujel
// Component: Progress & Statistics
// Sprint 1 - Tuesday 21st April 2026
// ============================================

class Streak {

    private $conn;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // -----------------------------------------------
    // METHOD 1: Get all streaks for a specific user
    // -----------------------------------------------
    public function getStreakByUser($userID) {
        $sql = "SELECT s.StreakID,
                       s.HabitID,
                       h.HabitName,
                       s.CurrentStreak,
                       s.LongestStreak,
                       s.IsActive,
                       s.LastUpdated
                FROM tblStreaks s
                JOIN tblHabits h ON s.HabitID = h.HabitID
                WHERE s.UserID = ?
                ORDER BY s.CurrentStreak DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 2: Get longest streak for a habit/user
    // -----------------------------------------------
    public function getLongestStreak($userID, $habitID) {
        $sql = "SELECT LongestStreak
                FROM tblStreaks
                WHERE UserID = ? AND HabitID = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userID, $habitID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['LongestStreak'] : 0;
    }
    // -----------------------------------------------
    // METHOD 3: Update streak when habit completed
    // -----------------------------------------------
    public function updateStreak($userID, $habitID) {
        $today = date('Y-m-d');
        $stmt  = $this->conn->prepare(
            "CALL sp_UpdateStreak(?, ?, ?)"
        );
        $stmt->bind_param("iis", $userID, $habitID, $today);
        return $stmt->execute();
    }

    // -----------------------------------------------
    // METHOD 4: Reset streak when day is missed
    // -----------------------------------------------
    public function resetStreak($userID, $habitID) {
        $today = date('Y-m-d');
        $stmt  = $this->conn->prepare(
            "CALL sp_ResetStreak(?, ?, ?)"
        );
        $stmt->bind_param("iis", $userID, $habitID, $today);
        return $stmt->execute();
    }

    // -----------------------------------------------
    // METHOD 5: Admin — get all streaks all users
    // -----------------------------------------------
    public function getAllStreaksAdmin() {
        $sql = "SELECT s.StreakID,
                       u.Username,
                       h.HabitName,
                       s.CurrentStreak,
                       s.LongestStreak,
                       s.IsActive,
                       s.LastUpdated
                FROM tblStreaks s
                JOIN tblUsers  u ON s.UserID  = u.UserID
                JOIN tblHabits h ON s.HabitID = h.HabitID
                ORDER BY s.LongestStreak DESC";
        return $this->conn->query($sql);
    }

}
?>
