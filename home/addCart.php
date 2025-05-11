<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

session_start();

require '../utils/load_env.php';
require '../includes/db.php';
require '../vendor/autoload.php';

if(!isset($_COOKIE['token'])){
    $_SESSION['error'] = "Must logged in first.";
    header('Location: ../auth/login.php');
    exit();
}

$token = $_COOKIE['token'];

$decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cake = $_POST['cake_id'];
    $user = $decoded->data->user_id;

   $stmt = $pdo->prepare("INSERT INTO cart(cake_id, user_id) VALUES (?, ?)");
   $stmt->execute([$cake, $user]);


   $_SESSION['success'] = "Cake added to cart successfully!";
   header("Location: home.php");
   exit();
}