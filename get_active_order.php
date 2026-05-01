<?php
header('Content-Type: application/json');
session_start();

$rider_id = $_SESSION['user_id'] ?? 1;

$conn = require 'setup.php';

$stmt = $conn->prepare("
    SELECT o.id, o.total_price as total_amount, o.dest_lat, o.dest_lng, o.note, o.delivery_status as status,
           u.name as customer_name
    FROM orders o
    JOIN users u ON o.customer_id = u.id
    WHERE o.delivery_rider_id = ? AND o.delivery_status IN ('assigned', 'picked_up')
    ORDER BY o.created_at ASC
    LIMIT 1
");
$stmt->bind_param("i", $rider_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    echo json_encode(null);
    exit;
}

$order = $order_result->fetch_assoc();

$stmt_items = $conn->prepare("SELECT food_name as name, quantity as qty FROM order_items WHERE order_id = ?");
$stmt_items->bind_param("i", $order['id']);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

$food_items = [];
while ($item = $items_result->fetch_assoc()) {
    $food_items[] = $item;
}

$order['items_json'] = json_encode($food_items);

echo json_encode($order);

$stmt->close();
$stmt_items->close();
$conn->close();
