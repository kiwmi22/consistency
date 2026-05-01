<?php
/**
 * HabitLog Class
 * Middle layer for Habit Logging / Check-ins component
 * Developer: Abin Rai
 * Calls stored procedures in the data layer via PDO
 */
class HabitLog {

    // Database connection
    private $db;

    /**
     * Constructor — receives PDO connection
     */
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * ADD a new log entry
     * Calls sp_AddHabitLog
     * Business rule: duplicate entries are blocked
     * at the stored procedure level
     */
    public function addLog(
        $habitID, $userID, $logDate,
        $isCompleted, $notes, $durationMins
    ) {
        // Validate required fields
        if (empty($habitID) || empty($userID) || empty($logDate)) {
            return false;
        }

        // Validate duration is not negative
        if ($durationMins < 0) {
            return false;
        }

        // Call stored procedure
        $stmt = $this->db->prepare(
            "CALL sp_AddHabitLog(?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $habitID, $userID, $logDate,
            $isCompleted, $notes, $durationMins
        ]);
        return true;
    }

    /**
     * GET logs for a user between two dates
     * Calls sp_GetLogsByUser
     */
    public function getLogsByUser($userID, $startDate, $endDate) {
        // Validate user ID
        if (empty($userID)) {
            return [];
        }

        // Call stored procedure
        $stmt = $this->db->prepare(
            "CALL sp_GetLogsByUser(?, ?, ?)"
        );
        $stmt->execute([$userID, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * UPDATE an existing log entry
     * Calls sp_UpdateHabitLog
     */
    public function updateLog(
        $logID, $isCompleted, $notes, $durationMins
    ) {
        // Validate log ID
        if (empty($logID)) {
            return false;
        }

        // Validate duration is not negative
        if ($durationMins < 0) {
            return false;
        }

        // Call stored procedure
        $stmt = $this->db->prepare(
            "CALL sp_UpdateHabitLog(?, ?, ?, ?)"
        );
        $stmt->execute([$logID, $isCompleted, $notes, $durationMins]);
        return true;
    }

    /**
     * DELETE a log entry
     * Calls sp_DeleteHabitLog
     */
    public function deleteLog($logID) {
        // Validate log ID
        if (empty($logID)) {
            return false;
        }

        // Call stored procedure
        $stmt = $this->db->prepare(
            "CALL sp_DeleteHabitLog(?)"
        );
        $stmt->execute([$logID]);
        return true;
    }
}
?>

/**
 * getAllLogs()
 * Retrieves all logs across all users for admin view
 * Calls sp_GetAllLogs stored procedure
 */
public function getAllLogs() {
    // Call stored procedure — returns all logs with username and habit name
    $stmt = $this->db->prepare("CALL sp_GetAllLogs()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * filterLogs()
 * Filters logs by userID and/or date range for admin view
 * Calls sp_FilterLogs stored procedure
 */
public function filterLogs($userID = null, $startDate = null, $endDate = null) {
    // Call stored procedure with optional filter parameters
    $stmt = $this->db->prepare("CALL sp_FilterLogs(?, ?, ?)");
    $stmt->execute([$userID, $startDate, $endDate]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * getLogByID()
 * Retrieves a single log entry by LogID
 * Calls sp_GetLogByID stored procedure
 * Used by the edit page to pre-fill the form
 */
public function getLogByID($logID) {
    // Validate log ID is present
    if (empty($logID)) {
        return null;
    }
    // Call stored procedure
    $stmt = $this->db->prepare("CALL sp_GetLogByID(?)");
    $stmt->execute([$logID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}