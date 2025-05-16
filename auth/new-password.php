<?php
session_start();

require '../includes/db.php';

if (!isset($_SESSION['email'])) {
    header("Location: forgot-password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: new-password.php");
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hash, $_SESSION['reset_email']]);
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
            <h1>Create New Password</h1>
            <p class="subheading">Please enter your new password below</p>
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
                <input type="password" placeholder="Enter new password" name="new_password" required>
                <input type="password" placeholder="Confirm new password" name="confirm_password" required>
                <button type="submit">Reset Password</button>
            </form>
            <p>Remember your password? <a href="login.php">Back to Login</a></p>
        </div>
    </div>
</body>

</html>