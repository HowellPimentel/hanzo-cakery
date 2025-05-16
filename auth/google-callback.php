<?php

session_start();

require '../vendor/autoload.php';
require '../includes/db.php';
require '../utils/load_env.php';
require '../utils/jwt.php';

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT']);
$client->setClientSecret($_ENV['GOOGLE_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT']);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userinfo = $oauth->userinfo->get();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$userinfo->email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "User not found";
        header("Location: ../auth/login.php");
        exit();
    }

    $token = jwt_token($user['id'], $user['firstname'], $user['lastname'], $user['role']);
    setcookie("token", $token, time() + 3600, "/", "", true, true);

    header("Location: ../home/home.php");
    exit();
} else {
    echo "No code returned from Google.";
}