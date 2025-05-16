<?php
session_start();

require '../includes/db.php';
require '../utils/jwt.php';

$user = decodeJWT($_COOKIE['token']);

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.*,
        c.*
    FROM
        orders o
    JOIN
        payment p ON o.payment_id = p.id
    JOIN
        cakes c ON o.cake_id = c.id
    WHERE user_id = ?
    ORDER BY o.order_date DESC
");
$stmt->execute([$user->data->user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group orders by transaction_id
$grouped_orders = [];
foreach ($orders as $order) {
    $transaction_id = $order['transaction_id'];
    if (!isset($grouped_orders[$transaction_id])) {
        $grouped_orders[$transaction_id] = [
            'transaction_id' => $transaction_id,
            'order_date' => $order['order_date'],
            'status' => $order['status'],
            'amount' => $order['amount'],
            'items' => []
        ];
    }
    $grouped_orders[$transaction_id]['items'][] = [
        'cake_name' => $order['cake_name'],
        'quantity' => $order['quantity'],
        'cake_price' => $order['cake_price'],
        'image_path' => $order['image_path']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icon/Circle_Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/orders.css">
    <title>My Orders - Hanzo Cakery</title>
    <style>
        .order-total {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .order-total p {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }

        .status-done {
            background-color: #27ae60;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-done::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
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

        .status-pending {
            background-color: #f39c12;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-pending::before {
            content: '\f017';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .order-header {
            background-color: #f8f9fa;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .order-info {
            display: flex;
            gap: 2rem;
        }

        .order-info div {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .order-info label {
            font-size: 0.8rem;
            color: #666;
        }

        .order-info span {
            font-weight: 500;
        }

        .order-items {
            padding: 1rem;
        }

        .order-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-details {
            flex: 1;
        }

        .item-details h3 {
            margin: 0 0 0.5rem;
            font-size: 1.1rem;
        }

        .item-details p {
            margin: 0.25rem 0;
            color: #666;
        }

        .item-price {
            font-weight: 500;
            color: var(--primary) !important;
        }

        .order-actions {
            padding: 1rem;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .mark-received-btn {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
        }

        .mark-received-btn i {
            font-size: 1rem;
        }

        .mark-received-btn:hover {
            background-color: #219a52;
        }

        .mark-received-btn:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }

        .status-label {
            background-color: #f8f9fa;
            color: #666;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            font-weight: 500;
            text-align: center;
            border: 1px solid #e9ecef;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: white;
            padding: 1rem 2rem;
            border-radius: 5px;
            display: none;
            align-items: center;
            gap: 0.5rem;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .toast.success {
            background-color: #27ae60;
        }

        .toast.error {
            background-color: #e74c3c;
        }

        .toast.show {
            display: flex;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .no-orders {
            text-align: center;
            padding: 3rem 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .no-orders i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1rem;
        }

        .no-orders h2 {
            margin: 0 0 0.5rem;
            color: #333;
        }

        .no-orders p {
            color: #666;
            margin: 0 0 1.5rem;
        }

        .no-orders a {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }

        .no-orders a:hover {
            background-color: var(--primary-dark);
        }
    </style>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1><a href="javascript:history.go(-1)"><i class="fa-solid fa-arrow-left"></i> </a></h1>
                <h1>My Orders</h1>
            </div>
        </div>
    </header>

    <main>
        <div class="orders-container">
            <?php if (!empty($grouped_orders)): ?>
                <?php foreach ($grouped_orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <div>
                                    <label>Order Number</label>
                                    <span style="font-size: 0.9rem;"><?= htmlspecialchars($order['transaction_id']) ?></span>
                                </div>
                                <div>
                                    <label>Date</label>
                                    <span><?= htmlspecialchars(date("F j, Y, h:i A", strtotime($order['order_date']))) ?></span>
                                </div>
                            </div>
                            <div class="order-status status-<?= strtolower($order['status']) ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </div>
                        </div>
                        <div class="order-items">
                            <?php
                            $subtotal = 0;
                            foreach ($order['items'] as $item):
                                $item_total = $item['cake_price'] * $item['quantity'];
                                $subtotal += $item_total;
                                ?>
                                <div class="order-item">
                                    <img src="../assets/cakes/<?= htmlspecialchars($item['image_path']) ?>"
                                        alt="<?= htmlspecialchars($item['cake_name']) ?>">
                                    <div class="item-details">
                                        <h3><?= htmlspecialchars($item['cake_name']) ?></h3>
                                        <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                                        <p class="item-price">&#8369; <?= htmlspecialchars(number_format($item['cake_price'], 2)) ?>
                                            × <?= htmlspecialchars($item['quantity']) ?> = &#8369;
                                            <?= htmlspecialchars(number_format($item_total, 2)) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="order-total">
                            <p>Total: &#8369; <?= htmlspecialchars(number_format($subtotal, 2)) ?></p>
                        </div>
                        <?php if (strtolower($order['status']) === 'done'): ?>
                            <div class="order-actions">
                                <button class="mark-received-btn"
                                    onclick="markAsReceived('<?= htmlspecialchars($order['transaction_id']) ?>')"
                                    id="btn-<?= htmlspecialchars($order['transaction_id']) ?>">
                                    <i class="fas fa-check"></i>
                                    Mark as Received
                                </button>
                            </div>
                        <?php elseif (strtolower($order['status']) === 'received'): ?>
                            <div class="order-actions">
                                <div class="status-label">Order has been received</div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <h2>No Orders Yet</h2>
                    <p>Start shopping to see your orders here</p>
                    <a href="cake-menu.php">Browse Cakes</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div class="toast" id="toast"></div>

    <script>
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;

            setTimeout(() => {
                toast.className = 'toast';
            }, 3000);
        }

        function markAsReceived(transactionId) {
            const button = document.getElementById(`btn-${transactionId}`);
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

            const formData = new FormData();
            formData.append('transaction_id', transactionId);

            fetch('update-order-status.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        // Update the status display
                        const statusElement = button.closest('.order-card').querySelector('.order-status');
                        statusElement.className = 'order-status status-received';
                        statusElement.textContent = 'Received';
                        // Remove the button
                        button.closest('.order-actions').remove();
                    } else {
                        showToast(data.message, 'error');
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-check"></i> Mark as Received';
                    }
                })
                .catch(error => {
                    showToast('An error occurred. Please try again.', 'error');
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-check"></i> Mark as Received';
                });
        }
    </script>
</body>

</html>