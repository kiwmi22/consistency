CREATE TABLE IF NOT EXISTS tblStreaks (
    StreakID        INT         PRIMARY KEY AUTO_INCREMENT,
    UserID          INT         NOT NULL,
    HabitID         INT         NOT NULL,
    CurrentStreak   INT         DEFAULT 0,
    LongestStreak   INT         DEFAULT 0,
    IsActive        BOOLEAN     DEFAULT TRUE,
    LastUpdated     DATE        NOT NULL,
    FOREIGN KEY (UserID)  REFERENCES tblUsers(UserID),
    FOREIGN KEY (HabitID) REFERENCES tblHabits(HabitID)
);