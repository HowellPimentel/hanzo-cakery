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

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && isset($_POST['transaction_id'])) {
    $new_status = $_POST['status'];
    $transaction_id = $_POST['transaction_id'];

    // Validate status
    $valid_statuses = ['pending', 'done', 'received'];
    if (in_array($new_status, $valid_statuses)) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE transaction_id = ?");
            $stmt->execute([$new_status, $transaction_id]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . urlencode($status_filter) . "&date=" . urlencode($date_filter));
            exit();
        } catch (PDOException $e) {
            $error = "Failed to update order status";
        }
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query
$query = "
    SELECT o.*, u.firstname, u.lastname, u.email, u.phone_number, c.cake_name as cake_name, c.image_path as cake_image, p.amount, p.payment_method
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN cakes c ON o.cake_id = c.id
    JOIN payment p ON o.payment_id = p.id
    WHERE 1=1
";

$params = [];

if ($status_filter) {
    $query .= " AND o.status = ?";
    $params[] = $status_filter;
}

if ($date_filter) {
    $query .= " AND DATE(o.created_at) = ?";
    $params[] = $date_filter;
}

$query .= " ORDER BY o.order_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Hanzo Cakery | Orders Management</title>
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
                    <li class="active">
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
                    <h2>Orders Management</h2>
                    <div class="user-info">
                        <span>Welcome, <?php echo htmlspecialchars($decoded->data->name); ?></span>
                    </div>
                </div>
            </header>

            <div class="filters">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status-filter">
                            <option value="">All Status</option>
                            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="done" <?= $status_filter === 'done' ? 'selected' : '' ?>>Done</option>
                            <option value="received" <?= $status_filter === 'received' ? 'selected' : '' ?>>Received
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" value="<?php echo $date_filter; ?>">
                    </div>
                    <button type="submit" class="filter-btn">Apply Filters</button>
                    <?php if ($status_filter || $date_filter): ?>
                        <a href="orders.php" class="clear-btn">Clear Filters</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><span
                                        class="transaction-id"><?php echo htmlspecialchars(substr($order['transaction_id'], 11)); ?></span>
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <strong><?php echo htmlspecialchars($order['firstname'] . ' ' . $order['lastname']); ?></strong>
                                        <span><?php echo htmlspecialchars($order['email']); ?></span>
                                        <span><?php echo htmlspecialchars($order['phone_number']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="product-info">
                                        <img src="../assets/cakes/<?php echo htmlspecialchars($order['cake_image']); ?>"
                                            alt="<?php echo htmlspecialchars($order['cake_name']); ?>"
                                            class="product-thumbnail">
                                        <span><?php echo htmlspecialchars($order['cake_name']); ?></span>
                                    </div>
                                </td>
                                <td>₱<?php echo number_format($order['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td>
                                    <form action="" method="POST" class="status-form">
                                        <input type="hidden" name="transaction_id"
                                            value="<?= htmlspecialchars($order['transaction_id']) ?>">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>
                                                Pending</option>
                                            <option value="done" <?= $order['status'] === 'done' ? 'selected' : '' ?>>Done
                                            </option>
                                            <option value="received" <?= $order['status'] === 'received' ? 'selected' : '' ?>>
                                                Received</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <style>
        .filters {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .filter-form {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .filter-btn,
        .clear-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .filter-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .filter-btn:hover {
            background-color: #5aa4e4;
        }

        .clear-btn {
            background-color: #95a5a6;
            color: white;
        }

        .clear-btn:hover {
            background-color: #7f8c8d;
        }

        .orders-table {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .customer-info {
            display: flex;
            flex-direction: column;
        }

        .customer-info span {
            color: #666;
            font-size: 0.9rem;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .status-form {
            margin: 0;
        }

        .status-form select {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 20px;
            background-color: white;
            cursor: pointer;
            font-weight: 600;
            min-width: 120px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.7rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        .status-form select:hover {
            border-color: #3498db;
        }

        .status-form select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .status-form select option {
            padding: 0.5rem;
            font-weight: 600;
        }

        .status-form select option[value="pending"] {
            color: #f39c12;
        }

        .status-form select option[value="done"] {
            color: #27ae60;
        }

        .status-form select option[value="received"] {
            color: #3498db;
        }

        .status-pending {
            color: #f39c12;
        }

        .status-done {
            color: #27ae60;
        }

        .status-received {
            background-color: #3498db;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-received::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        .view-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            background-color: #3498db;
            color: white;
            cursor: pointer;
        }

        .view-btn:hover {
            background-color: #2980b9;
        }

        .transaction-id {
            font-size: 0.9rem;
            color: #666;
            font-family: monospace;
        }

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
        function viewOrderDetails(orderId) {
            // Implement order details modal or page
            alert('View order details for ID: ' + orderId);
        }
    </script>
</body>

</html>