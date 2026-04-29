<?php
// Habit.php
// Developer: Sashi Khatri
// Component: Habit Management 

class Habit {

    // Properties matching tblHabits columns
    private $habitID;
    private $userID;
    private $habitName;
    private $description;
    private $targetPerWeek;
    private $isActive;
    private $createdDate;
    private $conn;

    // Constructor - takes database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // ---------- GETTERS ----------
    public function getHabitID()       { return $this->habitID; }
    public function getUserID()        { return $this->userID; }
    public function getHabitName()     { return $this->habitName; }
    public function getDescription()   { return $this->description; }
    public function getTargetPerWeek() { return $this->targetPerWeek; }
    public function getIsActive()      { return $this->isActive; }
    public function getCreatedDate()   { return $this->createdDate; }

    // ---------- SETTERS ----------
    public function setUserID($v)        { $this->userID = $v; }
    public function setHabitName($v)     { $this->habitName = $v; }
    public function setDescription($v)   { $this->description = $v; }
    public function setTargetPerWeek($v) { $this->targetPerWeek = $v; }
    public function setIsActive($v)      { $this->isActive = $v; }
    public function setCreatedDate($v)   { $this->createdDate = $v; }

    // ---------- ADD HABIT ----------
    public function addHabit() {
        $sql = "INSERT INTO tblHabits 
                (UserID, HabitName, Description, TargetPerWeek, IsActive, CreatedDate)
                VALUES (?, ?, ?, ?, TRUE, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "issis",
            $this->userID,
            $this->habitName,
            $this->description,
            $this->targetPerWeek,
            $this->createdDate
        );
        return $stmt->execute();
    }

    // ---------- LIST HABITS (for logged-in user) ----------
    public function listHabits($userID) {
        $sql = "SELECT * FROM tblHabits
                WHERE UserID = ? AND IsActive = TRUE
                ORDER BY CreatedDate";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // ---------- EDIT HABIT ----------
    public function editHabit() {
        $sql = "UPDATE tblHabits
                SET HabitName = ?, Description = ?, TargetPerWeek = ?
                WHERE HabitID = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssii",
            $this->habitName,
            $this->description,
            $this->targetPerWeek,
            $this->habitID
        );
        return $stmt->execute();
    }

    // ---------- DELETE HABIT (soft delete) ----------
    public function deleteHabit($habitID) {
        $sql = "UPDATE tblHabits SET IsActive = FALSE WHERE HabitID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $habitID);
        return $stmt->execute();
    }

    // ---------- FIND HABIT BY NAME ----------
    public function findHabit($userID, $search) {
        $search = "%" . $search . "%";
        $sql = "SELECT * FROM tblHabits
                WHERE UserID = ? AND HabitName LIKE ? AND IsActive = TRUE";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $userID, $search);
        $stmt->execute();
        return $stmt->get_result();
    }

    // ---------- GET SINGLE HABIT BY ID ----------
    public function getHabitByID($habitID) {
        $sql = "SELECT * FROM tblHabits WHERE HabitID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $habitID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

}
?>