<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni — Order History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/shared/global.css">
    <link rel="stylesheet" href="../assets/css/shared/nav.css">
    <link rel="stylesheet" href="../assets/css/customer/history.css">
</head>
<body>
<?php $activeNav = 'history'; include __DIR__ . '/../../includes/customer-nav.php'; ?>
<main class="pg-wrap">
    <div class="pg-head">
        <span class="pg-cat">Your Gastronomy</span>
        <h1 class="pg-title">Order Treasury</h1>
    </div>
    <table class="hist-table" id="histTable">
        <thead class="ht-head">
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="histBody"></tbody>
    </table>
</main>
<div class="modal" id="reviewModal">
    <div class="modal-card">
        <div class="modal-head">
            <h3>How was it?</h3>
            <p>Tell us about your experience with order #<span id="targetOrderId">...</span></p>
        </div>
        <div class="star-rating" id="starWrap">
            <span class="star" data-val="1">★</span>
            <span class="star" data-val="2">★</span>
            <span class="star" data-val="3">★</span>
            <span class="star" data-val="4">★</span>
            <span class="star" data-val="5">★</span>
        </div>
        <textarea class="rev-area" id="revComment" placeholder="Write a quick note (optional)..."></textarea>
        <div class="modal-btns">
            <button class="btn-m-sec" onclick="closeReviewModal()">Cancel</button>
            <button class="btn-m-pri" onclick="submitReview()">Submit Review</button>
        </div>
    </div>
</div>
<script src="../assets/js/customer/history.js"></script>
    <script src="../assets/js/shared/nav.js"></script>
    <script src="../assets/js/shared/nav.js"></script>
</body>
</html>

