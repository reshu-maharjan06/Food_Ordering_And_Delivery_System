<?php
session_start();
require_once __DIR__ . '/../includes/security.php';
if(($_SESSION['role'] ?? '') !== 'customer') { header("Location: ../index.php"); exit; }
require_once __DIR__ . '/../views/customer/history.view.php';
