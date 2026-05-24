<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);

require 'config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
$username = $_SESSION['username'] ?? 'Guest';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($conn->real_escape_string($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'user';

    if ($username === '' || $password === '' || $confirm_password === '') {
        $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> Please complete all fields. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    } elseif ($password !== $confirm_password) {
        $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> Passwords do not match. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    } elseif (strlen($password) < 8) {
        $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> Password must be at least 8 characters. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    } else {
        $check = $conn->query("SELECT user_id FROM user WHERE user_name = '$username'");
        if ($check && $check->num_rows > 0) {
            $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> Username is already taken. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO user (user_name, password, role) VALUES ('$username', '$hash', '$role')";
            if ($conn->query($insert)) {
                $message = '<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> User registered successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> Error: ' . $conn->error . ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User - Store Ltd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            overflow-y: auto;
        }
        .sidebar-brand {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .sidebar-brand i {
            font-size: 30px;
        }
        .sidebar-menu {
            list-style: none;
        }
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        .sidebar-menu a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 14px;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .sidebar-menu i {
            width: 20px;
            text-align: center;
        }
        .top-navbar {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 60px;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 0 24px;
            z-index: 10;
            transition: left 0.3s ease;
        }
        .top-navbar .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #2c3e50;
        }
        .top-navbar .nav-links {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            align-items: center;
        }
        .top-navbar .nav-links a {
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 999px;
            transition: background 0.3s, color 0.3s;
        }
        .top-navbar .nav-links a.active,
        .top-navbar .nav-links a:hover {
            background: #eef2ff;
            color: #4338ca;
        }
        .top-navbar .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .top-navbar .user-pill {
            background: #eef2ff;
            color: #4338ca;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .main-content {
            margin-left: 260px;
            padding: 100px 20px 20px;
        }
        body.logged-out .main-content {
            margin-left: 0;
        }
        .page-header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-header h2 {
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }
        .card-form {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 14px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .info-box {
            background: #eef4ff;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 30px;
            border: 1px solid #d8e2ff;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body<?php echo $isLoggedIn ? '' : ' class="logged-out"'; ?>>
    <?php if ($isLoggedIn): ?>
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-store"></i>
            <span>Store Ltd</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="register.php" class="active"><i class="fas fa-user-plus"></i> Register User</a></li>
            <li><a href="logout.php" style="margin-top: 30px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="top-navbar">
        <div class="nav-brand">
            <i class="fas fa-store"></i>
            <span>Store Ltd</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
            <a href="customers.php" class="<?php echo $currentPage === 'customers.php' ? 'active' : ''; ?>">Customers</a>
            <a href="products.php" class="<?php echo $currentPage === 'products.php' ? 'active' : ''; ?>">Products</a>
            <a href="orders.php" class="<?php echo $currentPage === 'orders.php' ? 'active' : ''; ?>">Orders</a>
            <a href="employees.php" class="<?php echo $currentPage === 'employees.php' ? 'active' : ''; ?>">Employees</a>
            <a href="audit.php" class="<?php echo $currentPage === 'audit.php' ? 'active' : ''; ?>">Audit</a>
        </div>
        <div class="nav-actions">
            <span class="user-pill"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-primary">Logout</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-user-plus"></i> Register New User</h2>
            <span>Register a new user</span>
        </div>

        <?php echo $message; ?>

        <div class="card-form">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                </div>

                <input type="hidden" name="role" value="user">

                <button type="submit" class="btn-submit">Create Account</button>
            </form>
        </div>

        <div class="info-box">
            <h5><i class="fas fa-shield-alt"></i> Security note</h5>
            <p>Only administrators can access this page. Use strong passwords and assign roles carefully to protect your store management dashboard.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
