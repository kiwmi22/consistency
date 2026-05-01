INSERT INTO tblHabitLogs 
    (HabitID, UserID, LogDate, IsCompleted, Notes, DurationMins)
VALUES
    (1, 1, '2026-04-14', TRUE,  'Felt great today',     30),
    (2, 1, '2026-04-14', FALSE, NULL,                    0),
    (1, 1, '2026-04-15', TRUE,  'Quick session',        20),
    (3, 2, '2026-04-15', TRUE,  NULL,                   45),
    (2, 2, '2026-04-16', TRUE,  'Did extra this time',  60),
    (1, 3, '2026-04-16', FALSE, 'Missed the morning',    0),
    (3, 1, '2026-04-17', TRUE,  NULL,                   15),
    (2, 3, '2026-04-17', TRUE,  'Completed on time',    25);