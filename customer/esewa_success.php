<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';


$data = $_GET['data'] ?? '';
if (!$data) {
    die("No data received from payment gateway.");
}

$decoded = json_decode(base64_decode($data), true);
if (!$decoded) {
    die("Invalid response data.");
}

$status           = $decoded['status'] ?? '';
$transaction_uuid = $decoded['transaction_uuid'] ?? '';
$oid              = explode('-', $transaction_uuid)[0]; // Extract numeric Order ID from {oid}-{uniqid}
$total_amount     = $decoded['total_amount'] ?? 0;
$response_sig     = $decoded['signature'] ?? '';
$field_names      = $decoded['signed_field_names'] ?? '';

if ($status === 'COMPLETE') {
    // 1. Verify Signature
    $secret = ESEWA_SECRET;

    $fields = explode(',', $field_names);
    $data_to_sign = "";
    foreach ($fields as $index => $field) {
        $data_to_sign .= $field . "=" . ($decoded[$field] ?? '') . ($index < count($fields) - 1 ? "," : "");
    }
    
    $expected_sig = base64_encode(hash_hmac('sha256', $data_to_sign, $secret, true));
    
    if (hash_equals($expected_sig, $response_sig)) {
        // 2. Fetch order and verify amount
        $stmt = $pdo->prepare("SELECT total_amount FROM `order_` WHERE id = ?");
        $stmt->execute([$oid]);
        $order = $stmt->fetch();
        
        if ($order && (float)$order['total_amount'] === (float)str_replace(',', '', $total_amount)) {
            // 3. Update payment status
            $upd = $pdo->prepare("UPDATE `order_` SET payment_status = 'paid' WHERE id = ?");
            $upd->execute([$oid]);
            
            // Redirect to tracker
            header("Location: tracker.php?payment_success=1&oid=" . $oid);
            exit;
        } else {
            die("Amount mismatch or order not found.");
        }
    } else {
        die("Security verification failed. Signature mismatch.");
    }
} else {
    header("Location: esewa_failure.php?oid=" . $oid . "&status=" . $status);
    exit;
}
