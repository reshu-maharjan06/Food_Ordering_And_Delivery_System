<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni — Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/shared/global.css">
    <link rel="stylesheet" href="../assets/css/shared/nav.css">
    <link rel="stylesheet" href="../assets/css/customer/cart.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body>
<?php $activeNav = 'checkout'; include __DIR__ . '/../../includes/customer-nav.php'; ?>
<div class="main-wrapper">
    <div class="left-col">
        <div class="lc-scroll">
            <div class="user-header">
                <div class="uh-av">
                    <div class="uh-av-img"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
                    <div>
                        <div class="uh-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Customer') ?></div>
                        <div class="uh-id">Checkout</div>
                    </div>
                </div>
            </div>
            <div class="form-section">
                <div class="fs-title">Delivery Address</div>
                <textarea id="addrText" class="fs-input" rows="2" placeholder="Searching map location..." readonly></textarea>
                <div class="fs-grid">
                    <div class="fsg-item">
                        <label>Delivery Note</label>
                        <input type="text" id="orderNote" placeholder="e.g. Leave at door">
                    </div>
                    <div class="fsg-item">
                        <label>Estimated Time</label>
                        <input type="text" value="35 Minutes" readonly style="color:var(--text);">
                    </div>
                </div>
            </div>
            <div class="cart-items" id="cartItems">
            </div>
            <div class="totals-box">
                <div class="t-row"><span>Sub Total</span><span id="stVal">Rs. 0</span></div>
                <div class="t-row"><span>Tax (0%)</span><span>Rs. 0.00</span></div>
                <div class="t-row final"><span>Total</span><span id="totVal">Rs. 0</span></div>
            </div>
            <div class="err-msg" id="errMsg"></div>
            <button class="btn-confirm" onclick="placeOrder()">Confirm Order</button>
            <button class="btn-confirm" style="background:#fff; color:#555; border:1px solid #d1d5db; margin-top:10px;" onclick="window.location.href='../landing.php'">Continue Shopping</button>
        </div>
    </div>
    <div class="right-col">
        <div class="map-card">
            <div class="map-search">
                <input type="text" id="placeSearch" placeholder="Search a location..." onkeydown="if(event.key==='Enter') searchPlace()">
                <button onclick="searchPlace()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></button>
            </div>
            <div class="place-results" id="placeResults"></div>
            <div id="map"></div>
            <div class="map-msg" id="mapHint">Tap map to refine location</div>
        </div>
    </div>
</div>
<script src="../assets/js/sauni-cart.js?v=333"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../assets/js/customer/cart.js?v=333"></script>
    <script src="../assets/js/shared/nav.js"></script>
</body>
</html>

