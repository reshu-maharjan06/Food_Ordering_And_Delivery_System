<?php
session_start();
require_once __DIR__ . '/includes/db.php';
if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($_SESSION['role'] === 'customer') header("Location: landing.php");
    elseif ($_SESSION['role'] === 'admin') header("Location: admin/dashboard.php");
    elseif ($_SESSION['role'] === 'delivery') header("Location: delivery/dashboard.php");
    exit;
}
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (isset($_SESSION['user_id'])) {
        session_unset();
        session_destroy();
        session_start();
    }
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role, profile_pic FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
        if ($user['role'] === 'customer') header("Location: landing.php");
        elseif ($user['role'] === 'admin') header("Location: admin/dashboard.php");
        elseif ($user['role'] === 'delivery') header("Location: delivery/dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
require_once __DIR__ . '/views/index.view.php';
