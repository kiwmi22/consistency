-- ============================================================
-- Stored Procedures for tblHabitLogs
-- Developer: Abin Rai
-- Component: Habit Logging / Check-ins
-- Sprint 1: SP1-SP5 | Sprint 2: SP6-SP7 | Sprint 3: SP8-SP10
-- ============================================================

-- SP1: ADD a new log (enforces 1 log per habit per day)
DELIMITER $$
CREATE PROCEDURE sp_AddHabitLog(
    IN p_HabitID INT, IN p_UserID INT, IN p_LogDate DATE,
    IN p_IsCompleted BOOLEAN, IN p_Notes VARCHAR(255),
    IN p_DurationMins INT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tblHabitLogs
        WHERE HabitID=p_HabitID
        AND UserID=p_UserID
        AND LogDate=p_LogDate
    ) THEN
        INSERT INTO tblHabitLogs
            (HabitID,UserID,LogDate,IsCompleted,Notes,DurationMins)
        VALUES
            (p_HabitID,p_UserID,p_LogDate,
             p_IsCompleted,p_Notes,p_DurationMins);
    END IF;
END$$
DELIMITER ;

-- SP2: GET logs for a user between two dates
DELIMITER $$
CREATE PROCEDURE sp_GetLogsByUser(
    IN p_UserID INT,
    IN p_StartDate DATE,
    IN p_EndDate DATE
)
BEGIN
    SELECT * FROM tblHabitLogs
    WHERE UserID=p_UserID
    AND LogDate BETWEEN p_StartDate AND p_EndDate;
END$$
DELIMITER ;

-- SP3: DELETE a log entry
DELIMITER $$
CREATE PROCEDURE sp_DeleteHabitLog(IN p_LogID INT)
BEGIN
    DELETE FROM tblHabitLogs WHERE LogID=p_LogID;
END$$
DELIMITER ;

-- SP4: UPDATE a log entry
DELIMITER $$
CREATE PROCEDURE sp_UpdateHabitLog(
    IN p_LogID INT, IN p_IsCompleted BOOLEAN,
    IN p_Notes VARCHAR(255), IN p_DurationMins INT
)
BEGIN
    UPDATE tblHabitLogs
    SET IsCompleted=p_IsCompleted,
        Notes=p_Notes,
        DurationMins=p_DurationMins
    WHERE LogID=p_LogID;
END$$
DELIMITER ;

-- SP5: GET single log by LogID
DELIMITER $$
CREATE PROCEDURE sp_GetLogByID(IN p_LogID INT)
BEGIN
    SELECT * FROM tblHabitLogs WHERE LogID=p_LogID;
END$$
DELIMITER ;

-- SP6: GET all logs for admin view
DELIMITER $$
CREATE PROCEDURE sp_GetAllLogs()
BEGIN
    SELECT l.LogID, l.LogDate, l.IsCompleted,
           l.Notes, l.DurationMins,
           u.Username, h.HabitName
    FROM tblHabitLogs l
    JOIN tblUsers  u ON l.UserID=u.UserID
    JOIN tblHabits h ON l.HabitID=h.HabitID
    ORDER BY l.LogDate DESC;
END$$
DELIMITER ;

-- SP7: FILTER logs for admin by user and date range
DELIMITER $$
CREATE PROCEDURE sp_FilterLogs(
    IN p_UserID INT,
    IN p_StartDate DATE,
    IN p_EndDate DATE
)
BEGIN
    SELECT l.LogID, l.LogDate, l.IsCompleted,
           l.Notes, l.DurationMins,
           u.Username, h.HabitName
    FROM tblHabitLogs l
    JOIN tblUsers  u ON l.UserID=u.UserID
    JOIN tblHabits h ON l.HabitID=h.HabitID
    WHERE (p_UserID IS NULL OR l.UserID=p_UserID)
    AND (p_StartDate IS NULL OR l.LogDate>=p_StartDate)
    AND (p_EndDate IS NULL OR l.LogDate<=p_EndDate)
    ORDER BY l.LogDate DESC;
END$$
DELIMITER ;

-- SP8: FILTER logs by HabitID
DELIMITER $$
CREATE PROCEDURE sp_FilterLogsByHabit(IN p_HabitID INT)
BEGIN
    SELECT l.LogID, l.LogDate, l.IsCompleted,
           l.Notes, l.DurationMins,
           u.Username, h.HabitName
    FROM tblHabitLogs l
    JOIN tblUsers  u ON l.UserID=u.UserID
    JOIN tblHabits h ON l.HabitID=h.HabitID
    WHERE l.HabitID=p_HabitID
    ORDER BY l.LogDate DESC;
END$$
DELIMITER ;

-- SP9: FILTER logs by completion status
DELIMITER $$
CREATE PROCEDURE sp_FilterLogsByStatus(IN p_IsCompleted BOOLEAN)
BEGIN
    SELECT l.LogID, l.LogDate, l.IsCompleted,
           l.Notes, l.DurationMins,
           u.Username, h.HabitName
    FROM tblHabitLogs l
    JOIN tblUsers  u ON l.UserID=u.UserID
    JOIN tblHabits h ON l.HabitID=h.HabitID
    WHERE l.IsCompleted=p_IsCompleted
    ORDER BY l.LogDate DESC;
END$$
DELIMITER ;

-- SP10: FILTER logs by UserID for admin
DELIMITER $$
CREATE PROCEDURE sp_FilterLogsByUser(IN p_UserID INT)
BEGIN
    SELECT l.LogID, l.LogDate, l.IsCompleted,
           l.Notes, l.DurationMins,
           u.Username, h.HabitName
    FROM tblHabitLogs l
    JOIN tblUsers  u ON l.UserID=u.UserID
    JOIN tblHabits h ON l.HabitID=h.HabitID
    WHERE l.UserID=p_UserID
    ORDER BY l.LogDate DESC;
END$$
DELIMITER ;