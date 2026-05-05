-- ============================================================
-- Consistency Project - User Account Management
-- Developer: Sri Krishna Shrestha
-- File: database/scripts/tblUsers.sql
-- Sprint 2 - Updated schema matching Option B
-- ============================================================

CREATE DATABASE IF NOT EXISTS consistency_db;
USE consistency_db;

-- Drop existing table and procedures cleanly
DROP TABLE IF EXISTS tblUsers;

-- ============================================================
-- TABLE: tblUsers
-- 7 attributes, all 4 data types covered (marking scheme)
-- VARCHAR (string), INT (numeric), BOOLEAN, DATE
-- ============================================================
CREATE TABLE tblUsers (
    UserID       INT           NOT NULL AUTO_INCREMENT,  -- INT (numeric)
    Username     VARCHAR(50)   NOT NULL,                 -- VARCHAR (string)
    Email        VARCHAR(100)  NOT NULL,                 -- VARCHAR (string)
    PasswordHash VARCHAR(255)  NOT NULL,                 -- VARCHAR (string)
    IsAdmin      BOOLEAN       NOT NULL DEFAULT FALSE,   -- BOOLEAN
    DateCreated  DATE          NOT NULL,                 -- DATE
    IsActive     BOOLEAN       NOT NULL DEFAULT TRUE,    -- BOOLEAN
    PRIMARY KEY (UserID),
    UNIQUE KEY uq_Email (Email)
);

-- ============================================================
-- STORED PROCEDURE: sp_RegisterUser
-- Adds a new user - supports Add function
-- ============================================================
DROP PROCEDURE IF EXISTS sp_RegisterUser;
DELIMITER $$
CREATE PROCEDURE sp_RegisterUser(
    IN p_Username     VARCHAR(50),
    IN p_Email        VARCHAR(100),
    IN p_PasswordHash VARCHAR(255)
)
BEGIN
    -- Check for duplicate email
    IF EXISTS (SELECT 1 FROM tblUsers WHERE Email = p_Email) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'This email address is already registered.';
    ELSE
        INSERT INTO tblUsers (Username, Email, PasswordHash, IsAdmin, DateCreated, IsActive)
        VALUES (p_Username, p_Email, p_PasswordHash, FALSE, CURDATE(), TRUE);
        SELECT LAST_INSERT_ID() AS NewUserID;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_LoginUser
-- Fetches user by email for authentication - supports Login
-- ============================================================
DROP PROCEDURE IF EXISTS sp_LoginUser;
DELIMITER $$
CREATE PROCEDURE sp_LoginUser(
    IN p_Email VARCHAR(100)
)
BEGIN
    SELECT UserID, Username, Email, PasswordHash, IsAdmin, IsActive
    FROM   tblUsers
    WHERE  Email = p_Email;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_GetUserByID
