<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauni — Browse Menu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/shared/global.css">
    <link rel="stylesheet" href="../assets/css/shared/nav.css">
    <link rel="stylesheet" href="../assets/css/customer/browse.css">
</head>
<body>
<?php $activeNav = 'browse'; include __DIR__ . '/../../includes/customer-nav.php'; ?>
<div class="layout">
    <aside class="aside">
        <h3>Cuisines</h3>
        <button class="cat-filter on" data-cat="all" onclick="setCat('all',this)">All <span class="cf-cnt" id="cnt-all">0</span></button>
        <div id="catFilters"></div>
    </aside>
    <div class="main">
        <div class="search-bar">
            <div class="search-inner">
                <span class="search-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" id="searchInput" placeholder="Search dishes..." oninput="filterItems()">
            </div>
        </div>
        <div class="grid-area">
            <div class="grid-info" id="gridInfo">Loading...</div>
            <div class="grid" id="menuGrid"></div>
        </div>
    </div>
</div>
<script src="../assets/js/sauni-cart.js"></script>
<script src="../assets/js/customer/browse.js"></script>
    <script src="../assets/js/shared/nav.js"></script>
</body>
</html>

