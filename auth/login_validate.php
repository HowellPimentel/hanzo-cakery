<?php
session_start();

if (isset($_COOKIE['token'])) {
    header("Location: ../home/home.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $
}

?>