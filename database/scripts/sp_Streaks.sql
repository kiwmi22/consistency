-- ============================================
-- Stored Procedures for tblStreaks
-- Author: Juna Bhujel
-- Sprint 1 - Wednesday 22nd April 2026
-- ============================================

-- SP 1: Add new streak
DELIMITER //
CREATE PROCEDURE sp_AddStreak(
    IN p_UserID     INT,
    IN p_HabitID    INT,
    IN p_LastUpdated DATE
)
BEGIN
    INSERT INTO tblStreaks
    (UserID, HabitID, CurrentStreak, LongestStreak, IsActive, LastUpdated)
    VALUES (p_UserID, p_HabitID, 1, 1, TRUE, p_LastUpdated);
END //
DELIMITER ;

-- SP 2: Get all streaks for a user
DELIMITER //
CREATE PROCEDURE sp_GetStreakByUser(IN p_UserID INT)
BEGIN
    SELECT s.StreakID, h.HabitName,
           s.CurrentStreak, s.LongestStreak,
           s.IsActive, s.LastUpdated
    FROM tblStreaks s
    JOIN tblHabits h ON s.HabitID = h.HabitID
    WHERE s.UserID = p_UserID
    ORDER BY s.CurrentStreak DESC;
END //
DELIMITER ;

-- SP 3: Get streak by ID
DELIMITER //
CREATE PROCEDURE sp_GetStreakById(IN p_StreakID INT)
BEGIN
    SELECT * FROM tblStreaks
    WHERE StreakID = p_StreakID;
END //
DELIMITER ;

-- SP 4: Update streak
DELIMITER //
CREATE PROCEDURE sp_UpdateStreak(
    IN p_UserID     INT,
    IN p_HabitID    INT,
    IN p_Today      DATE
)
BEGIN
    DECLARE v_Current INT;
    DECLARE v_Longest INT;

    SELECT CurrentStreak, LongestStreak
    INTO v_Current, v_Longest
    FROM tblStreaks
    WHERE UserID = p_UserID AND HabitID = p_HabitID;

    SET v_Current = v_Current + 1;

    IF v_Current > v_Longest THEN
        SET v_Longest = v_Current;
    END IF;

    UPDATE tblStreaks
    SET CurrentStreak = v_Current,
        LongestStreak = v_Longest,
        LastUpdated   = p_Today
    WHERE UserID  = p_UserID
      AND HabitID = p_HabitID;
END //
DELIMITER ;

-- SP 5: Reset streak
DELIMITER //
CREATE PROCEDURE sp_ResetStreak(
    IN p_UserID     INT,
    IN p_HabitID    INT,
    IN p_Today      DATE
)
BEGIN
    UPDATE tblStreaks
    SET CurrentStreak = 0,
        LastUpdated   = p_Today
    WHERE UserID  = p_UserID
      AND HabitID = p_HabitID;
END //
DELIMITER ;