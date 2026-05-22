<?php
$un   = htmlspecialchars($_SESSION['username'] ?? 'User');
if (isset($_SESSION['user_id'])) {
    if (!isset($pdo)) require_once __DIR__ . '/db.php';
    $un_stmt = $pdo->prepare("SELECT username, profile_pic FROM user WHERE id = ?");
    $un_stmt->execute([$_SESSION['user_id']]);
    $ud = $un_stmt->fetch();
    if ($ud) {
        $_SESSION['username'] = $ud['username'];
        $_SESSION['profile_pic'] = $ud['profile_pic'];
        $un = htmlspecialchars($ud['username']);
    }
}
$init = strtoupper(substr($un, 0, 1));
$act  = $activeNav ?? '';
?>
<header>
<nav class="customer-nav">
    <a href="../landing.php" class="cn-logo" onclick="navGo(event,'../landing.php')" style="font-family:'Outfit',sans-serif; font-size:1.4rem; font-weight:900; letter-spacing:0; word-spacing:0; text-decoration:none; display:inline-flex; align-items:center; height:38px; color:#ff3b00;">S<span style="font-size:1em; line-height:1; display:inline-block; margin:0 -0.15em; vertical-align:-0.05em;">🔥</span>UNI</a>
    <div class="cn-links">
        <a href="../landing.php" class="cn-link <?= $act==='home' ? 'active':'' ?>" onclick="navGo(event,'../landing.php')">Home</a>
        <a href="browse.php" class="cn-link <?= $act==='browse' ? 'active':'' ?>" onclick="navGo(event,'browse.php')">Browse All</a>
        <a href="tracker.php" class="cn-link <?= $act==='track' ? 'active':'' ?>" onclick="navGo(event,'tracker.php')">Track Order</a>
        <a href="history.php" class="cn-link <?= $act==='history' ? 'active':'' ?>" onclick="navGo(event,'history.php')">History</a>
        <div class="ns-drop-wrap" id="pfWrap">
            <button class="ns-user-btn" id="pfTrigger" onclick="togglePf(event)" title="Account">
                <?php if (!empty($_SESSION['profile_pic'])): ?>
                    <div class="ns-av-img" style="background-image:url('../<?= $_SESSION['profile_pic'] ?>'); background-size:cover; background-position:center; font-size:0;"></div>
                <?php else: ?>
                    <div class="ns-av-img"><?= $init ?></div>
                <?php endif; ?>
                <div class="ns-uname-txt"><?= $un ?></div>
                <svg viewBox="0 0 24 24" class="ns-chev"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="ns-dropdown-menu" id="pfDrop">
                <a href="profile.php" class="ns-link <?= $act==='profile' ? 'active':'' ?>" onclick="navGo(event,'profile.php')">Profile</a>
                <a href="history.php" class="ns-link <?= $act==='history' ? 'active':'' ?>" onclick="navGo(event,'history.php')">Order History</a>
                <a href="profile.php" class="ns-link <?= $act==='profile' ? 'active':'' ?>" onclick="navGo(event,'profile.php')">Settings</a>
                <div class="ns-divider"></div>
                <a href="../logout.php" class="ns-link ns-danger">Log Out</a>
            </div>
        </div>
        <button class="cn-cart" id="cartPill" onclick="typeof toggleDrawer!=='undefined' ? toggleDrawer() : location.href='cart.php'">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <span data-cart-count>0</span>
        </button>
    </div>
</nav>
</header>

<!-- FoodBot Smart AI Assistant -->
<link rel="stylesheet" href="../assets/css/customer/assistant.css?v=2.0">
<div id="ai-assistant-root">
    <div class="ai-window" id="aiWindow">
        <div class="ai-header">
            <div class="ai-header-icon">
                <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
            </div>
            <div class="ai-header-info">
                <h3>FoodBot</h3>
                <span>FoodBot Assistant Active</span>
            </div>
            <button class="ai-clear-btn" id="aiClearBtn" title="Clear chat history">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </button>
        </div>
        <div class="ai-messages" id="aiMessages"></div>
        <div class="ai-input-wrap">
            <input type="text" id="aiInput" class="ai-input" placeholder="Type a message...">
            <button id="aiSend" class="ai-send">
                <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </div>
    </div>
    <div class="ai-trigger" id="aiTrigger" title="Open Smart Assistant">
        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </div>
</div>
<script src="../assets/js/customer/assistant.js?v=2.0"></script>
