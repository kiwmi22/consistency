-- ============================================================
-- Test Data for tblHabitLogs
-- Developer: Abin Rai
-- Run AFTER tblUsers and tblHabits have test data
-- ============================================================

-- Temporary disable FK checks for safe insertion
SET FOREIGN_KEY_CHECKS = 0;

-- 8 rows covering all data types and scenarios
-- Mix of completed/incomplete, null/non-null notes,
-- varying durations and dates
INSERT INTO tblHabitLogs
    (HabitID, UserID, LogDate, IsCompleted, Notes, DurationMins)
VALUES
    (1, 1, '2026-04-14', TRUE,  'Felt great today',    30),
    (2, 1, '2026-04-14', FALSE, NULL,                   0),
    (1, 1, '2026-04-15', TRUE,  'Quick session',       20),
    (3, 2, '2026-04-15', TRUE,  NULL,                  45),
    (2, 2, '2026-04-16', TRUE,  'Did extra this time', 60),
    (1, 3, '2026-04-16', FALSE, 'Missed the morning',   0),
    (3, 1, '2026-04-17', TRUE,  NULL,                  15),
    (2, 3, '2026-04-17', TRUE,  'Completed on time',   25);

-- Re-enable FK checks
SET FOREIGN_KEY_CHECKS = 1;