<?php
header('Content-Type: application/json');
session_start();

$rider_id = $_SESSION['user_id'] ?? 1;

$conn = require 'setup.php';

$data = json_decode(file_get_contents("php://input"), true);
$order_id = isset($data['order_id']) ? (int)$data['order_id'] : 0;
$new_status = isset($data['status']) ? $data['status'] : '';

if (!$order_id || !$new_status) {
    echo json_encode(['success' => false]);
    exit;
}

$valid_statuses = ['picked_up', 'delivered'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT delivery_status FROM orders WHERE id = ? AND delivery_rider_id = ?");
$stmt->bind_param("ii", $order_id, $rider_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false]);
    exit;
}

$order = $result->fetch_assoc();
$current_status = $order['delivery_status'];

if ($current_status === 'delivered') {
    echo json_encode(['success' => false]);
    exit;
}

if ($new_status === 'picked_up' && $current_status !== 'assigned') {
    echo json_encode(['success' => false]);
    exit;
}

if ($new_status === 'delivered' && $current_status !== 'picked_up') {
    echo json_encode(['success' => false]);
    exit;
}

$update_stmt = $conn->prepare("UPDATE orders SET delivery_status = ? WHERE id = ?");
$update_stmt->bind_param("si", $new_status, $order_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$update_stmt->close();
$stmt->close();
$conn->close();
