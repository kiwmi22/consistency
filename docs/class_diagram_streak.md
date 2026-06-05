# STREAK CLASS — Team Class Diagram Contribution
# Author: Juna Bhujel
# Component: Progress & Statistics
# Sprint 4 - Thursday 15th May 2026

================================================
CLASS: Streak
File:  backend/classes/Streak.php
Table: tblStreaks
================================================

ATTRIBUTES:
- conn : mysqli (private)

CONSTRUCTOR:
+ __construct(db: mysqli) : void

SPRINT 1 METHODS:
+ getStreakByUser(userID: int) : mysqli_result
+ getLongestStreak(userID: int, habitID: int) : int
+ updateStreak(userID: int, habitID: int) : bool
+ resetStreak(userID: int, habitID: int) : bool
+ getAllStreaksAdmin() : mysqli_result

SPRINT 2 METHODS:
+ addStreak(userID: int, habitID: int) : bool
+ editStreak(streakID: int, currentStreak: int,
            longestStreak: int,
            isActive: bool) : bool
+ deleteStreak(streakID: int) : bool
+ findStreakById(streakID: int) : mysqli_result
+ filterStreakByUser(userID: int) : mysqli_result
+ filterStreakByHabit(habitID: int) : mysqli_result
+ validateStreak(userID: int, habitID: int,
                currentStreak: int,
                longestStreak: int) : array

SPRINT 3 METHODS:
+ filterByHabit(habitID: int) : mysqli_result
+ filterByStatus(isActive: bool) : mysqli_result
+ filterByLength(minLength: int) : mysqli_result

RELATIONSHIPS:
- Streak uses tblUsers via UserID FK
- Streak uses tblHabits via HabitID FK
- Streak manages tblStreaks as primary table
- Streak called by progress.php
- Streak called by admin_streaks.php
- Streak called by add_streak.php
- Streak called by edit_streak.php
- Streak called by delete_streak.php
- Streak called by find_streak.php
- Streak called by filter_streak.php