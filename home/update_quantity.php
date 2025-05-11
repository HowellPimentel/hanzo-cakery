<?php
session_start();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require '../utils/load_env.php';
require '../includes/db.php';
require '../vendor/autoload.php';

header('Content-Type: application/json'); // Set the content type to JSON

$token = $_COOKIE['token'];
$decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

$cake_id = $_POST['product_id'];
$quantity = $_POST['quantity'];
$user_id = $decoded->data->user_id;

try {
    // Update the cart quantity
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND cake_id = ?");
    $stmt->execute([$quantity, $user_id, $cake_id]);
    
    // Calculate the new total price
    $stmt = $pdo->prepare("
        SELECT SUM(c.quantity * ck.cake_price) as total
        FROM cart c
        JOIN cakes ck ON c.cake_id = ck.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Return success response with new total
    echo json_encode([
        'success' => true,
        'new_total' => (float) $result['total'],
        'message' => 'Quantity updated successfully'
    ]);
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Error updating quantity: ' . $e->getMessage()
    ]);
}