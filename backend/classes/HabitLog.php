<?php
/**
 * HabitLog Class — Middle Layer
 * Developer: Abin Rai
 * Component: Habit Logging / Check-ins
 * Calls stored procedures via PDO prepared statements
 */
class HabitLog {

    // PDO database connection
    private $db;

    /**
     * Constructor — receives PDO connection
     */
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * addLog()
     * Adds a new daily log entry
     * Calls sp_AddHabitLog
     * Business rule: one log per habit per day
     */
    public function addLog(
        $habitID, $userID, $logDate,
        $isCompleted, $notes, $durationMins
    ) {
        // Validate required fields
        if (empty($habitID) || empty($userID) || empty($logDate)) {
            return false;
        }
        // Reject negative duration
        if ($durationMins < 0) {
            return false;
        }
        $stmt = $this->db->prepare(
            "CALL sp_AddHabitLog(?,?,?,?,?,?)"
        );
        $stmt->execute([
            $habitID, $userID, $logDate,
            $isCompleted, $notes, $durationMins
        ]);
        return true;
    }

    /**
     * getLogsByUser()
     * Gets all logs for a user between two dates
     * Calls sp_GetLogsByUser
     */
    public function getLogsByUser(
        $userID, $startDate, $endDate
    ) {
        if (empty($userID)) {
            return [];
        }
        $stmt = $this->db->prepare(
            "CALL sp_GetLogsByUser(?,?,?)"
        );
        $stmt->execute([$userID, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * getLogByID()
     * Gets a single log entry by LogID
     * Calls sp_GetLogByID
     * Used by edit and find pages
     */
    public function getLogByID($logID) {
        if (empty($logID)) {
            return null;
        }
        $stmt = $this->db->prepare(
            "CALL sp_GetLogByID(?)"
        );
        $stmt->execute([$logID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * updateLog()
     * Updates an existing log entry
     * Calls sp_UpdateHabitLog
     */
    public function updateLog(
        $logID, $isCompleted, $notes, $durationMins
    ) {
        if (empty($logID)) {
            return false;
        }
        if ($durationMins < 0) {
            return false;
        }
        $stmt = $this->db->prepare(
            "CALL sp_UpdateHabitLog(?,?,?,?)"
        );
        $stmt->execute([
            $logID, $isCompleted, $notes, $durationMins
        ]);
        return true;
    }

    /**
     * deleteLog()
     * Deletes a log entry by LogID
     * Calls sp_DeleteHabitLog
     */
    public function deleteLog($logID) {
        if (empty($logID)) {
            return false;
        }
        $stmt = $this->db->prepare(
            "CALL sp_DeleteHabitLog(?)"
        );
        $stmt->execute([$logID]);
        return true;
    }

    /**
     * getAllLogs()
     * Gets all logs across all users for admin
     * Calls sp_GetAllLogs
     */
    public function getAllLogs() {
        $stmt = $this->db->prepare(
            "CALL sp_GetAllLogs()"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * filterLogs()
     * Filters logs by userID and date range for admin
     * Calls sp_FilterLogs
     */
    public function filterLogs(
        $userID = null,
        $startDate = null,
        $endDate = null
    ) {
        $stmt = $this->db->prepare(
            "CALL sp_FilterLogs(?,?,?)"
        );
        $stmt->execute([$userID, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * filterByHabit()
     * Filters logs by HabitID
     * Calls sp_FilterLogsByHabit — Sprint 3
     */
    public function filterByHabit($habitID) {
        if (empty($habitID)) {
            return [];
        }
        $habitID = htmlspecialchars(strip_tags($habitID));
        $stmt = $this->db->prepare(
            "CALL sp_FilterLogsByHabit(?)"
        );
        $stmt->execute([$habitID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * filterByStatus()
     * Filters logs by completion status
     * Calls sp_FilterLogsByStatus — Sprint 3
     */
    public function filterByStatus($isCompleted) {
        $stmt = $this->db->prepare(
            "CALL sp_FilterLogsByStatus(?)"
        );
        $stmt->execute([$isCompleted]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * filterByUser()
     * Filters logs by UserID for admin
     * Calls sp_FilterLogsByUser — Sprint 3
     */
    public function filterByUser($userID) {
        if (empty($userID)) {
            return [];
        }
        $userID = htmlspecialchars(strip_tags($userID));
        $stmt = $this->db->prepare(
            "CALL sp_FilterLogsByUser(?)"
        );
        $stmt->execute([$userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>