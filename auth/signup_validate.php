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
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($phone) || empty($password)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: signup.php");
        exit();
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords don't match! Please check and try again.";
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

    $stmt = $pdo->prepare("INSERT INTO users(firstname, lastname, username, email, phone_number, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $username, $email, $phone, $hashedPassword]);

    $_SESSION['success'] = "Account created successfully! You can now log in and start using your account.";
    header("Location: login.php");
    exit();
}
