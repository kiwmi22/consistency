-- ============================================================
-- Consistency Database — Create Scripts
-- Run in this exact order
-- ============================================================

-- Step 1: Sri Krishna's table (User Account Management)
CREATE TABLE IF NOT EXISTS tblUsers (
    UserID       INT          PRIMARY KEY AUTO_INCREMENT,
    Username     VARCHAR(50)  NOT NULL,
    Email        VARCHAR(100) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    IsAdmin      BOOLEAN      DEFAULT FALSE,
    DateCreated  DATE         NOT NULL,
    IsActive     BOOLEAN      DEFAULT TRUE
);

-- Step 2: Sashi's table (Habit Management)
CREATE TABLE IF NOT EXISTS tblHabits (
    HabitID       INT          PRIMARY KEY AUTO_INCREMENT,
    UserID        INT          NOT NULL,
    HabitName     VARCHAR(50)  NOT NULL,
    Description   VARCHAR(255),
    TargetPerWeek INT          NOT NULL,
    IsActive      BOOLEAN      DEFAULT TRUE,
    CreatedDate   DATE         NOT NULL,
    FOREIGN KEY (UserID) REFERENCES tblUsers(UserID)
);

-- Step 3: Abin's table (Habit Logging)
CREATE TABLE IF NOT EXISTS tblHabitLogs (
    LogID        INT          PRIMARY KEY AUTO_INCREMENT,
    HabitID      INT          NOT NULL,
    UserID       INT          NOT NULL,
    LogDate      DATE         NOT NULL,
    IsCompleted  BOOLEAN      NOT NULL,
    Notes        VARCHAR(255),
    DurationMins INT          DEFAULT 0,
    FOREIGN KEY (HabitID) REFERENCES tblHabits(HabitID),
    FOREIGN KEY (UserID)  REFERENCES tblUsers(UserID)
);