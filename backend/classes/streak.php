<?php
// ============================================
// File:        Streak.php
// Author:      Juna Bhujel
// Component:   Progress & Statistics
// Description: Middle layer class for managing
//              streak records in tblStreaks.
//              Handles all CRUD operations using
//              stored procedures and prepared
//              statements for security.
// Sprint 1:    Created 21st April 2026
// Sprint 2:    Updated 28th April 2026
// Sprint 3:    Updated 6th May 2026
// ============================================

class Streak {

    // Database connection variable
    private $conn;

    // ============================================
    // CONSTRUCTOR
    // Receives shared database connection
    // from backend/config/db.php
    // ============================================
    public function __construct($db) {
        $this->conn = $db;
    }

    // ============================================
    // SPRINT 1 METHODS
    // ============================================

    // -----------------------------------------------
    // METHOD 1: Get all streaks for a specific user
    // Used on: progress.php (customer front end)
    // Joins with tblHabits to get habit names
    // @param int $userID — logged in user ID
    // @return mysqli_result — all streaks for user
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
    // Used on: progress.php
    // Returns 0 if no record found
    // @param int $userID  — ID of the user
    // @param int $habitID — ID of the habit
    // @return int — longest streak value or 0
    // -----------------------------------------------
    public function getLongestStreak($userID, $habitID) {
        $sql = "SELECT LongestStreak
                FROM tblStreaks
                WHERE UserID = ? AND HabitID = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userID, $habitID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        return $row ? $row['LongestStreak'] : 0;
    }

    // -----------------------------------------------
    // METHOD 3: Update streak when habit completed
    // Business rule: increment CurrentStreak by 1
    // Update LongestStreak only if new high reached
    // @param int $userID  — ID of the user
    // @param int $habitID — ID of the habit
    // @return bool — true on success false on failure
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
    // METHOD 4: Reset streak to zero
    // Business rule: CurrentStreak resets to 0
    // when a day is missed
    // LongestStreak is never affected by reset
    // @param int $userID  — ID of the user
    // @param int $habitID — ID of the habit
    // @return bool — true on success false on failure
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
    // METHOD 5: Admin get all streaks all users
    // Used on: admin_streaks.php (admin back end)
    // Joins with tblUsers and tblHabits for names
    // @return mysqli_result — all streak records
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

    // ============================================
    // SPRINT 2 METHODS
    // ============================================

    // -----------------------------------------------
    // METHOD 6: Add new streak record
    // Used on: add_streak.php (admin back end)
    // Inserts new streak with CurrentStreak = 0
    // @param int $userID  — ID of the user
    // @param int $habitID — ID of the habit
    // @return bool — true on success false on failure
    // -----------------------------------------------
    public function addStreak($userID, $habitID) {
        $today = date('Y-m-d');
        $stmt  = $this->conn->prepare(
            "CALL sp_AddStreak(?, ?, ?)"
        );
        $stmt->bind_param("iis", $userID, $habitID, $today);
        return $stmt->execute();
    }

    // -----------------------------------------------
    // METHOD 7: Edit existing streak record
    // Used on: edit_streak.php (admin back end)
    // Updates CurrentStreak LongestStreak IsActive
    // @param int  $streakID      — ID of the streak
    // @param int  $currentStreak — new current value
    // @param int  $longestStreak — new longest value
    // @param bool $isActive      — active status
    // @return bool — true on success false on failure
    // -----------------------------------------------
    public function editStreak(
        $streakID, $currentStreak,
        $longestStreak, $isActive
    ) {
        $today = date('Y-m-d');
        $stmt  = $this->conn->prepare(
            "CALL sp_EditStreak(?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iiiis",
            $streakID,
            $currentStreak,
            $longestStreak,
            $isActive,
            $today
        );
        return $stmt->execute();
    }

    // -----------------------------------------------
    // METHOD 8: Delete streak record permanently
    // Used on: delete_streak.php (admin back end)
    // Removes record completely from tblStreaks
    // @param int $streakID — ID of streak to delete
    // @return bool — true on success false on failure
    // -----------------------------------------------
    public function deleteStreak($streakID) {
        $stmt = $this->conn->prepare(
            "CALL sp_DeleteStreak(?)"
        );
        $stmt->bind_param("i", $streakID);
        return $stmt->execute();
    }

