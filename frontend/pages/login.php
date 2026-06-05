<?php
/**
 * Login Page - Secure Authentication
 * Uses bcrypt password hashing (password_verify)
 * Author: Pratik Tamang
 */

session_start();

require_once '../../backend/config/database.php';

$message = "";

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $database = new Database();
        $db = $database->getConnection();
        
        // Get user by username only (don't compare password in SQL)
        $query = "SELECT UserID, Username, PasswordHash, IsAdmin FROM tblUsers 
                  WHERE Username = :username AND IsActive = 1";
        $stmt = $db->prepare($query);
        
        $username = htmlspecialchars(strip_tags($_POST['username']));
        $password = $_POST['password']; // Don't sanitize - password_verify handles it
        
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password against stored hash
            if (password_verify($password, $user['PasswordHash'])) {
                // Password correct - create session
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['is_admin'] = $user['IsAdmin'];
                
                // Redirect to main menu
                header("Location: ../index.html");
                exit();
            } else {
                // Wrong password - generic message for security
                $message = "<p style='color: red;'>Invalid username or password.</p>";
            }
        } else {
            // User not found - same generic message (prevents username enumeration)
            $message = "<p style='color: red;'>Invalid username or password.</p>";
        }
    } else {
        $message = "<p style='color: red;'>Please enter both username and password.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Consistency</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-container h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🎯 Consistency Login</h1>
        
        <?php echo $message; ?>
        
        <form method="POST" action="">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>
            
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            
            <button type="submit" style="width: 100%;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            <small>Demo: admin / admin123</small>
        </p>
    </div>
</body>
</html>