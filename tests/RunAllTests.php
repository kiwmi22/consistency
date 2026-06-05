<?php
/**
 * Test Runner Dashboard
 * Central hub for running all test suites
 * Author: Pratik Tamang
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consistency Test Suite</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            padding: 30px;
            max-width: 1000px;
            margin: auto;
            background: #f4f4f4;
        }
        h1 { color: #2c3e50; text-align: center; }
        .subtitle {
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .test-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .test-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .test-card h2 {
            color: #3498db;
            margin: 0 0 10px 0;
        }
        .test-card p {
            color: #555;
            margin: 10px 0;
        }
        .test-card .stats {
            display: flex;
            gap: 20px;
            margin: 15px 0;
        }
        .test-card .stat {
            background: #ecf0f1;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #2980b9;
        }
        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .logs-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .log-item {
            padding: 8px 12px;
            margin: 5px 0;
            background: #f8f9fa;
            border-left: 3px solid #95a5a6;
            font-family: monospace;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <h1>🧪 Consistency Test Suite</h1>
    <p class="subtitle">Reminders & Notifications Component | Author: Pratik Tamang</p>
    
    <div class="info-box">
        <strong>📋 Test Coverage:</strong>
        <ul style="margin: 10px 0;">
            <li>✅ CRUD Operations (Add, List, Find, Update, Delete)</li>
            <li>✅ Filter Operations (by Habit, Status, User)</li>
            <li>✅ Input Validation (UserID, HabitID, Time, Message)</li>
            <li>✅ Security (XSS Prevention, SQL Injection Prevention)</li>
            <li>✅ Database Integrity (Connections, Tables, Procedures, Foreign Keys)</li>
        </ul>
    </div>
    
    <!-- Reminder Class Tests -->
    <div class="test-card">
        <h2>📋 1. Reminder Class Tests</h2>
        <p>Tests all CRUD operations for the Reminder class including Add, List, Find, Update, Delete, and Filter operations with edge cases.</p>
        <div class="stats">
            <span class="stat">15+ test cases</span>
            <span class="stat">CRUD coverage</span>
            <span class="stat">Edge cases</span>
        </div>
        <a href="ReminderTest.php" class="btn">▶️ Run Reminder Tests</a>
    </div>
    
    <!-- Validation Tests -->
    <div class="test-card">
        <h2>🔒 2. Validation & Security Tests</h2>
        <p>Tests input validation rules, boundary values, XSS prevention, and SQL injection protection across all form inputs.</p>
        <div class="stats">
            <span class="stat">30+ test cases</span>
            <span class="stat">Boundary testing</span>
            <span class="stat">Security checks</span>
        </div>
        <a href="ValidationTest.php" class="btn">▶️ Run Validation Tests</a>
    </div>
    
    <!-- Database Tests -->
    <div class="test-card">
        <h2>🗄️ 3. Database Integrity Tests</h2>
        <p>Verifies database connection, required tables exist, stored procedures are available, and foreign key constraints work correctly.</p>
        <div class="stats">
            <span class="stat">20+ test cases</span>
            <span class="stat">Schema validation</span>
            <span class="stat">FK integrity</span>
        </div>
        <a href="DatabaseTest.php" class="btn">▶️ Run Database Tests</a>
    </div>
    
    <!-- Test Logs -->
    <div class="logs-section">
        <h2>📁 Recent Test Logs</h2>
        <?php
        $logDir = __DIR__ . '/logs';
        if (file_exists($logDir)) {
            $logFiles = glob($logDir . '/*.log');
            
            if (count($logFiles) > 0) {
                echo "<p>Total log files: " . count($logFiles) . "</p>";
                
                // Sort by modification time (newest first)
                usort($logFiles, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                foreach (array_slice($logFiles, 0, 5) as $file) {
                    $filename = basename($file);
                    $size = filesize($file);
                    $modified = date('Y-m-d H:i:s', filemtime($file));
                    
                    // Count pass/fail in log
                    $content = file_get_contents($file);
                    $passes = substr_count($content, '[PASS]');
                    $fails = substr_count($content, '[FAIL]');
                    
                    echo "<div class='log-item'>";
                    echo "<strong>📄 $filename</strong><br>";
                    echo "Modified: $modified | Size: " . number_format($size) . " bytes<br>";
                    echo "<span style='color: #27ae60;'>✓ $passes passed</span> | ";
                    echo "<span style='color: #e74c3c;'>✗ $fails failed</span>";
                    echo "</div>";
                }
            } else {
                echo "<p>No test logs yet. Run some tests above to generate logs.</p>";
            }
        } else {
            echo "<p>Logs folder will be created when tests run.</p>";
        }
        ?>
    </div>
    
    <!-- Setup Instructions -->
    <div class="info-box" style="margin-top: 30px;">
        <strong>📚 How to Run Tests:</strong>
        <ol style="margin: 10px 0;">
            <li>Make sure XAMPP is running (Apache + MySQL)</li>
            <li>Ensure database is set up with the SQL script</li>
            <li>Click any "Run" button above to execute that test suite</li>
            <li>Check the logs folder for detailed results</li>
        </ol>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <a href="../frontend/index.html" class="btn" style="background: #95a5a6;">← Back to Main Application</a>
    </div>
</body>
</html>
