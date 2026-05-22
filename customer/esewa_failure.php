<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
$oid = $_GET['oid'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed — Sauni</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #fffcfb; }
        .error-card { background: #fff; padding: 40px; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); text-align: center; max-width: 450px; width: 90%; border: 1px solid #fee2e2; }
        .icon-circle { width: 70px; height: 70px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: #ef4444; }
        h2 { margin: 0 0 10px; color: #111; font-size: 1.8rem; font-weight: 700; }
        p { color: #6b7280; margin: 0 0 30px; line-height: 1.6; }
        .btn-group { display: flex; flex-direction: column; gap: 12px; }
        .btn { padding: 14px 24px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: 0.3s; cursor: pointer; border: none; font-size: 0.95rem; }
        .btn-retry { background: #ff3b00; color: #fff; box-shadow: 0 10px 20px rgba(255,59,0,0.2); }
        .btn-retry:hover { background: #e63500; transform: translateY(-2px); }
        .btn-home { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        .btn-home:hover { background: #f9fafb; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-circle">
            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <h2>Payment Failed</h2>
        <p>Something went wrong with the payment transaction or it was cancelled. Your order remains pending. You can try paying again or contact support.</p>
        
        <div class="btn-group">
            <?php if($oid): ?>
                <a href="esewa_payment.php?oid=<?= $oid ?>" class="btn btn-retry">Retry eSewa Payment</a>
            <?php endif; ?>
            <a href="tracker.php" class="btn btn-home">Go to Order Tracking</a>
            <a href="../landing.php" class="btn btn-home">Back to Home</a>
        </div>
    </div>
</body>
</html>
