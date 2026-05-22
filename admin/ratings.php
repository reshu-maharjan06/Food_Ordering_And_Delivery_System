<?php
session_start();
if(($_SESSION['role'] ?? '') !== 'admin') { header("Location: ../index.php"); exit; }
require_once __DIR__ . '/../includes/db.php'; $uid = $_SESSION['user_id'];
$ud = $pdo->prepare("SELECT username, profile_pic FROM user WHERE id=?"); $ud->execute([$uid]);
$user = $ud->fetch();
$_SESSION['username'] = $user['username'] ?? $_SESSION['username'];
$_SESSION['profile_pic'] = $user['profile_pic'] ?? $_SESSION['profile_pic'];
require_once __DIR__ . '/../views/admin/ratings.view.php';
