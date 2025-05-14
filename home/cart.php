<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require '../utils/load_env.php';
require '../vendor/autoload.php';
require '../includes/db.php';

session_start();

if (!isset($_COOKIE['token'])) {
    $_SESSION['error'] = "Must logged in first.";
    header('Location: ../auth/login.php');
    exit();
}

$secret_key = $_ENV['JWT_SECRET'];
$decoded = JWT::decode($_COOKIE['token'], new Key($secret_key, 'HS256'));
$user_id = $decoded->data->user_id;

$stmt = $pdo->prepare("
SELECT
    c.*,
    u.*,
    ck.*
FROM
    cart c
JOIN
    users u ON c.user_id = u.id
JOIN
    cakes ck ON c.cake_id = ck.id
WHERE
    c.user_id = ?
");
$stmt->execute([$user_id]);
$cakes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Hanzo's Cakery | Cart</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1><a href="javascript:history.go(-1)"><i class="fa-solid fa-arrow-left"></i> </a></h1>
                <h1>Your Cart</h1>
            </div>
        </div>
    </header>
    <main>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message-success">
                <p><?= $_SESSION['success'] ?></p>
                <?php unset($_SESSION['success']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message-error">
                <p>
                <p><?= $_SESSION['error'] ?></p>
                <?php unset($_SESSION['error']) ?>
                </p>
            </div>
        <?php endif; ?>
        <div class="cart-container">
            <?php if (!empty($cakes)): ?>
                <div class="cart-content">
                    <h2>Your Cart</h2>
                    <?php foreach ($cakes as $cake): ?>
                        <div class="cart-card">
                            <img src="../assets/cakes/<?= $cake['image_path'] ?>" alt="<?= $cake['cake_name'] ?>">
                            <div class="cake-info">
                                <h3><?= $cake['cake_name'] ?></h3>
                                <p style="font-size: 0.8rem; margin-bottom: 1.5rem;"><?= $cake['cake_description'] ?></p>
                                <p style="font-weight: bold; color: var(--primary)">₱
                                    <?= number_format($cake['cake_price'], 2) ?>
                                </p>
                                <form action="removeCart.php" method="POST" style="margin-top: 1rem">
                                    <input type="hidden" name="cake_id" value="<?= $cake['cake_id'] ?>">
                                    <button type="submit" id="removeCart">Remove</button>
                                </form>
                                <div class="quantity-container">
                                    <div class="quantity-control" data-product-id="<?= $cake['cake_id'] ?>">
                                        <button type="button" class="quantity-btn decrease">−</button>
                                        <input type="number" id="quantity-<?= $cake['cake_id'] ?>" class="quantity-input"
                                            value="<?= $cake['quantity'] ?>" min="1">
                                        <button type="button" class="quantity-btn increase">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="invoice">
                    <div class="invoice-card">
                        <h3>Total:
                            <?php
                            $total = 0;
                            foreach ($cakes as $cake) {
                                $total += $cake['cake_price'] * $cake['quantity'];
                            }
                            echo '₱ ' . number_format($total, 2);
                            ?>
                        </h3>
                        <button form="form2" style="cursor: pointer">Place Order <i class="fa-solid fa-arrow-right"
                                style="font-size: 1rem;"></i> </button>
                    </div>
                </div>
                <div class="details">
                    <div class="detail-container">
                        <h2>Contact Details</h2>
                        <div class="detail-group">
                            <div class="left">
                                <p class="label">Phone Number:</p>
                                <p class="label">Name:</p>
                                <p class="label">Email:</p>
                            </div>
                            <div class="right">
                                <p><?= $cakes[0]['phone_number'] ?></p>
                                <p><?= $cakes[0]['firstname'] . ' ' . $cakes[0]['lastname'] ?></p>
                                <p><?= $cakes[0]['email'] ?></p>
                            </div>
                        </div>
                        <div class="payment-method">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="gcash"><img src="../assets/gcash.png" alt="" width="24"> GCash</label>
                                    <input type="radio" name="payment" id="gcash" value="GCash" checked>
                                </div>
                                <div class="form-group">
                                    <label for="bank"><i class="fa fa-bank"></i> Bank Transfer</label>
                                    <input type="radio" name="payment" id="bank" value="Bank">
                                </div>
                            </form>
                            <div class="payment-details">
                                <h2>Account Details</h2>
                                <form action="checkout.php" method="POST" id="form2">
                                    <div class="payment-group">
                                        <label for="">Account Name</label>
                                        <input type="text" name="account_name" placeholder="Account Name">
                                    </div>
                                    <div class="payment-group">
                                        <label for="">Account Number</label>
                                        <input type="number" name="account_number" placeholder="Account Number">
                                    </div>
                                </form>
                                <p class="payment-note">
                                    <strong>Notice: </strong> <?= $cakes[0]['firstname'] ?>, please ensure your phone number
                                    and selected payment method are correct before submitting your order.
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h1>No cake in cart</h1>
            <?php endif; ?>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to validate payment method
            function validatePayment(paymentMethod) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'validate-payment.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        try {
                            const response = JSON.parse(this.responseText);
                            if (response.success) {
                                console.log('Payment method validated successfully');
                            } else {
                                console.error('Payment validation error:', response.message);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                    }
                }
                xhr.send(`payment_method=${paymentMethod}`);
            }

            // Get all payment method radio buttons
            const paymentRadios = document.querySelectorAll('input[name="payment"]');

            // Add change event listener to each radio button
            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    validatePayment(this.value);
                });
            });

            // Validate initial payment method
            const initialPaymentMethod = document.querySelector('input[name="payment"]:checked');
            if (initialPaymentMethod) {
                validatePayment(initialPaymentMethod.value);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const quantityInputs = document.querySelectorAll('.quantity-input');

            quantityInputs.forEach(input => {
                const productId = input.closest('.quantity-control').getAttribute('data-product-id');
                const decreaseBtn = input.closest('.quantity-control').querySelector('.decrease');
                const increaseBtn = input.closest('.quantity-control').querySelector('.increase');

                function updateQuantity(newQuantity) {
                    // Validate the quantity
                    if (newQuantity < 1) newQuantity = 1;

                    // Update the input field
                    input.value = newQuantity;

                    // Send AJAX request to the server
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'update_quantity.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function () {
                        if (this.readyState === 4 && this.status === 200) {
                            try {
                                // Parse the response (expecting JSON)
                                const response = JSON.parse(this.responseText);

                                if (response.success) {
                                    console.log('Quantity updated successfully');

                                    // Format the total price with commas for thousands and 2 decimal places
                                    const formattedTotal = new Intl.NumberFormat('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(response.new_total);

                                    // Update the total dynamically
                                    const totalElement = document.querySelector('.invoice-card h3');
                                    totalElement.textContent = `Total: ₱ ${formattedTotal}`;
                                } else {
                                    console.error('Error updating quantity:', response.message);
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }
                        }
                    };
                    xhr.send(`product_id=${productId}&quantity=${newQuantity}`);
                }

                decreaseBtn.addEventListener('click', function () {
                    const currentValue = parseInt(input.value);
                    if (currentValue > 1) {
                        updateQuantity(currentValue - 1);
                    }
                });

                increaseBtn.addEventListener('click', function () {
                    const currentValue = parseInt(input.value);
                    updateQuantity(currentValue + 1);
                });

                // Handle direct input changes
                input.addEventListener('change', function () {
                    const newValue = parseInt(this.value);
                    if (!isNaN(newValue)) {
                        updateQuantity(newValue);
                    } else {
                        // Reset to 1 if the input is invalid
                        updateQuantity(1);
                    }
                });
            });
        });
    </script>
</body>

</html>