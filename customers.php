<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'config.php';
$currentPage = basename($_SERVER['PHP_SELF']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_customer'])) {
        $name = $conn->real_escape_string($_POST['customer_name']);
        $address = $conn->real_escape_string($_POST['address']);
        $phone = intval($_POST['phone_number']);

        $sql = "INSERT INTO customer (customer_name, address, phone_number) VALUES ('$name', '$address', $phone)";
        if ($conn->query($sql)) {
            $message = '<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> Customer added successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> Error: ' . $conn->error . ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    } elseif (isset($_POST['update_customer'])) {
        $id = intval($_POST['customer_id']);
        $name = $conn->real_escape_string($_POST['customer_name']);
        $address = $conn->real_escape_string($_POST['address']);
        $phone = intval($_POST['phone_number']);

        $sql = "UPDATE customer SET customer_name='$name', address='$address', phone_number=$phone WHERE customer_id=$id";
        if ($conn->query($sql)) {
            $message = '<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> Customer updated successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> Error: ' . $conn->error . ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    } elseif (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $sql = "DELETE FROM customer WHERE customer_id=$id";
        if ($conn->query($sql)) {
            $message = '<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> Customer deleted successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    }
}

$edit_customer = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM customer WHERE customer_id=$id");
    if ($result && $result->num_rows > 0) {
        $edit_customer = $result->fetch_assoc();
    }
}

$customers = $conn->query("SELECT * FROM customer ORDER BY customer_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Store Ltd</title>
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
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
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
        .form-control {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
        .table tbody tr {
            transition: background 0.3s;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .btn-edit, .btn-delete {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }
        .btn-edit {
            background: #3498db;
            color: white;
        }
        .btn-edit:hover {
            background: #2980b9;
            color: white;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        .btn-delete:hover {
            background: #c0392b;
            color: white;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                min-height: auto;
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
            .page-header {
                flex-direction: column;
                gap: 15px;
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
            <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="customers.php" class="active"><i class="fas fa-users"></i> <span>Customers</span></a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
            <li><a href="employees.php"><i class="fas fa-user-tie"></i> <span>Employees</span></a></li>
            <li><a href="audit.php"><i class="fas fa-history"></i> <span>Audit Log</span></a></li>
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
            <h2><i class="fas fa-users"></i> Manage Customers</h2>
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#customerModal">
                <i class="fas fa-plus-circle"></i> Add Customer
            </button>
        </div>

        <?php echo $message; ?>

        <?php if ($edit_customer): ?>
            <div class="card-form">
                <h4><i class="fas fa-edit"></i> Edit Customer</h4>
                <form method="POST">
                    <input type="hidden" name="customer_id" value="<?php echo $edit_customer['customer_id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" value="<?php echo $edit_customer['customer_name']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" class="form-control" name="phone_number" value="<?php echo $edit_customer['phone_number']; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="3" required><?php echo $edit_customer['address']; ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="update_customer" class="btn-add">
                            <i class="fas fa-save"></i> Update Customer
                        </button>
                        <a href="customers.php" class="btn-add" style="background: #95a5a6;">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-id-card"></i> ID</th>
                        <th><i class="fas fa-user"></i> Name</th>
                        <th><i class="fas fa-map-marker-alt"></i> Address</th>
                        <th><i class="fas fa-phone"></i> Phone</th>
                        <th><i class="fas fa-calendar"></i> Created</th>
                        <th><i class="fas fa-tools"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $customers->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['customer_id']; ?></td>
                            <td><?php echo $row['customer_name']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['phone_number']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['Created_at'])); ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['customer_id']; ?>" class="btn-edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="?delete=<?php echo $row['customer_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" class="form-control" name="phone_number" required>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_customer" class="btn-add">
                            <i class="fas fa-plus"></i> Add Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
