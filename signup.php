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
$success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (isset($_SESSION['user_id'])) {
        session_unset();
        session_destroy();
        session_start();
    }
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    if ($password !== $confirm_password) {
        $error = "Passwords do not match! Please try again.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or Email already exists. Please choose another.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $pdo->prepare("INSERT INTO user (name, email, username, password_hash, role) VALUES (?, ?, ?, ?, 'customer')")
                    ->execute([$name, $email, $username, $hash]);
                $success = "Account created successfully! You can now log in.";
            } catch (Exception $e) {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
require_once __DIR__ . '/views/signup.view.php';
