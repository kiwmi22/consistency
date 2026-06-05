<?php
/**
 * Unified Database Connection
 * Used by ALL team members for consistent database access
 * 
 * Team:
 *   - Sri Krishna Shrestha (User Management)
 *   - Sashi Khatri (Habit Management)
 *   - Abin Rai (Habit Logging)
 *   - Juna Bhujel (Streaks/Progress)
 *   - Pratik Tamang (Reminders & Notifications)
 */

class Database {
    private $host = "localhost";
    private $db_name = "consistency_db";
    private $username = "root";
    private $password = "";
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

// Also provide mysqli connection for compatibility with team members who use mysqli
function getMysqliConnection() {
    $host = "localhost";
    $dbname = "consistency_db";
    $username = "root";
    $password = "";
    
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}
?>
