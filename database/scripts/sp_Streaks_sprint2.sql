-- ============================================
-- Stored Procedures Sprint 2
-- Author: Juna Bhujel
-- Component: Progress & Statistics
-- ============================================

-- SP 1: Add new streak record
DELIMITER //
CREATE PROCEDURE sp_AddStreak(
    IN p_UserID       INT,
    IN p_HabitID      INT,
    IN p_LastUpdated  DATE
)
BEGIN
    INSERT INTO tblStreaks
    (UserID, HabitID, CurrentStreak, LongestStreak, IsActive, LastUpdated)
    VALUES (p_UserID, p_HabitID, 0, 0, TRUE, p_LastUpdated);
END //
DELIMITER ;

-- SP 2: Edit streak record
DELIMITER //
CREATE PROCEDURE sp_EditStreak(
    IN p_StreakID      INT,
    IN p_CurrentStreak INT,
    IN p_LongestStreak INT,
    IN p_IsActive      BOOLEAN,
    IN p_LastUpdated   DATE
)
BEGIN
    UPDATE tblStreaks
    SET CurrentStreak = p_CurrentStreak,
        LongestStreak = p_LongestStreak,
        IsActive      = p_IsActive,
        LastUpdated   = p_LastUpdated
    WHERE StreakID = p_StreakID;
END //
DELIMITER ;

-- SP 3: Delete streak record
DELIMITER //
CREATE PROCEDURE sp_DeleteStreak(IN p_StreakID INT)
BEGIN
    DELETE FROM tblStreaks
    WHERE StreakID = p_StreakID;
END //
DELIMITER ;

-- SP 4: Find streak by ID
DELIMITER //
CREATE PROCEDURE sp_FindStreakById(IN p_StreakID INT)
BEGIN
    SELECT s.StreakID, u.Username, h.HabitName,
           s.CurrentStreak, s.LongestStreak,
           s.IsActive, s.LastUpdated
    FROM tblStreaks s
    JOIN tblUsers  u ON s.UserID  = u.UserID
    JOIN tblHabits h ON s.HabitID = h.HabitID
    WHERE s.StreakID = p_StreakID;
END //
DELIMITER ;

-- SP 5: Filter streaks by UserID
DELIMITER //
CREATE PROCEDURE sp_FilterStreakByUser(IN p_UserID INT)
BEGIN
    SELECT s.StreakID, u.Username, h.HabitName,
           s.CurrentStreak, s.LongestStreak,
           s.IsActive, s.LastUpdated
    FROM tblStreaks s
    JOIN tblUsers  u ON s.UserID  = u.UserID
    JOIN tblHabits h ON s.HabitID = h.HabitID
    WHERE s.UserID = p_UserID
    ORDER BY s.CurrentStreak DESC;
END //
DELIMITER ;

-- SP 6: Filter streaks by HabitID
DELIMITER //
CREATE PROCEDURE sp_FilterStreakByHabit(IN p_HabitID INT)
BEGIN
    SELECT s.StreakID, u.Username, h.HabitName,
           s.CurrentStreak, s.LongestStreak,
           s.IsActive, s.LastUpdated
    FROM tblStreaks s
    JOIN tblUsers  u ON s.UserID  = u.UserID
    JOIN tblHabits h ON s.HabitID = h.HabitID
    WHERE s.HabitID = p_HabitID
    ORDER BY s.CurrentStreak DESC;
END //
DELIMITER ;
