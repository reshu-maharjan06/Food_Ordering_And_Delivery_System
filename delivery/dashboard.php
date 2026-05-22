<?php
session_start();
require_once __DIR__ . '/../includes/security.php';
if(($_SESSION['role'] ?? '') !== 'delivery') { header("Location: ../index.php"); exit; }
$driverName = htmlspecialchars($_SESSION['username']);
$driverId   = (int)$_SESSION['user_id'];
require_once __DIR__ . '/../views/delivery/dashboard.view.php';
