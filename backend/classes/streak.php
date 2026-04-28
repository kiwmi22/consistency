<?php
class Streak {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

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
}
?>