# COMPLETE TEST LOG
# Component: Progress & Statistics
# Author: Juna Bhujel
# Sprint 4 - Tuesday 13th May 2026

================================================
SECTION 1: DATABASE TESTS
================================================

Test ID:   DB01
Test:      Create tblStreaks table
Input:     Run tblStreaks.sql in phpMyAdmin
Expected:  Table created with 7 attributes
           2 foreign keys enforced
Actual:    
Pass/Fail: 

Test ID:   DB02
Test:      Insert valid test data
Input:     Run quality_testdata.sql
Expected:  10 rows inserted correctly
Actual:    
Pass/Fail: 

Test ID:   DB03
Test:      Foreign key — invalid UserID
Input:     INSERT with UserID = 999
Expected:  Error — foreign key constraint fails
Actual:    
Pass/Fail: 

Test ID:   DB04
Test:      Foreign key — invalid HabitID
Input:     INSERT with HabitID = 999
Expected:  Error — foreign key constraint fails
Actual:    
Pass/Fail: 

Test ID:   DB05
Test:      NULL constraint on LastUpdated
Input:     INSERT without LastUpdated value
Expected:  Error — column cannot be null
Actual:    
Pass/Fail: 

================================================
SECTION 2: STORED PROCEDURE TESTS
================================================

Test ID:   SP01
Test:      sp_AddStreak — valid inputs
Input:     CALL sp_AddStreak(1, 1, '2026-05-13')
Expected:  New record inserted CurrentStreak = 0
Actual:    
Pass/Fail: 

Test ID:   SP02
Test:      sp_EditStreak — valid inputs
Input:     CALL sp_EditStreak(1, 5, 10, TRUE, '2026-05-13')
Expected:  Record updated with new values
Actual:    
Pass/Fail: 

Test ID:   SP03
Test:      sp_DeleteStreak — valid ID
Input:     CALL sp_DeleteStreak(1)
Expected:  Record removed from tblStreaks
Actual:    
Pass/Fail: 

Test ID:   SP04
Test:      sp_FindStreakById — valid ID
Input:     CALL sp_FindStreakById(2)
Expected:  Returns matching streak record
Actual:    
Pass/Fail: 

Test ID:   SP05
Test:      sp_FindStreakById — invalid ID
Input:     CALL sp_FindStreakById(999)
Expected:  Returns empty result set
Actual:    
Pass/Fail: 

Test ID:   SP06
Test:      sp_FilterStreaksByHabit — valid ID
Input:     CALL sp_FilterStreaksByHabit(1)
Expected:  Returns all streaks for HabitID 1
Actual:    
Pass/Fail: 

Test ID:   SP07
Test:      sp_FilterStreaksByStatus — active
Input:     CALL sp_FilterStreaksByStatus(1)
Expected:  Returns only active streaks
Actual:    
Pass/Fail: 

Test ID:   SP08
Test:      sp_FilterStreaksByStatus — inactive
Input:     CALL sp_FilterStreaksByStatus(0)
Expected:  Returns only inactive streaks
Actual:    
Pass/Fail: 

Test ID:   SP09
Test:      sp_FilterStreaksByLength — min 3
Input:     CALL sp_FilterStreaksByLength(3)
Expected:  Only streaks of 3+ days returned
Actual:    
Pass/Fail: 

Test ID:   SP10
Test:      sp_UpdateStreak — increments
Input:     CALL sp_UpdateStreak(1, 1, '2026-05-13')
Expected:  CurrentStreak increments by 1
Actual:    
Pass/Fail: 

Test ID:   SP11
Test:      sp_ResetStreak — resets to zero
Input:     CALL sp_ResetStreak(1, 1, '2026-05-13')
Expected:  CurrentStreak = 0 LongestStreak unchanged
Actual:    
Pass/Fail: 

================================================
SECTION 3: PHP CLASS METHOD TESTS
================================================

Test ID:   PHP01
Test:      getStreakByUser() — valid UserID
Input:     getStreakByUser(1)
Expected:  Returns all streaks for User 1
Actual:    
Pass/Fail: 

Test ID:   PHP02
Test:      getStreakByUser() — invalid UserID
Input:     getStreakByUser(999)
Expected:  Returns empty result
Actual:    
Pass/Fail: 

Test ID:   PHP03
Test:      getLongestStreak() — valid IDs
Input:     getLongestStreak(1, 1)
Expected:  Returns numeric longest streak value
Actual:    
Pass/Fail: 

Test ID:   PHP04
Test:      getLongestStreak() — no record
Input:     getLongestStreak(99, 99)
Expected:  Returns 0
Actual:    
Pass/Fail: 

Test ID:   PHP05
Test:      updateStreak() — increments
Input:     updateStreak(1, 1) called 3 times
Expected:  CurrentStreak increases by 3
Actual:    
Pass/Fail: 

Test ID:   PHP06
Test:      updateStreak() — updates LongestStreak
Input:     updateStreak until new high reached
Expected:  LongestStreak updates to new high
Actual:    
Pass/Fail: 

Test ID:   PHP07
Test:      resetStreak() — resets to zero
Input:     resetStreak(1, 1)
Expected:  CurrentStreak = 0
Actual:    
Pass/Fail: 

Test ID:   PHP08
Test:      resetStreak() — LongestStreak unchanged
Input:     resetStreak after updateStreak
Expected:  LongestStreak stays the same
Actual:    
Pass/Fail: 

Test ID:   PHP09
Test:      addStreak() — valid inputs
Input:     addStreak(1, 2)
Expected:  New record inserted successfully
Actual:    
Pass/Fail: 

