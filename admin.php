
<?php
require_once 'db.php'; // Connect to database
session_start();

// Security Guard: Only admins can pass
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    die("Access Denied. You must be an administrator.");
}

$username = $_SESSION['username'];

// Fetch all users for the table
$users = [];
$result = $conn->query("SELECT id, username, role FROM users ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* --- CSS Variables (Rose Theme) --- */
        :root {
            --primary-dark: #2d0a1e;   /* Deep Burgundy */
            --primary-rose: #880e4f;   /* Deep Rose */
            --accent-rose: #c2185b;    /* Bright Rose */
            --light-rose: #fce4ec;     /* Soft Pink */
            --bg-color: #f8f9fa;       /* Light Grey Background */
            --white: #ffffff;
            --text-dark: #333333;
            --text-light: #888888;
        }

        /* Base Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* --- Top Navigation Bar --- */
        .top-navbar {
            background-color: var(--primary-dark);
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-left .logo-icon {
            font-size: 24px;
            color: var(--accent-rose);
        }

        .nav-left h1 {
            font-family: 'Montserrat', sans-serif;
            color: var(--white);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--light-rose);
            font-size: 14px;
        }

        .user-info i {
            font-size: 18px;
            background-color: rgba(255,255,255,0.1);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--white);
        }

        /* Styled Log Out Button */
        .btn-logout {
            background-color: transparent;
            color: var(--light-rose);
            border: 2px solid var(--accent-rose);
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background-color: var(--accent-rose);
            color: var(--white);
            box-shadow: 0 0 15px rgba(194, 24, 91, 0.5);
            transform: translateY(-2px);
        }

        /* --- Main Layout (Sidebar + Content) --- */
        .dashboard-wrapper {
            display: flex;
            flex: 1;
            height: calc(100vh - 70px);
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--white);
            border-right: 1px solid #e0e0e0;
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.02);
        }

        .sidebar a {
            text-decoration: none;
            color: var(--text-light);
            padding: 15px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background-color: var(--light-rose);
            color: var(--primary-rose);
            border-left: 4px solid var(--accent-rose);
        }

        .sidebar a.active {
            background-color: var(--light-rose);
            color: var(--primary-rose);
            border-left: 4px solid var(--accent-rose);
            font-weight: 600;
        }

        .sidebar a i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-family: 'Montserrat', sans-serif;
            color: var(--primary-dark);
            font-size: 28px;
            margin-bottom: 5px;
        }

        .page-header p {
            color: var(--text-light);
            font-size: 15px;
        }

        /* Dashboard Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background-color: var(--white);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 20px;
            border-left: 5px solid var(--accent-rose);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(194, 24, 91, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background-color: var(--light-rose);
            color: var(--primary-rose);
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
        }

        .stat-details h3 {
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-details p {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-dark);
            font-family: 'Montserrat', sans-serif;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-rose) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(136, 14, 79, 0.2);
        }

        .welcome-banner h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .welcome-banner p {
            font-size: 15px;
            color: var(--light-rose);
            font-weight: 300;
        }

        /* Users Table */
        .table-card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.04);
            padding: 25px;
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th {
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
            font-weight: 600;
            padding: 12px 15px;
            border-bottom: 2px solid var(--light-rose);
        }

        .users-table td {
            padding: 14px 15px;
            font-size: 14px;
            color: var(--text-dark);
            border-bottom: 1px solid #f0f0f0;
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .users-table tr:hover td {
            background-color: var(--light-rose);
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .role-badge.admin {
            background-color: var(--light-rose);
            color: var(--primary-rose);
        }

        .role-badge.user {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .no-users {
            text-align: center;
            color: var(--text-light);
            padding: 30px;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { display: none; } /* Hide sidebar on mobile */
            .main-content { padding: 20px; }
            .user-info span { display: none; } /* Hide username text on mobile */
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="nav-left">
            <i class="fa-solid fa-rose nav-left logo-icon"></i>
            <h1>Admin Portal</h1>
        </div>
        
        <div class="nav-right">
            <div class="user-info">
                <i class="fa-solid fa-user-shield"></i>
                <span>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
            </div>
            <!-- Styled Logout Button -->
            <a href="logout.php" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Log Out
            </a>
        </div>
    </nav>

    <!-- Main Layout -->
    <div class="dashboard-wrapper">
        
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="admin.php" class="active"><i class="fa-solid fa-users"></i> Manage Users</a>
            <a href="#"><i class="fa-solid fa-box"></i> Products</a>
            <a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <h2>Welcome back, <?php echo htmlspecialchars($username); ?>! 🌹</h2>
                <p>Here is a list of every registered user in the system.</p>
            </div>

            <div class="page-header">
                <h2>Manage Users</h2>
                <p>Total registered users: <?php echo count($users); ?></p>
            </div>

            <!-- Users Table -->
            <div class="table-card">
                <?php if (count($users) > 0): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($u['id']); ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td>
                                <span class="role-badge <?php echo htmlspecialchars($u['role']); ?>">
                                    <?php echo htmlspecialchars($u['role']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="no-users">No users found.</div>
                <?php endif; ?>
            </div>

        </main>
    </div>

</body>
</html>