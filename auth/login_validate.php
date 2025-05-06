<?php

require '../includes/db.php';
require '../utils/jwt.php';

if (isset($_COOKIE['token'])) {
    header("Location: ../home/home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $token = jwt_token($user['id'], $user['firstname'], $user['lastname']);
        setcookie("token", $token, time() + 3600, "/", "", true, true);

        header("Location: ../home/home.php");
        exit();
    } else {
        $_SESSION['error'] = "Incorrect credentials! Please check your username and password, then try again.";
        header("Location: login.php");
        exit();
    }
}
