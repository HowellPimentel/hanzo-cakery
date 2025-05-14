<?php
session_start();

require_once '../includes/db.php';
require '../utils/load_env.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$stmt = $pdo->prepare("SELECT * FROM cakes");
$stmt->execute();
$cakes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_COOKIE['token'])){
    $token = $_COOKIE['token'];
    $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cake = $_POST['cake_id'];
    $user = $decoded->data->user_id;

    $stmt = $pdo->prepare("SELECT * FROM cart WHERE cake_id = ? and user_id = ?");
    $stmt->execute([$cake, $user]);
    $exist = $stmt->fetch(PDO::FETCH_ASSOC);
    $quantity = $exist["quantity"];

    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? and cake_id = ?");
        $stmt->execute([$quantity + 1, $user, $cake]);
        header("Location: cart.php");
        exit();
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart(cake_id, user_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$cake, $user, 1]);
        header("Location: cart.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/icon/Circle_Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <title>Hanzo's Cakery</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="../assets/icon/Logo.svg" alt="Hanzo's Cakery Logo">
                <h1>Hanzo's Cakery</h1>
            </div>
            <div class="nav-content">
                <form class="search-container" method="GET">
                    <input type="text" placeholder="Search for cakes...">
                    <a href="#">
                        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiM2NjY2NjYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIiBjbGFzcz0ibHVjaWRlIGx1Y2lkZS1zZWFyY2giPjxjaXJjbGUgY3g9IjExIiBjeT0iMTEiIHI9IjgiLz48cGF0aCBkPSJtMjEgMjEtNC4zNS00LjM1Ii8+PC9zdmc+"
                            alt="Search">
                    </a>
                </form>
                <?php if (!isset($_COOKIE['token'])): ?>
                    <div class="auth-button">
                        <li>
                            <a href="../auth/login.php">
                                Log in
                            </a>
                        </li>
                        <li>
                            <a href="../auth/signup.php">
                                Sign up
                            </a>
                        </li>
                    </div>
                <?php else: ?>
                    <nav class='navlink'>
                        <ul>
                            <li>
                                <a href="cart.php" aria-label="Shopping Cart">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path
                                            d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.994,0,0,0,22.713,4.077ZM21.4,6.178l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z" />
                                        <circle cx="7" cy="22" r="2" />
                                        <circle cx="17" cy="22" r="2" />
                                    </svg>
                                </a>
                            </li>
                            <li id="user">
                                <a href="">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path
                                            d="M16.043,14H7.957A4.963,4.963,0,0,0,3,18.957V24H21V18.957A4.963,4.963,0,0,0,16.043,14Z" />
                                        <circle cx="12" cy="6" r="6" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        <nav class="nav-container">
            <ul>
                <li class="active">
                    <a href="#">Home</a>
                </li>
                <li>
                    <a href="cake-menu.php">Cake Menu</a>
                </li>
            </ul>
        </nav>
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
    </header>
    <main>
        <div class="hero-section">
            <div class="hero-content">
                <h2>Delicious Cakes for Every Occasion</h2>
                <p>Handcrafted with love using premium ingredients for unforgettable moments</p>
                <a href="cake-menu.php" class="hero-btn">Order Now</a>
            </div>
        </div>

        <div class="quote">
            <p>Bite into joy, one slice at a time</p>
        </div>

        <div class="cake-section">
            <div class="cake-header">
                <h1>Our Popular Cakes</h1>
                <a href="cake-menu.php">View All Cakes</a>
            </div>
            <div class="cake-container">
                <?php
                $categories = ['Cake in a Tub', 'Bento', 'Cake', 'Cake in a Tub'];
                $displayedCakes = [];

                foreach ($categories as $category) {
                    foreach ($cakes as $cake) {
                        if ($cake['cake_type'] === $category && !in_array($cake, $displayedCakes)) {
                            $displayedCakes[] = $cake;
                ?>
                            <div class="card">
                                <div class="card-image">
                                    <img src="../assets/cakes/<?= $cake['image_path'] ?>" alt="<?= $cake['cake_name'] ?>">
                                </div>
                                <div class="card-cake">
                                    <h3><?= $cake['cake_name'] ?></h3>
                                    <p>&#8369; <?= number_format($cake['cake_price'], 2) ?></p>
                                    <div class="description"><?= $cake['cake_description'] ?></div>
                                </div>
                                <div class="card-btn">
                                    <form action="addCart.php" method="POST">
                                        <input type="hidden" value="<?= $cake['id'] ?>" name="cake_id">
                                        <button class="add-cart" type="submit">Add to Cart</button>
                                    </form>
                                    <form action="" method="POST">
                                        <input type="hidden" value="<?= $cake['id'] ?>" name="cake_id">
                                        <button class="buy-now">Buy now</button>
                                    </form>
                                </div>
                            </div>
                <?php
                            break; // Stop after finding one cake for the category
                        }
                    }
                }
                ?>
            </div>
        </div>
        <section class="map-section">
            <div class="container">
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d493.68410964213484!2d125.12965904982917!3d8.155018769891907!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32ffab5160410f33%3A0xd56d32abee17670c!2sHanzo&#39;s%20cakery!5e0!3m2!1sen!2sph!4v1746951881893!5m2!1sen!2sph" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div style="text-align: center;">
                    <h1 style="color: var(--primary); margin-top: 1rem;">Hanzo's Cakery</h1>
                    <p style="text-transform: capitalize;">murillo street, corner mampaalong street, Malaybalay, Philippines</p>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <img src="../assets/icon/Circle_Logo.png" alt="">
                <h3>Hanzo's Cakery</h3>
                <p>&copy; All rights reserved. Hanzo's Cakery 2025</p>
            </div>
            <div class="footer-col">
                <div class="info">
                    <ul>
                        <li><i class="fa fa-envelope" aria-hidden="true"></i> hanzoscakery@gmail.com</li>
                        <li><i class="fa fa-phone" aria-hidden="true"></i> 0975 171 5127</li>
                        <li><i class="fa-brands fa-facebook-f"></i> Hanzo's Cakery</li>
                    </ul>
                </div>
                <div class="social-links">
                    <a href="https://www.facebook.com/profile.php?id=100085643388007" target="_blank">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>