    // -----------------------------------------------
    // METHOD 9: Find specific streak by ID
    // Used on: find_streak.php (admin back end)
    // Joins with tblUsers and tblHabits for names
    // @param int $streakID — ID of streak to find
    // @return mysqli_result — single streak record
    // -----------------------------------------------
    public function findStreakById($streakID) {
        $stmt = $this->conn->prepare(
            "CALL sp_FindStreakById(?)"
        );
        $stmt->bind_param("i", $streakID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 10: Filter streaks by user
    // Used on: filter_streak.php (admin back end)
    // @param int $userID — ID of user to filter by
    // @return mysqli_result — filtered records
    // -----------------------------------------------
    public function filterStreakByUser($userID) {
        $stmt = $this->conn->prepare(
            "CALL sp_FilterStreakByUser(?)"
        );
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 11: Filter streaks by habit
    // Used on: filter_streak.php (admin back end)
    // @param int $habitID — ID of habit to filter by
    // @return mysqli_result — filtered records
    // -----------------------------------------------
    public function filterStreakByHabit($habitID) {
        $stmt = $this->conn->prepare(
            "CALL sp_FilterStreakByHabit(?)"
        );
        $stmt->bind_param("i", $habitID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 12: Validate streak inputs
    // Used on: add_streak.php edit_streak.php
    // Returns array of errors empty if all valid
    // @param int $userID        — user ID to validate
    // @param int $habitID       — habit ID to validate
    // @param int $currentStreak — current streak value
    // @param int $longestStreak — longest streak value
    // @return array — list of error messages
    // -----------------------------------------------
    public function validateStreak(
        $userID, $habitID,
        $currentStreak, $longestStreak
    ) {
        $errors = [];

        // Check UserID is a valid number
        if (empty($userID) || !is_numeric($userID)) {
            $errors[] = "UserID must be a valid number";
        }

        // Check HabitID is a valid number
        if (empty($habitID) || !is_numeric($habitID)) {
            $errors[] = "HabitID must be a valid number";
        }

        // Check CurrentStreak is not negative
        if ($currentStreak < 0) {
            $errors[] = "Current streak cannot be negative";
        }

        // Check LongestStreak is not negative
        if ($longestStreak < 0) {
            $errors[] = "Longest streak cannot be negative";
        }

        // Check CurrentStreak does not exceed LongestStreak
        if ($currentStreak > $longestStreak) {
            $errors[] = "Current streak cannot exceed longest streak";
        }

        return $errors;
    }

    // ============================================
    // SPRINT 3 METHODS
    // ============================================

    // -----------------------------------------------
    // METHOD 13: Filter streaks by habit
    // Sprint 3 — Story 2
    // Used on: filter_streak.php
    // Shows all user streaks for one specific habit
    // @param int $habitID — ID of habit to filter by
    // @return mysqli_result — filtered records
    // -----------------------------------------------
    public function filterByHabit($habitID) {
        $stmt = $this->conn->prepare(
            "CALL sp_FilterStreaksByHabit(?)"
        );
        $stmt->bind_param("i", $habitID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 14: Filter streaks by active status
    // Sprint 3 — Story 3
    // Used on: filter_streak.php
    // TRUE = active streaks FALSE = broken streaks
    // @param bool $isActive — status to filter by
    // @return mysqli_result — filtered records
    // -----------------------------------------------
    public function filterByStatus($isActive) {
        $stmt = $this->conn->prepare(
            "CALL sp_FilterStreaksByStatus(?)"
        );
        $stmt->bind_param("i", $isActive);
        $stmt->execute();
        return $stmt->get_result();
    }

    // -----------------------------------------------
    // METHOD 15: Filter streaks by minimum length
    // Sprint 3 — Story 4
    // Used on: filter_streak.php
    // Returns streaks equal to or longer than minimum
    // @param int $minLength — minimum days to filter
    // @return mysqli_result — filtered records
    // -----------------------------------------------
    public function filterByLength($minLength) {
        $stmt = $this->conn->prepare(
            "CALL sp_FilterStreaksByLength(?)"
        );
        $stmt->bind_param("i", $minLength);
        $stmt->execute();
        return $stmt->get_result();
    }

} // end of Streak class
?>