Test ID:   PHP10
Test:      editStreak() — valid inputs
Input:     editStreak(1, 5, 10, 1)
Expected:  Record updated correctly
Actual:    
Pass/Fail: 

Test ID:   PHP11
Test:      deleteStreak() — valid ID
Input:     deleteStreak(2)
Expected:  Record removed from database
Actual:    
Pass/Fail: 

Test ID:   PHP12
Test:      findStreakById() — valid ID
Input:     findStreakById(2)
Expected:  Returns correct record with names
Actual:    
Pass/Fail: 

Test ID:   PHP13
Test:      findStreakById() — invalid ID
Input:     findStreakById(999)
Expected:  Returns empty result
Actual:    
Pass/Fail: 

Test ID:   PHP14
Test:      filterByHabit() — valid HabitID
Input:     filterByHabit(1)
Expected:  Returns filtered records for Habit 1
Actual:    
Pass/Fail: 

Test ID:   PHP15
Test:      filterByStatus() — active
Input:     filterByStatus(1)
Expected:  Returns only active streaks
Actual:    
Pass/Fail: 

Test ID:   PHP16
Test:      filterByLength() — minimum 3
Input:     filterByLength(3)
Expected:  Returns streaks of 3+ days sorted
Actual:    
Pass/Fail: 

================================================
SECTION 4: VALIDATION TESTS
================================================

Test ID:   VAL01
Test:      validateStreak() — all valid
Input:     validateStreak(1, 1, 5, 10)
Expected:  Returns empty errors array
Actual:    
Pass/Fail: 

Test ID:   VAL02
Test:      validateStreak() — empty UserID
Input:     validateStreak('', 1, 5, 10)
Expected:  Returns UserID error message
Actual:    
Pass/Fail: 

Test ID:   VAL03
Test:      validateStreak() — empty HabitID
Input:     validateStreak(1, '', 5, 10)
Expected:  Returns HabitID error message
Actual:    
Pass/Fail: 

Test ID:   VAL04
Test:      validateStreak() — negative current
Input:     validateStreak(1, 1, -1, 10)
Expected:  Returns negative streak error
Actual:    
Pass/Fail: 

Test ID:   VAL05
Test:      validateStreak() — negative longest
Input:     validateStreak(1, 1, 5, -1)
Expected:  Returns negative streak error
Actual:    
Pass/Fail: 

Test ID:   VAL06
Test:      validateStreak() — current > longest
Input:     validateStreak(1, 1, 15, 10)
Expected:  Returns exceed longest error
Actual:    
Pass/Fail: 

================================================
SECTION 5: FRONTEND PAGE TESTS
================================================

Test ID:   FE01
Test:      progress.php — logged in user
Input:     Open with valid session UserID
Expected:  User streak data displayed correctly
Actual:    
Pass/Fail: 

Test ID:   FE02
Test:      progress.php — not logged in
Input:     Open without session
Expected:  Redirects to login page
Actual:    
Pass/Fail: 

Test ID:   FE03
Test:      admin_streaks.php — admin user
Input:     Open with IsAdmin = true
Expected:  All streak records displayed
Actual:    
Pass/Fail: 

Test ID:   FE04
Test:      add_streak.php — valid inputs
Input:     Enter valid UserID and HabitID
Expected:  New streak added success message
Actual:    
Pass/Fail: 

Test ID:   FE05
Test:      add_streak.php — empty inputs
Input:     Submit empty form
Expected:  Validation errors shown
Actual:    
Pass/Fail: 

Test ID:   FE06
Test:      edit_streak.php — valid inputs
Input:     Update CurrentStreak and LongestStreak
Expected:  Record updated success message
Actual:    
Pass/Fail: 

Test ID:   FE07
Test:      edit_streak.php — current > longest
Input:     Enter CurrentStreak higher than Longest
Expected:  Validation error shown
Actual:    
Pass/Fail: 

Test ID:   FE08
Test:      delete_streak.php — confirm delete
Input:     Click Yes Delete button
Expected:  Record removed redirect to admin
Actual:    
Pass/Fail: 

Test ID:   FE09
Test:      delete_streak.php — cancel
Input:     Click Cancel button
Expected:  Record not deleted return to admin
Actual:    
Pass/Fail: 

Test ID:   FE10
Test:      find_streak.php — valid ID
Input:     Enter StreakID = 1
Expected:  Correct streak record displayed
Actual:    
Pass/Fail: 

Test ID:   FE11
Test:      find_streak.php — empty input
Input:     Submit empty form
Expected:  Validation error shown
Actual:    
Pass/Fail: 

Test ID:   FE12
Test:      find_streak.php — invalid ID
Input:     Enter StreakID = 999
Expected:  No streak found message shown
Actual:    
Pass/Fail: 

Test ID:   FE13
Test:      filter_streak.php — filter by habit
Input:     Select Habit ID enter 1
Expected:  Filtered results shown with count
Actual:    
Pass/Fail: 

Test ID:   FE14
Test:      filter_streak.php — filter by status
Input:     Select Active Status tick checkbox
Expected:  Only active streaks shown
Actual:    
Pass/Fail: 

Test ID:   FE15
Test:      filter_streak.php — filter by length
Input:     Select Minimum Length enter 3
Expected:  Only streaks of 3+ days shown sorted
Actual:    
Pass/Fail: 

Test ID:   FE16
Test:      filter_streak.php — empty input
Input:     Select Habit ID leave input empty
Expected:  Validation error shown
Actual:    
Pass/Fail: