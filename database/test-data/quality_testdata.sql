-- ============================================
-- Quality Test Data for tblStreaks
-- Author: Juna Bhujel
-- Sprint 4 - Thursday 15th May 2026
-- ============================================

-- Clear existing data
DELETE FROM tblStreaks;

-- Insert quality test data covering all scenarios
INSERT INTO tblStreaks
(UserID, HabitID, CurrentStreak, LongestStreak, IsActive, LastUpdated)
VALUES
-- Active high streaks — User 1
(1, 1, 15, 20, TRUE,  '2026-05-11'),
(1, 2, 7,  7,  TRUE,  '2026-05-11'),

-- Mixed streaks — User 2
(2, 1, 0,  10, TRUE,  '2026-05-10'),
(2, 2, 3,  8,  TRUE,  '2026-05-11'),

-- Inactive streaks — User 3
(3, 1, 0,  5,  FALSE, '2026-05-01'),
(3, 2, 2,  12, TRUE,  '2026-05-11'),

-- Zero and low streaks — User 4
(4, 1, 1,  1,  TRUE,  '2026-05-11'),
(4, 2, 0,  0,  TRUE,  '2026-05-09'),

-- Admin user — User 5
(5, 1, 30, 30, TRUE,  '2026-05-11'),
(5, 2, 14, 25, TRUE,  '2026-05-11');

-- Verify data
SELECT * FROM tblStreaks ORDER BY UserID, HabitID;