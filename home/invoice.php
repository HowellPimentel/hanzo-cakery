<?php

session_start();

require '../includes/db.php';
require '../vendor/autoload.php';
require '../utils/load_env.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$token = $_COOKIE['token'];
$decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

$user_id = $decoded->data->user_id;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Berkshire+Swash&family=Inter:wght@300;400;500;600;700&family=Roboto+Slab:wght@300;400;500;600;700&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Roboto', sans-serif;
    }

    .container {
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background: #007bff;
    }

    .container h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .message {
        text-align: center;
    }

    .message p {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    .message p:last-child {
        font-style: italic;
        font-size: 0.95rem;
        font-weight: 600;
    }



    .info {
        margin-top: 1rem;
        text-align: center;

        p {
            font-size: 0.9rem;
        }

        a {
            color: inherit;
        }
    }

    .container>a {
        background: white;
        color: #007bff;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        text-decoration: none;
        margin-top: 1rem;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .container>a:hover {
        transform: translatey(-2px);
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }
</style>

<body>
    <div class="container">
        <h1>Successfully Ordered!</h1>
        <div class="message">
            <p>Thank you for your order. Your order has been successfully placed.</p>
            <p>Your order number is: <span class="order-number"><?= $_SESSION['transaction_id'] ?></span></p>
        </div>
        <div class="info">
            <p>Your order will be delivered to your address: <?= htmlspecialchars($user['address']) ?></p>
            <p>If you have any questions, please contact us at <a
                    href="mailto:support@hanzocakery.com">hanzoscakery@gmail.com</a>.</p>
        </div>
        <a href="home.php">Continue</a>

    </div>
</body>

</html>