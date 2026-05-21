<?php
/**
 * Reminder Class - Business Logic Layer
 * Handles all reminder operations using stored procedures
 * Author: Pratik Tamang - Reminders & Notifications Component
 */

class Reminder {
    // Database connection
    private $conn;
    
    // Reminder properties matching tblReminders columns
    public $ReminderID;
    public $UserID;
    public $HabitID;
    public $ReminderTime;
    public $IsEnabled;
    public $Message;
    public $CreatedDate;
    
    // Constructor receives database connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Add new reminder to database
     * Calls sp_AddReminder stored procedure
     * @return boolean - true if successful, false otherwise
     */
    public function add() {
        $query = "CALL sp_AddReminder(:UserID, :HabitID, :ReminderTime, :IsEnabled, :Message)";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters to prevent SQL injection
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->bindParam(':HabitID', $this->HabitID);
        $stmt->bindParam(':ReminderTime', $this->ReminderTime);
        $stmt->bindParam(':IsEnabled', $this->IsEnabled);
        $stmt->bindParam(':Message', $this->Message);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Get all reminders from database
     * Calls sp_GetAllReminders stored procedure
     * @return PDOStatement - result set of all reminders
     */
    public function listAll() {
        $query = "CALL sp_GetAllReminders()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Find specific reminder by ID
     * Calls sp_GetReminderById stored procedure
     * Populates object properties if found
     * @return boolean - true if found, false otherwise
     */
    public function findById() {
        $query = "CALL sp_GetReminderById(:ReminderID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ReminderID', $this->ReminderID);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Populate object properties from database row
            $this->UserID = $row['UserID'];
            $this->HabitID = $row['HabitID'];
            $this->ReminderTime = $row['ReminderTime'];
            $this->IsEnabled = $row['IsEnabled'];
            $this->Message = $row['Message'];
            $this->CreatedDate = $row['CreatedDate'];
            return true;
        }
        return false;
    }
    
    /**
     * Update existing reminder
     * Calls sp_UpdateReminder stored procedure
     * @return boolean - true if successful, false otherwise
     */
    public function update() {
        $query = "CALL sp_UpdateReminder(:ReminderID, :UserID, :HabitID, :ReminderTime, :IsEnabled, :Message)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':ReminderID', $this->ReminderID);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->bindParam(':HabitID', $this->HabitID);
        $stmt->bindParam(':ReminderTime', $this->ReminderTime);
        $stmt->bindParam(':IsEnabled', $this->IsEnabled);
        $stmt->bindParam(':Message', $this->Message);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Delete reminder from database
     * Calls sp_DeleteReminder stored procedure
     * @return boolean - true if successful, false otherwise
     */
    public function delete() {
        $query = "CALL sp_DeleteReminder(:ReminderID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ReminderID', $this->ReminderID);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * Filter reminders by habit
     * Calls sp_FilterRemindersByHabit stored procedure
     * @return PDOStatement - filtered result set
     */
    public function filterByHabit() {
        $query = "CALL sp_FilterRemindersByHabit(:HabitID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':HabitID', $this->HabitID);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Filter reminders by enabled/disabled status
     * Calls sp_FilterRemindersByStatus stored procedure
     * @return PDOStatement - filtered result set
     */
    public function filterByStatus() {
        $query = "CALL sp_FilterRemindersByStatus(:IsEnabled)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':IsEnabled', $this->IsEnabled);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Filter reminders by user
     * Calls sp_FilterRemindersByUser stored procedure
     * Useful for admin viewing specific user's reminders
     * @return PDOStatement - filtered result set
     */
    public function filterByUser() {
        $query = "CALL sp_FilterRemindersByUser(:UserID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
        return $stmt;
    }
}
?>