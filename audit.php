<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'config.php';
$currentPage = basename($_SERVER['PHP_SELF']);

$audit_logs = $conn->query("SELECT * FROM audit_log ORDER BY action_time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log - Store Ltd</title>
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
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background: #f8f9fa;
            color: #333;
            font-weight: 700;
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .badge-table {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-user {
            background: #e3f2fd;
            color: #1976d2;
        }
        .badge-action {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        .badge-table-name {
            background: #e8f5e9;
            color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-store"></i>
            <span>Store Ltd</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> <span>Customers</span></a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
            <li><a href="employees.php"><i class="fas fa-user-tie"></i> <span>Employees</span></a></li>
            <li><a href="audit.php" class="active"><i class="fas fa-history"></i> <span>Audit Log</span></a></li>
            <li><a href="logout.php" style="margin-top: 30px;"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
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

    <div class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-history"></i> System Audit Log</h2>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-id-card"></i> ID</th>
                        <th><i class="fas fa-user"></i> User</th>
                        <th><i class="fas fa-tasks"></i> Action</th>
                        <th><i class="fas fa-database"></i> Table</th>
                        <th><i class="fas fa-clock"></i> Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $audit_logs->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['log_id']; ?></td>
                            <td><span class="badge-table badge-user"><?php echo htmlspecialchars($row['username'] ?? 'System'); ?></span></td>
                            <td><span class="badge-table badge-action"><?php echo htmlspecialchars($row['action']); ?></span></td>
                            <td><span class="badge-table badge-table-name"><?php echo htmlspecialchars($row['table_name']); ?></span></td>
                            <td><?php echo date('M d, Y H:i:s', strtotime($row['action_time'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
