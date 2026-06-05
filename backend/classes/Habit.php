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

    // Constructor - accepts database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // GETTERS
    public function getHabitID()       { return $this->habitID; }
    public function getUserID()        { return $this->userID; }
    public function getHabitName()     { return $this->habitName; }
    public function getDescription()   { return $this->description; }
    public function getTargetPerWeek() { return $this->targetPerWeek; }
    public function getIsActive()      { return $this->isActive; }
    public function getCreatedDate()   { return $this->createdDate; }

    // SETTERS
    public function setUserID($v)        { $this->userID = $v; }
    public function setHabitName($v)     { $this->habitName = $v; }
    public function setDescription($v)   { $this->description = $v; }
    public function setTargetPerWeek($v) { $this->targetPerWeek = $v; }
    public function setIsActive($v)      { $this->isActive = $v; }
    public function setCreatedDate($v)   { $this->createdDate = $v; }

    // ---------- ADD HABIT ----------
    // Inserts a new habit record into tblHabits
    // Parameters: none (uses setters)
    // Returns: true on success, false on failure
    public function addHabit() {
        $sql = "INSERT INTO tblHabits
                (UserID, HabitName, Description, TargetPerWeek, IsActive, CreatedDate)
                VALUES (?, ?, ?, ?, TRUE, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issis",
            $this->userID,
            $this->habitName,
            $this->description,
            $this->targetPerWeek,
            $this->createdDate
        );
        return $stmt->execute();
    }

    // ---------- LIST HABITS ----------
    // Returns all active habits for a specific user
    // Parameters: $userID - the logged in user
    // Returns: result set
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
    // Updates an existing habit record by HabitID
    // Parameters: $habitID - the habit to update
    // Returns: true on success, false on failure
    public function editHabit($habitID) {
        $sql = "UPDATE tblHabits
                SET HabitName = ?, Description = ?, TargetPerWeek = ?
                WHERE HabitID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii",
            $this->habitName,
            $this->description,
            $this->targetPerWeek,
            $habitID
        );
        return $stmt->execute();
    }

    // ---------- DELETE HABIT (soft delete) ----------
    // Sets IsActive to FALSE - does not permanently delete
    // Parameters: $habitID - the habit to soft delete
    // Returns: true on success, false on failure
    public function deleteHabit($habitID) {
        $sql = "UPDATE tblHabits SET IsActive = FALSE WHERE HabitID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $habitID);
        return $stmt->execute();
    }

    // ---------- GET HABIT BY ID ----------
    // Returns a single habit record by HabitID
    // Parameters: $habitID - the habit to retrieve
    // Returns: associative array or null
    public function getHabitByID($habitID) {
        $sql = "SELECT * FROM tblHabits WHERE HabitID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $habitID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // ---------- FIND HABIT BY ID ----------
    // Returns a single habit record by HabitID for the find page
    // Parameters: $habitID - the habit to find
    // Returns: associative array or null
    public function findHabitByID($habitID) {
        $sql = "SELECT * FROM tblHabits WHERE HabitID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $habitID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // ---------- FILTER BY ACTIVE STATUS ----------
    // Returns habits filtered by IsActive status
    // Parameters: $userID, $isActive (1 or 0)
    // Returns: result set
    public function filterByStatus($userID, $isActive) {
        $sql = "SELECT * FROM tblHabits
                WHERE UserID = ? AND IsActive = ?
                ORDER BY CreatedDate";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userID, $isActive);
        $stmt->execute();
        return $stmt->get_result();
    }

    // ---------- FILTER BY TARGET PER WEEK ----------
    // Returns habits filtered by TargetPerWeek
    // Parameters: $userID, $targetPerWeek (1-7)
    // Returns: result set
    public function filterByTarget($userID, $targetPerWeek) {
        $sql = "SELECT * FROM tblHabits
                WHERE UserID = ? AND TargetPerWeek = ? AND IsActive = TRUE
                ORDER BY CreatedDate";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $userID, $targetPerWeek);
        $stmt->execute();
        return $stmt->get_result();
    }

    // ---------- SEARCH HABITS BY NAME ----------
    // Searches habits by name using LIKE query
    // Parameters: $userID, $searchTerm - partial or full name
    // Returns: result set
    public function searchHabits($userID, $searchTerm) {
        $searchTerm = "%" . $searchTerm . "%";
        $sql = "SELECT * FROM tblHabits
                WHERE UserID = ? AND HabitName LIKE ? AND IsActive = TRUE
                ORDER BY CreatedDate";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $userID, $searchTerm);
        $stmt->execute();
        return $stmt->get_result();
    }

}
?>