-- Returns single user record - supports View/Edit
-- ============================================================
DROP PROCEDURE IF EXISTS sp_GetUserByID;
DELIMITER $$
CREATE PROCEDURE sp_GetUserByID(
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
-- Returns all users with optional filter - supports List/Filter
-- p_Filter: -1 = all, 1 = active only, 0 = inactive only
-- ============================================================
DROP PROCEDURE IF EXISTS sp_GetAllUsers;
DELIMITER $$
CREATE PROCEDURE sp_GetAllUsers(
    IN p_Filter INT
)
BEGIN
    IF p_Filter = 1 THEN
        SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
        FROM   tblUsers WHERE IsActive = TRUE ORDER BY DateCreated DESC;
    ELSEIF p_Filter = 0 THEN
        SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
        FROM   tblUsers WHERE IsActive = FALSE ORDER BY DateCreated DESC;
    ELSE
        SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
        FROM   tblUsers ORDER BY DateCreated DESC;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_FindUsers
-- Searches by username or email - supports Find function
-- ============================================================
DROP PROCEDURE IF EXISTS sp_FindUsers;
DELIMITER $$
CREATE PROCEDURE sp_FindUsers(
    IN p_SearchTerm VARCHAR(100)
)
BEGIN
    SELECT UserID, Username, Email, IsAdmin, DateCreated, IsActive
    FROM   tblUsers
    WHERE  Username LIKE CONCAT('%', p_SearchTerm, '%')
        OR Email    LIKE CONCAT('%', p_SearchTerm, '%')
    ORDER BY Username ASC;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_UpdateUser
-- Updates username and email - supports Edit function
-- ============================================================
DROP PROCEDURE IF EXISTS sp_UpdateUser;
DELIMITER $$
CREATE PROCEDURE sp_UpdateUser(
    IN p_UserID   INT,
    IN p_Username VARCHAR(50),
    IN p_Email    VARCHAR(100)
)
BEGIN
    -- Check email not taken by a different user
    IF EXISTS (
        SELECT 1 FROM tblUsers
        WHERE Email = p_Email AND UserID != p_UserID
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'This email is already used by another account.';
    ELSE
        UPDATE tblUsers
        SET    Username = p_Username, Email = p_Email
        WHERE  UserID = p_UserID;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_ChangePassword
-- Updates password hash - supports Edit/security function
-- ============================================================
DROP PROCEDURE IF EXISTS sp_ChangePassword;
DELIMITER $$
CREATE PROCEDURE sp_ChangePassword(
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
-- Soft delete via IsActive flag - supports Delete function
-- ============================================================
DROP PROCEDURE IF EXISTS sp_DeactivateUser;
DELIMITER $$
CREATE PROCEDURE sp_DeactivateUser(
    IN p_UserID INT
)
BEGIN
    UPDATE tblUsers
    SET    IsActive = FALSE
    WHERE  UserID = p_UserID;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_ToggleAdmin
-- Flips IsAdmin flag - supports admin management
-- ============================================================
DROP PROCEDURE IF EXISTS sp_ToggleAdmin;
DELIMITER $$
CREATE PROCEDURE sp_ToggleAdmin(
    IN p_UserID INT
)
BEGIN
    UPDATE tblUsers
    SET    IsAdmin = NOT IsAdmin
    WHERE  UserID = p_UserID;
END$$
DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: sp_AdminAddUser
-- Admin creates a new user directly - supports Add function
-- ============================================================
DROP PROCEDURE IF EXISTS sp_AdminAddUser;
DELIMITER $$
CREATE PROCEDURE sp_AdminAddUser(
    IN p_Username     VARCHAR(50),
    IN p_Email        VARCHAR(100),
    IN p_PasswordHash VARCHAR(255),
    IN p_IsAdmin      BOOLEAN
)
BEGIN
    IF EXISTS (SELECT 1 FROM tblUsers WHERE Email = p_Email) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'This email address is already registered.';
    ELSE
        INSERT INTO tblUsers (Username, Email, PasswordHash, IsAdmin, DateCreated, IsActive)
        VALUES (p_Username, p_Email, p_PasswordHash, p_IsAdmin, CURDATE(), TRUE);
        SELECT LAST_INSERT_ID() AS NewUserID;
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- TEST DATA (8 rows covering all data types)
-- INT: UserID auto, BOOLEAN: IsAdmin/IsActive, DATE: DateCreated
-- VARCHAR: Username, Email, PasswordHash
-- ============================================================
INSERT INTO tblUsers (Username, Email, PasswordHash, IsAdmin, DateCreated, IsActive) VALUES
('admin',       'admin@consistency.com',   '$2y$10$HASHADMIN000000000000uXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', TRUE,  '2026-04-01', TRUE),
('sri_krishna', 'sri@consistency.com',     '$2y$10$HASHSRI0000000000000uXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',  FALSE, '2026-04-02', TRUE),
('pratik_t',    'pratik@consistency.com',  '$2y$10$HASHPRATIK00000000000uXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', FALSE, '2026-04-03', TRUE),
('sashi_k',     'sashi@consistency.com',   '$2y$10$HASHSASHI00000000000uXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',  FALSE, '2026-04-04', TRUE),
('abin_r',      'abin@consistency.com',    '$2y$10$HASHABIN000000000000uXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',  FALSE, '2026-04-05', TRUE),
('juna_b',      'juna@consistency.com',    '$2y$10$HASHJUNA000000000000uXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',  FALSE, '2026-04-06', TRUE),
('testuser1',   'test1@example.com',       '$2y$10$HASHTEST1000000000000uXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', FALSE, '2026-04-10', TRUE),
('testuser2',   'test2@example.com',       '$2y$10$HASHTEST2000000000000uXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', FALSE, '2026-04-11', FALSE);
-- Note: Replace hash values above by registering through register.php
-- The real hashes will be generated by PHP password_hash() automatically