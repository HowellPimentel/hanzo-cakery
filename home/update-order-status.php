<?php
session_start();
require_once '../includes/db.php';
require_once '../utils/jwt.php';

header('Content-Type: application/json');

if (!isset($_COOKIE['token'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user = decodeJWT($_COOKIE['token']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$transaction_id = $_POST['transaction_id'] ?? '';

if (empty($transaction_id)) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID is required']);
    exit();
}

try {
    // Update the order status to 'Received' (capitalized)
    $stmt = $pdo->prepare("UPDATE orders SET status = 'received' WHERE transaction_id = ? AND user_id = ?");
    $result = $stmt->execute([$transaction_id, $user->data->user_id]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Order marked as received']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}