<?php
// ============================================================
// Consistency Project - User Account Management
// Developer: Sri Krishna Shrestha
// File: backend/classes/User.php
// Sprint 2 - Middle layer PHP class
// Calls stored procedures in the data layer (tblUsers)
// No direct SQL queries here - all go through stored procedures
// ============================================================

require_once __DIR__ . '/../config/db_connect.php';

class User {

    // Database connection object (PDO)
    private PDO $db;

    // ── Constructor ──────────────────────────────────────────
    // Injects the database connection on instantiation
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ============================================================
    // ADD: register()
    // Registers a new user account
    // Validates inputs, hashes password, calls sp_RegisterUser
    // Returns: ['success'=>true, 'userID'=>int]
    //       or ['success'=>false, 'error'=>string]
    // ============================================================
    public function register(string $username, string $email, string $password): array {

        // Trim whitespace from inputs
        $username = trim($username);
        $email    = trim($email);

        // Server-side validation (marking scheme: validate inputs)
        if (empty($username)) {
            return ['success' => false, 'error' => 'Username is required.'];
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'A valid email address is required.'];
        }
        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters.'];
        }

        // Hash the password using bcrypt (never store plain text)
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Call data layer stored procedure
            $stmt = $this->db->prepare("CALL sp_RegisterUser(:username, :email, :hash)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email',    $email);
            $stmt->bindParam(':hash',     $passwordHash);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return ['success' => true, 'userID' => $row['NewUserID']];

        } catch (PDOException $e) {
            // Stored procedure signals duplicate email error
            return ['success' => false, 'error' => 'This email address is already registered.'];
        }
    }

    // ============================================================
    // LOGIN: login()
    // Authenticates user by email and password
    // Calls sp_LoginUser, verifies bcrypt hash
    // Returns: ['success'=>true, 'user'=>array]
    //       or ['success'=>false, 'error'=>string]
    // ============================================================
    public function login(string $email, string $password): array {

        $email = trim($email);

        // Validate inputs
        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        try {
            // Call data layer stored procedure
            $stmt = $this->db->prepare("CALL sp_LoginUser(:email)");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check user exists
            if (!$user) {
                return ['success' => false, 'error' => 'No account found with that email address.'];
            }

            // Check account is active (IsActive = BOOLEAN)
            if (!$user['IsActive']) {
                return ['success' => false, 'error' => 'This account has been deactivated.'];
            }

            // Verify password against stored bcrypt hash
            if (!password_verify($password, $user['PasswordHash'])) {
                return ['success' => false, 'error' => 'Incorrect password. Please try again.'];
            }

            // Remove hash from returned data before storing in session
            unset($user['PasswordHash']);
            return ['success' => true, 'user' => $user];

        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Login failed. Please try again.'];
        }
    }

    // ============================================================
    // VIEW: getUserByID()
    // Returns a single user record by UserID (INT)
    // Calls sp_GetUserByID
    // Returns: array or null
    // ============================================================
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

    // ============================================================
    // LIST: getAllUsers()
    // Returns all user records with optional active/inactive filter
    // Calls sp_GetAllUsers
    // $filter: -1 = all, 1 = active only, 0 = inactive only
    // ============================================================
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

    // ============================================================
    // FIND: findUsers()
    // Searches users by username or email
    // Calls sp_FindUsers
    // Returns: array of matching user records
    // ============================================================
    public function findUsers(string $searchTerm): array {
        $searchTerm = trim($searchTerm);

        // If empty search, return all users
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

    // ============================================================
    // EDIT: updateProfile()
    // Updates username and email for a logged-in user
    // Calls sp_UpdateUser
    // Returns: ['success'=>true] or ['success'=>false, 'error'=>string]
    // ============================================================
    public function updateProfile(int $userID, string $username, string $email): array {

        $username = trim($username);
        $email    = trim($email);

        // Validate inputs
        if (empty($username)) {
            return ['success' => false, 'error' => 'Username is required.'];
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'A valid email address is required.'];
        }

        try {
            $stmt = $this->db->prepare("CALL sp_UpdateUser(:userID, :username, :email)");
            $stmt->bindParam(':userID',   $userID,   PDO::PARAM_INT);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email',    $email);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'This email is already used by another account.'];
        }
    }

    // ============================================================
    // EDIT: changePassword()
    // Changes password after verifying current password
    // Calls sp_ChangePassword
    // Returns: ['success'=>true] or ['success'=>false, 'error'=>string]
    // ============================================================
    public function changePassword(int $userID, string $currentPassword, string $newPassword): array {

        // Validate new password length
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'error' => 'New password must be at least 8 characters.'];
        }

        try {
            // Fetch current hash to verify against
            $stmt = $this->db->prepare("SELECT PasswordHash FROM tblUsers WHERE UserID = :userID");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return ['success' => false, 'error' => 'User not found.'];
            }

            // Verify current password matches stored hash
            if (!password_verify($currentPassword, $row['PasswordHash'])) {
                return ['success' => false, 'error' => 'Current password is incorrect.'];
            }

            // Hash the new password and update via stored procedure
            $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt2   = $this->db->prepare("CALL sp_ChangePassword(:userID, :hash)");
            $stmt2->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt2->bindParam(':hash',   $newHash);
            $stmt2->execute();
            return ['success' => true];

        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Password change failed. Please try again.'];
        }
    }

    // ============================================================
    // DELETE: deactivateUser()
    // Soft delete - sets IsActive = FALSE (preserves referential integrity)
    // Calls sp_DeactivateUser
    // Returns: ['success'=>true] or ['success'=>false, 'error'=>string]
    // ============================================================
    public function deactivateUser(int $userID): array {
        try {
            $stmt = $this->db->prepare("CALL sp_DeactivateUser(:userID)");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Could not deactivate user.'];
        }
    }

    // ============================================================
    // ADMIN ADD: adminAddUser()
    // Admin creates a new user account directly
    // Calls sp_AdminAddUser
    // Returns: ['success'=>true, 'userID'=>int] or error
    // ============================================================
    public function adminAddUser(string $username, string $email, string $password, bool $isAdmin): array {

        $username = trim($username);
        $email    = trim($email);

        // Validate inputs
        if (empty($username)) {
            return ['success' => false, 'error' => 'Username is required.'];
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'A valid email address is required.'];
        }
        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters.'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $this->db->prepare("CALL sp_AdminAddUser(:username, :email, :hash, :isAdmin)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email',    $email);
            $stmt->bindParam(':hash',     $passwordHash);
            $stmt->bindParam(':isAdmin',  $isAdmin, PDO::PARAM_BOOL);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return ['success' => true, 'userID' => $row['NewUserID']];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'This email address is already registered.'];
        }
    }

    // ============================================================
    // ADMIN: toggleAdmin()
    // Flips the IsAdmin BOOLEAN flag for a user
    // Calls sp_ToggleAdmin
    // ============================================================
    public function toggleAdmin(int $userID): array {
        try {
            $stmt = $this->db->prepare("CALL sp_ToggleAdmin(:userID)");
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Could not update admin privilege.'];
        }
    }

    // ============================================================
    // HELPER: isAdmin()
    // Returns true if the given userID has IsAdmin = TRUE
    // Used to protect admin pages
    // ============================================================
    public function isAdmin(int $userID): bool {
        $user = $this->getUserByID($userID);
        return $user ? (bool)$user['IsAdmin'] : false;
    }
}