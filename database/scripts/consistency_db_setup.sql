-- ============================================================
-- CONSISTENCY HABIT TRACKER - MASTER DATABASE SETUP (UPDATED JUNE 2026)
-- Team Members:
--   - Sri Krishna Shrestha (User Management)
--   - Sashi Khatri (Habit Management)
--   - Abin Rai (Habit Logging)
--   - Juna Bhujel (Streaks/Progress)
--   - Pratik Tamang (Reminders & Notifications)
--
-- INSTRUCTIONS:
--   1. Open phpMyAdmin
--   2. Click "SQL" tab
--   3. Paste this entire file and click "Go"
-- ============================================================
-- LOGIN CREDENTIALS (after setup):
--   User:  abin@gmail.com / abin1234
CREATE DATABASE IF NOT EXISTS consistency_db
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE consistency_db;

-- Drop tables in correct order
DROP TABLE IF EXISTS tblReminders;
DROP TABLE IF EXISTS tblStreaks;
DROP TABLE IF EXISTS tblHabitLogs;
DROP TABLE IF EXISTS tblHabits;
DROP TABLE IF EXISTS tblUsers;

-- ============================================================
-- TABLE 1: tblUsers (Sri Krishna's component)
-- ============================================================
CREATE TABLE tblUsers (
    UserID       INT          NOT NULL AUTO_INCREMENT,
    Username     VARCHAR(50)  NOT NULL,
    Email        VARCHAR(100) NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    IsAdmin      BOOLEAN      NOT NULL DEFAULT FALSE,
    DateCreated  DATE         NOT NULL,
    IsActive     BOOLEAN      NOT NULL DEFAULT TRUE,
    PRIMARY KEY (UserID),
    UNIQUE KEY uq_Email (Email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE 2: tblHabits (Sashi's component)
-- ============================================================
CREATE TABLE tblHabits (
    HabitID       INT          NOT NULL AUTO_INCREMENT,
    UserID        INT          NOT NULL,
    HabitName     VARCHAR(50)  NOT NULL,
    Description   VARCHAR(255),
    TargetPerWeek INT          NOT NULL,
    IsActive      BOOLEAN      DEFAULT TRUE,
    CreatedDate   DATE         NOT NULL,
    PRIMARY KEY (HabitID),
    FOREIGN KEY (UserID) REFERENCES tblUsers(UserID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE 3: tblHabitLogs (Abin's component)
-- ============================================================
CREATE TABLE tblHabitLogs (
    LogID        INT          NOT NULL AUTO_INCREMENT,
    HabitID      INT          NOT NULL,
    UserID       INT          NOT NULL,
    LogDate      DATE         NOT NULL,
    IsCompleted  BOOLEAN      NOT NULL,
    Notes        VARCHAR(255),
    DurationMins INT          DEFAULT 0,
    PRIMARY KEY (LogID),
    FOREIGN KEY (HabitID) REFERENCES tblHabits(HabitID) ON DELETE CASCADE,
    FOREIGN KEY (UserID)  REFERENCES tblUsers(UserID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE 4: tblStreaks (Juna's component)
-- ============================================================
CREATE TABLE tblStreaks (
    StreakID      INT     NOT NULL AUTO_INCREMENT,
    UserID        INT     NOT NULL,
    HabitID       INT     NOT NULL,
    CurrentStreak INT     DEFAULT 0,
    LongestStreak INT     DEFAULT 0,
    IsActive      BOOLEAN DEFAULT TRUE,
    LastUpdated   DATE    NOT NULL,
    PRIMARY KEY (StreakID),
    FOREIGN KEY (UserID)  REFERENCES tblUsers(UserID) ON DELETE CASCADE,
    FOREIGN KEY (HabitID) REFERENCES tblHabits(HabitID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE 5: tblReminders (Pratik's component)
-- ============================================================
CREATE TABLE tblReminders (
    ReminderID   INT          NOT NULL AUTO_INCREMENT,
    UserID       INT          NOT NULL,
    HabitID      INT          NOT NULL,
    ReminderTime TIME         NOT NULL,
    IsEnabled    BOOLEAN      DEFAULT TRUE,
    Message      VARCHAR(255),
    CreatedDate  DATE         NOT NULL,
    PRIMARY KEY (ReminderID),
    FOREIGN KEY (UserID)  REFERENCES tblUsers(UserID) ON DELETE CASCADE,
    FOREIGN KEY (HabitID) REFERENCES tblHabits(HabitID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TEST DATA: Users (Updated with Abin)
-- ============================================================
INSERT INTO tblUsers (Username, Email, PasswordHash, IsAdmin, DateCreated, IsActive) VALUES
('admin',     'admin@consistency.com', '$2y$10$YourBcryptHashForAdmin1234Replace', 1, '2026-04-01', 1),
('johndoe',   'john@example.com',      '$2y$10$YourBcryptHashForUser1234Replace',  0, '2026-04-05', 1),
('janedoe',   'jane@example.com',      '$2y$10$YourBcryptHashForUser1234Replace',  0, '2026-04-10', 1),
('mikebrown', 'mike@example.com',      '$2y$10$YourBcryptHashForUser1234Replace',  0, '2026-04-12', 1),
('sarahlee',  'sarah@example.com',     '$2y$10$YourBcryptHashForUser1234Replace',  0, '2026-04-15', 1),
('abin',      'abin@gmail.com',        '$2y$10$YourBcryptHashForAbin1234Replace',  0, '2026-06-05', 1);

-- NOTE: Run setup_passwords.php after this to generate real bcrypt hashes

-- ============================================================
-- TEST DATA: Habits
-- ============================================================
INSERT INTO tblHabits (UserID, HabitName, Description, TargetPerWeek, IsActive, CreatedDate) VALUES
(1, 'Morning Exercise', 'Run for 30 minutes every morning',  7, 1, '2026-04-01'),
(1, 'Meditation',       'Meditate for 10 minutes',           7, 1, '2026-04-02'),
(2, 'Reading',          'Read for 30 minutes before bed',    5, 1, '2026-04-05'),
(2, 'Water Intake',     'Drink 8 glasses of water daily',    7, 1, '2026-04-06'),
(3, 'Yoga',             'Practice yoga sessions',            3, 1, '2026-04-10'),
(4, 'Journaling',       'Write daily journal entries',       7, 1, '2026-04-12'),
(5, 'Healthy Eating',   'Eat balanced meals',                7, 1, '2026-04-15');

-- ============================================================
-- TEST DATA: HabitLogs
-- ============================================================
INSERT INTO tblHabitLogs (HabitID, UserID, LogDate, IsCompleted, Notes, DurationMins) VALUES
(1, 1, '2026-04-14', TRUE,  'Felt great today',    30),
(2, 1, '2026-04-14', FALSE, NULL,                   0),
(1, 1, '2026-04-15', TRUE,  'Quick session',       20),
(3, 2, '2026-04-15', TRUE,  NULL,                  45),
(4, 2, '2026-04-16', TRUE,  'Did extra this time', 60),
(1, 1, '2026-04-16', FALSE, 'Missed the morning',   0),
(5, 3, '2026-04-17', TRUE,  NULL,                  15),
(3, 2, '2026-04-17', TRUE,  'Completed on time',   25);

-- ============================================================
-- TEST DATA: Streaks
-- ============================================================
INSERT INTO tblStreaks (UserID, HabitID, CurrentStreak, LongestStreak, IsActive, LastUpdated) VALUES
(1, 1, 15, 20, TRUE,  '2026-05-11'),
(1, 2, 7,  7,  TRUE,  '2026-05-11'),
(2, 3, 0,  10, TRUE,  '2026-05-10'),
(2, 4, 3,  8,  TRUE,  '2026-05-11'),
(3, 5, 0,  5,  FALSE, '2026-05-01'),
(4, 6, 1,  1,  TRUE,  '2026-05-11'),
(5, 7, 30, 30, TRUE,  '2026-05-11');

-- ============================================================
-- TEST DATA: Reminders (Updated from your phpMyAdmin screenshot)
-- ============================================================
TRUNCATE TABLE tblReminders;

INSERT INTO tblReminders (ReminderID, UserID, HabitID, ReminderTime, IsEnabled, Message, CreatedDate) VALUES
(1,  1, 1, '06:00:00', 1, 'Early morning workout time!', '2026-05-01'),
(2,  1, 2, '09:00:00', 1, 'Time to meditate and relax', '2026-05-01'),
(3,  1, 1, '18:00:00', 0, 'Evening exercise - disabled for testing', '2026-05-02'),
(4,  2, 3, '20:00:00', 1, 'Evening reading session', '2026-05-02'),
(5,  2, 4, '08:00:00', 1, 'Drink your morning water', '2026-05-03'),
(6,  3, 5, '07:30:00', 1, 'Morning yoga practice', '2026-05-03'),
(7,  3, 5, '15:00:00', 0, 'Afternoon yoga - disabled', '2026-05-04'),
(8,  4, 6, '21:00:00', 1, 'Time to journal your day', '2026-05-04'),
(9,  2, 3, '22:00:00', 1, 'Late night reading reminder', '2026-05-05'),
(10, 1, 2, '12:00:00', 1, 'Midday meditation break', '2026-05-05'),
(11, 5, 7, '23:59:00', 0, 'Late night meal prep - edge case', '2026-05-06'),
(12, 1, 1, '00:00:00', 1, 'Midnight reminder - edge case', '2026-05-06'),
(13, 2, 4, '13:30:00', 1, 'Afternoon hydration check', '2026-05-07'),
(14, 3, 5, '16:45:00', 0, 'Late afternoon yoga', '2026-05-07'),
(15, 4, 6, '10:15:00', 1, 'Mid-morning journal time', '2026-05-08'),
(16, 1, 5, '09:34:00', 1, 'hi', '2026-05-28');

-- ============================================================
-- STORED PROCEDURES: USER MANAGEMENT (Sri Krishna)
-- ============================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_RegisterUser$$
CREATE PROCEDURE sp_RegisterUser(
    IN p_Username VARCHAR(50), IN p_Email VARCHAR(100), IN p_PasswordHash VARCHAR(255)
)
BEGIN
    IF EXISTS (SELECT 1 FROM tblUsers WHERE Email = p_Email) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This email is already registered.';
    ELSE
        INSERT INTO tblUsers (Username, Email, PasswordHash, IsAdmin, DateCreated, IsActive)
        VALUES (p_Username, p_Email, p_PasswordHash, FALSE, CURDATE(), TRUE);
        SELECT LAST_INSERT_ID() AS NewUserID;
    END IF;
END$$

DROP PROCEDURE IF EXISTS sp_LoginUser$$
CREATE PROCEDURE sp_LoginUser(IN p_Email VARCHAR(100))
BEGIN
    SELECT UserID, Username, Email, PasswordHash, IsAdmin, IsActive
    FROM tblUsers WHERE Email = p_Email;
END$$

DROP PROCEDURE IF EXISTS sp_GetAllUsers$$
CREATE PROCEDURE sp_GetAllUsers()
BEGIN
    SELECT * FROM tblUsers ORDER BY DateCreated DESC;
END$$

DROP PROCEDURE IF EXISTS sp_GetUserByID$$
CREATE PROCEDURE sp_GetUserByID(IN p_UserID INT)
BEGIN
    SELECT * FROM tblUsers WHERE UserID = p_UserID;
END$$

DROP PROCEDURE IF EXISTS sp_UpdateUser$$
CREATE PROCEDURE sp_UpdateUser(
    IN p_UserID INT, IN p_Username VARCHAR(50), IN p_Email VARCHAR(100)
)
BEGIN
    UPDATE tblUsers SET Username = p_Username, Email = p_Email WHERE UserID = p_UserID;
END$$

DROP PROCEDURE IF EXISTS sp_DeleteUser$$
CREATE PROCEDURE sp_DeleteUser(IN p_UserID INT)
BEGIN
    UPDATE tblUsers SET IsActive = FALSE WHERE UserID = p_UserID;
END$$

-- ============================================================
-- STORED PROCEDURES: HABIT MANAGEMENT (Sashi)
-- ============================================================

DROP PROCEDURE IF EXISTS sp_AddHabit$$
CREATE PROCEDURE sp_AddHabit(
    IN p_UserID INT, IN p_HabitName VARCHAR(50), IN p_Description VARCHAR(255),
    IN p_TargetPerWeek INT
)
BEGIN
    INSERT INTO tblHabits (UserID, HabitName, Description, TargetPerWeek, IsActive, CreatedDate)
    VALUES (p_UserID, p_HabitName, p_Description, p_TargetPerWeek, TRUE, CURDATE());
END$$

DROP PROCEDURE IF EXISTS sp_GetAllHabits$$
CREATE PROCEDURE sp_GetAllHabits()
BEGIN
    SELECT * FROM tblHabits ORDER BY CreatedDate DESC;
END$$

DROP PROCEDURE IF EXISTS sp_GetHabitByID$$
CREATE PROCEDURE sp_GetHabitByID(IN p_HabitID INT)
BEGIN
    SELECT * FROM tblHabits WHERE HabitID = p_HabitID;
END$$

DROP PROCEDURE IF EXISTS sp_UpdateHabit$$
CREATE PROCEDURE sp_UpdateHabit(
    IN p_HabitID INT, IN p_HabitName VARCHAR(50), IN p_Description VARCHAR(255),
    IN p_TargetPerWeek INT
)
BEGIN
    UPDATE tblHabits SET HabitName = p_HabitName, Description = p_Description,
        TargetPerWeek = p_TargetPerWeek WHERE HabitID = p_HabitID;
END$$

DROP PROCEDURE IF EXISTS sp_DeleteHabit$$
CREATE PROCEDURE sp_DeleteHabit(IN p_HabitID INT)
BEGIN
    UPDATE tblHabits SET IsActive = FALSE WHERE HabitID = p_HabitID;
END$$

DROP PROCEDURE IF EXISTS sp_FindHabit$$
CREATE PROCEDURE sp_FindHabit(IN p_HabitName VARCHAR(50))
BEGIN
    SELECT * FROM tblHabits WHERE HabitName LIKE CONCAT('%', p_HabitName, '%');
END$$

-- ============================================================
-- STORED PROCEDURES: HABIT LOGGING (Abin)
-- ============================================================

DROP PROCEDURE IF EXISTS sp_AddLog$$
CREATE PROCEDURE sp_AddLog(
    IN p_HabitID INT, IN p_UserID INT, IN p_LogDate DATE,
    IN p_IsCompleted BOOLEAN, IN p_Notes VARCHAR(255), IN p_DurationMins INT
)
BEGIN
    INSERT INTO tblHabitLogs (HabitID, UserID, LogDate, IsCompleted, Notes, DurationMins)
    VALUES (p_HabitID, p_UserID, p_LogDate, p_IsCompleted, p_Notes, p_DurationMins);
END$$

DROP PROCEDURE IF EXISTS sp_GetAllLogs$$
CREATE PROCEDURE sp_GetAllLogs()
BEGIN
    SELECT * FROM tblHabitLogs ORDER BY LogDate DESC;
END$$

DROP PROCEDURE IF EXISTS sp_GetLogByID$$
CREATE PROCEDURE sp_GetLogByID(IN p_LogID INT)
BEGIN
    SELECT * FROM tblHabitLogs WHERE LogID = p_LogID;
END$$

DROP PROCEDURE IF EXISTS sp_UpdateLog$$
CREATE PROCEDURE sp_UpdateLog(
    IN p_LogID INT, IN p_LogDate DATE, IN p_IsCompleted BOOLEAN,
    IN p_Notes VARCHAR(255), IN p_DurationMins INT
)
BEGIN
    UPDATE tblHabitLogs SET LogDate = p_LogDate, IsCompleted = p_IsCompleted,
        Notes = p_Notes, DurationMins = p_DurationMins WHERE LogID = p_LogID;
END$$

DROP PROCEDURE IF EXISTS sp_DeleteLog$$
CREATE PROCEDURE sp_DeleteLog(IN p_LogID INT)
BEGIN
    DELETE FROM tblHabitLogs WHERE LogID = p_LogID;
END$$

DROP PROCEDURE IF EXISTS sp_FilterLogsByHabit$$
CREATE PROCEDURE sp_FilterLogsByHabit(IN p_HabitID INT)
BEGIN
    SELECT * FROM tblHabitLogs WHERE HabitID = p_HabitID ORDER BY LogDate DESC;
END$$

-- ============================================================
-- STORED PROCEDURES: STREAKS (Juna)
-- ============================================================

DROP PROCEDURE IF EXISTS sp_AddStreak$$
CREATE PROCEDURE sp_AddStreak(
    IN p_UserID INT, IN p_HabitID INT, IN p_CurrentStreak INT,
    IN p_LongestStreak INT, IN p_IsActive BOOLEAN
)
BEGIN
    INSERT INTO tblStreaks (UserID, HabitID, CurrentStreak, LongestStreak, IsActive, LastUpdated)
    VALUES (p_UserID, p_HabitID, p_CurrentStreak, p_LongestStreak, p_IsActive, CURDATE());
END$$

DROP PROCEDURE IF EXISTS sp_GetAllStreaks$$
CREATE PROCEDURE sp_GetAllStreaks()
BEGIN
    SELECT * FROM tblStreaks ORDER BY CurrentStreak DESC;
END$$

DROP PROCEDURE IF EXISTS sp_GetStreakByID$$
CREATE PROCEDURE sp_GetStreakByID(IN p_StreakID INT)
BEGIN
    SELECT * FROM tblStreaks WHERE StreakID = p_StreakID;
END$$

DROP PROCEDURE IF EXISTS sp_UpdateStreak$$
CREATE PROCEDURE sp_UpdateStreak(
    IN p_StreakID INT, IN p_CurrentStreak INT, IN p_LongestStreak INT, IN p_IsActive BOOLEAN
)
BEGIN
    UPDATE tblStreaks SET CurrentStreak = p_CurrentStreak, LongestStreak = p_LongestStreak,
        IsActive = p_IsActive, LastUpdated = CURDATE() WHERE StreakID = p_StreakID;
END$$

DROP PROCEDURE IF EXISTS sp_DeleteStreak$$
CREATE PROCEDURE sp_DeleteStreak(IN p_StreakID INT)
BEGIN
    DELETE FROM tblStreaks WHERE StreakID = p_StreakID;
END$$

-- ============================================================
-- STORED PROCEDURES: REMINDERS (Pratik)
-- ============================================================

DROP PROCEDURE IF EXISTS sp_AddReminder$$
CREATE PROCEDURE sp_AddReminder(
    IN p_UserID INT, IN p_HabitID INT, IN p_ReminderTime TIME,
    IN p_IsEnabled BOOLEAN, IN p_Message VARCHAR(255)
)
BEGIN
    INSERT INTO tblReminders (UserID, HabitID, ReminderTime, IsEnabled, Message, CreatedDate)
    VALUES (p_UserID, p_HabitID, p_ReminderTime, p_IsEnabled, p_Message, CURDATE());
END$$

DROP PROCEDURE IF EXISTS sp_GetAllReminders$$
CREATE PROCEDURE sp_GetAllReminders()
BEGIN
    SELECT * FROM tblReminders ORDER BY CreatedDate DESC;
END$$

DROP PROCEDURE IF EXISTS sp_GetReminderById$$
CREATE PROCEDURE sp_GetReminderById(IN p_ReminderID INT)
BEGIN
    SELECT * FROM tblReminders WHERE ReminderID = p_ReminderID;
END$$

DROP PROCEDURE IF EXISTS sp_UpdateReminder$$
CREATE PROCEDURE sp_UpdateReminder(
    IN p_ReminderID INT, IN p_UserID INT, IN p_HabitID INT, IN p_ReminderTime TIME,
    IN p_IsEnabled BOOLEAN, IN p_Message VARCHAR(255)
)
BEGIN
    UPDATE tblReminders SET UserID = p_UserID, HabitID = p_HabitID,
        ReminderTime = p_ReminderTime, IsEnabled = p_IsEnabled, Message = p_Message
    WHERE ReminderID = p_ReminderID;
END$$

DROP PROCEDURE IF EXISTS sp_DeleteReminder$$
CREATE PROCEDURE sp_DeleteReminder(IN p_ReminderID INT)
BEGIN
    DELETE FROM tblReminders WHERE ReminderID = p_ReminderID;
END$$

DROP PROCEDURE IF EXISTS sp_FilterRemindersByHabit$$
CREATE PROCEDURE sp_FilterRemindersByHabit(IN p_HabitID INT)
BEGIN
    SELECT * FROM tblReminders WHERE HabitID = p_HabitID ORDER BY CreatedDate DESC;
END$$

DROP PROCEDURE IF EXISTS sp_FilterRemindersByStatus$$
CREATE PROCEDURE sp_FilterRemindersByStatus(IN p_IsEnabled BOOLEAN)
BEGIN
    SELECT * FROM tblReminders WHERE IsEnabled = p_IsEnabled ORDER BY CreatedDate DESC;
END$$

DROP PROCEDURE IF EXISTS sp_FilterRemindersByUser$$
CREATE PROCEDURE sp_FilterRemindersByUser(IN p_UserID INT)
BEGIN
    SELECT * FROM tblReminders WHERE UserID = p_UserID ORDER BY CreatedDate DESC;
END$$

DELIMITER ;

-- ============================================================
-- VERIFICATION QUERY
-- ============================================================
SELECT 'Database setup complete!' AS Status;
SELECT 
    (SELECT COUNT(*) FROM tblUsers) AS Users,
    (SELECT COUNT(*) FROM tblHabits) AS Habits,
    (SELECT COUNT(*) FROM tblHabitLogs) AS Logs,
    (SELECT COUNT(*) FROM tblStreaks) AS Streaks,
    (SELECT COUNT(*) FROM tblReminders) AS Reminders;

