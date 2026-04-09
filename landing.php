<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
require_once __DIR__ . '/includes/db.php';
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? '';
$profile_pic = $_SESSION['profile_pic'] ?? null;

if ($isLoggedIn) {
    $user_stmt = $pdo->prepare("SELECT profile_pic FROM user WHERE id = ?");
    $user_stmt->execute([$_SESSION['user_id']]);
    $user_data = $user_stmt->fetch();
    if ($user_data) {
        $profile_pic = $user_data['profile_pic'];
        $_SESSION['profile_pic'] = $profile_pic;
    }
}
$stmt = $pdo->query("SELECT m.*, ct.name as category FROM menu_item m JOIN category c ON m.category_id=c.id JOIN cuisine_type ct ON c.cuisine_type_id=ct.id WHERE m.is_available=1 LIMIT 10");
$menu_items = $stmt->fetchAll();
require_once __DIR__ . '/views/landing.view.php';
