<?php
session_start();
require_once __DIR__ . '/includes/db.php';
if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($_SESSION['role'] === 'admin') header("Location: admin/orders.php");
    else { session_destroy(); header("Location: index.php?error=forbidden"); }
    exit;
}
$error = $_GET['error'] ?? "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role, profile_pic FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['role'] !== 'admin') { $error = "Forbidden: Admin Only Access!"; }
        else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_pic'] = $user['profile_pic'];
            header("Location: admin/orders.php");
            exit;
        }
    } else {
        $error = "Invalid username or password!";
    }
}
require_once __DIR__ . '/views/index.view.php';
