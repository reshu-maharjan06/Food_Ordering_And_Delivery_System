<?php
require_once __DIR__ . '/includes/db.php';

if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . getRoleRedirect($_SESSION['role']));
    exit;
}

$error = "";
$success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || 
              (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $username         = trim($_POST['username'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role             = 'customer';

    if (empty($name) || empty($email) || empty($username) || empty($password)) {
        $msg = 'All fields are required!';
        $isAjax ? sendResponse(false, $msg) : ($error = $msg);
    } elseif ($password !== $confirm_password) {
        $msg = 'Passwords do not match!';
        $isAjax ? sendResponse(false, $msg) : ($error = $msg);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM user WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $msg = 'Email or username is already taken!';
            $isAjax ? sendResponse(false, $msg) : ($error = $msg);
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO user (name, email, username, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            if ($ins->execute([$name, $email, $username, $hash, $role])) {
                $msg = 'Registration successful! You can now log in.';
                $isAjax ? sendResponse(true, $msg, ['redirect' => 'index.php']) : ($success = $msg);
            } else {
                $msg = 'Failed to create account.';
                $isAjax ? sendResponse(false, $msg) : ($error = $msg);
            }
        }
    }
}

require_once __DIR__ . '/views/signup.view.php';
