<?php
require_once __DIR__ . '/includes/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username   = $_SESSION['username'] ?? '';
$role       = $_SESSION['role'] ?? 'customer';
$menu_items = [];

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . '/views/landing.view.php';
