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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $cake_type = $_POST['cake_type'];
    $status = $_POST['status'];

    // Validate inputs
    $errors = [];
    if (empty($name)) {
        $errors[] = "Product name is required";
    }
    if (empty($description)) {
        $errors[] = "Product description is required";
    }
    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $errors[] = "Valid price is required";
    }
    if (empty($cake_type)) {
        $errors[] = "Cake type is required";
    }

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Only JPG, JPEG & PNG files are allowed";
        }
        if ($_FILES['image']['size'] > $max_size) {
            $errors[] = "File size must be less than 5MB";
        }
    } else {
        $errors[] = "Product image is required";
    }

    if (empty($errors)) {
        // Generate unique filename
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;
        $upload_path = '../assets/cakes/' . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            // Insert product into database
            $stmt = $pdo->prepare("INSERT INTO cakes (cake_name, cake_description, cake_price, image_path, cake_type) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $description, $price, $filename, $cake_type])) {
                header("Location: products.php");
                exit();
            } else {
                $errors[] = "Failed to add product";
            }
        } else {
            $errors[] = "Failed to upload image";
        }
    }
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
    <title>Hanzo Cakery | Add Product</title>
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
                    <h2>Add New Product</h2>
                    <div class="user-info">
                        <span>Welcome, <?php echo htmlspecialchars($decoded->data->name); ?></span>
                    </div>
                </div>
            </header>

            <div class="form-container">
                <?php if (!empty($errors)): ?>
                    <div class="error-messages">
                        <?php foreach ($errors as $error): ?>
                            <p class="error"><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="product-form">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name"
                            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"
                            required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0"
                            value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="cake_type">Cake Type</label>
                        <select id="cake_type" name="cake_type" required>
                            <option value="">Select Cake Type</option>
                            <option value="Cake in a Tub" <?php echo (isset($_POST['cake_type']) && $_POST['cake_type'] === 'Cake in a Tub') ? 'selected' : ''; ?>>Cake in a Tub</option>
                            <option value="Bento" <?php echo (isset($_POST['cake_type']) && $_POST['cake_type'] === 'Bento') ? 'selected' : ''; ?>>Bento</option>
                            <option value="Cake" <?php echo (isset($_POST['cake_type']) && $_POST['cake_type'] === 'Cake') ? 'selected' : ''; ?>>Cake</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg" required>
                        <small>Max file size: 5MB. Allowed formats: JPG, JPEG, PNG</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn">Add Product</button>
                        <a href="products.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <style>
        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error-messages {
            background-color: #fee;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .error {
            color: #e74c3c;
            margin: 0.5rem 0;
        }

        .product-form {
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group small {
            display: block;
            margin-top: 0.25rem;
            color: #666;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .submit-btn,
        .cancel-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .submit-btn:hover {
            background-color: #5aa4e4;
        }

        .cancel-btn {
            background-color: #e74c3c;
            color: white;
        }

        .cancel-btn:hover {
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