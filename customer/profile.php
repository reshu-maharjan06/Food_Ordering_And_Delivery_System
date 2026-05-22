<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if (($_SESSION['role'] ?? '') !== 'customer') { header("Location: ../index.php"); exit; }
$user_id = $_SESSION['user_id'];
$success = "";
$error = "";
$stmt = $pdo->prepare("SELECT name, email, phone, address, profile_pic FROM user WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action_type = $_POST['action_type'] ?? 'profile';
    if ($action_type === 'profile') {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $profile_pic = $user['profile_pic'] ?? null;

        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['profile_pic']['tmp_name'];
            $file_ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($file_ext, $allowed)) {
                // Remove old pic if exists
                if ($profile_pic && file_exists(__DIR__ . "/../" . $profile_pic)) {
                    @unlink(__DIR__ . "/../" . $profile_pic);
                }
                $new_name = "prof_" . $user_id . "_" . time() . "." . $file_ext;
                $dest = __DIR__ . "/../assets/img/profiles/" . $new_name;
                if (move_uploaded_file($tmp_name, $dest)) {
                    $profile_pic = "assets/img/profiles/" . $new_name;
                }
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE user SET name=?, phone=?, address=?, profile_pic=? WHERE id=?");
            $stmt->execute([$name, $phone, $address, $profile_pic, $user_id]);
            $success = "Profile updated successfully!";
            $_SESSION['username'] = $name;
            $_SESSION['profile_pic'] = $profile_pic;
            $user['name'] = $name;
            $user['phone'] = $phone;
            $user['address'] = $address;
            $user['profile_pic'] = $profile_pic;
        } catch (Exception $e) {
            $error = "Failed to update profile.";
        }
    } elseif ($action_type === 'delete_pic') {
        try {
            if ($user['profile_pic'] && file_exists(__DIR__ . "/../" . $user['profile_pic'])) {
                @unlink(__DIR__ . "/../" . $user['profile_pic']);
            }
            $stmt = $pdo->prepare("UPDATE user SET profile_pic=NULL WHERE id=?");
            $stmt->execute([$user_id]);
            $_SESSION['profile_pic'] = null;
            $user['profile_pic'] = null;
            $success = "Profile picture removed.";
        } catch (Exception $e) {
            $error = "Failed to remove profile picture.";
        }
    } elseif ($action_type === 'password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $stmt = $pdo->prepare("SELECT password_hash FROM user WHERE id=?");
        $stmt->execute([$user_id]);
        $hash = $stmt->fetchColumn();
        if (!password_verify($current_password, $hash)) {
            $error = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = "New password must be at least 6 characters long.";
        } else {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE user SET password_hash=? WHERE id=?");
            $stmt->execute([$new_hash, $user_id]);
            $success = "Password updated successfully!";
        }
    }
}
require_once __DIR__ . '/../views/customer/profile.view.php';
