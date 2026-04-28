<?php
// ============================================================
// Consistency Project – User Account Management
// Developer: Sri Krishna Shrestha
// File: backend/classes/User.php
// Middle layer – calls stored procedures in the data layer
// ============================================================

require_once __DIR__ . '/../config/db_connect.php';

class User {

    private PDO $db;

    // Inject the database connection
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ----------------------------------------------------------
    // REGISTER – Add a new user
    // Returns: ['success' => true, 'userID' => int]
    //          ['success' => false, 'error'  => string]
    // ----------------------------------------------------------
    public function register(string $username, string $email, string $password): array {
        // Validate inputs
        $username = trim($username);
        $email    = trim($email);

        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'All fields are required.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address.'];
        }
        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters.'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $this->db->prepare("CALL sp_RegisterUser(:username, :email, :hash)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email',    $email);
            $stmt->bindParam(':hash',     $passwordHash);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return ['success' => true, 'userID' => $row['NewUserID']];

        } catch (PDOException $e) {
            // Stored procedure raises SIGNAL for duplicate email
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ----------------------------------------------------------
    // LOGIN – Authenticate a user by email and password
    // Returns: ['success' => true,  'user' => array]
    //          ['success' => false, 'error' => string]
    // ----------------------------------------------------------
    public function login(string $email, string $password): array {
        $email = trim($email);

        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        try {
            $stmt = $this->db->prepare("CALL sp_LoginUser(:email)");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return ['success' => false, 'error' => 'No account found with that email address.'];
            }
            if (!$user['IsActive']) {
                return ['success' => false, 'error' => 'This account has been deactivated.'];
            }
            if (!password_verify($password, $user['PasswordHash'])) {
                return ['success' => false, 'error' => 'Incorrect password.'];
            }

            // Don't expose the hash to the session
            unset($user['PasswordHash']);
            return ['success' => true, 'user' => $user];

        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ----------------------------------------------------------
    // GET USER BY ID
    // Returns: array of user data or null
    // ----------------------------------------------------------
    public function getUserByID(int $userID): ?array {
        try {
            $stmt = $this->db->prepare("CALL sp_GetUserByID(:userID)");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // ----------------------------------------------------------
    // GET ALL USERS (admin)
    // $filter: 1 = active, 0 = inactive, -1 = all
    // ----------------------------------------------------------
    public function getAllUsers(int $filter = -1): array {
        try {
            $stmt = $this->db->prepare("CALL sp_GetAllUsers(:filter)");
            $stmt->bindParam(':filter', $filter, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ----------------------------------------------------------
    // FIND USERS – search by username or email (admin)
    // ----------------------------------------------------------
    public function findUsers(string $searchTerm): array {
        $searchTerm = trim($searchTerm);
        if (empty($searchTerm)) {
            return $this->getAllUsers();
        }
        try {
            $stmt = $this->db->prepare("CALL sp_FindUsers(:term)");
            $stmt->bindParam(':term', $searchTerm);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ----------------------------------------------------------
    // UPDATE PROFILE – change username and/or email
    // ----------------------------------------------------------
    public function updateProfile(int $userID, string $username, string $email): array {
        $username = trim($username);
        $email    = trim($email);

        if (empty($username) || empty($email)) {
            return ['success' => false, 'error' => 'Username and email are required.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address.'];
        }

        try {
            $stmt = $this->db->prepare("CALL sp_UpdateUser(:userID, :username, :email)");
            $stmt->bindParam(':userID',   $userID,   PDO::PARAM_INT);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email',    $email);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ----------------------------------------------------------
    // CHANGE PASSWORD
    // Verifies current password before updating
    // ----------------------------------------------------------
    public function changePassword(int $userID, string $currentPassword, string $newPassword): array {
        if (empty($currentPassword) || empty($newPassword)) {
            return ['success' => false, 'error' => 'Both current and new passwords are required.'];
        }
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'error' => 'New password must be at least 8 characters.'];
        }

        // Fetch current hash to verify
        $user = $this->getUserByID($userID);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found.'];
        }

        // Fetch hash separately (getUserByID excludes it for security)
        try {
            $stmt = $this->db->prepare("SELECT PasswordHash FROM tblUsers WHERE UserID = :userID");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($currentPassword, $row['PasswordHash'])) {
                return ['success' => false, 'error' => 'Current password is incorrect.'];
            }

            $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt2   = $this->db->prepare("CALL sp_ChangePassword(:userID, :hash)");
            $stmt2->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt2->bindParam(':hash',   $newHash);
            $stmt2->execute();
            return ['success' => true];

        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ----------------------------------------------------------
    // DEACTIVATE USER – soft delete (admin or self)
    // ----------------------------------------------------------
    public function deactivateUser(int $userID): array {
        try {
            $stmt = $this->db->prepare("CALL sp_DeactivateUser(:userID)");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ----------------------------------------------------------
    // TOGGLE ADMIN PRIVILEGE (admin only)
    // ----------------------------------------------------------
    public function toggleAdmin(int $userID): array {
        try {
            $stmt = $this->db->prepare("CALL sp_ToggleAdminPrivilege(:userID)");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ----------------------------------------------------------
    // IS ADMIN – helper to check if a session user is admin
    // ----------------------------------------------------------
    public function isAdmin(int $userID): bool {
        $user = $this->getUserByID($userID);
        return $user && (bool)$user['IsAdmin'];
    }
}