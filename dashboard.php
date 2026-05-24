<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'config.php';

$customers = (int) $conn->query("SELECT COUNT(*) AS count FROM customer")->fetch_assoc()['count'];
$products = (int) $conn->query("SELECT COUNT(*) AS count FROM products")->fetch_assoc()['count'];
$orders = (int) $conn->query("SELECT COUNT(*) AS count FROM orders")->fetch_assoc()['count'];
$employees = (int) $conn->query("SELECT COUNT(*) AS count FROM employees")->fetch_assoc()['count'];
$totalSales = (float) $conn->query("SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders")->fetch_assoc()['total'];
$averageOrder = (float) $conn->query("SELECT COALESCE(AVG(total_amount), 0) AS avg_order FROM orders")->fetch_assoc()['avg_order'];
$inventoryValue = (float) $conn->query("SELECT COALESCE(SUM(price * quantity), 0) AS value FROM products")->fetch_assoc()['value'];
$lowStockCount = (int) $conn->query("SELECT COUNT(*) AS count FROM products WHERE quantity < 10")->fetch_assoc()['count'];
$newOrdersToday = (int) $conn->query("SELECT COUNT(*) AS count FROM orders WHERE order_date = CURDATE()")->fetch_assoc()['count'];

$salesLabels = [];
$salesData = [];
$trend = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $trend[$date] = 0;
    $salesLabels[] = date('M j', strtotime($date));
}
$result = $conn->query("SELECT order_date, SUM(total_amount) AS revenue FROM orders WHERE order_date >= CURDATE() - INTERVAL 6 DAY GROUP BY order_date ORDER BY order_date");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $trend[$row['order_date']] = (float) $row['revenue'];
    }
}
$salesData = array_values($trend);

$categorySummary = [];
$categoryResult = $conn->query("SELECT COALESCE(categories, 'Uncategorized') AS category, COUNT(*) AS count FROM products GROUP BY category ORDER BY count DESC");
$totalCategories = 0;
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $totalCategories += (int) $row['count'];
        $categorySummary[] = $row;
    }
}
foreach ($categorySummary as &$category) {
    $category['percentage'] = $totalCategories > 0 ? ($category['count'] / $totalCategories) * 100 : 0;
}
unset($category);

