<?php
session_start();
require_once '../includes/db.php';
require_once '../utils/jwt.php';

if (!isset($_COOKIE['token'])) {
    header("Location: ../auth/login.php");
    exit();
}

$token = $_COOKIE['token'];
$decoded = decodeJWT($token);

if ($decoded->data->role !== 'admin') {
    header("Location: ../home/home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/Logo.png">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Hanzo Cakery | Admin Dashboard</title>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="../assets/icon/Logo.svg" alt="Hanzo's Cakery Logo">
                <h1>Admin Panel</h1>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li>
                        <a href="../home/home.php">
                            <i class="fas fa-arrow-left"></i>
                            Back to Home
                        </a>
                    </li>
                    <li class="active">
                        <a href="dashboard.php">
                            <i class="fas fa-home"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="products.php">
                            <i class="fas fa-cake"></i>
                            Products
                        </a>
                    </li>
                    <li>
                        <a href="orders.php">
                            <i class="fas fa-shopping-cart"></i>
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="customers.php">
                            <i class="fas fa-users"></i>
                            Customers
                        </a>
                    </li>
                    <li>
                        <a href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="admin-header">
                <div class="header-content">
                    <h2>Dashboard</h2>
                    <div class="user-info">
                        <span>Welcome, <?php echo htmlspecialchars($decoded->data->name); ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-shopping-cart"></i>
                    <div class="stat-info">
                        <h3>Total Orders</h3>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
                        $totalOrders = $stmt->fetchColumn();
                        ?>
                        <p><?php echo $totalOrders; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3>Total Customers</h3>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
                        $totalCustomers = $stmt->fetchColumn();
                        ?>
                        <p><?php echo $totalCustomers; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-cake"></i>
                    <div class="stat-info">
                        <h3>Total Products</h3>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM cakes");
                        $totalProducts = $stmt->fetchColumn();
                        ?>
                        <p><?php echo $totalProducts; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-money-bill-wave"></i>
                    <div class="stat-info">
                        <h3>Total Revenue</h3>
                        <?php
                        $stmt = $pdo->query("SELECT SUM(amount) FROM payment");
                        $totalRevenue = $stmt->fetchColumn();
                        ?>
                        <p>₱<?php echo number_format($totalRevenue, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="recent-orders">
                <h3>Recent Orders</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT o.transaction_id, u.firstname, u.lastname, c.cake_name as cake_name, p.amount, o.status, o.order_date
                            FROM orders o
                            JOIN users u ON o.user_id = u.id
                            JOIN cakes c ON o.cake_id = c.id
                            JOIN payment p ON o.payment_id = p.id
                            ORDER BY o.order_date DESC
                            LIMIT 5
                        ");
                        while ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($order['transaction_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['cake_name']) . "</td>";
                            echo "<td>₱" . number_format($order['amount'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($order['status']) . "</td>";
                            echo "<td>" . date('M d, Y', strtotime($order['order_date'])) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <style>
        .admin-nav li:first-child a {
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            color: #666;
        }

        .admin-nav li:first-child a:hover {
            background-color: #e9ecef;
            color: #333;
        }

        .admin-nav li:first-child i {
            margin-right: 0.5rem;
        }
    </style>
</body>

</html>