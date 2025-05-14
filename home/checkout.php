<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

session_start();

require '../includes/db.php';
require '../vendor/autoload.php';
require '../utils/load_env.php';

if (!isset($_COOKIE['token'])) {
    header("Location: ../auth/login.php");
    exit();
}

$token = $_COOKIE['token'];
$decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

$user_id = $decoded->data->user_id;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_name = $_POST['account_name'];
    $account_number = $_POST['account_number'];
    $transaction_id = uniqid('HANZOCAKERY#', true);

    $_SESSION['transaction_id'] = $transaction_id;

    if (empty($account_name) || empty($account_number)) {
        $_SESSION['error'] = "Please fill up all fields";
        header("Location: cart.php");
        exit();
    }

    if (strlen($account_number) !== 10) {
        $_SESSION['error'] = "Account number must be 10 digits";
        header("Location: cart.php");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO payment(payment_method, account_name, account_number) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['payment_method'], $account_name, $account_number]);

    $payment_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO orders(user_id, cake_id, transaction_id, payment_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $item['cake_id'], $transaction_id, $payment_id]);

        $order_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("DELETE FROM cart WHERE cake_id = ? AND user_id = ?");
        $stmt->execute([$item['cake_id'], $user_id]);
    }

    header("Location: invoice.php");
    exit();
}
