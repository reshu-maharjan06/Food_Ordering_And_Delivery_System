<?php
$host = 'localhost'; 
$dbname = 'admin_db'; 
$user = 'root'; 
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
} catch(PDOException $e) { 
    die("DB Connection Error: " . $e->getMessage()); 
}

require_once __DIR__ . '/security.php';
?>
