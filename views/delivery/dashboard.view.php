<?php
if (!isset($driverName)) $driverName = "Rider One";
if (!function_exists('csrf_token')) { function csrf_token() { return ""; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni Rider — <?= $driverName ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/delivery/dashboard.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <header class="sb-header">
            <div class="sb-logo">Sauni</div>
            <div class="gps-status"><div class="gps-dot"></div> GPS LIVE</div>
        </header>
        <nav class="nav-tabs">
            <button class="nav-tab active" onclick="switchTab('mission', this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px; vertical-align:middle"><rect x="1" y="3" width="15" height="13"/><polyline points="16 8 20 8 23 11 23 16 16 16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                Mission
            </button>
            <button class="nav-tab" onclick="switchTab('earnings', this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px; vertical-align:middle"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Earnings
            </button>
        </nav>
        <div class="tab-view">
            <div id="pane-mission" class="pane active">
                <div id="missionContent">
                    <div class="mission-empty">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <p>Awaiting assignment…<br>Keep your GPS active.</p>
                    </div>
                </div>
            </div>
            <div id="pane-earnings" class="pane">
                <div class="stat-grid" id="statGrid">
                    <div class="stat-card"><div class="st-val">Rs. 0</div><div class="st-lbl">Today</div></div>
                    <div class="stat-card"><div class="st-val">Rs. 0</div><div class="st-lbl">Weekly</div></div>
                    <div class="stat-card"><div class="st-val">0</div><div class="st-lbl">Trips</div></div>
                    <div class="stat-card"><div class="st-val">—</div><div class="st-lbl">Rating</div></div>
                </div>
                <h3 style="font-size:0.8rem; font-weight:800; text-transform:uppercase; color:#9ca3af; margin-bottom:1.2rem; letter-spacing:1px">Trip History</h3>
                <div class="history-list" id="historyList"></div>
            </div>
        </div>
        <footer class="footer-action">
            <a href="#" class="logout-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                End Active Shift
            </a>
        </footer>
    </aside>
    <main class="map-section">
        <div id="map"></div>
        <button class="btn-center" onclick="recenterMap()" title="Recenter Location">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
        </button>
        <div class="map-overlay">
            <div class="lg-item"><div class="lg-dot" style="background:var(--rider)"></div> You</div>
            <div class="lg-item"><div class="lg-dot" style="background:var(--kitchen)"></div> Kitchen</div>
            <div class="lg-item"><div class="lg-dot" style="background:var(--dest)"></div> Customer</div>
        </div>
    </main>
</div>
<div class="modal" id="finishModal">
    <div class="modal-body">
        <span class="modal-icon">🥇</span>
        <h2>Great Job!</h2>
        <p>Your delivery mission is complete. Your commission has been added to your wallet.</p>
        <div class="modal-commission" id="finalEarned">Rs. 0</div>
        <div class="modal-label">Trip commission</div>
        <button class="action-primary btn-modal-close btn-deliver" onclick="closeSuccessModal()">Ready for More</button>
    </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../assets/js/delivery/dashboard.js"></script>
</body>
</html>
