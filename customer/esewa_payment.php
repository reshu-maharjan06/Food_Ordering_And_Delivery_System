<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';


$oid = $_GET['oid'] ?? 0;
$uid = $_SESSION['user_id'] ?? 0;

if (!$oid || !$uid) {
    die("Unauthorized access.");
}

$stmt = $pdo->prepare("SELECT * FROM `order_` WHERE id = ? AND customer_id = ?");
$stmt->execute([$oid, $uid]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

// eSewa Parameters
$amount = (float)$order['total_amount'];
$tax_amount = 0;
$total_amount = $amount + $tax_amount;

// Format amounts for eSewa (UAT is extremely sensitive: if integer, use integer string)
$f_total_amount = strval((int)$total_amount);

$transaction_uuid = $oid . '-' . uniqid(); // Ensuring uniqueness for every attempt to prevent 'Duplicate' error
$product_code = ESEWA_PRODUCT_CODE;
$secret = ESEWA_SECRET;

// URLs
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . explode('customer/', $_SERVER['REQUEST_URI'])[0] . 'customer/';
$success_url = $base_url . "esewa_success.php";
$failure_url = $base_url . "esewa_failure.php";

// Signature Generation
$data_to_sign = "total_amount=$f_total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code";
$signature = base64_encode(hash_hmac('sha256', $data_to_sign, $secret, true));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to eSewa — Sauni</title>
    <style>
        body { font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #fafaf9; }
        .loader-card { background: #fff; padding: 40px; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); text-align: center; max-width: 400px; width: 90%; }
        .spinner { width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #ff3b00; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        h2 { margin: 0 0 10px; color: #111; font-size: 1.5rem; }
        p { color: #6b7280; margin: 0; font-size: 0.9rem; }
    </style>
</head>
<body onload="document.getElementById('esewa-form').submit();">
    <div class="loader-card">
        <div class="spinner"></div>
        <h2>Secure Payment</h2>
        <p>Redirecting you to eSewa portal. Please do not close this window.</p>
        
        <form id="esewa-form" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
            <input type="hidden" name="amount" value="<?= strval((int)$amount) ?>">
            <input type="hidden" name="tax_amount" value="<?= strval((int)$tax_amount) ?>">
            <input type="hidden" name="total_amount" value="<?= $f_total_amount ?>">
            <input type="hidden" name="transaction_uuid" value="<?= $transaction_uuid ?>">
            <input type="hidden" name="product_code" value="<?= $product_code ?>">
            <input type="hidden" name="product_service_charge" value="0">
            <input type="hidden" name="product_delivery_charge" value="0">
            <input type="hidden" name="success_url" value="<?= $success_url ?>">
            <input type="hidden" name="failure_url" value="<?= $failure_url ?>">
            <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
            <input type="hidden" name="signature" value="<?= $signature ?>">
            <input type="submit" value="Click here if not redirected" style="display:none;">
        </form>
    </div>
</body>
</html>
