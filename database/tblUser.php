-- ============================================================
-- Consistency Project – User Account Management
-- Developer: Sri Krishna Shrestha
-- File: database/scripts/tblUsers.sql
-- ============================================================

-- Create the database (run once)
CREATE DATABASE IF NOT EXISTS consistency_db;
USE consistency_db;

-- ============================================================
-- TABLE: tblUsers
-- ============================================================
CREATE TABLE IF NOT EXISTS tblUsers (
    UserID       INT           NOT NULL AUTO_INCREMENT,
    Username     VARCHAR(50)   NOT NULL,
    Email        VARCHAR(100)  NOT NULL,
    PasswordHash VARCHAR(255)  NOT NULL,
    IsAdmin      BOOLEAN       NOT NULL DEFAULT FALSE,
    DateCreated  DATE          NOT NULL,
    IsActive     BOOLEAN       NOT NULL DEFAULT TRUE,
    PRIMARY KEY (UserID),
    UNIQUE KEY uq_Email (Email)
);

-- ============================================================
-- STORED PROCEDURE: sp_RegisterUser
-- Inserts a new user record
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_RegisterUser (
    IN  p_Username     VARCHAR(50),
    IN  p_Email        VARCHAR(100),
    IN  p_PasswordHash VARCHAR(255)
)
BEGIN
    -- Reject if email already exists
    IF EXISTS (SELECT 1 FROM tblUsers WHERE Email = p_Email) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Email address is already registered.';
    ELSE
        INSERT INTO tblUsers (Username, Email, PasswordHash, IsAdmin, DateCreated, IsActive)
        VALUES (p_Username, p_Email, p_PasswordHash, FALSE, CURDATE(), TRUE);
        SELECT LAST_INSERT_ID() AS NewUserID;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_LoginUser
-- Returns user record for authentication (by email)
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_LoginUser (
    IN p_Email VARCHAR(100)
)
BEGIN
    SELECT UserID, Username, Email, PasswordHash, IsAdmin, IsActive
    FROM   tblUsers
    WHERE  Email    = p_Email
      AND  IsActive = TRUE;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_GetUserByID
-- Returns a single user by UserID
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_GetUserByID (
    IN p_UserID INT
)
BEGIN
    SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
    FROM   tblUsers
    WHERE  UserID = p_UserID;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_GetAllUsers
-- Returns all users (admin use) with optional active filter
-- p_FilterActive: 1 = active only, 0 = inactive only, -1 = all
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_GetAllUsers (
    IN p_FilterActive INT
)
BEGIN
    IF p_FilterActive = 1 THEN
        SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
        FROM   tblUsers
        WHERE  IsActive = TRUE
        ORDER BY DateCreated DESC;
    ELSEIF p_FilterActive = 0 THEN
        SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
        FROM   tblUsers
        WHERE  IsActive = FALSE
        ORDER BY DateCreated DESC;
    ELSE
        SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
        FROM   tblUsers
        ORDER BY DateCreated DESC;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_FindUsers
-- Searches users by username or email (admin use)
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_FindUsers (
    IN p_SearchTerm VARCHAR(100)
)
BEGIN
    SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
    FROM   tblUsers
    WHERE  Username LIKE CONCAT('%', p_SearchTerm, '%')
      OR   Email    LIKE CONCAT('%', p_SearchTerm, '%')
    ORDER BY Username ASC;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_UpdateUser
-- Updates username and email for a user
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_UpdateUser (
    IN p_UserID   INT,
    IN p_Username VARCHAR(50),
    IN p_Email    VARCHAR(100)
)
BEGIN
    -- Reject if new email is taken by a different user
    IF EXISTS (
        SELECT 1 FROM tblUsers
        WHERE Email = p_Email AND UserID != p_UserID
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Email address is already in use by another account.';
    ELSE
        UPDATE tblUsers
        SET    Username = p_Username,
               Email    = p_Email
        WHERE  UserID   = p_UserID;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_ChangePassword
-- Updates the password hash for a user
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_ChangePassword (
    IN p_UserID      INT,
    IN p_NewPassHash VARCHAR(255)
)
BEGIN
    UPDATE tblUsers
    SET    PasswordHash = p_NewPassHash
    WHERE  UserID = p_UserID;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_DeactivateUser
-- Soft-deletes a user (sets IsActive = FALSE)
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_DeactivateUser (
    IN p_UserID INT
)
BEGIN
    UPDATE tblUsers
    SET    IsActive = FALSE
    WHERE  UserID   = p_UserID;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_ToggleAdminPrivilege
-- Flips the IsAdmin flag for a user (admin use)
-- ============================================================
DELIMITER $$
CREATE PROCEDURE sp_ToggleAdminPrivilege (
    IN p_UserID INT
)
BEGIN
    UPDATE tblUsers
    SET    IsAdmin = NOT IsAdmin
    WHERE  UserID  = p_UserID;
END$$
DELIMITER ;

-- ============================================================
-- TEST DATA (optional – remove before production)
-- ============================================================
-- Password hashes below represent 'Password1!' hashed with PHP password_hash()
-- Replace with real hashes generated by your PHP register function
INSERT INTO tblUsers (Username, Email, PasswordHash, IsAdmin, DateCreated, IsActive) VALUES
('admin',       'admin@consistency.com',       '$2y$10$examplehashADMIN',   TRUE,  '2026-04-20', TRUE),
('sri_krishna', 'sri@consistency.com',         '$2y$10$examplehashSRI',     FALSE, '2026-04-20', TRUE),
('testuser1',   'testuser1@example.com',        '$2y$10$examplehashT1',      FALSE, '2026-04-21', TRUE),
('testuser2',   'testuser2@example.com',        '$2y$10$examplehashT2',      FALSE, '2026-04-21', FALSE);