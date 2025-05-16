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

// Build query
$query = "
    SELECT 
        u.*,
        COUNT(DISTINCT o.id) as total_orders,
        COALESCE(SUM(p.amount), 0) as total_spent
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    LEFT JOIN payment p ON o.payment_id = p.id
    GROUP BY u.id 
    ORDER BY u.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Hanzo Cakery | Customers Management</title>
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
                    <li>
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
                    <li class="active">
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
                    <h2>Customers Management</h2>
                    <div class="user-info">
                        <span>Welcome, <?php echo htmlspecialchars($decoded->data->name); ?></span>
                    </div>
                </div>
            </header>

            <div class="customers-table">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact Info</th>
                            <th>Address</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>
                                    <div class="customer-name">
                                        <strong><?php echo htmlspecialchars($customer['firstname'] . ' ' . $customer['lastname']); ?></strong>
                                        <span
                                            class="username">@<?php echo htmlspecialchars($customer['username']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <span><i class="fas fa-envelope"></i>
                                            <?php echo htmlspecialchars($customer['email']); ?></span>
                                        <span><i class="fas fa-phone"></i>
                                            <?php echo htmlspecialchars($customer['phone_number']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($customer['address']); ?></td>
                                <td><?php echo $customer['total_orders']; ?></td>
                                <td>₱<?php echo number_format($customer['total_spent'] ?? 0, 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <style>
        .customers-table {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .customers-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .customers-table th {
            background-color: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }

        .customers-table td {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .customers-table tr:hover {
            background-color: #f8f9fa;
        }

        .customer-name {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .customer-name strong {
            color: #333;
            font-size: 1rem;
        }

        .customer-name .username {
            color: #666;
            font-size: 0.9rem;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .contact-info span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .contact-info i {
            width: 16px;
            color: var(--primary-color);
        }

        .view-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            background-color: #3498db;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .view-btn:hover {
            background-color: #2980b9;
        }

        @media (max-width: 1024px) {
            .customers-table {
                padding: 1rem;
            }

            .customers-table th,
            .customers-table td {
                padding: 0.75rem;
            }
        }
    </style>

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

    <script>
        function viewCustomerDetails(customerId) {
            // Implement customer details modal or page
            alert('View customer details for ID: ' + customerId);
        }
    </script>
</body>

</html>