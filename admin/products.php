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

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $stmt = $pdo->prepare("DELETE FROM cakes WHERE id = ?");
    $stmt->execute([$product_id]);
    header("Location: products.php");
    exit();
}

// Handle product status update
if (isset($_POST['update_status'])) {
    $product_id = $_POST['product_id'];
    $new_status = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE cakes SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $product_id]);
    header("Location: products.php");
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
    <title>Hanzo Cakery | Products Management</title>
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
                    <li class="active">
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
                    <h2>Products Management</h2>
                    <div class="user-info">`
                        <span>Welcome, <?php echo htmlspecialchars($decoded->data->name); ?></span>
                    </div>
                </div>
            </header>

            <div class="content-header">
                <button class="add-product-btn" onclick="location.href='add-product.php'">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>

            <div class="products-table">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM cakes ORDER BY id DESC");
                        while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td><img src='../assets/cakes/" . htmlspecialchars($product['image_path']) . "' alt='" . htmlspecialchars($product['cake_name']) . "' class='product-thumbnail'></td>";
                            echo "<td>" . htmlspecialchars($product['cake_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($product['cake_description']) . "</td>";
                            echo "<td>" . htmlspecialchars($product['cake_type']) . "</td>";
                            echo "<td>₱" . number_format($product['cake_price'], 2) . "</td>";
                            echo "<td class='actions'>";
                            echo "<a href='edit-product.php?id=" . $product['id'] . "' class='edit-btn'><i class='fas fa-edit'></i></a>";
                            echo "<form method='POST' class='delete-form' onsubmit='return confirm(\"Are you sure you want to delete this product?\");'>";
                            echo "<input type='hidden' name='product_id' value='" . $product['id'] . "'>";
                            echo "<button type='submit' name='delete_product' class='delete-btn'><i class='fas fa-trash'></i></button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <style>
        .content-header {
            margin-bottom: 1.5rem;
        }

        .add-product-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
        }

        .add-product-btn:hover {
            background-color: #5aa4e4;
        }

        .products-table {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .edit-btn,
        .delete-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }

        .edit-btn {
            background-color: #3498db;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .edit-btn:hover {
            background-color: #2980b9;
        }

        .delete-btn:hover {
            background-color: #c0392b;
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

</body>

</html>