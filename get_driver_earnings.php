<?php
header('Content-Type: application/json');
session_start();

$rider_id = $_SESSION['user_id'] ?? 1;
$conn = require 'setup.php';

// Fixed commission per delivery
$commission = 70;

// Get today count
$stmt_today = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE delivery_rider_id = ? AND delivery_status = 'delivered' AND DATE(created_at) = CURDATE()");
$stmt_today->bind_param("i", $rider_id);
$stmt_today->execute();
$today_count = $stmt_today->get_result()->fetch_assoc()['c'];

// Get week count
$stmt_week = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE delivery_rider_id = ? AND delivery_status = 'delivered' AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
$stmt_week->bind_param("i", $rider_id);
$stmt_week->execute();
$week_count = $stmt_week->get_result()->fetch_assoc()['c'];

// Get total count
$stmt_total = $conn->prepare("SELECT COUNT(*) as c FROM orders WHERE delivery_rider_id = ? AND delivery_status = 'delivered'");
$stmt_total->bind_param("i", $rider_id);
$stmt_total->execute();
$total_count = $stmt_total->get_result()->fetch_assoc()['c'];

// Get history
$stmt_hist = $conn->prepare("
    SELECT u.name as customer_name, o.created_at
    FROM orders o
    JOIN users u ON o.customer_id = u.id
    WHERE o.delivery_rider_id = ? AND o.delivery_status = 'delivered'
    ORDER BY o.created_at DESC
    LIMIT 10
");
$stmt_hist->bind_param("i", $rider_id);
$stmt_hist->execute();
$hist_result = $stmt_hist->get_result();

$history = [];
while ($row = $hist_result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode([
    'today' => $today_count * $commission,
    'week' => $week_count * $commission,
    'total_count' => $total_count,
    'avg_rating' => '—', // Ratings not implemented in standalone tables yet
    'history' => $history
]);

$stmt_today->close();
$stmt_week->close();
$stmt_total->close();
$stmt_hist->close();
$conn->close();
