<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni — Order Tracking</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/shared/global.css">
    <link rel="stylesheet" href="../assets/css/shared/nav.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="../assets/css/customer/tracker.css">
</head>
<body>
<?php $activeNav = 'track'; include __DIR__ . '/../../includes/customer-nav.php'; ?>
<div class="main-wrapper" id="mainWrapper" style="display:none;">
    <div class="left-col">
        <div class="lc-scroll">
            <div class="user-header">
                <div class="uh-av">
                    <div class="uh-av-img"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
                    <div>
                        <div class="uh-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Customer') ?></div>
                        <div class="uh-id">ID <span id="lblOrdId">---</span></div>
                    </div>
                </div>
                <div class="uh-status" id="lblStatusTop">Pending</div>
            </div>
            <div class="delivery-info">
                <div class="di-title">Delivery Address</div>
                <div class="di-address" id="lblAddr">---</div>
                <div class="di-grid">
                    <div>
                        <label>Phone Number</label>
                        <p id="lblPhone"><?= htmlspecialchars($_SESSION['phone'] ?? '---') ?></p>
                    </div>
                    <div>
                        <label>Estimated Time</label>
                        <p id="lblEta">-- Minutes</p>
                    </div>
                </div>
            </div>
            <div class="cart-items" id="domItems">
            </div>
            <div class="totals-box">
                <div class="t-row"><span>Sub Total</span><span id="lblSubTotal">Rs. 0.00</span></div>
                <div class="t-row"><span>Tax (0%)</span><span>Rs. 0.00</span></div>
                <div class="t-row final"><span>Total</span><span id="lblTotal">Rs. 0.00</span></div>
            </div>
            <div class="status-tracker">
                <div class="track-step" id="ts_pending">
                    <div class="ts-ic"></div><div class="ts-line"></div>
                    <div class="ts-txt"><h4>Order Placed</h4><p>We received your order.</p></div>
                </div>
                <div class="track-step" id="ts_prepared">
                    <div class="ts-ic"></div><div class="ts-line"></div>
                    <div class="ts-txt"><h4>Preparing</h4><p>Chef is cooking your meal.</p></div>
                </div>
                <div class="track-step" id="ts_assigned">
                    <div class="ts-ic"></div><div class="ts-line"></div>
                    <div class="ts-txt"><h4>Driver Assigned</h4><p>Your driver is heading to the kitchen.</p></div>
                </div>
                <div class="track-step" id="ts_picked_up">
                    <div class="ts-ic"></div><div class="ts-line"></div>
                    <div class="ts-txt"><h4>Out for Delivery</h4><p>Driver is on the way to you.</p></div>
                </div>
                <div class="track-step" id="ts_delivered">
                    <div class="ts-ic"></div>
                    <div class="ts-txt"><h4>Delivered</h4><p>Enjoy your meal.</p></div>
                </div>
            </div>
            <button id="btnReviewOp" style="display:none; width:100%; padding:15px; margin-top:20px; background:var(--text); color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer;" onclick="location.href='rating.php'">Leave a Review</button>
        </div>
    </div>
    <div class="right-col">
        <div class="map-wrapper">
            <div id="map"></div>
        </div>
        <div class="delivery-by-card" id="driverPanel" style="display:none;">
            <div>
                <div class="db-title">Delivery By</div>
                <div class="db-info">
                    <div class="db-av" id="drAv">D</div>
                    <div>
                        <div class="db-name" id="drName">driver</div>
                        <div class="db-id">ID <span id="drId">---</span></div>
                    </div>
                </div>
            </div>
            <div class="db-contact">
                <label>Phone Number</label>
                <a href="#" id="drPhone">---</a>
            </div>
        </div>
    </div>
</div>
<div id="noAct" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh; padding:1rem; text-align:center;">
    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="2" style="margin-bottom:20px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    <h2 style="font-family:'Outfit',sans-serif; margin-bottom:10px;">No Active Orders</h2>
    <p style="color:#6b7280; margin-bottom:25px;">You don't have any orders currently being processed.</p>
    <button onclick="location.href='../landing.php'" style="padding:12px 24px; background:var(--o,#ff3b00); color:#fff; border:none; border-radius:50px; font-weight:600; cursor:pointer; box-shadow:0 5px 15px rgba(255,59,0,0.2);">Browse Menu</button>
</div>
<script src="../assets/js/sauni-cart.js?v=2"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../assets/js/customer/tracker.js?v=2"></script>
    <script src="../assets/js/shared/nav.js"></script>
</body>
</html>

