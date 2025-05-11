<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

session_start();

require '../utils/load_env.php';
require '../includes/db.php';
require '../vendor/autoload.php';

if (!isset($_COOKIE['token'])) {
    $_SESSION['error'] = "Must logged in first.";
    header('Location: ../auth/login.php');
    exit();
}

$token = $_COOKIE['token'];

$decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cake = $_POST['cake_id'];

    $stmt = $pdo->prepare('DELETE FROM cart WHERE cake_id = ?');
    $stmt->execute([$cake]);

    $_SESSION['success'] = "Cake removed from the cart";
    header("Location: cart.php");
    exit();
}