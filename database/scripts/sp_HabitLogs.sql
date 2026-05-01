-- SP 1: ADD a new log (enforces 1 log per habit per day)
DELIMITER $$
CREATE PROCEDURE sp_AddHabitLog(
    IN p_HabitID      INT,
    IN p_UserID       INT,
    IN p_LogDate      DATE,
    IN p_IsCompleted  BOOLEAN,
    IN p_Notes        VARCHAR(255),
    IN p_DurationMins INT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tblHabitLogs
        WHERE HabitID = p_HabitID
          AND UserID  = p_UserID
          AND LogDate = p_LogDate
    ) THEN
        INSERT INTO tblHabitLogs
            (HabitID, UserID, LogDate, IsCompleted, Notes, DurationMins)
        VALUES
            (p_HabitID, p_UserID, p_LogDate,
             p_IsCompleted, p_Notes, p_DurationMins);
    END IF;
END$$
DELIMITER ;

-- SP 2: GET logs for a user between two dates
DELIMITER $$
CREATE PROCEDURE sp_GetLogsByUser(
    IN p_UserID    INT,
    IN p_StartDate DATE,
    IN p_EndDate   DATE
)
BEGIN
    SELECT * FROM tblHabitLogs
    WHERE UserID  = p_UserID
      AND LogDate BETWEEN p_StartDate AND p_EndDate;
END$$
DELIMITER ;

-- SP 3: DELETE a log entry
DELIMITER $$
CREATE PROCEDURE sp_DeleteHabitLog(
    IN p_LogID INT
)
BEGIN
    DELETE FROM tblHabitLogs
    WHERE LogID = p_LogID;
END$$
DELIMITER ;

-- SP 6: GET all logs for admin view (joined with username and habit name)
DELIMITER $$
CREATE PROCEDURE sp_GetAllLogs()
BEGIN
    SELECT
        l.LogID,
        l.LogDate,
        l.IsCompleted,
        l.Notes,
        l.DurationMins,
        u.Username,
        h.HabitName
    FROM tblHabitLogs l
    JOIN tblUsers  u ON l.UserID  = u.UserID
    JOIN tblHabits h ON l.HabitID = h.HabitID
    ORDER BY l.LogDate DESC;
END$$
DELIMITER ;

-- SP 7: FILTER logs by userID and/or date range for admin view
DELIMITER $$
CREATE PROCEDURE sp_FilterLogs(
    IN p_UserID    INT,
    IN p_StartDate DATE,
    IN p_EndDate   DATE
)
BEGIN
    SELECT
        l.LogID,
        l.LogDate,
        l.IsCompleted,
        l.Notes,
        l.DurationMins,
        u.Username,
        h.HabitName
    FROM tblHabitLogs l
    JOIN tblUsers  u ON l.UserID  = u.UserID
    JOIN tblHabits h ON l.HabitID = h.HabitID
    WHERE (p_UserID    IS NULL OR l.UserID  = p_UserID)
      AND (p_StartDate IS NULL OR l.LogDate >= p_StartDate)
      AND (p_EndDate   IS NULL OR l.LogDate <= p_EndDate)
    ORDER BY l.LogDate DESC;
END$$
DELIMITER ;