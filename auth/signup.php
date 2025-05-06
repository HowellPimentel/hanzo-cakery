<?php
session_start();
if (isset($_COOKIE['token'])) {
    header("Location: ../home/home.php");
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
    <title>Hanzo Cakery | Login</title>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <div class="logo">
                <img src="../assets/icon/Logo.svg" alt="">
            </div>
            <h1>Sign up</h1>
            <p>Sign up to satisfy your sweet tooth anytime</p>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="message-success">
                    <p><?= $_SESSION['success'] ?></p>
                    <?php unset($_SESSION['success']) ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message-error">
                    <p>
                    <p><?= $_SESSION['error'] ?></p>
                    <?php unset($_SESSION['error']) ?>
                    </p>
                </div>
            <?php endif; ?>
            <form action="signup_validate.php" method="POST">
                <div class="name-group">
                    <input type="text" placeholder="First Name" name="firstname">
                    <input type="text" placeholder="Last Name" name="lastname">
                </div>
                <input type="text" placeholder="Username" name="username">
                <input type="email" placeholder="Email" name="email">
                <input type="number" placeholder="Phone Number" name="phone_number">
                <input type="password" placeholder="Password" name="password">
                <input type="password" placeholder="Confirm Password" name="confirm_password">
                <button>Sign up</button>
            </form>
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>
</body>

</html>