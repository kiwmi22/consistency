-- Test data for tblHabits
-- Developer: Sashi Khatri
-- Sprint 2

-- Make sure UserID 1 exists in tblUsers first
INSERT INTO tblHabits (UserID, HabitName, Description, TargetPerWeek, IsActive, CreatedDate)
VALUES
(1, 'Exercise',      'Go to the gym or run outside',    3, TRUE,  '2026-04-27'),
(1, 'Read',          'Read for at least 30 minutes',    5, TRUE,  '2026-04-27'),
(1, 'Meditate',      'Morning meditation for 10 mins',  7, TRUE,  '2026-04-27'),
(1, 'Drink Water',   'Drink 2 litres of water daily',   7, TRUE,  '2026-04-27'),
(1, 'Study',         'Study for college assignments',   5, FALSE, '2026-04-27');