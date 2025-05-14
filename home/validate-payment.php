<?php

session_start();

header('Content-Type: application/json'); // Set the content type to JSON

if (empty($_COOKIE['token'])) {
    header("Location: ../auth/login.php");
    exit();
}

$payment_method = $_POST['payment_method'] ?? '';

if (empty($payment_method)) {
    $_SESSION['error'] = "Please select payment method.";
    header("Location: cart.php");
    exit();
}

$_SESSION['payment_method'] = $payment_method;