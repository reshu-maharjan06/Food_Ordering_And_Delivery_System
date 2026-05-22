<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sauni Admin — Drivers &amp; Map</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin/shared.css">
<link rel="stylesheet" href="../assets/css/admin/delivery_staff.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body>
<div class="shell">
<aside class="sb">
  <div class="sb-brand">
    <div style="font-family:'Outfit',sans-serif; font-size:1.4rem; font-weight:900; letter-spacing:0; word-spacing:0; color:#ff3b00; display:flex; align-items:center;">S<span style="font-size:1em; line-height:1; display:inline-block; margin:0 -0.15em; vertical-align:-0.05em;">🔥</span>UNI</div>
  </div>
  <div class="sb-body">
    <div class="sb-section-label">Main</div>
    <a href="dashboard.php" class="sb-link">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
      Dashboard
    </a>
    <a href="orders.php" class="sb-link">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg></div>
      Orders
    </a>
    <a href="menu.php" class="sb-link">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M18 2v20"/><path d="M21 2v5c0 1.1-.9 2-2 2h-1"/></svg></div>
      Menu
    </a>
    <div class="sb-section-label" style="margin-top:0.5rem">Operations</div>
    <a href="delivery_staff.php" class="sb-link active">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polyline points="16 8 20 8 23 11 23 16 16 16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
      Drivers &amp; Map
    </a>
    <a href="ratings.php" class="sb-link">
      <div class="sb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
      Reviews
    </a>
  </div>
  <div class="sb-footer">
    <div class="sb-user-wrap">
      <?php if (!empty($_SESSION['profile_pic'])): ?>
        <div class="sb-av" style="background-image:url('../<?= $_SESSION['profile_pic'] ?>'); background-size:cover; background-position:center; font-size:0; border:2px solid #fff;"></div>
      <?php else: ?>
        <div class="sb-av"><?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?></div>
      <?php endif; ?>
      <div><div class="sb-uname"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div><div class="sb-urole">Platform Manager</div></div>
    </div>
    <a href="../logout.php" class="sb-logout">↩ Sign out</a>
  </div>
</aside>
<div class="main">
  <div class="topbar">
    <div class="topbar-left">
      <div class="topbar-title">Dispatch Control</div>
      <div class="topbar-sub">Live rider tracking</div>
    </div>
    <div class="topbar-right">
      <div class="topbar-identity" style="display:flex; align-items:center; gap:0.6rem; background:#f9fafb; padding:0.4rem 1rem; border-radius:50px; border:1px solid #e5e7eb; margin-right: 1rem;">
        <svg width="20" height="24" viewBox="0 0 457 547" fill="none"><path d="M0 547H456.551L170.838 273.5H380.459L0 0V547Z" fill="#DC143C"/><path d="M0 547H456.551L170.838 273.5H380.459L0 0V547Z" stroke="#003594" stroke-width="30"/><circle cx="106.333" cy="413.667" r="69" fill="white"/><path d="M38.3333 154.333C38.3333 154.333 73 203.667 110.333 203.667C147.667 203.667 182.333 154.333 182.333 154.333C182.333 154.333 166.333 167 110.333 167C54.3333 167 38.3333 154.333 38.3333 154.333Z" fill="white"/></svg>
        <span style="font-size:0.75rem; font-weight:800; color:#111; letter-spacing:0.5px;">THAMEL, NEPAL</span>
      </div>
      <div class="topbar-live"><div class="live-pulse"></div> Live</div>
    </div>
  </div>
  <div class="shell-row" style="display:flex;flex:1;overflow:hidden">
    <div class="riders-panel">
      <div class="riders-head">
        <h2>Live Rider Roster</h2>
        <div class="riders-sub" id="riderCount">Loading...</div>
      </div>
      <div class="riders-list" id="riderList">
        <div class="empty-state"><p>Loading riders...</p></div>
      </div>
    </div>
    <div class="map-area" style="flex:1;position:relative">
      <div class="map-overlay">
        <div class="live-pulse"></div>
        Live Dispatch Map — <span id="mapRiderCount">0</span> riders tracked
      </div>
      <div id="map" style="height:100%"></div>
    </div>
  </div>
</div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../assets/js/admin/delivery_staff.js"></script>
</body>
</html>

