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

// Process login form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check user in database
    $sql = "SELECT id, username, password, role FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role']; // 'admin' or 'user'

        // Redirect based on role
        if ($row['role'] == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: user.php");
        }
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Login | Rose Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fcfcfc;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* --- Top Navigation Bar --- */
        .top-navbar {
            background-color: #2d0a1e;
            color: #ffffff;
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
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

        .nav-brand span {
            font-size: 24px;
            color: #c2185b;
        }

        /* --- Main Split Layout --- */
        .main-wrapper {
            display: flex;
            flex: 1;
            height: calc(100vh - 70px);
        }

        /* Left Side: Branding */
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

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        }

        .brand-content {
            position: relative;
            z-index: 10;
            max-width: 450px;
        }

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

        .brand-content p {
            font-size: 16px;
            color: #e0b0c4;
            line-height: 1.6;
            font-weight: 300;
        }

        /* Right Side: Login Form */
        .form-panel {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            padding: 40px;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2d0a1e;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #888888;
            font-size: 14px;
        }

        /* --- FIXED Floating Label Inputs --- */
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 22px 15px 10px 15px; /* Extra top padding so text doesn't overlap the label */
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            background-color: #ffffff;
            outline: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02); /* Subtle shadow */
        }

        .input-group input:focus {
            border-color: #c2185b;
            box-shadow: 0 4px 10px rgba(194, 24, 91, 0.15); /* Enhanced shadow on focus */
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
            background-color: #ffffff; /* REQUIRED: Masks the border when it moves up */
            padding: 0 5px; /* REQUIRED: Adds padding to the mask */
        }

        /* When the input is focused OR has text, move the label up onto the border */
        .input-group input:focus ~ label,
        .input-group input:not(:placeholder-shown) ~ label {
            top: 0;
            font-size: 12px;
            color: #c2185b;
            font-weight: 600;
        }

        /* Forgot Password Link */
        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: #888888;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #c2185b;
        }

        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #888888;
        }

        .register-link a {
            color: #c2185b;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-login {
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

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(194, 24, 91, 0.4);
            background: linear-gradient(135deg, #d81b60 0%, #c2185b 100%);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        /* Error Message */
        .error-box {
            background-color: #ffebee;
            border-left: 5px solid #c62828;
            color: #c62828;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: pulse 0.5s ease-in-out;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .brand-panel { display: none; }
            .form-panel { flex: 1; width: 100%; }
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="nav-brand">
            <span>🌹</span> SYSTEM PORTAL
        </div>
    </nav>

    <!-- Split Screen Layout -->
    <div class="main-wrapper">
        
        <!-- Left Side: Branding -->
        <div class="brand-panel">
            <div class="brand-content">
                <h1>Secure Access.</h1>
                <p>Welcome to the Rose Portal. Please log in to manage your dashboard, view user data, and access system settings.</p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="form-panel">
            <div class="login-box">
                <div class="login-header">
                    <h2>Welcome Back</h2>
                    <p>Please enter your details to sign in.</p>
                </div>
                
                <?php if($error) { echo "<div class='error-box'>⚠️ $error</div>"; } ?>

                <form method="POST" action="login.php">
                    
                    <div class="input-group">
                        <!-- The placeholder=" " is crucial for the CSS floating label trick -->
                        <input type="text" name="username" placeholder=" " required autocomplete="off" value="admin">
                        <label>Username</label>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" name="password" placeholder=" " required value="123">
                        <label>Password</label>
                    </div>

                    <div class="forgot-password">
                        <a href="#">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login">SIGN IN</button>
                </form>

                <div class="register-link">
                    Don't have an account? <a href="register.php">Sign Up</a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>