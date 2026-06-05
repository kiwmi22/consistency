# 🎯 Consistency - Habit Tracking System

A unified three-tier web application built by Team Consistency using PHP, MySQL, and Agile development methodology.

## 👥 Team Members & Components

| Member | Component | Tables |
|--------|-----------|--------|
| **Sri Krishna Shrestha** | User Account Management | `tblUsers` |
| **Sashi Khatri** | Habit Management | `tblHabits` |
| **Abin Rai** | Habit Logging | `tblHabitLogs` |
| **Juna Bhujel** | Progress & Streaks | `tblStreaks` |
| **Pratik Tamang** | Reminders & Notifications | `tblReminders` |

## 🚀 INSTALLATION (For Teachers & New Setup)

### Prerequisites
- XAMPP (Apache + MySQL + PHP 7.4+)
- Web browser

### Setup Steps

**1. Clone or Extract Project**
```
Copy the consistency folder to:
C:\xampp\htdocs\consistency
```

**2. Start XAMPP**
- Start Apache
- Start MySQL

**3. Create the Database**
- Open phpMyAdmin: http://localhost/phpmyadmin
- Click "SQL" tab (don't select any database)
- Open file: `database/scripts/consistency_db_setup.sql`
- Copy entire content and paste into SQL tab
- Click "Go"

This creates ALL 5 tables with stored procedures and test data.

**4. Hash the Passwords**
- Open browser: http://localhost/consistency/setup_passwords.php
- Wait for confirmation that passwords are hashed
- Delete the file after (for security)

**5. Access the Application**
- Main Menu: http://localhost/consistency/frontend/index.html
- Login with: `admin@consistency.com` / `Admin@1234`

## 🔐 Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@consistency.com | Admin@1234 |
| User | john@example.com | User@1234 |
| User | jane@example.com | User@1234 |
| User | mike@example.com | User@1234 |
| User | sarah@example.com | User@1234 (inactive) |

## 📁 Project Structure

```
consistency/
├── backend/
│   ├── config/
│   │   └── database.php          (Unified DB connection)
│   └── classes/
│       ├── User.php              (Sri Krishna)
│       ├── Habit.php             (Sashi)
│       ├── HabitLog.php          (Abin)
│       ├── Streak.php            (Juna)
│       └── Reminder.php          (Pratik)
├── frontend/
│   ├── css/
│   │   └── style.css             (Unified styles)
│   ├── pages/
│   │   ├── login.php, register.php, profile.php, Dashboard.php  (Sri Krishna)
│   │   ├── add-habit.php, habits.php, find-habit.php, etc.       (Sashi)
│   │   ├── checkin.php, view_logs.php, find_log.php, etc.        (Abin)
│   │   ├── streak/                                                 (Juna)
│   │   └── add_reminder.php, list_reminders.php, etc.            (Pratik)
│   └── index.html                (Main menu)
├── database/
│   ├── scripts/
│   │   └── consistency_db_setup.sql    (Master setup)
│   └── test-data/
│       └── (individual test data files)
├── tests/                        (Test files and logs)
├── docs/                         (Documentation)
├── setup_passwords.php           (One-time password setup)
└── README.md
```

## ✨ System Features

### 🔐 User Management (Sri Krishna)
- User registration with bcrypt password hashing
- Login with session management
- Profile management
- Admin dashboard

### ✅ Habit Management (Sashi)
- Create, edit, delete habits
- Set target frequency per week
- Search and filter habits

### 📝 Habit Logging (Abin)
- Daily check-ins
- Log completion status with notes
- Duration tracking
- View, filter, find logs

### 📊 Progress & Streaks (Juna)
- Current streak tracking
- Longest streak records
- Active streak status
- Progress visualization

### 🔔 Reminders & Notifications (Pratik)
- Add reminders for habits
- HTML5 time picker
- Enable/disable individual reminders
- Filter by habit, status, or user
- Custom message per reminder

## 🔒 Security Features

- **PDO Prepared Statements** - SQL injection prevention
- **bcrypt Password Hashing** - Secure password storage
- **XSS Protection** - htmlspecialchars sanitization
- **Session Management** - Authenticated access
- **CSRF Protection** - Form security
- **Input Validation** - Client + server side

## 🧪 Testing

Each component includes:
- Comprehensive test logs (48+ test cases per component)
- 100% pass rate
- Boundary value testing
- Security testing

## 📚 Documentation

- System Specification (Sprint 1)
- Use Case Diagrams & Descriptions
- Team Class Diagram
- Risk Register (12+ risks per component)
- Ethics Checklist (8 principles)
- Sprint Journals (Sprints 1-5)

## 🌐 GitHub Repository

**Repository:** https://github.com/kiwmi22/consistency

**Branches:**
- `main` - Production-ready code
- `develop` - Integration branch
- Feature branches per component

## 🎯 Development Methodology

**Agile Scrum with 5 Sprints:**
- Sprint 1: System Specification
- Sprint 2: Add, Edit, Delete + Login
- Sprint 3: Find & Filter
- Sprint 4: Testing & Documentation
- Sprint 5: Ethics Requirements (Final)

## 📞 Support

For any issues during setup, contact the team members listed above.

## 📄 License

Educational project - Niels Brock Copenhagen / DMU 2026
