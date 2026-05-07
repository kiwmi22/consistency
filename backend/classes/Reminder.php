<?php
// Reminder class - Middle layer for Reminders & Notifications component
// Handles all business logic for reminders

class Reminder {
    private $conn;
    private $table = "tblReminders";

    // Reminder properties
    public $ReminderID;
    public $UserID;
    public $HabitID;
    public $ReminderTime;
    public $IsEnabled;
    public $Message;
    public $CreatedDate;

    // Constructor - receives database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Add a new reminder using stored procedure
    public function add() {
        $query = "CALL sp_AddReminder(:UserID, :HabitID, :ReminderTime, :IsEnabled, :Message, :CreatedDate)";
        $stmt = $this->conn->prepare($query);

        // Sanitise inputs
        $this->UserID = htmlspecialchars(strip_tags($this->UserID));
        $this->HabitID = htmlspecialchars(strip_tags($this->HabitID));
        $this->ReminderTime = htmlspecialchars(strip_tags($this->ReminderTime));
        $this->IsEnabled = htmlspecialchars(strip_tags($this->IsEnabled));
        $this->Message = htmlspecialchars(strip_tags($this->Message));
        $this->CreatedDate = htmlspecialchars(strip_tags($this->CreatedDate));

        // Bind parameters
        $stmt->bindParam(":UserID", $this->UserID);
        $stmt->bindParam(":HabitID", $this->HabitID);
        $stmt->bindParam(":ReminderTime", $this->ReminderTime);
        $stmt->bindParam(":IsEnabled", $this->IsEnabled);
        $stmt->bindParam(":Message", $this->Message);
        $stmt->bindParam(":CreatedDate", $this->CreatedDate);

        // Execute and return result
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

// Update an existing reminder using stored procedure
public function update() {
    $query = "CALL sp_UpdateReminder(:ReminderID, :UserID, :HabitID, :ReminderTime, :IsEnabled, :Message)";
    $stmt = $this->conn->prepare($query);

    // Sanitise inputs
    $this->ReminderID = htmlspecialchars(strip_tags($this->ReminderID));
    $this->UserID = htmlspecialchars(strip_tags($this->UserID));
    $this->HabitID = htmlspecialchars(strip_tags($this->HabitID));
    $this->ReminderTime = htmlspecialchars(strip_tags($this->ReminderTime));
    $this->IsEnabled = htmlspecialchars(strip_tags($this->IsEnabled));
    $this->Message = htmlspecialchars(strip_tags($this->Message));

    // Bind parameters
    $stmt->bindParam(":ReminderID", $this->ReminderID);
    $stmt->bindParam(":UserID", $this->UserID);
    $stmt->bindParam(":HabitID", $this->HabitID);
    $stmt->bindParam(":ReminderTime", $this->ReminderTime);
    $stmt->bindParam(":IsEnabled", $this->IsEnabled);
    $stmt->bindParam(":Message", $this->Message);

    // Execute and return result
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

// Delete a reminder using stored procedure
public function delete() {
    $query = "CALL sp_DeleteReminder(:ReminderID)";
    $stmt = $this->conn->prepare($query);

    $this->ReminderID = htmlspecialchars(strip_tags($this->ReminderID));
    $stmt->bindParam(":ReminderID", $this->ReminderID);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}

    // List all reminders using stored procedure
    public function listAll() {
        $query = "CALL sp_GetAllReminders()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Find a reminder by ID using stored procedure
    public function findById() {
        $query = "CALL sp_GetReminderById(:ReminderID)";
        $stmt = $this->conn->prepare($query);

        $this->ReminderID = htmlspecialchars(strip_tags($this->ReminderID));
        $stmt->bindParam(":ReminderID", $this->ReminderID);

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

    // Filter reminders by habit using stored procedure
public function filterByHabit() {
    $query = "CALL sp_FilterRemindersByHabit(:HabitID)";
    $stmt = $this->conn->prepare($query);

    // Sanitise input
    $this->HabitID = htmlspecialchars(strip_tags($this->HabitID));

    // Bind parameter
    $stmt->bindParam(":HabitID", $this->HabitID);

    // Execute and return result
    $stmt->execute();
    return $stmt;
}

// Filter reminders by enabled status using stored procedure
public function filterByStatus() {
    $query = "CALL sp_FilterRemindersByStatus(:IsEnabled)";
    $stmt = $this->conn->prepare($query);

    // Sanitise input
    $this->IsEnabled = htmlspecialchars(strip_tags($this->IsEnabled));

    // Bind parameter
    $stmt->bindParam(":IsEnabled", $this->IsEnabled);

    // Execute and return result
    $stmt->execute();
    return $stmt;
}

// Filter reminders by user using stored procedure (admin function)
public function filterByUser() {
    $query = "CALL sp_FilterRemindersByUser(:UserID)";
    $stmt = $this->conn->prepare($query);

    // Sanitise input
    $this->UserID = htmlspecialchars(strip_tags($this->UserID));

    // Bind parameter
    $stmt->bindParam(":UserID", $this->UserID);

    // Execute and return result
    $stmt->execute();
    return $stmt;
}
}
?>