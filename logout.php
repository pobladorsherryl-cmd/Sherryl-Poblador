<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <!-- Auto redirect to login.php after 5 seconds -->
    <meta http-equiv="refresh" content="5;url=login.php">
    
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Matching the soft rose gradient from the login page */
            background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Logout Card */
        .logout-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(136, 14, 79, 0.15);
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        /* Decorative top accent line */
        .logout-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(to right, #c2185b, #e91e63, #f06292);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Icon Container */
        .icon-box {
            width: 80px;
            height: 80px;
            background-color: #fce4ec; /* Soft rose */
            color: #c2185b; /* Deep rose */
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
            margin: 0 auto 25px auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(194, 24, 91, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(194, 24, 91, 0); }
            100% { box-shadow: 0 0 0 0 rgba(194, 24, 91, 0); }
        }

        /* Typography */
        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #2d0a1e; /* Deep burgundy */
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        p {
            color: #888888;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Return Button */
        .btn-return {
            display: inline-block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #c2185b 0%, #880e4f 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(136, 14, 79, 0.25);
        }

        .btn-return:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(136, 14, 79, 0.35);
            background: linear-gradient(135deg, #d81b60 0%, #c2185b 100%);
        }

        .btn-return:active {
            transform: translateY(-1px);
        }

        /* Small text for auto-redirect notice */
        .redirect-notice {
            margin-top: 20px;
            font-size: 12px;
            color: #bdbdbd;
            font-weight: 400;
        }

        .redirect-notice i {
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <div class="logout-card">
        <!-- Icon -->
        <div class="icon-box">
            <i class="fa-solid fa-rose"></i>
        </div>
        
        <!-- Message -->
        <h2>You've Logged Out</h2>
        <p>Your session has been securely ended. Thank you for using the Rose Portal.</p>
        
        <!-- Button -->
        <a href="login.php" class="btn-return">RETURN TO LOGIN</a>
        
        <!-- Auto-redirect notice -->
        <div class="redirect-notice">
            <i class="fa-regular fa-clock"></i> Redirecting automatically in 5 seconds...
        </div>
    </div>

</body>
</html>