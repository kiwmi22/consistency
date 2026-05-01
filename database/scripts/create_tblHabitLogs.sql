CREATE TABLE tblHabitLogs (
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