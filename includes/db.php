<?php

require '../utils/load_env.php';

$server  = $_ENV['DB_SERVERNAME'];
$username  = $_ENV['DB_USERNAME'];
$password  = $_ENV['DB_PASSWORD'];
$dbname  = $_ENV['DB_NAME'];

$conn = mysqli_connect($server, $username, $password, $dbname);

try {
    $dsn = "mysql:host=" . $server . ";dbname=" . $dbname;
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
