<?php
session_start();
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel = "stylesheet" href = "styles/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
        <title>Admin Login</title>
</head>

<body>
    <!-- Login credentials card -->
    <div class = "container">
        <div class = "card">
            <img src = "img/logo.png" alt = "This is Logo">
            <h1>Login</h1>
            <p>Please enter your username and password</p>

            <?php if (isset($_SESSION['error'])): ?>
                    <p id="alertMessage" style="border: 1px solid #F74141; padding: 0.5rem 1rem !important; border-radius: 0.25rem; display: block; color: #F74141;">
                    <?= $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                    </p>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <p id="alertMessage" style="border: 1px solid #77DD77; padding: 0.5rem 1rem !important; border-radius: 0.25rem; display: block; color: #77DD77;">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                    </p>
                <?php endif; ?>

                <form action="login-validate.php" method="POST">
                <input  type = "text" placeholder = "Username" name="username">
                <input  type = "password" placeholder = "Password" name="password">
                <button type="submit">Login</button>
                
                <p>Dont have an account? <a href="../Ochavillo/signup.php"> Sign-Up</a> </p>
                <p>Fogot Password? <a href="../Ochavillo/forgot-pass.php"> Click here!</a> </p>
            </form>
            

        </div>
    </div>
</body>
</html>