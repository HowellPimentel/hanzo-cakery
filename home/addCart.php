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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cake = $_POST['cake_id'];
    $user = $decoded->data->user_id;

    $stmt = $pdo->prepare("SELECT * FROM cart WHERE cake_id = ? and user_id = ?");
    $stmt->execute([$cake, $user]);
    $exist = $stmt->fetch(PDO::FETCH_ASSOC);
    $quantity = $exist["quantity"];

    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? and cake_id = ?");
        $stmt->execute([$quantity + 1, $user, $cake]);
        $_SESSION['success'] = "Cake added to cart successfully!";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart(cake_id, user_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$cake, $user, 1]);
        $_SESSION['success'] = "Cake added to cart successfully!";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

}
