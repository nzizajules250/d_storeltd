<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Ltd - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top left, #667eea 0%, transparent 38%),
                        radial-gradient(circle at bottom right, #764ba2 0%, transparent 36%),
                        #f5f7fa;
            color: #2c3e50;
        }
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 20;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid rgba(229, 231, 235, 0.9);
            backdrop-filter: blur(10px);
        }
        .top-navbar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: #2c3e50;
            text-decoration: none;
        }
        .top-navbar .brand i {
            color: #667eea;
            font-size: 1.3rem;
        }
        .top-navbar .nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .top-navbar .nav-actions a {
            border-radius: 999px;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .top-navbar .nav-actions .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .top-navbar .nav-actions .btn-secondary {
            background: #eef2ff;
            color: #4338ca;
            border: none;
        }
        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 120px 20px 40px;
        }
        .hero-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(44, 62, 80, 0.12);
            max-width: 1100px;
            width: 100%;
            overflow: hidden;
        }
        .hero-content {
            padding: 50px;
        }
        .hero-content h1 {
            font-size: clamp(2.75rem, 4vw, 4rem);
            font-weight: 800;
            letter-spacing: -0.05em;
            margin-bottom: 20px;
        }
        .hero-content p {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #52606d;
            max-width: 620px;
            margin-bottom: 30px;
        }
        .hero-buttons .btn {
            min-width: 160px;
            border-radius: 10px;
            padding: 14px 24px;
            font-weight: 600;
        }
        .hero-buttons .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .hero-visual {
            background: linear-gradient(180deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.06));
            padding: 40px;
            display: grid;
            place-items: center;
        }
        .hero-visual .card {
            border: none;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 45px rgba(44, 62, 80, 0.1);
        }
        .feature-list {
            display: grid;
            gap: 18px;
            margin-top: 20px;
        }
        .feature-item {
            background: #fbfcfd;
            border: 1px solid #e6ecf3;
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .feature-item i {
            color: #667eea;
            font-size: 1.4rem;
            width: 38px;
            text-align: center;
        }
        @media (max-width: 992px) {
            .hero {
                padding: 20px;
            }
            .hero-card {
                box-shadow: 0 18px 50px rgba(44, 62, 80, 0.12);
            }
            .hero-content,
            .hero-visual {
                padding: 32px;
            }
        }
        @media (max-width: 768px) {
            .hero-card {
                border-radius: 20px;
            }
            .hero {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="top-navbar">
        <a href="index.php" class="brand">
            <i class="fas fa-store"></i>
            <span>Store Ltd</span>
        </a>
        <div class="nav-actions">
            <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
            <a href="login.php" class="btn btn-primary">Sign In</a>
        </div>
    </div>
    <section class="hero">
        <div class="hero-card row g-0 align-items-center">
            <div class="hero-content col-lg-7">
                <div class="mb-4">
                    <span class="badge bg-gradient rounded-pill" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">Store Ltd</span>
                </div>
                <h1>Smart store management for performance-minded teams.</h1>
                <p>Securely manage customers, products, orders, and staff from one clean web dashboard. Designed for retail teams that need fast insights and intuitive controls.</p>
                <div class="hero-buttons d-flex flex-wrap gap-3">
                    <a href="login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Sign In
                    </a>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i> View Demo Dashboard
                    </a>
                </div>
                <div class="feature-list">
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Customer & order oversight</strong>
                            <div>Quick access to customer records and order history.</div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-boxes-stacked"></i>
                        <div>
                            <strong>Product inventory control</strong>
                            <div>Track product listings, stock counts, and pricing in one place.</div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-user-tie"></i>
                        <div>
                            <strong>Secure role-based access</strong>
                            <div>Only admin and manager users can sign in to the system.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-visual col-lg-5">
                <div class="card p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-store fa-3x text-primary"></i>
                    </div>
                    <h3 class="mb-3">Welcome to Store Ltd</h3>
                    <p class="text-muted">Your retail operations dashboard is ready. Use the button above to sign in and start managing customers, products, orders, and your team.</p>
                    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                        <span class="badge bg-light text-dark rounded-pill py-2 px-3">Customers</span>
                        <span class="badge bg-light text-dark rounded-pill py-2 px-3">Products</span>
                        <span class="badge bg-light text-dark rounded-pill py-2 px-3">Orders</span>
                        <span class="badge bg-light text-dark rounded-pill py-2 px-3">Employees</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
