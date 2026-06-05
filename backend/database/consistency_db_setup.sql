-- ============================================
-- CONSISTENCY HABIT TRACKER - DATABASE SETUP
-- Author: Pratik Tamang
-- Component: Reminders & Notifications


CREATE DATABASE IF NOT EXISTS consistency_db;
USE consistency_db;

-- Drop tables if they exist
DROP TABLE IF EXISTS tblStreaks;
DROP TABLE IF EXISTS tblHabitLogs;
DROP TABLE IF EXISTS tblReminders;
DROP TABLE IF EXISTS tblHabits;
DROP TABLE IF EXISTS tblUsers;

-- [Rest of the SQL from before - the complete script]
-- (all tables, test data, stored procedures)