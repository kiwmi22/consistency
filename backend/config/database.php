<?php
/**
 * Database Configuration Class
 * Handles database connection using PDO
 * Author: Pratik Tamang
 */

class Database {
    // Database credentials
    private $host = "localhost";           // Database server
    private $db_name = "consistency_db";   // Database name
    private $username = "root";            // MySQL username
    private $password = "";                // MySQL password (empty for XAMPP default)
    public $conn;                          // Connection object
    
    /**
     * Get database connection
     * Uses PDO for secure database access
     * @return PDO connection object
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Create PDO connection with UTF-8 encoding
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            
            // Set character encoding to UTF-8
            $this->conn->exec("set names utf8");
            
            // Set error mode to exception for better error handling
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $exception) {
            // Display connection error
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>