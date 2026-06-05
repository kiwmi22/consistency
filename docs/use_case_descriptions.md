# USE CASE DESCRIPTIONS
# Component: Progress & Statistics
# Author: Juna Bhujel
# Sprint 3 - May 2026

================================================
USE CASE 1: View My Progress
================================================
Actor:         Registered User
Precondition:  User must be logged in
Postcondition: User sees all their streak data

Main Flow:
1. User logs in to Consistency system
2. User clicks My Progress in the menu
3. System gets all streaks for logged in user
4. System displays current and longest streaks
5. User views their progress

Variant Path:
- If no streaks exist show empty message

================================================
USE CASE 2: Find Streak by ID
================================================
Actor:         Administrator
Precondition:  Admin must be logged in
Postcondition: Specific streak record shown

Main Flow:
1. Admin clicks Find Streak in the menu
2. Admin enters a StreakID number
3. System checks StreakID is valid number
4. System searches database for that ID
5. System displays the matching record

Variant Path:
- If StreakID empty show validation error
- If ID not found show not found message

================================================
USE CASE 3: Filter Streaks by Habit
================================================
Actor:         Administrator
Precondition:  Admin must be logged in
Postcondition: Filtered records shown

Main Flow:
1. Admin goes to Filter Streaks page
2. Admin selects Habit ID from dropdown
3. Admin enters a HabitID number
4. System validates HabitID is a number
5. System returns all streaks for that habit
6. Admin views filtered results

Variant Path:
- If HabitID empty show validation error
- If no records found show empty message

================================================
USE CASE 4: Filter Streaks by Status
================================================
Actor:         Administrator
Precondition:  Admin must be logged in
Postcondition: Filtered records shown

Main Flow:
1. Admin goes to Filter Streaks page
2. Admin selects Active Status from dropdown
3. Admin ticks or unticks Active checkbox
4. System returns matching streaks
5. Admin views active or inactive streaks

Variant Path:
- If no records found show empty message

================================================
USE CASE 5: Filter Streaks by Minimum Length
================================================
Actor:         Administrator
Precondition:  Admin must be logged in
Postcondition: Filtered records shown

Main Flow:
1. Admin goes to Filter Streaks page
2. Admin selects Minimum Length from dropdown
3. Admin enters minimum number of days
4. System validates input is a number
5. System returns streaks of that length or more
6. Admin views results sorted by streak length

Variant Path:
- If input not numeric show validation error
- If no records found show empty message