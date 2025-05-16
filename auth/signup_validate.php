<?php

session_start();

require '../includes/db.php';

if (isset($_COOKIE['token'])) {
    header("Location: ../home/home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $recaptchaSecret = $_ENV['RECAPTCHA_SECRET'];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
    $captchaSuccess = json_decode($verify);

    if (!$captchaSuccess->success) {
        $_SESSION['error'] = 'Recaptcha Verification Failed';
        header('Location: signup.php');
        exit();
    }

    // Validate inputs
    if (empty($firstname)) {
        $_SESSION['error'] = "First name is required";
        header("Location: signup.php");
        exit();
    }
    if (empty($lastname)) {
        $_SESSION['error'] = "Last name is required";
        header("Location: signup.php");
        exit();
    }
    if (empty($username)) {
        $_SESSION['error'] = "Username is required";
        header("Location: signup.php");
        exit();
    }
    if (empty($email)) {
        $_SESSION['error'] = "Email is required";
        header("Location: signup.php");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: signup.php");
        exit();
    }
    if (empty($phone)) {
        $_SESSION['error'] = "Phone number is required";
        header("Location: signup.php");
        exit();
    }
    if (empty($address)) {
        $_SESSION['error'] = "Address is required";
        header("Location: signup.php");
        exit();
    }
    if (empty($password)) {
        $_SESSION['error'] = "Password is required";
        header("Location: signup.php");
        exit();
    } elseif (strlen($password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters long";
        header("Location: signup.php");
        exit();
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $_SESSION['error'] = "Password must contain at least one uppercase letter";
        header("Location: signup.php");
        exit();
    } elseif (!preg_match("/[a-z]/", $password)) {
        $_SESSION['error'] = "Password must contain at least one lowercase letter";
        header("Location: signup.php");
        exit();
    }

    if (empty($confirm_password)) {
        $_SESSION['error'] = "Please confirm your password";
        header("Location: signup.php");
        exit();
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: signup.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['$email']);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email already exists! Try logging in or use a different email address.";
        header("Location: signup.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Username already exists! Please choose a different username.";
        header("Location: signup.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users(firstname, lastname, username, email, phone_number, address, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $username, $email, $phone, $address, $hashedPassword]);

    $_SESSION['success'] = "Account created successfully! You can now log in and start using your account.";
    header("Location: login.php");
    exit();
}
