<?php
session_start();

require '../includes/db.php';
require '../utils/phpmailer.php';

if (!isset($_SESSION['code'])) {
    header("Location: forgot-password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $code = $_POST['code'];
    $code = (int) $code;

    if ($code === $_SESSION['code']) {
        $_SESSION['success'] = "Code correct";

        header("Location: new-password.php");
        unset($_SESSION['code']);
        exit();
    }

    $_SESSION['error'] = "Code incorrect";
    header("Location: enter-code.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/Logo.png">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/auth.css">
    <title>Hanzo Cakery | Forgot Password</title>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <div class="logo">
                <img src="../assets/icon/Logo.svg" alt="">
            </div>
            <h1>Enter code</h1>
            <p class="subheading">We've sent a code to your email.</p>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="message-success">
                    <p class="success"><?= $_SESSION['success'] ?></p>
                    <?php unset($_SESSION['success']) ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message-error">
                    <p class="error"><?= $_SESSION['error'] ?></p>
                    <?php unset($_SESSION['error']) ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <input type="number" placeholder="Enter the code" name="code" required>
                <button type="submit">Enter code</button>
            </form>
        </div>
    </div>
</body>

</html>