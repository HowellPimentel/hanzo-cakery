<?php
session_start();

require '../includes/db.php';
require '../utils/phpmailer.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {
        $_SESSION['code'] = rand(100000, 999999);

        $subject = "Forgot Password | Hanzo's Cakery";
        $body = "Your password reset code is " . $_SESSION['code'];
        $alt = "Your password reset code is " . $_SESSION['code'];

        if (send_mail($user['email'], $user['firstname'], $user['lastname'], $subject, $body, $alt)) {
            $_SESSION['email'] = $user['email'];
            header("Location: enter-code.php");
            exit();
        }
    }

    $_SESSION['error'] = "User not found";
    header("Location: forgot-password.php");
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
            <h1>Forgot Password</h1>
            <p class="subheading">Enter your email address and we'll send you a code to reset your password</p>
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
                <input type="email" placeholder="Enter your email" name="email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            <p>Remember your password? <a href="login.php">Back to Login</a></p>
            <p>Don't have an account yet? <a href="signup.php">Sign up</a></p>
        </div>
    </div>
</body>

</html>