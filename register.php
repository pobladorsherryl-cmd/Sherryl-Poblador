<?php
require_once 'db.php'; // Connect to database
session_start();
 
// If already logged in, redirect to correct page
if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: user.php");
    }
    exit();
}
 
$error = "";
$success = "";
 
// Process registration form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
 
    if ($username === "" || $password === "") {
        $error = "Username and password are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if username already exists
        $checkSql = "SELECT id FROM users WHERE username = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
 
        if ($checkResult->num_rows > 0) {
            $error = "That username is already taken!";
        } else {
            $role = 'user'; // Default role for new registrations
 
            // id is AUTO_INCREMENT, so we don't set it manually
            $insertSql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("sss", $username, $password, $role);
 
            if ($insertStmt->execute()) {
                $success = "Account created successfully! You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Rose Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
 
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fcfcfc;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
 
        .top-navbar {
            background-color: #2d0a1e;
            color: #ffffff;
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
 
        .nav-brand {
            font-family: 'Montserrat', sans-serif;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #f8bbd0;
        }
 
        .nav-brand span { font-size: 24px; color: #c2185b; }
 
        .main-wrapper {
            display: flex;
            flex: 1;
            height: calc(100vh - 70px);
        }
 
        .brand-panel {
            flex: 1;
            background: linear-gradient(135deg, #880e4f 0%, #2d0a1e 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
            color: #ffffff;
        }
 
        .brand-content { position: relative; z-index: 10; max-width: 450px; }
 
        .brand-content h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 46px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(to right, #ffffff, #f8bbd0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
 
        .brand-content p { font-size: 16px; color: #e0b0c4; line-height: 1.6; font-weight: 300; }
 
        .form-panel {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            padding: 40px;
        }
 
        .form-box { width: 100%; max-width: 400px; animation: fadeIn 0.8s ease-out; }
 
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
 
        .form-header { margin-bottom: 40px; }
 
        .form-header h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2d0a1e;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 10px;
        }
 
        .form-header p { color: #888888; font-size: 14px; }
 
        .input-group { position: relative; margin-bottom: 25px; }
 
        .input-group input {
            width: 100%;
            padding: 22px 15px 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            background-color: #ffffff;
            outline: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
 
        .input-group input:focus {
            border-color: #c2185b;
            box-shadow: 0 4px 10px rgba(194, 24, 91, 0.15);
        }
 
        .input-group label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #999999;
            font-size: 16px;
            pointer-events: none;
            transition: all 0.3s ease;
            background-color: #ffffff;
            padding: 0 5px;
        }
 
        .input-group input:focus ~ label,
        .input-group input:not(:placeholder-shown) ~ label {
            top: 0;
            font-size: 12px;
            color: #c2185b;
            font-weight: 600;
        }
 
        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #c2185b 0%, #880e4f 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(194, 24, 91, 0.3);
            margin-top: 10px;
        }
 
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(194, 24, 91, 0.4);
            background: linear-gradient(135deg, #d81b60 0%, #c2185b 100%);
        }
 
        .btn-register:active { transform: translateY(-1px); }
 
        .error-box {
            background-color: #ffebee;
            border-left: 5px solid #c62828;
            color: #c62828;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
        }
 
        .success-box {
            background-color: #e8f5e9;
            border-left: 5px solid #2e7d32;
            color: #2e7d32;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
        }
 
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #888888;
        }
 
        .login-link a {
            color: #c2185b;
            font-weight: 600;
            text-decoration: none;
        }
 
        @media (max-width: 900px) {
            .brand-panel { display: none; }
            .form-panel { flex: 1; width: 100%; }
        }
    </style>
</head>
<body>
 
    <nav class="top-navbar">
        <div class="nav-brand"><span>🌹</span> SYSTEM PORTAL</div>
    </nav>
 
    <div class="main-wrapper">
 
        <div class="brand-panel">
            <div class="brand-content">
                <h1>Join Us.</h1>
                <p>Create your Rose Portal account to get started. It only takes a minute.</p>
            </div>
        </div>
 
        <div class="form-panel">
            <div class="form-box">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Fill in your details to register.</p>
                </div>
 
                <?php if ($error) { echo "<div class='error-box'>⚠️ " . htmlspecialchars($error) . "</div>"; } ?>
                <?php if ($success) { echo "<div class='success-box'>✅ " . htmlspecialchars($success) . "</div>"; } ?>
 
                <form method="POST" action="register.php">
 
                    <div class="input-group">
                        <input type="text" name="username" placeholder=" " required autocomplete="off">
                        <label>Username</label>
                    </div>
 
                    <div class="input-group">
                        <input type="password" name="password" placeholder=" " required>
                        <label>Password</label>
                    </div>
 
                    <div class="input-group">
                        <input type="password" name="confirm_password" placeholder=" " required>
                        <label>Confirm Password</label>
                    </div>
 
                    <button type="submit" class="btn-register">CREATE ACCOUNT</button>
                </form>
 
                <div class="login-link">
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
            </div>
        </div>
 
    </div>
 
</body>
</html>
 