$topProducts = $conn->query("SELECT p.product_name, COALESCE(SUM(od.quantity), 0) AS sold_qty FROM order_details od JOIN products p ON od.product_id = p.product_id GROUP BY od.product_id ORDER BY sold_qty DESC LIMIT 5");
$activity = $conn->query("SELECT username, action, table_name, action_time FROM audit_log ORDER BY action_time DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Store Ltd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
            margin-bottom: 30px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-custom h2 {
            color: #333;
            margin: 0;
            font-weight: 700;
        }
        .navbar-custom p {
            margin: 0;
            color: #6b7280;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card-stat {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-stat:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        .card-stat.customers { border-left-color: #1abc9c; }
        .card-stat.products { border-left-color: #f39c12; }
        .card-stat.sales { border-left-color: #e74c3c; }
        .card-stat.orders { border-left-color: #3498db; }
        .card-stat.employees { border-left-color: #9b59b6; }
        .card-stat.alerts { border-left-color: #ff6b6b; }
        .stat-icon {
            font-size: 34px;
            opacity: 0.9;
            margin-bottom: 18px;
        }
        .stat-number {
            font-size: 34px;
            font-weight: 700;
            color: #222;
            margin-bottom: 8px;
        }
        .stat-label {
            color: #6b7280;
            font-size: 14px;
        }
        .action-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 30px;
        }
        .action-card {
            background: white;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: 160px;
            justify-content: space-between;
        }
        .action-card h5 {
            margin-bottom: 8px;
            font-weight: 700;
        }
        .action-card p {
            margin: 0;
            color: #6b7280;
        }
        .action-card a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .analytics-panel {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .analytics-panel .card,
        .content-grid .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }
        .card-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
        }
        .card-header a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .summary-list {
            display: grid;
            gap: 16px;
        }
        .summary-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 18px 20px;
            background: #f8fbff;
            border-radius: 14px;
        }
        .summary-item strong {
            font-size: 0.95rem;
            color: #333;
        }
        .summary-item span {
            font-size: 0.9rem;
            color: #6b7280;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 20px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table thead th {
            background: #f8f9fa;
            color: #333;
            border-bottom: 1px solid #e9ecef;
            font-weight: 700;
            padding: 15px;
        }
        .table tbody td {
            padding: 14px 15px;
            vertical-align: middle;
            border-top: 1px solid #f1f3f5;
        }
        .badge-status {
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-sales {
            color: #e74c3c;
            background: #fde8e8;
        }
        .badge-stock {
            color: #1abc9c;
            background: #e8fbf2;
        }
        @media (max-width: 1100px) {
            .analytics-panel,
            .content-grid {
                grid-template-columns: 1fr;
            }
            .main-content {
                margin-left: 0;
                padding-top: 140px;
            }
            .top-navbar {
                position: relative;
                left: 0;
                right: 0;
                border-bottom: none;
                background: white;
            }
            .top-navbar .nav-links {
                gap: 10px;
            }
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
            <li>
                <a href="dashboard.php" class="active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="customers.php">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li>
                <a href="products.php">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="employees.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Employees</span>
                </a>
            </li>
            <li>
                <a href="audit.php">
                    <i class="fas fa-history"></i>
                    <span>Audit Log</span>
                </a>
            </li>
            <li>
                <a href="logout.php" style="margin-top: 30px;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
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
        <div class="navbar-custom">
            <div>
                <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
                <p>Live performance overview for your store operations.</p>
            </div>
            <div class="user-info">
                <span><?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?>)</span>
                <div class="user-avatar">
                    <?php echo strtoupper(substr(htmlspecialchars($_SESSION['username']), 0, 1)); ?>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card-stat customers">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number"><?php echo $customers; ?></div>
                <div class="stat-label">Customers</div>
            </div>
            <div class="card-stat products">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div class="stat-number"><?php echo $products; ?></div>
                <div class="stat-label">Products</div>
            </div>
            <div class="card-stat sales">
                <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-number">$<?php echo number_format($totalSales, 2); ?></div>
                <div class="stat-label">Total Sales</div>
            </div>
            <div class="card-stat orders">
                <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-number"><?php echo $orders; ?></div>
                <div class="stat-label">Orders</div>
            </div>
            <div class="card-stat employees">
                <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                <div class="stat-number"><?php echo $employees; ?></div>
                <div class="stat-label">Employees</div>
            </div>
            <div class="card-stat alerts">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-number"><?php echo $lowStockCount; ?></div>
                <div class="stat-label">Low Stock Items</div>
            </div>
        </div>

        <div class="action-box">
            <div class="action-card">
                <div>
                    <h5>Quick actions</h5>
                    <p class="text-muted">Jump straight into core workflows.</p>
                </div>
                <div class="d-flex flex-column gap-2">
                    <a href="customers.php"><i class="fas fa-users me-2"></i>Manage Customers</a>
                    <a href="products.php"><i class="fas fa-boxes-stacked me-2"></i>Manage Products</a>
                    <a href="register.php"><i class="fas fa-user-plus me-2"></i>Create User</a>
                </div>
            </div>
            <div class="action-card">
                <div>
                    <h5>Order insights</h5>
                    <p class="text-muted">Today's order activity and averages.</p>
                </div>
                <div class="summary-item">
                    <strong><?php echo $newOrdersToday; ?> New Orders Today</strong>
                    <span>Current pace</span>
                </div>
                <div class="summary-item">
                    <strong>$<?php echo number_format($averageOrder, 2); ?> Avg. Order</strong>
                    <span>Last 7 days</span>
                </div>
            </div>
            <div class="action-card">
                <div>
                    <h5>Inventory health</h5>
                    <p class="text-muted">Track total stock and alerts.</p>
                </div>
                <div class="summary-item">
                    <strong>$<?php echo number_format($inventoryValue, 2); ?></strong>
                    <span>Inventory value</span>
                </div>
                <div class="summary-item">
                    <strong><?php echo $products > 0 ? number_format($inventoryValue / $products, 2) : '0.00'; ?> $/item</strong>
                    <span>Average SKU value</span>
                </div>
            </div>
        </div>

        <div class="analytics-panel">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Sales Trend</h3>
                    <a href="orders.php">View orders</a>
                </div>
                <canvas id="salesChart" height="220"></canvas>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-pie-chart"></i> Category Breakdown</h3>
                    <a href="products.php">View products</a>
                </div>
                <div class="summary-list">
                    <?php if (!empty($categorySummary)): ?>
                        <?php foreach ($categorySummary as $category): ?>
                            <div class="summary-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($category['category']); ?></strong>
                                    <span><?php echo $category['count']; ?> products</span>
                                </div>
                                <span><?php echo round($category['percentage'], 1); ?>%</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="summary-item">
                            <strong>No categories available</strong>
                            <span>Add products to populate this view.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-trophy"></i> Top Products</h3>
                    <a href="products.php">Manage inventory</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Units Sold</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($topProducts && $topProducts->num_rows > 0): ?>
                                <?php while ($row = $topProducts->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                        <td><?php echo $row['sold_qty']; ?></td>
                                        <td><span class="badge-status badge-stock">Top seller</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3">No product sales data available yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Recent Activity</h3>
                    <a href="audit.php">View audit log</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Table</th>
                                <th>When</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($activity && $activity->num_rows > 0): ?>
                                <?php while ($row = $activity->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                                        <td><?php echo htmlspecialchars($row['table_name']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($row['action_time'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4">No recent activity recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesChart = document.getElementById('salesChart');
        const salesData = <?php echo json_encode($salesData); ?>;
        const salesLabels = <?php echo json_encode($salesLabels); ?>;

        new Chart(salesChart, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Revenue',
                    data: salesData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.18)',
                    tension: 0.36,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        ticks: {
                            callback: function(value) { return '$' + value; }
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
