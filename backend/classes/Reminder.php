<?php
/**
 * Reminder Class - Business Logic Layer
 * Handles all reminder operations using stored procedures
 * Author: Pratik Tamang - Reminders & Notifications Component
 */

class Reminder {
    private $conn;
    
    public $ReminderID;
    public $UserID;
    public $HabitID;
    public $ReminderTime;
    public $IsEnabled;
    public $Message;
    public $CreatedDate;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function add() {
        $query = "CALL sp_AddReminder(:UserID, :HabitID, :ReminderTime, :IsEnabled, :Message)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->bindParam(':HabitID', $this->HabitID);
        $stmt->bindParam(':ReminderTime', $this->ReminderTime);
        $stmt->bindParam(':IsEnabled', $this->IsEnabled);
        $stmt->bindParam(':Message', $this->Message);
        
        return $stmt->execute();
    }
    
    public function listAll() {
        $query = "CALL sp_GetAllReminders()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function findById() {
        $query = "CALL sp_GetReminderById(:ReminderID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ReminderID', $this->ReminderID);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
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
    
    public function update() {
        $query = "CALL sp_UpdateReminder(:ReminderID, :UserID, :HabitID, :ReminderTime, :IsEnabled, :Message)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':ReminderID', $this->ReminderID);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->bindParam(':HabitID', $this->HabitID);
        $stmt->bindParam(':ReminderTime', $this->ReminderTime);
        $stmt->bindParam(':IsEnabled', $this->IsEnabled);
        $stmt->bindParam(':Message', $this->Message);
        
        return $stmt->execute();
    }
    
    public function delete() {
        $query = "CALL sp_DeleteReminder(:ReminderID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ReminderID', $this->ReminderID);
        return $stmt->execute();
    }
    
    public function filterByHabit() {
        $query = "CALL sp_FilterRemindersByHabit(:HabitID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':HabitID', $this->HabitID);
        $stmt->execute();
        return $stmt;
    }
    
    public function filterByStatus() {
        $query = "CALL sp_FilterRemindersByStatus(:IsEnabled)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':IsEnabled', $this->IsEnabled);
        $stmt->execute();
        return $stmt;
    }
    
    public function filterByUser() {
        $query = "CALL sp_FilterRemindersByUser(:UserID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserID', $this->UserID);
        $stmt->execute();
        return $stmt;
    }
}
?>
