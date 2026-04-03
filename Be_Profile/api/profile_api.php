<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/profile_db.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access. Please log in.']);
    exit;
}
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT name, email, phone, address FROM user WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo json_encode(['success' => true, 'data' => $user]);
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_profile') {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Name cannot be empty.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE user SET name = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->execute([$name, $phone, $address, $user_id]);
            $_SESSION['username'] = $name;
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
        exit;
    }
    if ($action === 'update_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'error' => 'All password fields are required.']);
            exit;
        }
        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'error' => 'New passwords do not match.']);
            exit;
        }
        if (strlen($new_password) < 8) {
            echo json_encode(['success' => false, 'error' => 'New password must be at least 8 characters long.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("SELECT password_hash FROM user WHERE id = ?");
            $stmt->execute([$user_id]);
            $hash = $stmt->fetchColumn();
            if (!password_verify($current_password, $hash)) {
                echo json_encode(['success' => false, 'error' => 'Current password is incorrect.']);
                exit;
            }
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE user SET password_hash = ? WHERE id = ?");
            $stmt->execute([$new_hash, $user_id]);
            echo json_encode(['success' => true, 'message' => 'Password updated successfully!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to update password: ' . $e->getMessage()]);
        }
        exit;
    }
    echo json_encode(['success' => false, 'error' => 'Invalid action.']);
    exit;
}
?>
